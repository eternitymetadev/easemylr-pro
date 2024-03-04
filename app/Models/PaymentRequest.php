<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'drs_no',
        'vendor_id',
        'vehicle_no',
        'total_amount',
        'payment_type',
        'advanced',
        'balance',
        'tds_deduct_balance',
        'branch_id',
        'user_id',
        'current_paid_amt',
        'remarks',
        'payment_status',
        'status',
        'created_at',
        'updated_at'

    ];

    public function VendorDetails()
    {
        return $this->hasOne('App\Models\Vendor','id','vendor_id');
    }

    public function Branch(){
        return $this->belongsTo('App\Models\Location','branch_id');
    }

    public function TransactionDetails(){
        return $this->hasMany('App\Models\TransactionSheet','drs_no','drs_no');
    }

    public function TransactionDetail()
    {
        return $this->hasOne('App\Models\TransactionSheet', 'drs_no', 'drs_no')->select('drs_no', 'created_at');
    }

    public function PaymentHistory(){
        return $this->hasMany('App\Models\PaymentHistory','transaction_id','transaction_id');
    }

    public function latestPayment(){
        return $this->hasOne('App\Models\PaymentHistory','transaction_id','transaction_id')
                    ->select('id','transaction_id', 'payment_date', 'created_at');
    }
}
