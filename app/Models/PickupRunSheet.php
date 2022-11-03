<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRunSheet extends Model
{
    use HasFactory;
    protected $fillable = [
        'regclient_id', 'location_id', 'consigner_id', 'prs_type', 'vehicletype_id', 'vehicle_id', 'driver_id', 'prs_date','user_id', 'branch_id', 'status', 'created_at', 'updated_at'
    ];

    public function VehicleDetail()
    {
        return $this->hasOne('App\Models\Vehicle','id','vehicle_id');
    }

    public function DriverDetail()
    {
        return $this->hasOne('App\Models\Driver','id','driver_id');
    }
    public function ConsignerDetail()
    {
        return $this->hasOne('App\Models\Consigner','id','consigner_id');
    }

    public function RegClient()
    {
        return $this->hasOne('App\Models\RegionalClient','id','regclient_id');
    }

}
