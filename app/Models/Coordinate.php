<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
    use HasFactory;
    protected $fillable = [
        'consignment_id',
        'latitude',
        'longitude',
        'created_at',
        'updated_at'

    ];
}
