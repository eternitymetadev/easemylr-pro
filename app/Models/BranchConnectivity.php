<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchConnectivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'efpl_hub','direct_connectivity','status','created_at', 'updated_at'
    ];
}
