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
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Maatwebsite\Excel\Facades\Excel;
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
      $this->title =  "Secondary Reports";
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
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $lastsevendays = \Carbon\Carbon::today()->subDays(7);
        $date = Helper::yearmonthdate($lastsevendays);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = $query
            ->where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
                'ConsignerDetail:regionalclient_id,id,nick_name,city,postal_code,district,state_id',
                'ConsignerDetail.GetState:id,name',
                'ConsigneeDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ConsigneeDetail.GetState:id,name', 
                'ShiptoDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ShiptoDetail.GetState:id,name',
                'VehicleDetail:id,regn_no', 
                'DriverDetail:id,name,fleet_id,phone', 
                'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
                'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
                'VehicleType:id,name');

        if($authuser->role_id ==1)
        {
            $query = $query;            
        }elseif($authuser->role_id == 4){
            $query = $query->whereIn('user_id', [$authuser->id, $user->id]);   
        }else{
            $query = $query->where('branch_id', $cc);
        }

        if($request->ajax()){
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            if($authuser->role_id ==1)
            {
                $query = $query;            
            }elseif($authuser->role_id == 4){
                $query = $query->whereIn('user_id', [$authuser->id, $user->id]);   
            }else{
                $query = $query->where('branch_id', $cc);
            }

            $startdate = $request->startdate;
            $enddate = $request->enddate;

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

            if(isset($startdate) && isset($enddate)){
                $consignments = $query->whereBetween('consignment_date',[$startdate,$enddate])->orderby('created_at','DESC')->paginate($peritem);
                // $consignments = $consignments->appends($request->query());
            }else {
                $consignments = $query->orderBy('id','DESC')->paginate($peritem);
                // $consignments = $consignments->appends($request->query());
            }

            $html =  view('consignments.consignment-reportAll-ajax',['prefix'=>$this->prefix,'consignments' => $consignments,'peritem'=>$peritem])->render();

            return response()->json(['html' => $html]);
        }

        // $consignments = json_decode(json_encode($query->orderBy('id','DESC')->paginate(100)), true);
        
        $consignments = $query->orderBy('id','DESC')->paginate($peritem);
        // echo "<pre>"; print_r($consignments); die;
        
        return view('consignments.consignment-reportAll', ['consignments' => $consignments, 'prefix' => $this->prefix,'peritem'=>$peritem]);
    }
    
    public function getFilterReportall(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        if($authuser->role_id ==1){
            // $query = $query
            // ->where('status', '!=', 5)
            // ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            // ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','VehicleType')->orderBy('id','DESC')->get();

            $query = $query
            ->where('status', '!=', 5)
            ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
                'ConsignerDetail:regionalclient_id,id,nick_name,city,postal_code,district,state_id',
                'ConsignerDetail.GetState:id,name',
                'ConsigneeDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ConsigneeDetail.GetState:id,name', 
                'ShiptoDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ShiptoDetail.GetState:id,name',
                'VehicleDetail:id,regn_no', 
                'DriverDetail:id,name,fleet_id,phone', 
                'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
                'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
                'VehicleType:id,name')
            ->orderBy('id','DESC')->get();
        }elseif($authuser->role_id == 4){
            $query = $query->whereIn('user_id', [$authuser->id, $user->id])
            ->where('status', '!=', 5)
            ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','VehicleType')->orderBy('id','DESC')->get();  

        }else{
            $query = $query->whereIn('branch_id', $cc)
            ->where('status', '!=', 5)
            ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','VehicleType')->orderBy('id','DESC')->get();
        }

        $consignments = json_decode(json_encode($query), true);

        $response['fetch'] = $consignments;
        $response['success'] = true;
        $response['messages'] = 'Succesfully loaded';
        return Response::json($response);
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
          
                $consigners = DB::table('consigners')->select('consigners.*', 'regional_clients.name as regional_clientname','base_clients.client_name as baseclient_name', 'states.name as state_id','consignees.nick_name as consignee_nick_name', 'consignees.contact_name as consignee_contact_name', 'consignees.phone as consignee_phone', 'consignees.postal_code as consignee_postal_code', 'consignees.district as consignee_district','consignees.state_id as consignee_states','consigne_stat.name as consignee_state')
                ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                ->join('consignees', 'consignees.consigner_id', '=', 'consigners.id')
                ->leftjoin('states', 'states.id', '=', 'consigners.state_id')
                ->leftjoin('states as consigne_stat', 'consigne_stat.id', '=', 'consignees.state_id')
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

    public function exportExcelReport2(Request $request)
    {
      return Excel::download(new Report2Export($request->startdate,$request->enddate), 'sec_reports.csv');
    }

}
