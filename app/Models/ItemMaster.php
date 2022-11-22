<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'technical_formula','technical_id', 'erp_mat_code','manufacturer', 'brand_name', 'net_weight', 'gross_weight', 'chargable_weight', 'status','created_at','updated_at'
    ];
}
