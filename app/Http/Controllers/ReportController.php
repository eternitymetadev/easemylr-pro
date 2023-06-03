<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Location;
use App\Models\TransactionSheet;
use App\Models\Vehicle;
use App\Models\Role;
use App\Models\VehicleType;
use App\Models\User;
use App\Exports\MisReportExport;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Report1Export;
use App\Exports\Report2Export;
use Session;
use Config;
use Auth; 
use DB;
use QrCode;
use Storage;
use Validator;
use DataTables;
use Helper;
use Response;
use URL;

class ReportController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
      $this->title =  "MIS Reports";
      $this->segment = \Request::segment(2);
    }

    public function consignmentReportsAll(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $sessionperitem = Session::get('peritem');
        if(!empty($sessionperitem)){
            $peritem = $sessionperitem;
        }else{
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();
        
        if($request->ajax()){
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }
            
            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $regclient = explode(',',$authuser->regionalclient_id);
            $cc = explode(',',$authuser->branch_id);
            $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

            $query = $query
                ->where('status', '!=', 5)
                ->with(
                    'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
                );

            if($authuser->role_id ==1)
            {
                $query = $query;            
            }elseif($authuser->role_id == 4){
                $query = $query->whereIn('regclient_id', $regclient);   
            }else{
                $query = $query->whereIn('branch_id', $cc);
            }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                            $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('ConsigneeDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($cneequery)use($search,$searchT) {
                            $cneequery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    });

                });
            }

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }
            
            $startdate = $request->startdate;
            $enddate = $request->enddate;

            if(isset($startdate) && isset($enddate)){
                $consignments = $query->whereBetween('consignment_date',[$startdate,$enddate])->orderby('created_at','DESC')->paginate($peritem);
            }else {
                $consignments = $query->orderBy('id','DESC')->paginate($peritem);
            }

            $html =  view('consignments.consignment-reportAll-ajax',['prefix'=>$this->prefix,'consignments' => $consignments,'peritem'=>$peritem])->render();
            // $consignments = $consignments->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = $query
            ->where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
            );

        if($authuser->role_id ==1) 
        {
            $query = $query;            
        }elseif($authuser->role_id == 4){
            $query = $query->whereIn('regclient_id', $regclient);   
        }else{
            $query = $query->whereIn('branch_id', $cc);
        }
        
        $consignments = $query->orderBy('id','DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());
        
        return view('consignments.consignment-reportAll', ['consignments' => $consignments, 'prefix' => $this->prefix,'peritem'=>$peritem]);
    }


    // MIS report1 get records
    public function consignmentReports(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $sessionperitem = Session::get('peritem');
        if(!empty($sessionperitem)){
            $peritem = $sessionperitem;
        }else{
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();
        
        if($request->ajax()){
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }
            
            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $regclient = explode(',',$authuser->regionalclient_id);
            $cc = explode(',',$authuser->branch_id);
            $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

            $query = $query->where('status', '!=', 5)
            ->with('ConsignmentItems', 'ConsignerDetail.Zone', 'ConsigneeDetail.Zone', 'ShiptoDetail.Zone', 'VehicleDetail', 'DriverDetail','ConsignerDetail.GetRegClient.BaseClient','vehicletype');
               
            if($authuser->role_id ==1){
                $query;
            }
            elseif($authuser->role_id ==4){
                $query = $query->whereIn('regclient_id', $regclient);
            }
            elseif($authuser->role_id ==7){
                $query = $query->whereIn('regclient_id', $regclient);
            }
            else{
                $query = $query->whereIn('branch_id', $cc);
            }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                            $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('ConsigneeDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($cneequery)use($search,$searchT) {
                            $cneequery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    });
                });
            }

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }
            
            $startdate = $request->startdate;
            $enddate = $request->enddate;

            if(isset($startdate) && isset($enddate)){
                $consignments = $query->whereBetween('consignment_date',[$startdate,$enddate])->orderby('created_at','DESC')->paginate($peritem);
            }else {
                $consignments = $query->orderBy('id','DESC')->paginate($peritem);
            }

            $html =  view('consignments.mis-report-list-ajax',['prefix'=>$this->prefix,'consignments' => $consignments,'peritem'=>$peritem])->render();
            // $consignments = $consignments->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = $query
                ->where('status', '!=', 5)
                ->with('ConsignmentItems', 'ConsignerDetail.Zone', 'ConsigneeDetail.Zone', 'ShiptoDetail.Zone', 'VehicleDetail', 'DriverDetail','ConsignerDetail.GetRegClient.BaseClient','vehicletype');

        if($authuser->role_id ==1){
            $query;
        }
        elseif($authuser->role_id ==4){
            $query = $query->whereIn('regclient_id', $regclient);
        }
        elseif($authuser->role_id ==7){
            $query = $query->whereIn('regclient_id', $regclient);
        }
        else{
            $query = $query->whereIn('branch_id', $cc);
        }
        
        $consignments = $query->orderBy('id','DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());
        
        return view('consignments.mis-report-list', ['consignments' => $consignments, 'prefix' => $this->prefix,'peritem'=>$peritem]);
    }
    
    // =============================Admin Report ============================= //
       
    public function adminReport1(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
      
            $query = Consigner::query();
            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $regclient = explode(',',$authuser->regionalclient_id); 
            $cc = explode(',',$authuser->branch_id);
          
                $consigners = DB::table('consigners')->select('consigners.*', 'regional_clients.name as regional_clientname','base_clients.client_name as baseclient_name', 'zones.state as consigner_state','consignees.nick_name as consignee_nick_name', 'consignees.contact_name as consignee_contact_name', 'consignees.phone as consignee_phone', 'consignees.postal_code as consignee_postal_code', 'consignees.district as consignee_district','consigne_stat.state as consignee_state')
                ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                ->join('consignees', 'consignees.consigner_id', '=', 'consigners.id')
                ->leftjoin('zones', 'zones.postal_code', '=', 'consigners.postal_code')
                ->leftjoin('zones as consigne_stat', 'consigne_stat.postal_code', '=', 'consignees.postal_code')
                ->get();
                

        return view('consignments.admin-report1',["prefix" => $this->prefix,'adminrepo' => $consigners]);
    }

    public function adminReport2(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        
        $lr_data = DB::table('consignment_notes')->select('consignment_notes.*','consigners.nick_name as consigner_nickname','regional_clients.name as regional_client_name','base_clients.client_name as base_client_name', 'locations.name as locations_name')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->leftjoin('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                ->join('locations', 'locations.id', '=', 'regional_clients.location_id')
                ->get();
        return view('consignments.admin-report2',["prefix" => $this->prefix, 'lr_data' => $lr_data]);
    }

    public function exportExcelReport1(Request $request)
    {
        return Excel::download(new Report1Export($request->startdate,$request->enddate), 'mis_report1.csv');
    }

    // public function exportExcelReport2(Request $request)
    // {
    //     return Excel::download(new Report2Export($request->startdate,$request->enddate), 'mis_report2.csv');
    // }

    public function exportExcelReport2(Request $request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $fileName = 'mis_report2.csv';

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = ConsignmentNote::query();
        $query = $query->where('status', '!=', 5)
        ->with(
            'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
            'ConsignerDetail.GetZone',
            'ConsigneeDetail.GetZone',
            'ShiptoDetail.GetZone',
            'VehicleDetail:id,regn_no',
            'DriverDetail:id,name,fleet_id,phone', 
            'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
            'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
            'VehicleType:id,name',
            'DrsDetail:consignment_no,drs_no,created_at'
        );

        if($authuser->role_id ==1)
        {
            $query = $query;            
        }elseif($authuser->role_id == 4){
            $query = $query->whereIn('regclient_id', $regclient);   
        }else{
            $query = $query->whereIn('branch_id', $cc);
        }


        $consignments = $query->orderBy('id','DESC')->get();
        // $consignments = json_decode($consignments);
        // echo "<pre>"; print_r($consignments); die;

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('LR No','LR Date','DRS No','DRS Date','Order No','Base Client','Regional Client','Consigner','Consigner City','Consignee Name','Contact Person Name','Consignee Phone','Consignee city','Consignee Pin Code','Consignee District','Consignee State','ShipTo Name','ShipTo City','ShipTo pin','ShipTo District','ShipTo State','Invoice No','Invoice Date','Invoice Amount','Vehicle No','Vehicle Type','Transporter Name','Boxes','Net Weight','Gross Weight','Driver Name','Driver Phone','Driver Fleet','Lr Status','Dispatch Date','Delivery Date','Delivery Status','Tat','Delivery Mode','POD','Payment Type','Freight on Delivery','COD', 'LR Type');

        $callback = function() use($consignments, $columns) {
             $file = fopen('php://output', 'w');
             fputcsv($file, $columns);
            // if($consignments->count() > 0){
                // $consignments->chunk(100, function ($consignments) {
                foreach ($consignments as $consignment){
                    $start_date = strtotime($consignment->consignment_date);
                    $end_date = strtotime($consignment->delivery_date);
                    $tat = ($end_date - $start_date)/60/60/24;
                    if(empty($consignment->delivery_date)){
                        $tatday = '-';
                    }else{
                        if($tat == 0){
                            $tatday = '0';
                        }else{
                            $tatday = $tat;
                        }
                    }
                    
                    if(!empty($consignment->id )){
                        $consignment_id = ucfirst($consignment->id);
                    }else{
                        $consignment_id = '-';
                    }
    
                    if(!empty($consignment->consignment_date )){
                        $consignment_date = $consignment->consignment_date;
                    }else{
                        $consignment_date = '-';
                    }
    
                    if(empty($consignment->order_id)){ 
                        if(!empty($consignment->ConsignmentItems)){
                            $order = array();
                            $invoices = array();
                            $inv_date = array();
                            $inv_amt = array();
                            foreach($consignment->ConsignmentItems as $orders){ 
                                
                                $order[] = $orders->order_id;
                                $invoices[] = $orders->invoice_no;
                                $inv_date[] = Helper::ShowDayMonthYearslash($orders->invoice_date);
                                $inv_amt[] = $orders->invoice_amount;
                            }
                            $order_item['orders'] = implode('/', $order);
                            $order_item['invoices'] = implode('/', $invoices);
                            $invoice['date'] = implode(',', $inv_date);
                            $invoice['amt'] = implode(',', $inv_amt);
    
                            if(!empty($orders->order_id)){
                                $order_id = $orders->order_id;
                            }else{
                                $order_id = '-';
                            }
                        }else{
                            $order_id = '-';
                        }
                    }else{
                        $order_id = $consignment->order_id;
                    }
    
                    if(empty($consignment->invoice_no)){
                        $invno =  $order_item['invoices'] ?? '-';
                        $invdate = $invoice['date']  ?? '-';
                        $invamt = $invoice['amt']  ?? '-';
                     }else{
                      $invno =  $consignment->invoice_no ?? '-';
                      $invdate = $consignment->invoice_date  ?? '-';
                      $invamt = $consignment->invoice_amount  ?? '-';
                     }
      
                     if($consignment->status == 1){
                        $status = 'Active';
                     }elseif($consignment->status == 2 || $consignment->status == 6){
                       $status = 'Unverified';
                     }elseif($consignment->status == 0){
                      $status = 'Cancel';
                     }else{
                      $status = 'Unknown';
                     }
    
                    if($consignment->lr_mode == 1){
                        $deliverymode = 'Shadow';
                      }else{
                       $deliverymode = 'Manual';
                      }
    
                      if(!empty($consignment->DrsDetail->drs_no)){
                        $drs = 'DRS-'.@$consignment->DrsDetail->drs_no;
                      }else{
                        $drs = '-';
                      }
    
                      if(!empty($consignment->DrsDetail->created_at)){
                        // $date = new \DateTime(@$consignment->DrsDetail->created_at, new \DateTimeZone('GMT-7'));
                        // $date->setTimezone(new \DateTimeZone('IST'));
                        $drsdate = $consignment->DrsDetail->created_at;
                        $drs_date = $drsdate->format('d-m-Y');
                       }else{
                       $drs_date = '-';
                       }
    
                       if($consignment->lr_mode == 1){
                        $deliverymode = 'Shadow'; 
                      }else{
                       $deliverymode = 'Manual';
                      }
    
                    // pod status
                    if($consignment->lr_mode == 0){
                        if(empty($consignment->signed_drs)){
                            $pod= 'Not Available'; 
                        } else {
                            $pod= 'Available';
                        } 
                    } else { 
                        $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id','desc')->first();
            
                        if(!empty($job->response_data)){
                            $trail_decorator = json_decode($job->response_data);
                            $img_group = array();
                            foreach($trail_decorator->task_history as $task_img){
                                if($task_img->type == 'image_added'){
                                    $img_group[] = $task_img->description;
                                }
                            }
                            if(empty($img_group)){
                                $pod= 'Not Available';
                            } else {
                                $pod= 'Available';
                            }
                        }else{
                            $pod= 'Not Available';
                        }
                    }
                    // lr type //
                    if($consignment->lr_type == 0){ 
                        $lr_type = "FTL";
                    }elseif($consignment->lr_type == 1 || $consignment->lr_type ==2){
                        $lr_type = "PTL";
                    }else{ 
                        $lr_type = "-";
                    }
                    
                    $row['consignment_id']      = @$consignment_id;
                    $row['consignment_date']    = Helper::ShowDayMonthYearslash($consignment_date);
                    $row['drs_no']              = @$drs;
                    $row['drs_date']            = @$drs_date;
                    $row['order_id' ]           = @$order_id;
                    $row['base_client'] = @$consignment->ConsignerDetail->GetRegClient->BaseClient->client_name;
                    $row['regional_client']     = @$consignment->ConsignerDetail->GetRegClient->name;
                    $row['consigner_nick_name'] = @$consignment->ConsignerDetail->nick_name;
                    $row['consigner_city']      = @$consignment->ConsignerDetail->city;
                    $row['consignee_nick_name'] = @$consignment->ConsigneeDetail->nick_name;
                    $row['contact_person']      = @$consignment->ConsigneeDetail->contact_name;
                    $row['consignee_phone']     = @$consignment->ConsigneeDetail->phone;
                    $row['consignee_city']      = @$consignment->ConsigneeDetail->city;
                    $row['consignee_postal']    = @$consignment->ConsigneeDetail->postal_code;
                    $row['consignee_district']  = @$consignment->ConsigneeDetail->GetZone->district;
                    $row['consignee_state']     = @$consignment->ConsigneeDetail->GetZone->state;
                    $row['Ship_to_name']        = @$consignment->ShiptoDetail->nick_name;
                    $row['Ship_to_city']        = @$consignment->ShiptoDetail->city;
                    $row['Ship_to_pin']         = @$consignment->ShiptoDetail->postal_code;
                    $row['Ship_to_district']    = @$consignment->ShiptoDetail->GetZone->district;
                    $row['Ship_to_state']       = @$consignment->ShiptoDetail->GetZone->state;
                    $row['invoice_no']          = @$invno;
                    $row['invoice_date']        = @$invdate;
                    $row['invoice_amt']         = @$invamt;
                    $row['vehicle_no']          = @$consignment->VehicleDetail->regn_no;
                    $row['vehicle_type']        = @$consignment->vehicletype->name;
                    $row['transporter_name']    = @$consignment->transporter_name;
                    $row['total_quantity']      = $consignment->total_quantity;
                    $row['total_weight']        = $consignment->total_weight;
                    $row['total_gross_weight']  = $consignment->total_gross_weight;
                    $row['driver_name']         = @$consignment->DriverDetail->name;
                    $row['driver_phone']        = @$consignment->DriverDetail->phone;
                    $row['driver_fleet']        = @$consignment->DriverDetail->fleet_id;
                    $row['lr_status']           = @$status;
                    $row['dispatch_date']       = @$consignment->consignment_date;
                    $row['delivery_date']       = @$consignment->delivery_date;
                    $row['delivery_status']     = @$consignment->delivery_status;
                    $row['tat']                 = @$tatday;
                    $row['delivery_mode']       = @$deliverymode;
                    $row['pod']                 = @$pod;
                    $row['payment_type']        = @$consignment->payment_type;
                    $row['freight_on_delivery'] = @$consignment->freight_on_delivery;
                    $row['cod']                 = @$consignment->cod;
                    $row['lr_type']             = @$lr_type;
                    
                    
                    fputcsv($file, $row);

                    }
            fclose($file);
        };

       return response()->stream($callback, 200, $headers);
    }

}
