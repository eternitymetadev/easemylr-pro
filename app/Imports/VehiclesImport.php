<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Vehicle;
use App\Models\State;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VehiclesImport implements ToModel, WithHeadingRow  //ToCollection
{
    /**
    * @param Collection $collection
    */
    
    public function model(array $row)
    {
        $getState = State::where('name',$row['state'])->first();
        
        if(empty($getState)){
            $state = 'N/A';
        }
        else{
            $state = $getState->id;
        }

        if(empty($row['status'])){
            $status = '0';
        }
        else{
            $status = $row['status'];
        }
        $tonnage_capacity = (int)$row['gross_vehicle_weight'] - (int)$row['unladen_weight'];

        $regn_date= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['regn_date']);
        $vehicle = Vehicle::where('regn_no', $row['regn_no'])->first();
        if(empty($vehicle)){
            return new Vehicle([
                'regn_no'        => $row['regn_no'],
                'mfg'            => $row['manufacture'],
                'make'           => $row['make'],
                'engine_no'      => $row['engine_no'],
                'chassis_no'     => $row['chassis_no'], 
                'gross_vehicle_weight' => $row['gross_vehicle_weight'],
                'unladen_weight' => $row['unladen_weight'], 
                'body_type'      => $row['body_type'],
                'tonnage_capacity' => $tonnage_capacity,
                'state_id'       => $state,
                'regndate'       => $regn_date,
                'hypothecation'  => $row['hypothecation'],
                'ownership'      => $row['ownership'],
                'status'         => $status,
                'created_at'     => time(),
            ]);
        }
    }

}
