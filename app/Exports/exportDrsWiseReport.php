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
    protected $startdate;
    protected $enddate;
    protected $search;

    function __construct($startdate,$enddate,$search) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->search = $search;
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
        $search = $this->search;

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $query = PaymentRequest::with('Branch:id,name,nick_name','TransactionDetails:id,drs_no,consignment_no', 'TransactionDetails.ConsignmentNote:id,regclient_id,vehicle_type,purchase_price,ship_to_id','TransactionDetails.ConsignmentNote.RegClient:id,name', 'VendorDetails:id,name,pan', 'TransactionDetails.ConsignmentNote.vehicletype','TransactionDetail','TransactionDetails.ConsignmentNote.ShiptoDetail:id,city');
        if ($authuser->role_id == 2) {
            $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }

        
        if(isset($startdate) && isset($enddate)){
            $query = $query->whereBetween('created_at',[$startdate,$enddate]);
        }

        if (!empty($search)) {
            $search = $search;
            $searchT = str_replace("'", "", $search);
            $query->where(function ($query) use ($search, $searchT) {
                $query->where('drs_no', 'like', '%' . $search . '%')
                    ->orWhere('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('vehicle_no', 'like', '%' . $search . '%');
            });
        } 

        $drswiseReports = $query->orderBy('id','ASC')->where('payment_status', '!=', 0)->get();
      
        if ($drswiseReports->count() > 0) {
            $i = 0;
            foreach ($drswiseReports as $key => $drswiseReport) { 
                $i++;
                $drsDate = date('d-m-Y',strtotime($drswiseReport->TransactionDetail->created_at));
                $transactionDate = date('d-m-Y',strtotime($drswiseReport->created_at));
                $no_ofcases = Helper::totalQuantity($drswiseReport->drs_no);
                $totlwt = Helper::totalWeight($drswiseReport->drs_no);
                $grosswt = Helper::totalGrossWeight($drswiseReport->drs_no);
                $lrgr = array();
                $regnclt = array();
                $vel_type = array();
                $shiptoLocation = array();
                
                foreach($drswiseReport->TransactionDetails as $lrgroup){
                        $lrgr[] =  $lrgroup->ConsignmentNote->id;
                        $regnclt[] = @$lrgroup->ConsignmentNote->RegClient->name;
                        $vel_type[] = @$lrgroup->ConsignmentNote->vehicletype->name;
                        $purchase = @$lrgroup->ConsignmentDetail->purchase_price;
                        $shiptoLocation[] = @$lrgroup->ConsignmentNote->ShiptoDetail->city;
                        
                    // echo'<pre>'; print_r($lrgroup->ConsignmentNote->ShiptoDetail->city); die;
                }
                $lrCounts = count($lrgr);
                $lr = implode('/', $lrgr);
                $unique_regn = array_unique($regnclt);
                $regn = implode('/', $unique_regn);

                // $uniqueShipto = array_unique($shiptoLocation);
                $shiptoCity = implode('/', $shiptoLocation);
                
                $unique_veltype = array_unique($vel_type);
                $vehicle_type = implode('/', $unique_veltype);
                $paymentHistories = DB::table('payment_histories')->where('transaction_id',  $drswiseReport->transaction_id)->get();
                $historyCount = $paymentHistories->count();
                
                if($historyCount){
                    if($historyCount > 1){
                    $paidAmt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance + $drswiseReport->PaymentHistory[1]->tds_deduct_balance;
                    }else{
                        $paidAmt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance;
                    }
                }else{
                    $paidAmt = 0;
                }

                $arr[] = [
                    'sr_no' => $i,
                    'drs_no' => 'DRS-'.$drswiseReport->drs_no,
                    'drs_date' => @$drsDate,
                    'transaction_date' => @$transactionDate,
                    'vehicle_no' => @$drswiseReport->vehicle_no,
                    'vehicle_type' => @$vehicle_type,
                    'purchase_amt' => @$purchase,
                    'vendor_name' => @$drswiseReport->VendorDetails->name,
                    'vendor_pan' => @$drswiseReport->VendorDetails->pan,
                    'transaction_id' => $drswiseReport->transaction_id,
                    'transaction_idamt' => @$drswiseReport->total_amount,
                    'paid_amt' => @$paidAmt,
                    'client' => @$regn,
                    'location' => @$drswiseReport->Branch->name,
                    'state'    => @$drswiseReport->Branch->nick_name,
                    'lr_count' => @$lrCounts,
                    'lr_no' => @$lr,
                    'delivery_locations' => @$shiptoCity,
                    'no_of_case' => @$no_ofcases,
                    'net_wt' => @$totlwt,
                    'gross_wt' => @$grosswt,
                    'status' => Helper::getdeleveryStatus(@$drswiseReport->drs_no),

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
            'Drs Date',
            'Transaction Date',
            'Vehicle No',
            'Vehicle Type',
            'Purchase Amount',
            'Vendor Name',
            'Vendor Pan No',
            'Transaction ID',
            'Transaction ID Amount',
            'Paid Amount',
            'Client',
            'Location',
            'State',
            'Lr Counts',
            'Lr No',
            'Delivery Locations',
            'No Of Cases',
            'Net Weight',
            'Gross Weight',
            'Status',

        ];
    }
}
