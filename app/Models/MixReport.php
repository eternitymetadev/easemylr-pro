<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MixReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'transaction_date',
        'transaction_id',
        'drs_no',
        'no_of_drs',
        'no_of_lrs',
        'box_count',
        'gross_wt',
        'net_wt',
        'consignee_distt',
        'vehicle_type',
        'vehicle_no',
        'branch_id'
    ];

    public function PaymentRequest()
    {
        return $this->hasOne('App\Models\PaymentRequest','transaction_id','transaction_id');
    }

    public function PrsPaymentRequest()
    {
        return $this->hasOne('App\Models\PrsPaymentRequest','transaction_id','transaction_id');
    }

    public function HrsPaymentRequest()
    {
        return $this->hasOne('App\Models\HrsPaymentRequest','transaction_id','transaction_id');
    }
}
