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

class RegionalReport implements FromCollection, WithHeadings, ShouldQueue
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
                
                
                $arr[] = [
                    'consignment_id'      => @$consignment->id,
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
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
                    'tat'                 => $tatday,
                    'payment_type'        => @$consignment->payment_type,
                    'dispatch_date'       => @$consignment->consignment_date,
                    'delivery_status'     => @$consignment->delivery_status,
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
            'LR No',
            'LR Date',
            'Regional Client',
            'Consigner',
            'Consigner District',
            'Consigner PinCode',
            'Consignee Name',
            'Consignee District', 
            'Consignee PinCode',
            'Boxes',
            'Net Weight',
            'Gross Weight',
            'Expected Tat',
            'Payment Type',
            'Dispatch Date',
            'Delivery Status',
            'Delivery Date',
            'Issue',        
        ];
    }
}