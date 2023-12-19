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
}
