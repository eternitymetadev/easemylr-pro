<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Zone;
use App\Models\Location;
use DB;

class ZoneImport implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $zone = Zone::where('postal_code', $row['postal_code'])->first();
        $pickup_location = Location::where('name',$row['pickup_hub'])->first();
        $delivery_location = Location::where('name',$row['delivery_hub'])->first();
        
        if(empty($zone)){
            return new Zone([
                'postal_code'   => @$row['postal_code'],
                'state'         => @$row['state'],
                'district'      => @$row['district'],
                'pickup_hub'    => @$pickup_location->id,
                'hub_transfer'  => @$delivery_location->name,
                'hub_nickname'  => @$delivery_location->id,
                'status'        => "1",
                'created_at'    => time(),
            ]);
        }else{
            $zoneUpdate = Zone::where('postal_code', $row['postal_code'])->update([
                'state'          =>  @$row['state'],
                'district'       =>  @$row['district'],
                'pickup_hub'     =>  @$row['pickup_hub'],
                'hub_transfer'   =>  @$delivery_location->name,
                'hub_nickname'   =>  @$delivery_location->id,
                'updated_at'     =>  time(),
            ]);
        }

    }
}