<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsPaymentHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_no','prs_date', 'status', 'created_at', 'updated_at'
    ];
}
