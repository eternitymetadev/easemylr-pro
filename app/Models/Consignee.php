<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consignee extends Model
{
    use HasFactory;
    protected $fillable = [
        'nick_name', 'legal_name', 'user_id', 'branch_id', 'consigner_id', 'zone_id', 'dealer_type', 'gst_number', 'contact_name', 'phone', 'email', 'sales_officer_name', 'sales_officer_email', 'sales_officer_phone', 'address_line1', 'address_line2', 'address_line3','address_line4', 'city', 'district', 'postal_code', 'state_id', 'status', 'created_at', 'updated_at'
    ];

    public function Branch(){
        return $this->belongsTo('App\Models\Location','branch_id');
    }

    public function State(){
        return $this->belongsTo('App\Models\State','state_id');
    }

    public function Consigner(){
        return $this->belongsTo('App\Models\Consigner','consigner_id');
    }

    public function GetConsigner(){
        return $this->hasOne('App\Models\Consigner','id','consigner_id');
    }

    public function GetBranch(){
        return $this->hasOne('App\Models\Location','id','branch_id');
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
