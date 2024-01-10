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

        $startDate = now()->subDays(90);

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
            'purchase_price',
            'freight_on_delivery',
            'cod',
            'user_id',
            'branch_id',
            'driver_id',
            'edd',
            'status',
            'lr_mode',
            'delivery_status',
            'delivery_date',
            'signed_drs',
            'job_id'
        ]);

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $baseclient_id = $this->baseclient_id;
        $regclient_id = $this->regclient_id;
        $branch_id = $this->branch_id;
        $startDate = now()->subDays(90);
        
        $query = $query->where('status', '!=', 5)
        ->where('consignment_date', '>=', $startDate)
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
                    // $date = new \DateTime(@$consignment->DrsDetail->created_at, new \DateTimeZone('GMT-7'));
                    // $date->setTimezone(new \DateTimeZone('IST'));
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

                $arr[] = [
                    'consignment_id'      => $consignment_id,
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
                    'drs_no'              => $drs,
                    'drs_date'            => $drs_date,
                    'order_id'            => $order_id,
                    'base_client'         => @$consignment->ConsignerDetail->GetRegClient->BaseClient->client_name,
                    'regional_client'     => @$consignment->ConsignerDetail->GetRegClient->name,
                    'consigner_nick_name' => @$consignment->ConsignerDetail->nick_name,
                    'consigner_city'      => @$consignment->ConsignerDetail->city,
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
                    // 'purchase_price'      => @$consignment->purchase_price,
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
            'DRS No',
            'DRS Date',
            'Order No',
            'Base Client',
            'Regional Client',
            'Consignor',
            'Consignor City',
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
            // 'Delivery Mode',
            // 'POD',
            'Payment Type',
            'Freight on Delivery',
            'COD',
            'LR Type',
        ];
    }
}