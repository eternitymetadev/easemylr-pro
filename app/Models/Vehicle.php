<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'regn_no',
        'mfg',
        'make',
        'engine_no',
        'chassis_no',
        'gross_vehicle_weight',
        'unladen_weight',
        'tonnage_capacity',
        'body_type',
        'state_id',
        'regndate',
        'hypothecation',
        'driver_id',
        'ownership',
        'owner_name',
        'owner_phone',
        'rc_image',
        'second_rc_image',
        'status',
        'created_at',
        'updated_at'
    ];

    public function GetState(){
        return $this->hasOne('App\Models\State','id','state_id');
    }
    public function State(){
        return $this->belongsTo('App\Models\State','state_id');
    }
    public function Driver(){
        return $this->belongsTo('App\Models\Driver','driver_id');
    }

}