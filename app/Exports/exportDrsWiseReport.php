<?php

namespace App\Exports;

use App\Models\Vendor;
use App\Models\PaymentRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;
use App\Models\Role;
use Helper;
use DB;

class exportDrsWiseReport implements FromCollection, WithHeadings, ShouldQueue
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
        $query = PaymentRequest::with('Branch', 'TransactionDetails.ConsignmentNote.RegClient', 'VendorDetails', 'TransactionDetails.ConsignmentNote.vehicletype');
        if ($authuser->role_id == 2) {
            $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }
        $drswiseReports = $query->where('payment_status', '!=', 0)->get();

        if ($drswiseReports->count() > 0) {
            $i = 0;
            foreach ($drswiseReports as $key => $drswiseReport) {
                $i++;
                $date = date('d-m-Y',strtotime($drswiseReport->created_at));
                $no_ofcases = Helper::totalQuantity($drswiseReport->drs_no);
                $totlwt = Helper::totalWeight($drswiseReport->drs_no);
                $grosswt = Helper::totalGrossWeight($drswiseReport->drs_no);
               $lrgr = array();
               $regnclt = array();
               $vel_type = array();
                   foreach($drswiseReport->TransactionDetails as $lrgroup){
                          $lrgr[] =  $lrgroup->ConsignmentNote->id;
                          $regnclt[] = @$lrgroup->ConsignmentNote->RegClient->name;
                          $vel_type[] = @$lrgroup->ConsignmentNote->vehicletype->name;
                          $purchase = @$lrgroup->ConsignmentDetail->purchase_price;
                   }
                   $lr = implode('/', $lrgr);
                   $unique_regn = array_unique($regnclt);
                   $regn = implode('/', $unique_regn);

                   $unique_veltype = array_unique($vel_type);
                   $vehicle_type = implode('/', $unique_veltype);
                   $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $drswiseReport->transaction_id)->get();
                        $histrycount = count($trans_id);
                        
                        if($histrycount > 1){
                           $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance + $drswiseReport->PaymentHistory[1]->tds_deduct_balance;
                        }else{
                            $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance;
                        }


                $arr[] = [
                    'sr_no' => $i,
                    'drs_no' => 'DRS-'.$drswiseReport->drs_no,
                    'date' => @$date,
                    'vehicle_no' => @$drswiseReport->vehicle_no,
                    'vehicle_type' => @$vehicle_type,
                    'purchase_amt' => @$purchase,
                    'transaction_id' => $drswiseReport->transaction_id,
                    'transaction_idamt' => @$drswiseReport->total_amount,
                    'paid_amt' => @$paid_amt,
                    'client' => @$regn,
                    'location' => @$drswiseReport->Branch->name,
                    'lr_no' => @$lr,
                    'no_of_case' => @$no_ofcases,
                    'net_wt' => @$totlwt,
                    'gross_wt' => @$grosswt,

                ];
            }
        }
        return collect($arr);

    }
    public function headings(): array
    {
        return [
            'Sr No',
            'Drs No',
            'Date',
            'Vehicle No',
            'Vehicle Type',
            'Purchase Amount',
            'Transaction ID',
            'Transaction ID Amount',
            'Paid Amount',
            'Client',
            'Location',
            'Lr NO',
            'No Of Cases',
            'Net Weight',
            'Gross Weight',

        ];
    }
}
