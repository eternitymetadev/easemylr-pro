<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'vendor_no',
        'name',
        'email',
        'driver_id',
        'bank_details',
        'pan',
        'upload_pan',
        'cancel_cheaque',
        'other_details',
        'vendor_type',
        'declaration_available',
        'declaration_file',
        'tds_rate',
        'branch_id',
        'gst_register',
        'gst_no',
        'is_acc_verified',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function DriverDetail()
    {
        return $this->hasOne('App\Models\Driver','id','driver_id');
    }
    public function Branch(){
        return $this->belongsTo('App\Models\Location','branch_id');
    }
}
