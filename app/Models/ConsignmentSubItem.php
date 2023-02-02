<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentSubItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'conitem_id',
        'item',
        'quantity',
        'net_weight',
        'gross_weight',
        'chargeable_weight',
        'status',
        'created_at',
        'updated_at'
    ];

}
