<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfShipnow extends Model
{
    use HasFactory;
    protected $fillable = [
        'pickUp','drop','phone','status','created_at','updated_at'
    ];
}
