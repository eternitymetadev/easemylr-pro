<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsPaymentRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'prs_no',
        'vendor_id',
        'vehicle_no',
        'vehicle_type',
        'total_amount',
        'payment_type',
        'advanced',
        'balance',
        'amt_without_tds',
        'current_paid_amt',
        'tds_deduct_balance',
        'branch_id',
        'user_id',
        'rm_id',
        'is_approve',
        'payment_status',
        'rejected_remarks',
        'status',
        'created_at',
        'updated_at'

    ];

    public function Branch(){
        return $this->belongsTo('App\Models\Location','branch_id');
    }
    public function User(){
        return $this->belongsTo('App\Models\User','user_id');
    }
    public function VendorDetails()
    {
        return $this->hasOne('App\Models\Vendor','id','vendor_id');
    }
}
