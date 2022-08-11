<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'consignment_id',
        'description',
        'packing_type',
        'quantity',
        'weight',
        'gross_weight',
        'freight',
        'payment_type',
        'order_id',
        'invoice_no',
        'invoice_date',
        'invoice_amount',
        'e_way_bill',
        'e_way_bill_date',
        'status',
        'created_at',
        'updated_at'
    ];
}
