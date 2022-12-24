<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsRegConsigner extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_regclientid', 'consigner_id', 'status', 'created_at', 'updated_at'
    ];
}
