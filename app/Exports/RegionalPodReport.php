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

class RegionalPodReport implements FromCollection, WithHeadings, ShouldQueue, WithEvents
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
        ->whereDate('consignment_date', '>=', now()->subDays(45));
        // ->whereMonth('consignment_date', date('m'))->whereYear('consignment_date', date('Y'));

        $consignments = $query->orderBy('id','desc')->get();
        
        if($consignments->count() > 0){
            foreach ($consignments as $key => $consignment){
            
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

                // end pod image status
                
                $arr[] = [
                    'consignment_id'      => @$consignment->id,
                    'delivery_status'     => @$consignment->delivery_status,
                    'pod'                 => @$pod_img,
                ];
            }
        }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'LR Number',
            'Shipment Status',
            'Pod Status',     
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(20);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(11);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(19);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(14);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(14);

                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(6);
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(6);
     
            },
        ];
    }
}