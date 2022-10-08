<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalClientDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'regclient_id', 'docket_price', 'status', 'created_at', 'updated_at'
    ];

    public function RegClient(){
        return $this->hasOne('App\models\RegionalClient','id','regclient_id');
    }

    // public function ClientPriceDetail()
    // {
    //     return $this->hasOne('App\models\ClientPriceDetail','regclientdetail_id','id');
    // }

    public function ClientPriceDetails()
    {
        return $this->hasMany('App\models\ClientPriceDetail','regclientdetail_id','id');
    }
    
}
