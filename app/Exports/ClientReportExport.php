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

class ClientReportExport implements FromCollection, WithHeadings, ShouldQueue
{

    protected $startdate;
    protected $enddate;
    protected $search;
    protected $regclient;

    function __construct($startdate,$enddate,$search,$regclient) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->search = $search;
        $this->regclient = $regclient;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = ConsignmentNote::query();

        $startdate = $this->startdate;
        $enddate = $this->enddate;

        if(!empty($request->search)){
            $search = $request->search;
            $searchT = str_replace("'","",$search);
            $query->where(function ($query)use($search,$searchT) {
                $query->where('id', 'like', '%' . $search . '%')
                ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                    $regclientquery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                    $query->where(function ($cnrquery)use($search,$searchT) {
                        $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                    });
                })
                ->orWhereHas('ConsigneeDetail',function( $query ) use($search,$searchT){
                    $query->where(function ($cneequery)use($search,$searchT) {
                        $cneequery->where('nick_name', 'like', '%' . $search . '%');
                    });
                });
            });
        }

        if(!empty($request->regclient)){
            $search = $request->regclient;
            $query = $query->where('regclient_id',$search);
        }
    
        if(isset($startdate) && isset($enddate)){
            $consignments = $query->whereBetween('consignment_date',[$startdate,$enddate])->orderby('created_at','DESC')->get();
        }else {
            $consignments = $query->orderBy('id','DESC')->get();
        }

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

                if(!empty($consignment->ConsignerDetail->GetRegClient->BaseClient->client_name )){
                    $baseclient_name = ucfirst($consignment->ConsignerDetail->GetRegClient->BaseClient->client_name);
                }else{
                    $baseclient_name = '-';
                }

                if(!empty($consignment->ConsignerDetail->GetRegClient->name )){
                    $regclient_name = ucfirst($consignment->ConsignerDetail->GetRegClient->name);
                }else{
                    $regclient_name = '-';
                }

                if(!empty($consignment->ConsignerDetail->nick_name )){
                    $cnr_nickname = ucfirst($consignment->ConsignerDetail->nick_name);
                }else{
                    $cnr_nickname = '-';
                }

                if(!empty($consignment->ConsignerDetail->city )){
                    $cnr_city = ucfirst($consignment->ConsignerDetail->city);
                }else{
                    $cnr_city = '-';
                }

                if(!empty($consignment->ConsigneeDetail->nick_name )){
                    $cnee_nickname = ucfirst($consignment->ConsigneeDetail->nick_name);
                }else{
                    $cnee_nickname = '-';
                }

                if(!empty($consignment->ConsigneeDetail->city )){
                    $cnee_city = ucfirst($consignment->ConsigneeDetail->city);
                }else{
                    $cnee_city = '-';
                }

                if(!empty($consignment->ConsigneeDetail->postal_code )){
                    $cnee_postal_code = ucfirst($consignment->ConsigneeDetail->postal_code);
                }else{
                    $cnee_postal_code = '-';
                }

                if(!empty($consignment->ConsigneeDetail->district )){
                    $cnee_district = ucfirst($consignment->ConsigneeDetail->district);
                }else{
                    $cnee_district = '-';
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

                if($consignment->total_quantity>0){
                    $avg_wt_per_carton = $consignment->total_gross_weight/$consignment->total_quantity;
                }else{
                    $avg_wt_per_carton = 0;
                }

                if($avg_wt_per_carton > 5){
                    $check_cft_kgs = $avg_wt_per_carton;
                } else{
                    $check_cft_kgs = 5;
                }

                if($consignment->total_gross_weight > 25){
                    $check_per_shipment_kgs_moq = $consignment->total_gross_weight;
                } else{
                    $check_per_shipment_kgs_moq = 25;
                }

                if($check_per_shipment_kgs_moq > $consignment->total_gross_weight){
                    $final_chargeable_weight_check2 = $check_per_shipment_kgs_moq;
                } else{
                    $final_chargeable_weight_check2 = $consignment->total_gross_weight;
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
                    
                if($cnr_state == 'Punjab' && $shipto_state == 'Punjab'){
                    $per_kg_rate = 3.80;
                } elseif($cnr_state == 'Punjab' && $shipto_state == 'Jammu and Kashmir'){
                    $per_kg_rate = 6.20;
                } elseif($cnr_state == 'Punjab' && $shipto_state == 'Himachal Pradesh'){
                    $per_kg_rate = 6.90;
                } if($cnr_state == 'Haryana' && $shipto_state == 'Haryana'){
                    $per_kg_rate = 4.50;
                } else{
                    $per_kg_rate = 'Delivery Rate Awaited';
                }

                $perkg_rate3 = (int)$final_chargeable_weight_check2 * (int)$per_kg_rate;
                if(isset($perkg_rate3)){
                    $perkg_rate3 = $perkg_rate3;
                } else{
                    $perkg_rate3 = 0;
                }

                $open_del_charge = 250;
                $docket_charge = 30;
                $final_freight_amt = $perkg_rate3+$open_del_charge+$docket_charge;

                $arr[] = [
                    'consignment_id'        => $consignment_id,
                    'consignment_date'      => Helper::ShowDayMonthYearslash($consignment_date),
                    'order_id'              => $order_id,
                    'base_client'           => $baseclient_name,
                    'regional_client'       => $regclient_name,
                    'consigner_nick_name'   => $cnr_nickname,
                    'consigner_city'        => $cnr_city ,
                    'consignee_nick_name'   => $cnee_nickname,
                    'consignee_city'        => $cnee_city,
                    'consignee_postal'      => $cnee_postal_code,
                    'consignee_district'    => $cnee_district,
                    'consignee_state'       => @$consignment->ConsigneeDetail->GetState->name,
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
                    'per_kg_rate'           => $per_kg_rate,
                    'perkg_rate3'           => $perkg_rate3,
                    'open_del_charge'       => $open_del_charge,
                    'docket_charge'         => $docket_charge,
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