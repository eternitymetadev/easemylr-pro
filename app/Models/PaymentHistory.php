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
        'payment_status',
        'created_at',
        'updated_at'
    ];
}
