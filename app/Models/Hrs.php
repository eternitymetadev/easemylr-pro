<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hrs extends Model
{
    use HasFactory;
    protected $fillable = [
        'hrs_no',
        'consignment_id',
        'vehicle_id',
        'driver_id',
        'branch_id',
        'status',
        'created_at',
        'updated_at'
    ];

    public function ConsignmentDetail()
    {
        return $this->hasOne('App\Models\ConsignmentNote','id','consignment_id');
    }
    public function VehicleDetail()
    {
        return $this->hasOne('App\Models\Vehicle','id','vehicle_id');
    }
    public function DriverDetail()
    {
        return $this->hasOne('App\Models\Driver','id','driver_id');
    }
}