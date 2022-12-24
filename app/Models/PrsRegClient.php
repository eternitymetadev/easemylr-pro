<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsRegClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_id', 'regclient_id', 'status', 'created_at', 'updated_at'
    ];
}
