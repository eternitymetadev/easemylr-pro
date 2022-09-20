<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'email', 'phone', 'license_number', 'license_image','team_id','fleet_id','login_id','password','driver_password','status','created_at','updated_at'        
    ];

    public function BankDetail()
    {
        return $this->hasOne('App\Models\Bank','broker_id','id');
    }
}
