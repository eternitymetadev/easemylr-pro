<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPartner extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'driving_record',
        'exp_details',
        'is_available',
        'is_compliant',
        'is_flexible',
        'preferred_state',
        'reference',
        'valid_license',
        'working_years',
        'status',
        'created_at',
        'updated_at'
    ];
}
