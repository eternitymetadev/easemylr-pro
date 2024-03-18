<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'baseclient_id', 'location_id', 'name','regional_client_nick_name', 'email', 'secondary_email', 'phone', 'gst_no','pan','upload_gst','upload_pan','payment_term','is_multiple_invoice', 'is_prs_pickup', 'status','is_email_sent', 'is_misemail', 'is_podemail', 'created_at', 'updated_at'
    ];


    public function BaseClient()
    {
        return $this->hasOne('App\Models\BaseClient','id','baseclient_id');
    }

    public function Location()
    {
        return $this->hasOne('App\Models\Location','id','location_id');
    }

}
