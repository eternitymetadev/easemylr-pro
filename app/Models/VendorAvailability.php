<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAvailability extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'pickup_state',
        'pickup_district',
        'drop_state',
        'drop_district',
        'status',
        'created_at',
        'updated_at'
    ];
}
