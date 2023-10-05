<?php

namespace App\Exports;

use App\models\SecondaryAvailStock;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\User;
use Session;
use Helper;
use Auth;
use DateTime;
use DB;

class TransactionStatusExport implements FromCollection, WithHeadings, ShouldQueue
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

        $query = PaymentRequest::query();

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $paymentstatus_id = $this->paymentstatus_id;
        $search = $this->search;
        
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->with('VendorDetails', 'Branch')
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
                $date = date('d-m-Y',strtotime($requestlist->created_at));

                if($requestlist->payment_status == 1){
                    $create_payment = 'Fully Paid';
                }elseif($requestlist->payment_status == 2 || $requestlist->payment_status == 1){ 
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
                
                $arr[] = [
                    'transaction_id'  => @$requestlist->transaction_id,
                    'date'            => @$date,
                    'total_drs'       => Helper::countDrsInTransaction(@$requestlist->transaction_id),
                    'vendor'          => @$requestlist->VendorDetails->name,
                    'total_amt'       => @$requestlist->total_amount,
                    'advanced'        => @$requestlist->advanced,
                    'balance'         => @$requestlist->balance,
                    'branch_name'     => @$requestlist->Branch->nick_name,
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
            'Transaction Id',
            'Date',
            'Total Drs',
            'Vendor',
            'Total Amount',
            'Advanced',
            'Balance',
            'Branch',
            'Create Payment',
            'Status', 
        ];
    }
}