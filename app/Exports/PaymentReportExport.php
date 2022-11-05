<?php

namespace App\Exports;

use App\Models\PaymentHistory;
use App\Models\PaymentRequest;
use DB;
use Auth;
use App\Models\Role;
use Helper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentReportExport implements FromCollection, WithHeadings, ShouldQueue
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(6000);
        $arr = array();
              $authuser = Auth::user();
                 $role_id = Role::where('id', '=', $authuser->role_id)->first();
                 $cc = explode(',', $authuser->branch_id);
               $query = PaymentHistory::with('PaymentRequest.Branch','PaymentRequest.TransactionDetails.ConsignmentNote.RegClient','PaymentRequest.VendorDetails','PaymentRequest.TransactionDetails.ConsignmentNote.ConsignmentItems','PaymentRequest.TransactionDetails.ConsignmentNote.vehicletype');
               if($authuser->role_id == 2){
                $query->whereHas('PaymentRequest', function ($query) use ($cc) {
                    $query->whereIn('branch_id', $cc);
                });
            }else{
                $query = $query;
             }
            $payment_lists = $query->groupBy('transaction_id')->get();

        
        // $payment_lists = PaymentHistory::with('PaymentRequest.Branch', 'PaymentRequest.TransactionDetails.ConsignmentNote.RegClient', 'PaymentRequest.VendorDetails', 'PaymentRequest.TransactionDetails.ConsignmentNote.ConsignmentItems', 'PaymentRequest.TransactionDetails.ConsignmentNote.vehicletype')->groupBy('transaction_id')->get();

        if ($payment_lists->count() > 0) {
            $i = 0;
            foreach ($payment_lists as $key => $payment_list) {
                $i++;

                $bankdetails = json_decode($payment_list->PaymentRequest[0]->VendorDetails->bank_details);

                $date = date('d-m-Y', strtotime($payment_list->created_at));
                $lr_arra = array();
                $consigneecity = array();
                $itm_arra = array();
                $qty = array();
                $totlwt = array();
                $grosswt = array();
                $drsvehicel = array();
                $vel_type = array();
                $regn_clt = array();
                foreach ($payment_list->PaymentRequest as $lr_no) {
                    $drsvehicel[] = $lr_no->vehicle_no;
                    $qty[] = Helper::totalQuantity($lr_no->drs_no);
                    $totlwt[] = Helper::totalWeight($lr_no->drs_no);
                    $grosswt[] = Helper::totalGrossWeight($lr_no->drs_no);
                    // echo'<pre>'; print_r($lr_no->drs_no); die;
                    foreach ($lr_no->TransactionDetails as $lr_group) {
                        $lr_arra[] = $lr_group->consignment_no;
                        $consigneecity[] = @$lr_group->ConsignmentNote->ShiptoDetail->city;
                        $vel_type[] = @$lr_group->ConsignmentNote->vehicletype->name;
                        $regn_clt[] = @$lr_group->ConsignmentNote->RegClient->name;
                    }

                    foreach ($lr_group->ConsignmentNote->ConsignmentItems as $lr_no_item) {
                        $itm_arra[] = $lr_no_item->invoice_no;
                    }
                }
                $csd = array_unique($vel_type);
                $group_vehicle_type = implode('/', $csd);
                $group_vehicle = implode('/', $drsvehicel);
                $ttqty = array_sum($qty);
                $groupwt = array_sum($totlwt);
                $groupgross = array_sum($grosswt);
                // $ttqty = implode('/', $qty);
                // $groupwt = implode('/', $totlwt);
                // $groupgross = implode('/', $grosswt);
                $city = implode('/', $consigneecity);
                $multilr = implode('/', $lr_arra);
                $lr_itm = implode('/', $itm_arra);

                $unique_regn = array_unique($regn_clt);
                $regn = implode('/', $unique_regn);


                if ($payment_list->PaymentRequest[0]->VendorDetails->declaration_available == 1) {
                    $decl = 'Yes';
                } else {
                    $decl = 'No';
                }

                $exp_drs = explode(',', $payment_list->drs_no);
                $exp_arra = array();
                foreach ($exp_drs as $exp) {
                    $exp_arra[] = 'DRS-' . $exp;
                }
                $newDrs = implode(',', $exp_arra);

                $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $payment_list->transaction_id)->get();
                $histrycount = count($trans_id);
                if ($histrycount > 1) {
                    $paid_amt = $trans_id[0]->tds_deduct_balance + $trans_id[1]->tds_deduct_balance;
                    $curr_paid_amt = @$trans_id[1]->tds_deduct_balance;
                    $paymt_date_2 = @$trans_id[1]->payment_date;
                    $ref_no_2 = @$trans_id[1]->bank_refrence_no;
                    $tds_amt = $payment_list->PaymentRequest[0]->total_amount - $paid_amt;

                    $sumof_paid_tds = $paid_amt + $tds_amt;
                    $balance_due = $payment_list->PaymentRequest[0]->total_amount - $sumof_paid_tds;
                } else {
                    $paid_amt = $trans_id[0]->tds_deduct_balance;
                    $curr_paid_amt = '';
                    $paymt_date_2 = '';
                    $ref_no_2 = '';
                    if ($payment_list->payment_type == 'Balance') {
                        $tds_amt = $payment_list->balance - $payment_list->tds_deduct_balance;
                    } else {
                        $tds_amt = $payment_list->advance - $payment_list->tds_deduct_balance;
                    }
                    $sumof_paid_tds = $paid_amt + $tds_amt;
                    $balance_due = $payment_list->PaymentRequest[0]->total_amount - $sumof_paid_tds;
                }

                if ($payment_list->payment_type == 'Balance') {
                    $advan = $payment_list->tds_deduct_balance;
                } else {
                    $advan = $payment_list->tds_deduct_balance;
                }

                $arr[] = [
                    'Sr_no' => $i,
                    'transaction_id' => $payment_list->transaction_id,
                    'date' => $date,
                    'client' => @$regn,
                    'depot' => @$payment_list->PaymentRequest[0]->Branch->nick_name,
                    'station' => @$city,
                    'drs_no' => $newDrs,
                    'lr_no' => @$multilr,
                    'lr_inv' => @$lr_itm,
                    'type_of_vehicle' => @$group_vehicle_type,
                    'no_of_carton' => @$ttqty,
                    'net_wt' => @$groupwt,
                    'gross_wt' => @$groupgross,
                    'truck_no' => @$group_vehicle,
                    'vendor_name' => @$payment_list->PaymentRequest[0]->VendorDetails->name,
                    'vendor_type' => @$payment_list->PaymentRequest[0]->VendorDetails->vendor_type,
                    'declaration' => @$decl,
                    'tds_rate' => @$payment_list->PaymentRequest[0]->VendorDetails->tds_rate,
                    'bank_name' => @$bankdetails->bank_name,
                    'account_no' => @$bankdetails->account_no,
                    'ifsc_code' => @$bankdetails->ifsc_code,
                    'vendor_pan' => @$payment_list->PaymentRequest[0]->VendorDetails->pan,
                    'purchase_freight' => @$payment_list->PaymentRequest[0]->total_amount,
                    'paid_amt' => @$paid_amt,
                    'tds_amt' => @$tds_amt,
                    'balance_due' => @$balance_due,
                    'advance' => @$advan,
                    'payment_date' => @$payment_list->payment_date,
                    'ref_no' => @$payment_list->bank_refrence_no,
                    'balance_amt' => @$curr_paid_amt,
                    'payment_date_2' => @$paymt_date_2,
                    'ref_no_2' => @$ref_no_2,

                ];
            }
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
            'Payment Date',
            'Ref No',
            'Balance Amount',
            'Payment Date',
            'Ref No',

        ];
    }
}
