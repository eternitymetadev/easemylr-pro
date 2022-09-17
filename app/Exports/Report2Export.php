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

class Report2Export implements FromCollection, WithHeadings, ShouldQueue
{

    protected $startdate;
    protected $enddate;
    protected $search;

    function __construct($startdate,$enddate,$search) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->search = $search;
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


                $arr[] = [
                    'consignment_id'        => $consignment_id,
                    'consignment_date'      => Helper::ShowDayMonthYearslash($consignment_date),
                    'order_id'              => $order_id,
                    'client_name'           => $baseclient_name,
                    'name'                  => $regclient_name,
                    'nick_name'             => $cnr_nickname,
                    'city'                  => $cnr_city ,
                    'nick_name'             => $cnee_nickname,
                    'city'                  => $cnee_city,
                    'postal_code'           => $cnee_postal_code,
                    'district'              => $cnee_district,
                    'state_id'              => @$consignment->ConsigneeDetail->state_id,

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
        'City',
        'Pin Code',
        'District',
        'State',
        ];
    }
}
