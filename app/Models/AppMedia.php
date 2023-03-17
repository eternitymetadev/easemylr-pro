<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_no', 'pod_img', 'type', 'status'
    ];
}
