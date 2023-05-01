<?php

namespace App\Exports;

use App\Models\PrsPaymentHistory;
use DB;
use Auth;
use App\Models\Role;
use Helper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrsPaymentReportExport implements FromCollection, WithHeadings, ShouldQueue
{
    protected $startdate;
    protected $enddate;
    // protected $search;

    function __construct($startdate,$enddate) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        // $this->search = $search;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() 
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(6000);
        $arr = array();

        $startdate = $this->startdate;
        $enddate = $this->enddate;

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);

        $query = PrsPaymentHistory::with('PrsPaymentRequest.PickupRunSheet.Consignments.ShiptoDetail','PrsPaymentRequest.Branch','PrsPaymentRequest.PickupRunSheet.Consignments.RegClient','PrsPaymentRequest.VendorDetails','PrsPaymentRequest.VendorDetails','PrsPaymentRequest.PickupRunSheet.Consignments.ConsignmentItems','PrsPaymentRequest.PickupRunSheet.Consignments.vehicletype','PrsPaymentRequest.PickupRunSheet.Consignments.VehicleDetail');

        if ($authuser->role_id == 2) {
            $query->whereHas('PrsPaymentRequest', function ($query) use ($cc) {
                $query->whereIn('branch_id', $cc);
            });
        } else {
            $query = $query;
        }
        
        if(isset($startdate) && isset($enddate)){
            $payment_lists = $query->whereBetween('created_at',[$startdate,$enddate])->groupBy('transaction_id')->get();
        }else {
            $payment_lists = $query->groupBy('transaction_id')->get();
        }

        //
        $i = 0;
        foreach($payment_lists as $value){
        $i++; 
        $date = date('d-m-Y',strtotime($value->created_at));

        $lr_no = array();
        $regclient_name = array();
        $cnee_city = array();
        $vel_type = array();
        $regn_no = array();
        $total_qty = array();
        $total_wt = array();
        $total_gross_wt = array();
        // echo'<pre>'; print_r(json_decode($value->PrsPaymentRequest)); die;

        foreach($value->PrsPaymentRequest as $reqval){
            if($reqval->Branch){
                $branch_name = $reqval->Branch->nick_name;
            }else{
                $branch_name = '';
            }
            $vendor_name = @$reqval->VendorDetails->name;
            $vendor_type = @$reqval->VendorDetails->vendor_type;
            $tds_rate = @$reqval->VendorDetails->tds_rate;
            $vendor_pan = @$reqval->VendorDetails->pan;
            $purchase_freight = @$reqval->total_amount;

            if($reqval->VendorDetails->declaration_available == 1){
                $vendor_decl = 'Yes';
            }else{
                $vendor_decl = 'No';
            }
            
            $bankdetails = json_decode($reqval->VendorDetails->bank_details);
            
            foreach($reqval->PickupRunSheet->Consignments as $lr_group){
                // echo'<pre>'; print_r(json_decode($value->PrsPaymentRequest)); die;
                $lr_no[] = @$lr_group->id;
                $vel_type[] = @$lr_group->vehicletype->name;
                $regclient_name[] = @$lr_group->RegClient->name;
                $cnee_city[] = @$lr_group->ShiptoDetail->city;
                $regn_no[] = @$lr_group->VehicleDetail->regn_no;
                $invc_no = array();
                foreach($lr_group->ConsignmentItems as $lr_no_item){
                    $invc_no[] = @$lr_no_item->invoice_no;
                }

                $total_qty[] = @$lr_group->total_quantity;
                $total_wt[] = @$lr_group->total_weight;
                $total_gross_wt[] = @$lr_group->total_gross_weight;
                
            }
        }
        $lr_nos = implode('/',$lr_no );

        $regclient_names = implode('/',array_unique($regclient_name) );
        $cnee_citys = implode('/',$cnee_city );
        $invc_nos = implode('/',$invc_no );
        $vel_types = implode('/',array_unique($vel_type));
        $regn_no = implode('/',array_unique($regn_no));
        $total_qty = array_sum($total_qty);
        $total_wt = array_sum($total_wt);
        $total_gross_wt = array_sum($total_gross_wt);

        $trans_id = DB::table('prs_payment_histories')->where('transaction_id', $value->transaction_id)->get();
        $histrycount = count($trans_id);
        if($histrycount > 1){
            $paid_amt = $trans_id[0]->tds_deduct_balance + $trans_id[1]->tds_deduct_balance ;
            $curr_paid_amt = $trans_id[1]->current_paid_amt;
            $paymt_date_2 = $trans_id[1]->payment_date;
            $ref_no_2 = $trans_id[1]->bank_refrence_no;
            $tds_amt = $value->PrsPaymentRequest[0]->total_amount - $paid_amt ;

            $sumof_paid_tds = $paid_amt + $tds_amt ;
            $balance_due =  $value->PrsPaymentRequest[0]->total_amount - $sumof_paid_tds ;

            $tds_cut1 = $trans_id[1]->current_paid_amt - $trans_id[1]->tds_deduct_balance ;

        }else{
            $paid_amt = $trans_id[0]->tds_deduct_balance ;
            $curr_paid_amt = '';
            $paymt_date_2 = '';
            $ref_no_2 = '';
            if($value->payment_type == 'Balance'){
                $tds_amt =  $value->balance - $value->tds_deduct_balance ;
            }else{
            $tds_amt =  $value->advance - $value->tds_deduct_balance ;
            }
            $sumof_paid_tds = $paid_amt + $tds_amt ;
            $balance_due =  $value->PrsPaymentRequest[0]->total_amount - $sumof_paid_tds ;
        }
        $tds_cut = $value->current_paid_amt - $value->tds_deduct_balance;

        $trans_id = $lrdata = DB::table('prs_payment_histories')->where('transaction_id', $value->transaction_id)->get();
        $histrycount = count($trans_id);
        if($histrycount > 1){
            $tds_cut1 = $trans_id[1]->current_paid_amt - $trans_id[1]->tds_deduct_balance ;
            $tds_deduct_balance = $trans_id[1]->tds_deduct_balance;
            $payment_date = $trans_id[1]->payment_date;
            $bank_refrence_no = $trans_id[1]->bank_refrence_no;
        }else{
            $tds_cut1 = '';
            $tds_deduct_balance = '';
            $payment_date = '';
            $bank_refrence_no = '';
        }
        $arr[] = [
            'Sr_no' => $i,
            'transaction_id' => @$value->transaction_id,
            'date' => $date,
            'client' => @$regclient_names,
            'depot' => @$branch_name,
            'station' => @$cnee_citys,
            'drs_no' => @$value->prs_no,
            'lr_no' => @$lr_nos,
            'lr_inv' => @$invc_nos,
            'type_of_vehicle' => @$vel_types,
            'no_of_carton' => @$total_qty,
            'net_wt' => @$total_wt,
            'gross_wt' => @$total_gross_wt,
            'truck_no' => @$regn_no,
            'vendor_name' => @$vendor_name,
            'vendor_type' => @$vendor_type,
            'declaration' => @$vendor_decl,
            'tds_rate' => @$tds_rate,
            'bank_name' => @$bankdetails->bank_name,
            'account_no' => @$bankdetails->account_no,
            'ifsc_code' => @$bankdetails->ifsc_code,
            'vendor_pan' => @$vendor_pan,
            'purchase_freight' => @$purchase_freight,
            'paid_amt' => @$paid_amt,
            'tds_amt' => @$tds_amt,
            'balance_due' => @$balance_due,
            'advance' => @$value->tds_deduct_balance,
            'tds_deduct1' => @$tds_cut,
            'payment_date' => @$value->payment_date,
            'ref_no' => @$value->bank_refrence_no,
            'balance_amt' => @$tds_deduct_balance,
            'tds_deduct2' => @$tds_cut1,
            'payment_date_2' => @$payment_date,
            'ref_no_2' => @$bank_refrence_no,

            ];
        }
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'Sr No',
            'Transaction Id',
            'Date',
            'Client',
            'Depot',
            'Station',
            'Drs No',
            'Lr No',
            'Lr Invoice',
            'Type Of Vehicle',
            'No Of Carton',
            'Net Weight',
            'Gross Weight',
            'Truck No',
            'Vendor Name',
            'Vendor Type',
            'Declaration',
            'Tds Rate',
            'Bank Name',
            'Account No',
            'IFSC code',
            'Vendor Pan',
            'Purchase Freight',
            'Paid Amount',
            'Tds Amount',
            'Balance Due',
            'Advance',
            'Tds Deduct',
            'Payment Date',
            'Ref No',
            'Balance Amount',
            'Tds Deduct',
            'Payment Date',
            'Ref No',

        ];
    }
}