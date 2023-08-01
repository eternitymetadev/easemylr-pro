<?php

namespace App\Exports;

use App\models\SecondaryAvailStock;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
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

class Report3Export implements FromCollection, WithHeadings, ShouldQueue, WithEvents
{
    protected $startdate;
    protected $enddate;
    protected $baseclient_id;
    protected $regclient_id;

    function __construct($startdate,$enddate,$baseclient_id,$regclient_id) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->baseclient_id = $baseclient_id;
        $this->regclient_id = $regclient_id;

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
            'DrsDetail:consignment_no,drs_no,created_at',
            'PrsDetail.PrsDriverTask:prs_id,pickup_date'
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
                // ageing formula = deliverydate - createdate / prs_pickupdate
                $start_date = $consignment->consignment_date;
                $end_date = $consignment->delivery_date;

                $date1 = new DateTime($start_date);
                $date2 = new DateTime($end_date);
                // Calculate the difference
                if(!$date1 || !$date2){
                    $age_diff = '';
                }else{
                    $interval = $date1->diff($date2);

                    // Get the difference in days
                    $age_diff = $interval->days;
                }

                $prspickup_date = @$consignment->PrsDetail->PrsDriverTask->pickup_date;
                if(!empty($prspickup_date)){
                    $prspickup_date = new DateTime($prspickup_date);
                }else{
                    $prspickup_date = '';
                }
                if(!empty($prspickup_date)){
                    if(!$prspickup_date || !$date2){
                        $pickup_diff = '';
                    }else{
                        $interval_prs = $prspickup_date->diff($date2);
                        $pickup_diff = $interval_prs->days;
                    }
                }else{
                    $pickup_diff = '';
                }
                
                if(!empty($consignment->prs_id)){
                    if($pickup_diff > 0){
                        $ageing_day = $pickup_diff;
                    }else{
                        if($age_diff < 0){
                            $ageing_day = '-';
                        }else{
                            $ageing_day = $age_diff;
                        }
                    }
                }else{
                    if($age_diff < 0){
                        $ageing_day = '-';
                    }else{
                        $ageing_day = $age_diff;
                    }
                }

                // tat formula = edd - createdate
                $start_date = $consignment->consignment_date;
                $end_date = $consignment->edd;

                $s_date1 = new DateTime($start_date);
                $e_date2 = new DateTime($end_date);
                // Calculate the difference
                if(!$s_date1 || !$e_date2){
                    $tat_diff = '';
                }else{
                    $interval = $s_date1->diff($e_date2);

                    // Get the difference in days
                    $tat_diff = $interval->days;
                }

                if($tat_diff < 0){
                    $tat_day = '-';
                }else{
                    $tat_day = $tat_diff;
                }                
                                
                if(!empty($consignment->consignment_date )){
                    $consignment_date = $consignment->consignment_date;
                }else{
                    $consignment_date = '-';
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

                if(!empty($consignment->DrsDetail->drs_no)){
                    $drs = 'DRS-'.@$consignment->DrsDetail->drs_no;
                }else{
                    $drs = '-';
                }
                // LR type
                if($consignment->lr_type == 0){ 
                    $lr_type = "FTL";
                }elseif($consignment->lr_type == 1 || $consignment->lr_type ==2){ 
                    $lr_type = "PTL";
                }else{ 
                    $lr_type = "-";
                } 
                // invoice no
                if(empty($consignment->order_id)){ 
                    if(!empty($consignment->ConsignmentItems)){
                        $invoices = array();
                        foreach($consignment->ConsignmentItems as $orders){ 
                            $invoices[] = $orders->invoice_no;
                        }
                        $order_item['invoices'] = implode(',', $invoices);
                    }
                }

                if(empty($consignment->invoice_no)){
                    $invoice_number =  $order_item['invoices'] ?? '-';
                }else{
                    $invoice_number =  $consignment->invoice_no ?? '-';
                }
                
                $arr[] = [
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
                    'consignment_id'      => @$consignment->id,
                    'lr_type'             => @$lr_type,
                    'invoice_number'      => @$invoice_number,
                    'regional_client'     => @$consignment->ConsignerDetail->GetRegClient->name,
                    'consigner_nick_name' => @$consignment->ConsignerDetail->nick_name,
                    'consigner_district'  => @$consignment->ConsignerDetail->district,
                    'consigner_postal'    => @$consignment->ConsignerDetail->postal_code,
                    'consignee_nick_name' => @$consignment->ConsigneeDetail->nick_name,
                    'consignee_district'  => @$consignment->ConsigneeDetail->GetZone->district,
                    'consignee_postal'    => @$consignment->ConsigneeDetail->postal_code,

                    'total_quantity'      => @$consignment->total_quantity,
                    'total_weight'        => @$consignment->total_weight,
                    'total_gross_weight'  => @$consignment->total_gross_weight,
                    
                    'tat'                 => @$tat_day,
                    'payment_type'        => @$consignment->payment_type,
                    'dispatch_date'       => @$consignment->consignment_date,
                    'delivery_status'     => @$consignment->delivery_status,
                    'ageing'              => @$ageing_day,
                    'delivery_date'       => @$consignment->delivery_date,
                    'issue'               => '',
                ];
            }
        }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'LR Date',
            'LR Number',
            'Type of Shipment',
            'Invoice Number',
            'Regional Client',
            'Consignor Name',
            'Consignor District',
            'Consignor PinCode',
            'Consignee Name',
            'Consignee District', 
            'Consignee PinCode',
            
            'Number of Box',
            'Net Weight',
            'Gross Weight',
            'Expected TAT',

            'Payment Mode',
            'Dispatch Date',
            'Shipment Status',
            'Ageing',
            'Delivery Date',
            'Issue',        
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(20);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(20);
     
            },
        ];
    }

}