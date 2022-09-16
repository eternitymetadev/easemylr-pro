<?php

namespace App\Exports;
use App\Models\ConsignmentNote;
use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\Driver;
use App\Models\Job;
use App\Models\User;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Role;
use App\Models\TransactionSheet;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Auth;
use DataTables;
use DB;
use Helper;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;

class MisReportExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // echo'<pre>'; print_r($_POST); die;
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();
        if($authuser->role_id ==1)
        {
            $consignments = $query->where('consignment_notes.status', '!=', 5)
            ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();
        }elseif($authuser->role_id == 4){
            $consignments = $query
            ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->where('consignment_notes.status', '!=', 5)
            ->whereIn('user_id', [$authuser->id, $user->id])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();                
        }else{
            $consignments = $query
            ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->where('consignment_notes.status', '!=', 5)
            ->where('branch_id', $cc)
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();
        } 
        // dd($consignments);
        if($consignments->count() > 0){
            foreach ($consignments as $key => $value){  
            //    echo'<pre>'; print_r($value); die;
            if(empty($value->order_id)){ 
                               $order = array();
                               $invoices = array();
                               $inv_date = array();
                               $inv_amt = array();
               foreach($value->ConsignmentItems as $itm){

                $order[] = $itm->order_id;
                $invoices[] = $itm->invoice_no;
                $inv_date[] = Helper::ShowDayMonthYearslash($itm->invoice_date);
                $inv_amt[] = $itm->invoice_amount;

               }
               $order_item['orders'] = implode(',', $order);
               $order_item['invoices'] = implode(',', $invoices);
               $invoice['date'] = implode(',', $inv_date);
               $invoice['amt'] = implode(',', $inv_amt);

               $order = $order_item['orders'];
            }else{
                $order = $value->order_id;
            }
               if(empty($value->invoice_no)){
                  $invno =  $order_item['invoices'] ?? '-';
                  $invdate = $invoice['date']  ?? '-';
                  $invamt = $invoice['amt']  ?? '-';
               }else{
                $invno =  $value->invoice_no ?? '-';
                $invdate = $invoice->invoice_date  ?? '-';
                $invamt = $invoice->invoice_amount  ?? '-';
               }

               if($value->status == 1){
                  $status = 'Active';
               }elseif($value->status == 2){
                 $status = 'Unverified';
               }elseif($value->status == 0){
                $status = 'Cancle';
               }else{
                $status = 'Unknown';
               }

               $start_date = strtotime($value->consignment_date);
               $end_date = strtotime($value->delivery_date);
               $tat = ($end_date - $start_date)/60/60/24;
               if(empty($value->delivery_date)){
                 $tatday = '-';
               }else{
                $tatday = $tat;
               }

               if(!empty($value->job_id)){
                 $deliverymode = 'Shadow';
               }else{
                $deliverymode = 'Manual';
               }


            
                $arr[] = [
                    'id'                    => $value->id,
                    'consignment_date'      => $value->consignment_date,
                    'order_id'              => $order,    
                    'base_client'           => @$value->ConsignerDetail->GetRegClient->BaseClient->client_name,
                    'regional_client'       => @$value->ConsignerDetail->GetRegClient->name,
                    'consigner_nick_name'   => @$value->ConsignerDetail->nick_name,
                    'consigner_city'        => @$value->ConsignerDetail->city,
                    'consignee_nick_name'   => @$value->ConsigneeDetail->nick_name,
                    'consignee_city'        => @$value->ConsigneeDetail->city,
                    'consignee_postal'      => @$value->ConsigneeDetail->postal_code, 
                    'consignee_district'    => @$value->ConsigneeDetail->district,      
                    'consignee_state'       => @$value->ConsigneeDetail->GetState->name,
                    'Ship_to_name'          => @$value->ShiptoDetail->nick_name,
                    'Ship_to_city'          => @$value->ShiptoDetail->city,
                    'Ship_to_pin'           => @$value->ShiptoDetail->postal_code,
                    'Ship_to_district'      => @$value->ShiptoDetail->district,
                    'Ship_to_state'         => @$value->ShiptoDetail->GetState->name,
                    'invoice_no'            => $invno,
                    'invoice_date'          => $invdate,
                    'invoice_amt'           => $invamt,
                    'vehicle_no'            => @$value->VehicleDetail->regn_no,
                    'vehicle_type'          => @$value->vehicletype->name,
                    'transporter_name'      => @$value->transporter_name,
                    'purchase_price'        => @$value->purchase_price,
                    'total_quantity'        => $value->total_quantity,
                    'total_weight'          => $value->total_weight,
                    'total_gross_weight'    => $value->total_gross_weight,
                    'driver_name'           => @$value->DriverDetail->name,
                    'driver_phone'          => @$value->DriverDetail->phone,
                    'driver_fleet'          => @$value->DriverDetail->fleet_id,
                    'lr_status'             => $status,
                    'dispatch_date'         => @$value->consignment_date,
                    'delivery_date'         => @$value->delivery_date,
                    'delivery_status'       => @$value->delivery_status,
                    'tat'                   => $tatday,
                    'delivery_mode'         => $deliverymode,

                ];
            }
        }                 
        return collect($arr);
    }

    public function headings(): array  
    {
        return [
            'Lr No',
            'Lr Date',
            'Order No',
            'Base Client',
            'Regional Client',
            'Consigner Name',
            'Consigner City',
            'Consignee Name',            
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
            'Purchase Price',
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
            'Delivery Mode'

        ];
    }
}