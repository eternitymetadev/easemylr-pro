<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrsItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'prs_id', 'prsconsigner_id', 'order_id', 'invoice_no', 'invoice_date', 'quantity', 'net_weight', 'gross_weight', 'prs_date', 'status', 'created_at', 'updated_at'
    ];

    public function ConsignerDetail()
    {
        return $this->hasOne('App\Models\Consigner','id','prsconsigner_id');
    }
}
