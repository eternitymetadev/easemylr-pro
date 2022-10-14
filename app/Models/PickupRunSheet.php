<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRunSheet extends Model
{
    use HasFactory;
    protected $fillable = [
        'regclient_id', 'prs_type', 'vehicle_id', 'driver_id', 'prs_date', 'status', 'created_at', 'updated_at'
    ];
}
