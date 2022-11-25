<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsReceiveVehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_id', 'consigner_id', 'invoice_no', 'total_qty', 'receive_qty', 'remaining_qty', 'remarks', 'user_id', 'branch_id', 'status', 'created_at', 'updated_at'
    ];
}
