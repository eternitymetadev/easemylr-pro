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
        'payment_status',
        'status',
        'created_at',
        'updated_at'

    ];

    public function VendorDetails()
    {
        return $this->hasOne('App\Models\Vendor','id','vendor_id');
    }
}
