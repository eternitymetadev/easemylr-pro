<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrsPaymentRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'hrs_no',
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
        'rejected_remarks',
        'is_approve',
        'payment_status',
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

    public function HrsDetails(){
        return $this->hasMany('App\Models\Hrs','hrs_no','hrs_no');
    }

    public function HrsPaymentHistory()
    {
        return $this->hasOne('App\Models\HrsPaymentHistory','transaction_id','transaction_id');
    }
    public function latestPayment(){
        return $this->hasOne('App\Models\HrsPaymentHistory','transaction_id','transaction_id')
                    ->select('id','transaction_id', 'payment_date', 'created_at');
    }
}
