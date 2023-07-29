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

class RegionalReport implements FromCollection, WithHeadings, ShouldQueue, WithEvents
{
    protected $regional_id;
    

    function __construct($regional_id) {
        $this->regional_id = $regional_id;
        
    }


    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $current_month = date('m');
        $regional_client = $this->regional_id;
        
        // $authuser = Auth::user();
        // $role_id = Role::where('id','=',$authuser->role_id)->first();
        // $regclient = explode(',',$authuser->regionalclient_id);
        // $cc = explode(',',$authuser->branch_id);
        // $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();
        
        
        $query = ConsignmentNote::where('status', '!=', 5)
        ->with(
            'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
            'ConsignerDetail.GetZone',
            'ConsigneeDetail.GetZone',
            'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
            'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
        )->where('regclient_id',$regional_client)
        ->whereMonth('consignment_date', date('m'))->whereYear('consignment_date', date('Y'));

        $consignments = $query->orderBy('id','ASC')->get();
        
        if($consignments->count() > 0){
            foreach ($consignments as $key => $consignment){
            
                // ageing formula = deliverydate - createdate
                $start_date = strtotime($consignment->consignment_date);
                $end_date = strtotime($consignment->delivery_date);
                $age_diff = ($end_date - $start_date)/60/60/24;

                if($age_diff < 0){
                    $ageing_day = '-';
                }else{
                    $ageing_day = $age_diff;
                }

                // tat formula = edd - createdate
                $start_date = strtotime($consignment->consignment_date);
                $end_date = strtotime($consignment->edd);
                $tatday = ($end_date - $start_date)/60/60/24;

                if($tatday < 0){
                    $tat_day = '-';
                }else{
                    $tat_day = $tatday;
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
                        $order_item['invoices'] = implode('/', $invoices);
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
                    'total_quantity'      => $consignment->total_quantity,
                    'total_weight'        => $consignment->total_weight,
                    'total_gross_weight'  => $consignment->total_gross_weight,
                    'tat'                 => $tat_day,
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
            'Consigner',
            'Consigner District',
            'Consigner PinCode',
            'Consignee Name',
            'Consignee District', 
            'Consignee PinCode',
            'Number of Box',
            'Net Weight',
            'Gross Weight',
            'Expected TAT',
            'Payment Type',
            'Dispatch Date',
            'Delivery Status',
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