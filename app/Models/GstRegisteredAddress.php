<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstRegisteredAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'gst_no',
        'state',
        'address_line_1',
        'address_line_2',
        'upload_gst',
        'created_at', 
        'updated_at'
    ];

    public function Branch(){
        return $this->hasMany('App\Models\Location','gst_registered_id','id');
    }

}
