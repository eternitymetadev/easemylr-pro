<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'refrence_transaction_id',
        'transaction_id',
        'drs_no',
        'bank_details',
        'purchase_amount',
        'payment_type',
        'advance',
        'balance',
        'tds_deduct_balance',
        'current_paid_amt',
        'finfect_status',
        'paid_amt',
        'bank_refrence_no',
        'payment_date',
        'payment_status',
        'created_at',
        'updated_at'
    ];

    public function PaymentRequest(){
        return $this->hasMany('App\Models\PaymentRequest','transaction_id','transaction_id');
    }

}
