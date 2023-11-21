<?php

namespace App\Exports;

use App\Models\Vendor;
use App\Models\PaymentRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;
use App\Models\Role;
use App\Models\DrsWiseReport;
use Helper;
use DB;

class exportDrsWiseReport implements FromCollection, WithHeadings, ShouldQueue
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
        $query = DrsWiseReport::query();
        if ($authuser->role_id == 2) {
            $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }

        
        if(isset($startdate) && isset($enddate)){
            $drswiseReports = $query->whereBetween('date',[$startdate,$enddate])->orderby('date','ASC')->get();
        }else {
            $drswiseReports = $query->orderBy('id','ASC')->get();
        }
      
        if ($drswiseReports->count() > 0) {
            $i = 0;
            foreach ($drswiseReports as $key => $drswiseReport) { 
                

                $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $drswiseReport->transaction_id)->get();
                $histrycount = count($trans_id);

                if ($histrycount > 1) {
                    @$paid_amt = @$trans_id[0]->tds_deduct_balance + @$trans_id[1]->tds_deduct_balance;
                } else {
                    @$paid_amt = @$trans_id[0]->tds_deduct_balance;
                }
                
                if(@$drswiseReport->DrsDetails->status == '0' ){
                    @$drs_status = 'Cancelled';
                    }else{
                        @$drs_status = 'Active';
                    }

                $arr[] = [
                    'drs_no' => 'DRS-'.$drswiseReport->drs_no,
                    'date' => Helper::ShowDayMonthYear($drswiseReport->date),
                    'vehicle_no' => @$drswiseReport->vehicle_no,
                    'vehicle_type' => @$drswiseReport->vehicle_type,
                    'purchase_amt' => @$drswiseReport->purchase_amount,
                    'transaction_id' => $drswiseReport->transaction_id,
                    'transaction_idamt' => @$drswiseReport->transaction_id_amt,
                    'paid_amt' => @$paid_amt,
                    'client' => @$drswiseReport->client,
                    'location' => @$drswiseReport->branch_id,
                    'lr_no' => @$drswiseReport->lr_no,
                    'no_of_case' => @$drswiseReport->no_of_cases,
                    'net_wt' => @$drswiseReport->net_wt,
                    'gross_wt' => @$drswiseReport->gross_wt,
                    'status' => @$drs_status,

                ];
            }
        }
        return collect($arr);

    }
    public function headings(): array
    {
        return [
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
            'Status',

        ];
    }
}