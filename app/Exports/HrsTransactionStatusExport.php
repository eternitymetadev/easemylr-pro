<?php

namespace App\Exports;

use App\models\SecondaryAvailStock;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\HrsPaymentRequest;
use App\Models\Role;
use App\Models\User;
use Session;
use Helper;
use Auth;
use DateTime;
use DB;

class HrsTransactionStatusExport implements FromCollection, WithHeadings, ShouldQueue
{
    protected $startdate;
    protected $enddate;
    protected $paymentstatus_id;
    protected $search;

    function __construct($startdate,$enddate,$paymentstatus_id,$search) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->paymentstatus_id = $paymentstatus_id;
        $this->search = $search;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = HrsPaymentRequest::query();

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $paymentstatus_id = $this->paymentstatus_id;
        $search = $this->search;
        
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->with(['latestPayment','VendorDetails', 'Branch'])
            ->groupBy('transaction_id');

        if ($authuser->role_id == 2) {
            $query = $query->where('branch_id', $cc);
        } else {
            $query = $query;
        }

        if (!empty($search)) {
            $search = $search;
            $searchT = str_replace("'", "", $search);
            $query->where(function ($query) use ($search, $searchT) {
                $query->where('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('total_amount', 'like', '%' . $search . '%')
                    ->orWhere('advanced', 'like', '%' . $search . '%')
                    ->orWhere('balance', 'like', '%' . $search . '%')
                    ->orWhereHas('VendorDetails', function ($query) use ($search, $searchT) {
                        $query->where(function ($vndrquery) use ($search, $searchT) {
                            $vndrquery->where('name', 'like', '%' . $search . '%');
                        });
                    });
            });
        }

        if ($paymentstatus_id !== null) {
            if ($paymentstatus_id || $paymentstatus_id == 0) {
                $query = $query->where('payment_status', $paymentstatus_id);
            }
        }

        if(isset($startdate) && isset($enddate)){
            $query = $query->whereBetween('created_at',[$startdate,$enddate]);                
        }
        
        $requestlists = $query->orderBy('id','DESC')->get();
        
        if($requestlists->count() > 0){
            foreach ($requestlists as $key => $requestlist){

                if($requestlist->payment_status == 1){
                    $create_payment = 'Fully Paid';
                }else if($requestlist->payment_status == 2){ 
                    $create_payment = 'Processing...';
                } else if($requestlist->payment_status == 0){
                    if(!empty($requestlist->current_paid_amt)){
                        $create_payment = 'Repay';
                    } else{ 
                        $create_payment = 'Failed';
                    }
                }else{
                    if($requestlist->balance < 1){
                        $create_payment = 'Fully Paid';
                    }else{ 
                        $create_payment = 'Create Payment';
                    }
                } 

                // payment Status 
                if($requestlist->payment_status == 0){ 
                    $payment_status = 'Failed';
                } elseif($requestlist->payment_status == 1) { 
                    $payment_status = 'Paid';
                } elseif($requestlist->payment_status == 2) { 
                    $payment_status = 'Sent to Account';
                } elseif($requestlist->payment_status == 3) { 
                    $payment_status = 'Partial Paid';
                } else{
                    $payment_status = 'Unknown';
                } 
                
                $hrsTotalQty = Helper::HrsPaymentTotalQty($requestlist->transaction_id);

                $arr[] = [
                    'transaction_id'  => @$requestlist->transaction_id,
                    'created_date'    => Helper::ShowDayMonthYear(@$requestlist->created_at),
                    'vendor'          => @$requestlist->VendorDetails->name,
                    'branch_name'     => @$requestlist->Branch->name,
                    'branch_state'    => @$requestlist->Branch->nick_name,
                    'total_hrs'       => Helper::countHrsInTransaction(@$requestlist->transaction_id),
                    'total_boxes'     => @$hrsTotalQty['totalQuantitySum'],
                    'total_netwt'     => @$hrsTotalQty['totalNetwtSum'],
                    'total_grosswt'   => @$hrsTotalQty['totalGrosswtSum'],
                    'payment_type'    => @$requestlist->payment_type,
                    'payment_date'    => Helper::ShowDayMonthYear(@$requestlist->latestPayment->payment_date),
                    'advanced'        => @$requestlist->advanced,
                    'balance'         => @$requestlist->balance,
                    'total_amt'       => @$requestlist->total_amount,
                    'create_payment'  => @$create_payment,
                    'payment_status'  => @$payment_status,                    
                ];
            }
        }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Created Date',
            'Vendor Name',
            'Branch',
            'State',
            'Total Hrs',
            'Sum of Boxes',
            'Sum of NetWt',
            'Sum of GrossWt',
            'Payment Type',
            'Payment Date',
            'Advanced',
            'Balance',
            'Total Amount',
            'Create Payment',
            'Status', 
        ];
    }
}