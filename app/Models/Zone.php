<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    protected $fillable = [
        'primary_zone', 'postal_code', 'district', 'state', 'status','hub_transfer', 'created_at', 'updated_at'
    ];
}
