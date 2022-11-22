<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'technical_formula', 'manufacturer', 'brand_name', 'created_at','updated_at'
    ];
}
