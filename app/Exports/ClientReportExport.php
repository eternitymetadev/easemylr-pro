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
use DB;

class ClientReportExport implements FromCollection, WithHeadings, ShouldQueue
{

    protected $startdate;
    protected $enddate;
    // protected $search;
    protected $regclient;

    function __construct($regclient,$startdate,$enddate) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        // $this->search = $search;
        $this->regclient = $regclient;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = ConsignmentNote::query();
        
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        // $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = $query
                ->where('status', '!=', 5)
                ->with(
                    'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
                );
        
    
        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $regclient = $this->regclient;
        
        if(isset($regclient)){
            $query = $query->where('regclient_id',$regclient);
        }
        if(isset($startdate) && isset($enddate)){
            $query = $query->whereBetween('consignment_date',[$startdate,$enddate]);
        } 
        if(isset($startdate) && isset($enddate) && isset($regclient)){
            $query = $query->whereBetween('consignment_date',[$startdate,$enddate])->where('regclient_id',$regclient);
        } 
        else {
            $query = $query;
        }

        $consignments = $query->orderBy('id','ASC')->get();

        if($consignments->count() > 0){
            foreach ($consignments as $key => $consignment){
                $start_date = strtotime($consignment->consignment_date);
                $end_date = strtotime($consignment->delivery_date);
                $tat = ($end_date - $start_date)/60/60/24;
                
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
                        $order_item['orders'] = implode(',', $order);
                        $order_item['invoices'] = implode(',', $invoices);
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
                }elseif($consignment->status == 2){
                    $status = 'Unverified';
                }elseif($consignment->status == 0){
                    $status = 'Cancle';
                }else{
                    $status = 'Unknown';
                }
                
                if((int)$consignment->total_quantity>0){
                    $avg_wt_per_carton = (int)$consignment->total_gross_weight/(int)$consignment->total_quantity;
                }else{
                    $avg_wt_per_carton = 0;
                }
                
                if($avg_wt_per_carton > 5){
                    $check_cft_kgs = $avg_wt_per_carton;
                } else{
                    $check_cft_kgs = 5;
                }
                
                if((int)$consignment->total_gross_weight > 25){
                    $check_per_shipment_kgs_moq = (int)$consignment->total_gross_weight;
                } else{
                    $check_per_shipment_kgs_moq = 25;
                }
                
                if($check_per_shipment_kgs_moq > (int)$consignment->total_gross_weight){
                    $final_chargeable_weight_check2 = $check_per_shipment_kgs_moq;
                } else{
                    $final_chargeable_weight_check2 = (int)$consignment->total_gross_weight;
                }

                if($check_per_shipment_kgs_moq > $check_cft_kgs){
                    $final_chargeable_weight_check1 = $check_per_shipment_kgs_moq;
                } else{
                    $final_chargeable_weight_check1 = $check_cft_kgs;
                }

                if($final_chargeable_weight_check1 > $final_chargeable_weight_check2){
                    $final = $final_chargeable_weight_check1;
                } else{
                    $final = $final_chargeable_weight_check2;
                }

                if(isset($consignment->consigner_detail->get_state)){
                    $cnr_state = $consignment->consigner_detail->get_state->name;
                } else{
                    $cnr_state = '';
                }
                if(isset($consignment->shipto_detail->get_state)){
                    $shipto_state = $consignment->shipto_detail->get_state->name;
                } else{
                    $shipto_state = '';
                }
                
                $cnr_state = @$consignment->ConsignerDetail->Zone->state;
                $cnee_state = @$consignment->ConsigneeDetail->Zone->state;

                $data = DB::table('client_price_details')->select('from_state','to_state','price_per_kg','open_delivery_price')->where('from_state',$cnr_state)->where('to_state',$cnee_state)->first();
                if(isset($data->price_per_kg)){
                    $price_per_kg = $data->price_per_kg;
                }else{
                    $price_per_kg = 0;
                }
                        
                $perkg_rate3 = (int)$final_chargeable_weight_check2 * (int)$price_per_kg;
                if(isset($perkg_rate3)){
                    $perkg_rate3 = $perkg_rate3;
                } else{
                    $perkg_rate3 = 0;
                }
                
                if(isset($data->open_delivery_price)){
                    $open_del_charge = $data->open_delivery_price;
                }else{
                    $open_del_charge = 0; 
                }


                if(isset($consignment->RegClientdetail)){
                    $docket_price = (int)$consignment->RegClientdetail->docket_price;
                }else{
                    $docket_price = 0;
                }

                $final_freight_amt = $perkg_rate3+$open_del_charge+$docket_price;

                $arr[] = [
                    'consignment_id'        => $consignment_id,
                    'consignment_date'      => Helper::ShowDayMonthYearslash($consignment_date),
                    'order_id'              => $order_id,
                    'base_client'           => @$consignment->ConsignerDetail->GetRegClient->BaseClient->client_name,
                    'regional_client'       => @$consignment->ConsignerDetail->GetRegClient->name,
                    'consigner_nick_name'   => @$consignment->ConsignerDetail->nick_name,
                    'consigner_city'        => @$consignment->ConsignerDetail->city,
                    'consignee_nick_name'   => @$consignment->ConsigneeDetail->nick_name,
                    'consignee_city'        => @$consignment->ConsigneeDetail->city,
                    'consignee_postal'      => @$consignment->ConsigneeDetail->postal_code,
                    'consignee_district'    => @$consignment->ConsigneeDetail->Zone->district,
                    'consignee_state'       => @$consignment->ConsigneeDetail->Zone->state,
                    'invoice_no'            => $invno,
                    'invoice_date'          => $invdate,
                    'invoice_amt'           => $invamt,
                    'vehicle_no'            => @$consignment->VehicleDetail->regn_no,
                    'total_quantity'        => $consignment->total_quantity,
                    'total_weight'          => $consignment->total_weight,
                    'total_gross_weight'    => $consignment->total_gross_weight,
                    'lr_status'             => $status,
                    'dispatch_date'         => @$consignment->consignment_date,
                    'delivery_date'         => @$consignment->delivery_date,
                    'delivery_status'       => @$consignment->delivery_status,
                    'tat'                   => $tat,
                    'avg_wt_per_carton'     => number_format($avg_wt_per_carton,2),
                    'check_cft_kgs'         => number_format($check_cft_kgs,2),
                    'check_per_shipment_kgs_moq' => $check_per_shipment_kgs_moq,
                    'final_chargeable_weight_check2' => $final_chargeable_weight_check2,
                    'final_chargeable_weight_check1' => $final_chargeable_weight_check1,
                    'final'                 => $final,
                    'per_kg_rate'           => $price_per_kg,
                    'perkg_rate3'           => $perkg_rate3,
                    'open_del_charge'       => $open_del_charge,
                    'docket_price'         => $docket_price,
                    'final_freight_amt'     => $final_freight_amt,
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
            'Order No',
            'Base Client',
            'Regional Client',
            'Consigner',
            'Consigner City',
            'Consignee Name',
            'Consignee city',
            'Consignee Pin Code',
            'Consignee District',
            'Consignee State',       
            'Invoice No',
            'Invoice Date',
            'Invoice Amount',
            'Vehicle No',
            'Boxes',
            'Net Weight',
            'Gross Weight',
            'Lr Status',
            'Dispatch Date',
            'Delivery Date',
            'Delivery Status',
            'Tat',
            'Average Weight Per Carton',
            'Check 1 - CFT 5 KGs',
            'Check 2 - Per Shipment 25 Kgs MOQ',
            'Final Chargeable Weight Check2',
            'Final Chargeable Weight Check1',
            'Final',
            'Per Kg Rate',
            'Per Kg Rate - 3.80',
            'Open Delivery Charges Intra & Inter State',
            'Docket Charges',
            'Final Freight Amount'
        ];
    }
}