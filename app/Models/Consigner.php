<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consigner extends Model
{
    use HasFactory;
    protected $fillable = [
        'nick_name', 'legal_name', 'gst_number', 'contact_name', 'phone', 'regionalclient_id', 'branch_id', 'email', 'address_line1','address_line2','address_line3','address_line4', 'city', 'district', 'postal_code', 'state_id', 'status', 'created_at', 'updated_at'
    ];

    public function Branch(){
        return $this->belongsTo('App\Models\Location','branch_id');
    }

    public function State(){
        return $this->belongsTo('App\Models\State','state_id');
    }

    public function GetBranch(){
        return $this->hasOne('App\Models\Location','id','branch_id');
    }

    public function GetRegClient(){
        return $this->hasOne('App\Models\RegionalClient','id','regionalclient_id');
    }
    
    public function RegClient(){
        return $this->belongsTo('App\Models\RegionalClient','regionalclient_id');
    }

    public function GetState(){
        return $this->hasOne('App\Models\State','id','state_id');
    }

    public function GetZone(){
        return $this->hasOne('App\Models\Zone','postal_code','postal_code');
    }

    public function Zone(){
        return $this->belongsTo('App\Models\Zone','postal_code','postal_code');
    }

}
