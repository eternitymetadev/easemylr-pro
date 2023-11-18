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

class MixReportExport implements FromCollection, WithHeadings, ShouldQueue
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
        $query = PaymentRequest::with('PaymentHistory', 'Branch', 'TransactionDetails.ConsignmentNote.RegClient', 'VendorDetails', 'TransactionDetails.ConsignmentNote.vehicletype')->where('payment_status', '!=', 0)
        ->select('*', \DB::raw('COUNT(DISTINCT drs_no) as drs_no_count'), \DB::raw('GROUP_CONCAT(DISTINCT drs_no SEPARATOR ",DRS-") as drs_no_list'))
        ->groupBy('transaction_id');
        
        if ($authuser->role_id == 2) {
            $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }

        
        if(isset($startdate) && isset($enddate)){
            $drswiseReports = $query->whereBetween('created_at',[$startdate,$enddate])->where('payment_status', '!=', 0)->orderby('created_at','ASC')->get();
        }else {
            $drswiseReports = $query->orderBy('id','ASC')->where('payment_status', '!=', 0)->get();
        }
      
        if ($drswiseReports->count() > 0) {
            $i = 0;
            foreach ($drswiseReports as $key => $drswiseReport) { 
                $i++;
                $date = date('d-m-Y',strtotime($drswiseReport->created_at));
                $result = Helper::totalQuantityMixReport($drswiseReport->transaction_id);
                $lr_count = Helper::LrCountMix($drswiseReport->transaction_id);
                $consignee = Helper::mixReportConsignee($drswiseReport->transaction_id);


                $arr[] = [
                    'date' => @$date,
                    'transaction_id' => @$drswiseReport->transaction_id,
                    'drs_no' => 'DRS-'.$drswiseReport->drs_no_list,
                    'drs_count' => @$drswiseReport->drs_no_count,
                    'lr_count' => $lr_count,
                    'box_count' => @$result->total_quantity,
                    'total_gross' => @$result->total_gross,
                    'total_weight' => @$result->total_weight,
                    'consignee_distt' => @$consignee->district_consignee,
                    'vehicle_type' => @$consignee->vehicle_type,
                    
                ];
            }
        }
        return collect($arr);

    }
    public function headings(): array
    {
        return [
            'Transaction Date',
            'Transaction Id',
            'Drs No',
            'No Of Drs',
            'No Of LRs',
            'Box Count',
            'Gross Wt',
            'Net Wt',
            'Consignee Distt',
            'Vehicle Type',

        ];
    }
}
