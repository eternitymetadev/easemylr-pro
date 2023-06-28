<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    protected $fillable = [
        'primary_zone', 'postal_code', 'district', 'state','hub_transfer','hub_nickname', 'pickup_hub', 'status', 'created_at', 'updated_at'
    ];

    public function Branch(){
        return $this->belongsTo('App\Models\Location','hub_nickname');
    }

    public function GetLocation(){
        return $this->hasOne('App\Models\Location','id','pickup_hub');
    }
}
