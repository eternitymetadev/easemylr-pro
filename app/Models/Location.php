<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'hub_nickname', 'nick_name', 'team_id', 'consignment_no', 'email', 'phone', 'with_vehicle_no', 'status', 'is_hub','created_at', 'updated_at'
    ];

}
