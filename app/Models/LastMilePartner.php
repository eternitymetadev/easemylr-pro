<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastMilePartner extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name', 'company_add', 'contact_name', 'deliveryFrequency', 'deliveryType', 'email', 'expectedTimeline', 'goodsType', 'phone','reference','specialDeliveryConsideration','volume','workingState','status','created_at', 'updated_at'
    ];
}
