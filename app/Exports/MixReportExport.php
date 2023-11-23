<?php

namespace App\Exports;

use App\Models\Vendor;
use App\Models\PaymentRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;
use App\Models\Role;
use App\Models\MixReport;
use Helper;
use DB;

class MixReportExport implements FromCollection, WithHeadings, ShouldQueue
{
    protected $startdate;
    protected $enddate;
    // protected $search;

    function __construct($startdate,$enddate,$type_name) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
         $this->type_name = $type_name;
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
        $type_name = $this->type_name;

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);

        $query = MixReport::query();

            if ($authuser->role_id == 2) {
                $query->whereIn('branch_id', $cc);
            } else {
                $query = $query;
            }

            if ($type_name) {
                $query = $query->where('type',$type_name);
            }

        
        if(isset($startdate) && isset($enddate)){
            $drswiseReports = $query->whereBetween('transaction_date',[$startdate,$enddate])->orderby('transaction_date','ASC')->get();
        }else {
            $drswiseReports = $query->orderBy('id','ASC')->get();
        }
      
        if ($drswiseReports->count() > 0) {
            $i = 0;
            foreach ($drswiseReports as $key => $drswiseReport) { 


                $arr[] = [
                    'type' => @$drswiseReport->type,
                    'date' => Helper::ShowDayMonthYear($drswiseReport->transaction_date),
                    'transaction_id' => @$drswiseReport->transaction_id,
                    'drs_no' => @$drswiseReport->drs_no,
                    'drs_count' => @$drswiseReport->no_of_drs,
                    'lr_count' => @$drswiseReport->no_of_lrs,
                    'box_count' => @$drswiseReport->box_count,
                    'total_gross' => @$drswiseReport->gross_wt,
                    'total_weight' => @$drswiseReport->net_wt,
                    'consignee_distt' => @$drswiseReport->consignee_distt,
                    'vehicle_type' => @$drswiseReport->vehicle_type,
                    
                ];
            }
        }
        return collect($arr);

    }
    public function headings(): array
    {
        return [
            'Type',
            'Transaction Date',
            'Transaction Id',
            'Drs/PRS/HRS',
            'No Of Drs/Prs/Hrs',
            'No Of LRs',
            'Box Count',
            'Gross Wt',
            'Net Wt',
            'Consignee Distt',
            'Vehicle Type',

        ];
    }
}
