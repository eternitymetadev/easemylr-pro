<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrsWiseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'drs_no',
        'date',
        'vehicle_no',
        'vehicle_type',
        'purchase_amount',
        'transaction_id',
        'transaction_id_amt',
        'paid_amount',
        'client',
        'location',
        'lr_no',
        'no_of_cases',
        'net_wt',
        'gross_wt',
        'status',
        'branch_id',
    ];
}
