<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfCareer extends Model
{
    use HasFactory;
    protected $fillable = [
        'fullName',
        'email',
        'phone',
        'education',
        'location',
        'cv',
        'status',
        'created_at',
        'updated_at'
    ];

}
