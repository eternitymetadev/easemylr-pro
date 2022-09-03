<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalClientDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'regclient_id', 'docket_price', 'status', 'created_at', 'updated_at'
    ];
}
