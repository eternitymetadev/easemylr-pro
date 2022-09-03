<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'baseclient_id', 'location_id', 'name', 'email', 'phone', 'is_multiple_invoice', 'status', 'created_at', 'updated_at'
    ];


    public function BaseClient()
    {
        return $this->hasOne('App\Models\BaseClient','id','baseclient_id');
    }

    public function Location()
    {
        return $this->belongsTo('App\Models\Location','location_id');
    }

}
