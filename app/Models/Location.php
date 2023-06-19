<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'hub_nickname','nick_name', 'team_id', 'consignment_no', 'email', 'phone', 'with_vehicle_no', 'status', 'is_hub','gst_registered_id','created_at', 'updated_at'

    ];

    public function GstAddress()
    {
        return $this->hasOne('App\Models\GstRegisteredAddress','id','gst_registered_id');
    }

}
 