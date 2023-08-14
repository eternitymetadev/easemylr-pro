<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mail;
use App\Mail\EfContactusMail;

class EfContactUs extends Model
{
    use HasFactory;
    protected $fillable = [
        'companyName',
        'companyWebsite',
        'connectionPreference',
        'consent',
        'email',
        'fullName',
        'phone',
        'serviceType',
        'state',
        'status',
        'created_at',
        'updated_at'
    ];

    public static function boot() {
  
        parent::boot();
  
        static::created(function ($item) {
                
            $adminEmail = "vineet.thakur@eternitysolutions.net";
            Mail::to($adminEmail)->send(new EfContactusMail($item));
        });
    }
}
