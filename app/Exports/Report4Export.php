<?php

namespace App\Exports;

use App\models\SecondaryAvailStock;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
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
use Session;
use Helper;
use Auth;
use DateTime;
use DB;

class Report4Export implements FromCollection, WithHeadings, ShouldQueue
{
    protected $startdate;
    protected $enddate;
    protected $baseclient_id;
    protected $regclient_id;
    // protected $awsUrl;

    function __construct($startdate,$enddate,$baseclient_id,$regclient_id) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->baseclient_id = $baseclient_id;
        $this->regclient_id = $regclient_id;
        // $this->awsUrl = $awsUrl;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = ConsignmentNote::query();

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $baseclient_id = $this->baseclient_id;
        $regclient_id = $this->regclient_id;
        // $awsUrl = $this->awsUrl;
        // dd($awsUrl);
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();
        
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

        if(isset($startdate) && isset($enddate)){
            $query = $query->whereBetween('consignment_date',[$startdate,$enddate]);                
        }
        if($baseclient_id){
            $query = $query->whereHas('ConsignerDetail.GetRegClient.BaseClient', function($q) use ($baseclient_id){
                $q->where('id', $baseclient_id);
            });
        }
        if($regclient_id){
            $query = $query->whereHas('ConsignerDetail.GetRegClient', function($q) use ($regclient_id){
                $q->where('id', $regclient_id);
            });
        }

        $consignments = $query->orderBy('id','ASC')->get();
        
        if($consignments->count() > 0){
            foreach ($consignments as $key => $consignment){
            
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

                // if(empty($consignment->order_id)){ 
                //     if(!empty($consignment->ConsignmentItems)){
                //         $order = array();
                //         $invoices = array();
                //         $inv_date = array();
                //         $inv_amt = array();
                //         foreach($consignment->ConsignmentItems as $orders){ 
                            
                //             $order[] = $orders->order_id;
                //             $invoices[] = $orders->invoice_no;
                //             $inv_date[] = Helper::ShowDayMonthYearslash($orders->invoice_date);
                //             $inv_amt[] = $orders->invoice_amount;
                //         }
                //         // $order_item['orders'] = implode('/', $order);
                //         // $order_item['invoices'] = implode('/', $invoices);
                //         // $invoice['date'] = implode(',', $inv_date);
                //         // $invoice['amt'] = implode(',', $inv_amt);

                //         if(!empty($orders->order_id)){
                //             $order_id = $orders->order_id;
                //         }else{
                //             $order_id = '-';
                //         }
                //     }else{
                //         $order_id = '-';
                //     }
                // }else{
                //     $order_id = $consignment->order_id;
                // }

                // if(empty($consignment->invoice_no)){
                //     $invno =  $order_item['invoices'] ?? '-';
                //     $invdate = $invoice['date']  ?? '-';
                //     $invamt = $invoice['amt']  ?? '-';
                //  }else{
                //   $invno =  $consignment->invoice_no ?? '-';
                //   $invdate = $consignment->invoice_date  ?? '-';
                //   $invamt = $consignment->invoice_amount  ?? '-';
                //  }
  
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
                }elseif($consignment->lr_mode == 2){
                    $deliverymode = 'ShipRider';
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
                // if($consignment->lr_mode == 0){
                //     if(empty($consignment->signed_drs)){
                //         $pod= 'Not Available'; 
                //     } else {
                //         $pod= 'Available';
                //     } 
                // } else if($consignment->lr_mode == 1){ 
                //     $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id','desc')->first();
        
                //     if(!empty($job->response_data)){
                //         $trail_decorator = json_decode($job->response_data);
                //         $img_group = array();
                //         foreach($trail_decorator->task_history as $task_img){
                //             if($task_img->type == 'image_added'){
                //                 $img_group[] = $task_img->description;
                //             }
                //         }
                //         if(empty($img_group)){
                //             $pod= 'Not Available';
                //         } else {
                //             $pod= 'Available';
                //         }
                //     }else{
                //         $pod= 'Not Available';
                //     }
                // }else{
                //     $getjobimg = DB::table('app_media')->where('consignment_no', $consignment->id)->get();
                //     $count_arra = count($getjobimg);
                //     if ($count_arra > 1) { 
                //         $pod= 'Available';
                //     }else{
                //         $pod= 'Not Available'; 
                //     }
                // }

                //pod img status
                $awsUrl = env('AWS_S3_URL');
                if ($consignment->lr_mode == 1) {
                    $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id', 'desc')->first();
                    
                    if (!empty($job->response_data)) {
                        $trail_decorator = json_decode($job->response_data);
                        $img_group = [];
                        
                        foreach ($trail_decorator->task_history as $task_img) {
                            if ($task_img->type == 'image_added') {
                                $img_group[] = $task_img->description;
                            }
                        }
                    }
                } elseif ($consignment->lr_mode == 0) {
                    // New path for lr_mode 0
                    $img = $awsUrl . '/pod_images/' . $consignment->signed_drs;
                    $pdfcheck = explode('.', $consignment->signed_drs);
                    
                    if (!empty($consignment->signed_drs)) {
                        $pod_img = $img;
                    } else {
                        $pod_img = "Not Available";
                    }
                } else { // Assuming this is a catch-all case
                    $getjobimg = DB::table('app_media')->where('consignment_no', $consignment->id)->get();
                    $count_arra = count($getjobimg);
                    
                    if ($count_arra > 1) {
                        $pods = [];
                        foreach ($getjobimg as $img) {
                            $pods[] = $img->pod_img;
                        }
                        $pod_img = implode(',', $pods);
                    } else {
                        $pod_img = "Not Available";
                    }
                }
                
                // If lr_mode is 1 and $img_group is not empty, override $pod_img
                if ($consignment->lr_mode == 1 && !empty($img_group)) {
                    $pods = [];
                    foreach ($img_group as $img) {
                        $pods[] = $img;
                    }
                    $pod_img = implode(',', $pods);
                }
                
                // If none of the conditions match, $pod_img will be "Not Available."
                if (!isset($pod_img)) {
                    $pod_img = "Not Available";
                }
              
                // lr type //
                if ($consignment->lr_type == 0){ 
                    $lr_type = "FTL";
                } elseif($consignment->lr_type == 1 || $consignment->lr_type ==2){ 
                    $lr_type = "PTL";
                } else{ 
                    $lr_type = "-";
                }
                            
                $arr[] = [
                    'branch'              => @$consignment->Branch->name,
                    'consignment_id'      => $consignment_id,
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
                    'drs_no'              => $drs,
                    'drs_date'            => $drs_date,
                    // 'order_id'            => $order_id,
                    'base_client'         => @$consignment->ConsignerDetail->GetRegClient->BaseClient->client_name,
                    'regional_client'     => @$consignment->ConsignerDetail->GetRegClient->name,
                    // 'consigner_nick_name' => @$consignment->ConsignerDetail->nick_name,
                    // 'consigner_city'      => @$consignment->ConsignerDetail->city,
                    // 'consignee_nick_name' => @$consignment->ConsigneeDetail->nick_name,
                    // 'contact_person'      => @$consignment->ConsigneeDetail->contact_name,
                    // 'consignee_phone'     => @$consignment->ConsigneeDetail->phone,
                    // 'consignee_city'      => @$consignment->ConsigneeDetail->city,
                    // 'consignee_postal'    => @$consignment->ConsigneeDetail->postal_code,
                    // 'consignee_district'  => @$consignment->ConsigneeDetail->GetZone->district,
                    // 'consignee_state'     => @$consignment->ConsigneeDetail->GetZone->state,
                    // 'Ship_to_name'        => @$consignment->ShiptoDetail->nick_name,
                    // 'Ship_to_city'        => @$consignment->ShiptoDetail->city,
                    // 'Ship_to_pin'         => @$consignment->ShiptoDetail->postal_code,
                    // 'Ship_to_district'    => @$consignment->ShiptoDetail->GetZone->district,
                    // 'Ship_to_state'       => @$consignment->ShiptoDetail->GetZone->state,
                    // 'invoice_no'          => $invno,
                    // 'invoice_date'        => $invdate,
                    // 'invoice_amt'         => $invamt,
                    // 'vehicle_no'          => @$consignment->VehicleDetail->regn_no,
                    // 'vehicle_type'        => @$consignment->vehicletype->name,
                    // 'transporter_name'    => @$consignment->transporter_name,
                    // 'total_quantity'      => $consignment->total_quantity,
                    // 'total_weight'        => $consignment->total_weight,
                    // 'total_gross_weight'  => $consignment->total_gross_weight,
                    // 'driver_name'         => @$consignment->DriverDetail->name,
                    // 'driver_phone'        => @$consignment->DriverDetail->phone,
                    // 'driver_fleet'        => @$consignment->DriverDetail->fleet_id,
                    'lr_status'           => $status,
                    'dispatch_date'       => @$consignment->consignment_date,
                    'delivery_date'       => @$consignment->delivery_date,
                    'delivery_status'     => @$consignment->delivery_status,
                    'tat'                 => $tatday,
                    'delivery_mode'       => $deliverymode,
                    'pod'                 => $pod_img,
                    'payment_type'        => @$consignment->payment_type,
                    'freight_on_delivery' => @$consignment->freight_on_delivery,
                    'cod'                 => @$consignment->cod,
                    'lr_type'             => @$lr_type,

                ];
            }
        }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'Branch',
            'LR No',
            'LR Date',
            'DRS No',
            'DRS Date',
            // 'Order No',
            'Base Client',
            'Regional Client',
            // 'Consignor',
            // 'Consignor City',
            // 'Consignee Name',
            // 'Contact Person Name',
            // 'Consignee Phone',
            // 'Consignee city',
            // 'Consignee Pin Code',
            // 'Consignee District', 
            // 'Consignee State',
            // 'ShipTo Name',
            // 'ShipTo City', 
            // 'ShipTo pin',            
            // 'ShipTo District',            
            // 'ShipTo State',           
            // 'Invoice No',
            // 'Invoice Date',
            // 'Invoice Amount',
            // 'Vehicle No',
            // 'Vehicle Type',
            // 'Transporter Name',
            // 'Boxes',
            // 'Net Weight',
            // 'Gross Weight',
            // 'Driver Name',
            // 'Driver Phone',
            // 'Driver Fleet',
            'Lr Status',
            'Dispatch Date',
            'Delivery Date',
            'Delivery Status',
            'Tat',
            'Delivery Mode',
            'POD',
            'Payment Type',
            'Freight on Delivery',
            'COD',
            'LR Type',
        ];
    }
}