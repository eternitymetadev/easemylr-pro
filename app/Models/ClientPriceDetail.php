<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPriceDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'regclientdetail_id', 'from_state', 'to_state', 'price_per_kg', 'open_delivery_price', 'status', 'created_at', 'updated_at'
    ];

    public function ZoneFromState(){
        return $this->belongsTo('App\models\Zone','from_state');
    }

    public function ZoneToState(){
        return $this->belongsTo('App\models\Zone','to_state');
    }

}
