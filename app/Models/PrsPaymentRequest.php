<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsPaymentRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_id', '', '', 'prs_date', 'status', 'created_at', 'updated_at'
    ];
}
