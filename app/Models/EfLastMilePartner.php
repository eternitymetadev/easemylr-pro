<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfLastMilePartner extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name', 'contact_name', 'email', 'phone', 'company_add', 'goods_type', 'state', 'volume','delivery_frequency', 'special_delivery','expected_timeline', 'delivery_type', 'reference','status','created_at', 'updated_at'
    ];
}
