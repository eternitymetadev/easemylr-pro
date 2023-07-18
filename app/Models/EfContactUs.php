<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
