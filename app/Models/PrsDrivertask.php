<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsDrivertask extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_id', 'prsconsigner_id', 'prs_date', 'status', 'created_at', 'updated_at'
    ];

    public function ConsignerDetail()
    {
        return $this->hasOne('App\Models\Consigner','id','prsconsigner_id');
    }
}
