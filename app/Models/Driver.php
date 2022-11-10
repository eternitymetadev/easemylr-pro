<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;


class Driver extends Authenticatable implements JWTSubject
{ 
    protected $guard = 'api';
    protected $table = "drivers";
    public $timestamps = false;
    public $boolean = 1;
    protected $primaryKey = 'id';
    use HasFactory, Notifiable;

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name','email','phone','license_number','license_image','team_id','fleet_id','login_id','password','driver_password','status','created_at','updated_at'
    ];

    public function BankDetail()
    {
        return $this->hasOne('App\Models\Bank','broker_id','id');
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
         
        
    ];
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

     /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }
}
