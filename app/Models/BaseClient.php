<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_name','gst_no','pan','tan','upload_gst','upload_pan','upload_tan','upload_moa','email', 'phone', 'address', 'status', 'created_at', 'updated_at'
    ];

    public function RegClients(){
        return $this->hasMany('App\Models\RegionalClient','baseclient_id');
    }
}
