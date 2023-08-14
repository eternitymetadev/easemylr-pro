<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfCarrierPartner extends Model
{
    use HasFactory;
    protected $fillable = [
        'areaOfDelivery',
        'company',
        'companyAddress',
        'contactPerson',
        'email',
        'fleetSize',
        'isCompliant',
        'leaseVehicle',
        'phone',
        'reference',
        'specializedTransportation',
        'typeOfShipment',
        'valueAddedServices',
        'workingYears',
        'status',
        'created_at',
        'updated_at'
    ];
}
