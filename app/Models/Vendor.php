<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'vendor_no',
        'name',
        'email',
        'bank_details',
        'pan',
        'upload_pan',
        'cancel_cheaque',
        'other_details',
        'is_acc_verified',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
