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
use Carbon\Carbon;

class Report2Export implements FromCollection, WithHeadings, ShouldQueue
{

    protected $startdate;
    protected $enddate;
    // protected $search;

    function __construct($startdate,$enddate) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        // $this->search = $search;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = ConsignmentNote::query();

        $startdate = $this->startdate;
        $enddate = $this->enddate;

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
            $consignments = $query->whereBetween('consignment_date',[$startdate,$enddate])->orderby('id','ASC')->take(500)->get()->toArray();
        }else {
            $consignments = $query->orderBy('id','ASC')->get()->toArray();
        }
        $consignments = array_chunk($consignments, 50, true);
        
        if(count($consignments)){
            foreach ($consignments as $value){
                foreach ($value as $consignment){
                // dd($consignment['consignment_date']);
            
                $start_date = strtotime($consignment['consignment_date']);
                $end_date = strtotime($consignment['delivery_date']);
                $tat = ($end_date - $start_date)/60/60/24;
                if(empty($consignment['delivery_date'])){
                    $tatday = '-';
                }else{
                    if($tat == 0){
                        $tatday = '0';
                    }else{
                        $tatday = $tat;
                    }
                }
                
                if(!empty($consignment['id'] )){
                    $consignment_id = ucfirst($consignment['id']);
                }else{
                    $consignment_id = '-';
                }

                if(!empty($consignment['consignment_date'] )){
                    $consignment_date = $consignment['consignment_date'];
                }else{
                    $consignment_date = '-';
                }

                if(empty($consignment['order_id'])){ 
                    if(!empty($consignment['ConsignmentItems'])){
                        $order = array();
                        $invoices = array();
                        $inv_date = array();
                        $inv_amt = array();
                        foreach($consignment['ConsignmentItems'] as $orders){ 
                            
                            $order[] = $orders['order_id'];
                            $invoices[] = $orders['invoice_no'];
                            $inv_date[] = Helper::ShowDayMonthYearslash($orders['invoice_date']);
                            $inv_amt[] = $orders['invoice_amount'];
                        }
                        $order_item['orders'] = implode('/', $order);
                        $order_item['invoices'] = implode('/', $invoices);
                        $invoice['date'] = implode(',', $inv_date);
                        $invoice['amt'] = implode(',', $inv_amt);

                        if(!empty($orders['order_id'])){
                            $order_id = $orders['order_id'];
                        }else{
                            $order_id = '-';
                        }
                    }else{
                        $order_id = '-';
                    }
                }else{
                    $order_id = $consignment['order_id'];
                }

                if(empty($consignment['invoice_no'])){
                    $invno =  $order_item['invoices'] ?? '-';
                    $invdate = $invoice['date']  ?? '-';
                    $invamt = $invoice['amt']  ?? '-';
                 }else{
                  $invno =  $consignment['invoice_no'] ?? '-';
                  $invdate = $consignment['invoice_date']  ?? '-';
                  $invamt = $consignment['invoice_amount']  ?? '-';
                 }
  
                 if($consignment['status'] == 1){
                    $status = 'Active';
                 }elseif($consignment['status'] == 2 || $consignment['status'] == 6){
                   $status = 'Unverified';
                 }elseif($consignment['status'] == 0){
                  $status = 'Cancel';
                 }else{
                  $status = 'Unknown';
                 }

                if($consignment['lr_mode'] == 1){
                    $deliverymode = 'Shadow';
                  }else{
                   $deliverymode = 'Manual';
                  }

                  if(!empty($consignment['drs_detail']['drs_no'])){
                    $drs = 'DRS-'.@$consignment['drs_detail']['drs_no'];
                  }else{
                    $drs = '-';
                  }

                  if(!empty($consignment['drs_detail']['created_at'])){
                    // $date = new \DateTime(@$consignment['drs_detail']->created_at, new \DateTimeZone('GMT-7'));
                    // $date->setTimezone(new \DateTimeZone('IST'));
                    $drsdate = $consignment['drs_detail']['created_at'];
                    // $drs_date = $drsdate->format('d-m-Y');
                    $drs_date = Carbon::parse($drsdate)->format('d-m-Y');
                   }else{
                   $drs_date = '-';
                   }

                   if($consignment['lr_mode'] == 1){
                    $deliverymode = 'Shadow'; 
                  }else{
                   $deliverymode = 'Manual';
                  }

                // pod status
                if($consignment['lr_mode'] == 0){
                    if(empty($consignment['signed_drs'])){
                        $pod= 'Not Available'; 
                    } else {
                        $pod= 'Available';
                    } 
                } else { 
                    $job = DB::table('jobs')->where('job_id', $consignment['job_id'])->orderBy('id','desc')->first();
        
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


                $arr[] = [
                    'consignment_id'      => $consignment_id,
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
                    'drs_no'              => $drs,
                    'drs_date'            => $drs_date,
                    'order_id'            => $order_id,
                    'base_client'         => @$consignment['consigner_detail']['get_reg_client']['base_client']['client_name'],
                    'regional_client'     => @$consignment['consigner_detail']['get_reg_client']['name'],
                    'consigner_nick_name' => @$consignment['consigner_detail']['nick_name'],
                    'consigner_city'      => @$consignment['consigner_detail']['city'],
                    'consignee_nick_name' => @$consignment['consignee_detail']['nick_name'],
                    'contact_person'      => @$consignment['consignee_detail']['contact_name'],
                    'consignee_phone'     => @$consignment['consignee_detail']['phone'],
                    'consignee_city'      => @$consignment['consignee_detail']['city'],
                    'consignee_postal'    => @$consignment['consignee_detail']['postal_code'],
                    'consignee_district'  => @$consignment['consignee_detail']['get_zone']['district'],
                    'consignee_state'     => @$consignment['consignee_detail']['get_zone']['state'],
                    'Ship_to_name'        => @$consignment['shipto_detail']['nick_name'],
                    'Ship_to_city'        => @$consignment['shipto_detail']['city'],
                    'Ship_to_pin'         => @$consignment['shipto_detail']['postal_code'],
                    'Ship_to_district'    => @$consignment['shipto_detail']['get_zone']['district'],
                    'Ship_to_state'       => @$consignment['shipto_detail']['get_zone']['state'],
                    'invoice_no'          => $invno,
                    'invoice_date'        => $invdate,
                    'invoice_amt'         => $invamt,
                    'vehicle_no'          => @$consignment['vehicle_detail']['regn_no'],
                    'vehicle_type'        => @$consignment['vehicle_type']['name'],
                    'transporter_name'    => @$consignment['transporter_name'],
                    // 'purchase_price'      => @$consignment['purchase_price'],
                    'total_quantity'      => $consignment['total_quantity'],
                    'total_weight'        => $consignment['total_weight'],
                    'total_gross_weight'  => $consignment['total_gross_weight'],
                    'driver_name'         => @$consignment['driver_detail']['name'],
                    'driver_phone'        => @$consignment['driver_detail']['phone'],
                    'driver_fleet'        => @$consignment['driver_detail']['fleet_id'],
                    'lr_status'           => $status,
                    'dispatch_date'       => @$consignment['consignment_date'],
                    'delivery_date'       => @$consignment['delivery_date'],
                    'delivery_status'     => @$consignment['delivery_status'],
                    'tat'                 => $tatday,
                    'delivery_mode'       => $deliverymode,
                    'pod'                 => $pod,
                    'payment_type'        => @$consignment['payment_type'],
                    'freight_on_delivery' => @$consignment['freight_on_delivery'],
                    'cod'                 => @$consignment['cod'],

                ];
            }
        }
    }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'LR No',
            'LR Date',
            'DRS No',
            'DRS Date',
            'Order No',
            'Base Client',
            'Regional Client',
            'Consigner',
            'Consigner City',
            'Consignee Name',
            'Contact Person Name',
            'Consignee Phone',
            'Consignee city',
            'Consignee Pin Code',
            'Consignee District', 
            'Consignee State',
            'ShipTo Name',
            'ShipTo City', 
            'ShipTo pin',            
            'ShipTo District',            
            'ShipTo State',           
            'Invoice No',
            'Invoice Date',
            'Invoice Amount',
            'Vehicle No',
            'Vehicle Type',
            'Transporter Name',
            // 'Purchase Price',
            'Boxes',
            'Net Weight',
            'Gross Weight',
            'Driver Name',
            'Driver Phone',
            'Driver Fleet',
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
        ];
    }
}
