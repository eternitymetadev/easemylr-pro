<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LrRoute extends Model
{
    use HasFactory;
    protected $fillable = [
        'lr_id', 'route','created_at', 'updated_at'
    ];
}
