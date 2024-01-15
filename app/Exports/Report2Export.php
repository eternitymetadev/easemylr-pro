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

class Report2Export implements FromCollection, WithHeadings, ShouldQueue
{

    protected $startdate;
    protected $enddate;
    protected $baseclient_id;
    protected $regclient_id;
    protected $branch_id;

    function __construct($startdate,$enddate,$baseclient_id,$regclient_id,$branch_id) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->baseclient_id = $baseclient_id;
        $this->regclient_id = $regclient_id;
        $this->branch_id = $branch_id;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = ConsignmentNote::query();

        // Select only the necessary columns
        $query = $query->select([
            'id',
            'lr_type',
            'regclient_id',
            'consigner_id',
            'consignee_id',
            'ship_to_id',
            'consignment_date',
            'payment_type',
            'vehicle_id',
            'total_quantity',
            'total_weight',
            'total_gross_weight',
            'total_freight',
            'transporter_name',
            'vehicle_type',
            'freight_on_delivery',
            'cod',
            'user_id',
            'branch_id',
            'to_branch_id',
            'driver_id',
            'edd',
            'status',
            'lr_mode',
            'delivery_status',
            'delivery_date',
            'signed_drs',
            'job_id',
            'reattempt_reason',
        ]);

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $baseclient_id = $this->baseclient_id;
        $regclient_id = $this->regclient_id;
        $branch_id = $this->branch_id;

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();
        
        $query = $query->where('status', '!=', 5)
        ->with(
            'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
            'ConsigneeDetail.GetZone:postal_code,district,state',
            'ShiptoDetail.GetZone:postal_code,district,state',
            'VehicleDetail:id,regn_no',
            'DriverDetail:id,name,fleet_id,phone', 
            'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
            'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
            'VehicleType:id,name',
            'DrsDetail:consignment_no,drs_no,created_at',
            'Branch:id,name',
            'ToBranch:id,name',
        );

        if ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id != 1) {
            $query = $query->whereIn('branch_id', $cc);
        }

        if ($branch_id !== null) {
            if ($branch_id) {
                $branch_id_array = explode(",", $branch_id);
                $query = $query->whereIn('branch_id', $branch_id_array);
            }
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
                    $drsdate = $consignment->DrsDetail->created_at;
                    $drs_date = $drsdate->format('d-m-Y');
                   }else{
                   $drs_date = '-';
                   }

                // lr type //
                if($consignment->lr_type == 0){ 
                    $lr_type = "FTL";
                     }elseif($consignment->lr_type == 1 || $consignment->lr_type ==2){ 
                        $lr_type = "PTL";
                         }else{ 
                            $lr_type = "-";
                            }

                // No of reattempt
                if($consignment->reattempt_reason != null){
                    $no_reattempt = count(json_decode($consignment->reattempt_reason,true));
                }else{
                    $no_reattempt = '';
                }

                // reatempted drs nos
                if(!empty($consignment->DrsDetailReattempted)){
                    $drs_nos = array();
                    foreach($consignment->DrsDetailReattempted as $reattemptDrs){ 
                        $drs_nos[] = $reattemptDrs->drs_no;
                    }
                    $reattempt_drs['drs_nos'] = implode('/', $drs_nos);
                }

                // delivery branch 
                if($consignment->lr_type == 0){
                    $delivery_branch = @$consignment->Branch->name;
                }else{
                    $delivery_branch = @$consignment->ToBranch->name;
                }

                $arr[] = [
                    'consignment_id'      => $consignment_id,
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
                    'drs_no'              => @$drs,
                    'reattempt_drsno'     => @$reattempt_drs['drs_nos'],
                    'drs_date'            => $drs_date,
                    'order_id'            => $order_id,
                    'booking_branch'      => @$consignment->Branch->name,
                    'delivery_branch'     => @$delivery_branch,
                    'base_client'         => @$consignment->ConsignerDetail->GetRegClient->BaseClient->client_name,
                    'regional_client'     => @$consignment->ConsignerDetail->GetRegClient->name,
                    'consigner_nick_name' => @$consignment->ConsignerDetail->nick_name,
                    'consigner_city'      => @$consignment->ConsignerDetail->city,
                    'consigner_postal'    => @$consignment->ConsignerDetail->postal_code,
                    'consignee_nick_name' => @$consignment->ConsigneeDetail->nick_name,
                    'contact_person'      => @$consignment->ConsigneeDetail->contact_name,
                    'consignee_phone'     => @$consignment->ConsigneeDetail->phone,
                    'consignee_city'      => @$consignment->ConsigneeDetail->city,
                    'consignee_postal'    => @$consignment->ConsigneeDetail->postal_code,
                    'consignee_district'  => @$consignment->ConsigneeDetail->GetZone->district,
                    'consignee_state'     => @$consignment->ConsigneeDetail->GetZone->state,
                    'Ship_to_name'        => @$consignment->ShiptoDetail->nick_name,
                    'Ship_to_city'        => @$consignment->ShiptoDetail->city,
                    'Ship_to_pin'         => @$consignment->ShiptoDetail->postal_code,
                    'Ship_to_district'    => @$consignment->ShiptoDetail->GetZone->district,
                    'Ship_to_state'       => @$consignment->ShiptoDetail->GetZone->state,
                    'invoice_no'          => $invno,
                    'invoice_date'        => $invdate,
                    'invoice_amt'         => $invamt,
                    'vehicle_no'          => @$consignment->VehicleDetail->regn_no,
                    'vehicle_type'        => @$consignment->vehicletype->name,
                    'transporter_name'    => @$consignment->transporter_name,
                    'total_quantity'      => $consignment->total_quantity,
                    'total_weight'        => $consignment->total_weight,
                    'total_gross_weight'  => $consignment->total_gross_weight,
                    'driver_name'         => @$consignment->DriverDetail->name,
                    'driver_phone'        => @$consignment->DriverDetail->phone,
                    'driver_fleet'        => @$consignment->DriverDetail->fleet_id,
                    'lr_status'           => $status,
                    'dispatch_date'       => @$consignment->consignment_date,
                    'delivery_date'       => @$consignment->delivery_date,
                    'delivery_status'     => @$consignment->delivery_status,
                    'tat'                 => $tatday,
                    //'delivery_mode'       => $deliverymode,
                    //'pod'                 => $pod,
                    'payment_type'        => @$consignment->payment_type,
                    'freight_on_delivery' => @$consignment->freight_on_delivery,
                    'cod'                 => @$consignment->cod,
                    'lr_type'             => @$lr_type,
                    'reattempt_reason'   => @$no_reattempt,
                ];
            }
        }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'LR No',
            'LR Date',
            'Delivered DRS No',
            'DRS Nos',
            'DRS Date',
            'Order No',
            'Booking Branch',
            'Delivery Branch',
            'Base Client',
            'Regional Client',
            'Consignor',
            'Consignor City',
            'Consignor PinCode',
            'Consignee Name',
            'Contact Person Name',
            'Consignee Phone',
            'Consignee city',
            'Consignee PinCode',
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
            //'Delivery Mode',
            //'POD',
            'Payment Type',
            'Freight on Delivery',
            'COD',
            'LR Type',
            'No of Reattempt',
        ];
    }
}