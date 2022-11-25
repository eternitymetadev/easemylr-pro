<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsTaskItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'drivertask_id', 'order_id', 'invoice_no', 'invoice_date', 'quantity', 'net_weight', 'gross_weight', 'user_id', 'branch_id', 'status', 'created_at', 'updated_at'
    ];
}
