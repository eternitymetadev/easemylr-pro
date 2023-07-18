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

class Report3Export implements FromCollection, WithHeadings, ShouldQueue
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
                
                $arr[] = [
                    'consignment_date'    => Helper::ShowDayMonthYearslash($consignment_date),
                    'consignment_id'      => @$consignment->id,
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
}