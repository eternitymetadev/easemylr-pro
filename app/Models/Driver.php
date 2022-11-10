<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Driver as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable = [
        'name', 'email', 'phone', 'license_number', 'license_image','team_id','fleet_id','login_id','password','driver_password','status','created_at','updated_at'        
    ];
    protected $hidden = [
        'password', 
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword()

    {
        return $this->password;

    }

    public function setPasswordAttribute($value)

    {

        $this->attributes['password'] = $value;

    }

    public function BankDetail()
    {
        return $this->hasOne('App\Models\Bank','broker_id','id');
    }
}
