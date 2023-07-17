<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfDeliveryRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'lr_id',
        'rating',
        'feedback',
        'status',
        'created_at',
        'updated_at'
    ];
}
