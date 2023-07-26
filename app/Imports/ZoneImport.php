<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Zone;
use DB;

class ZoneImport implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $zone = Zone::where('postal_code', $row['postal_code'])->first();
        $location = Location::where('id',$row['hub_transfer_id'])->first();
        if(empty($zone)){
            return new Zone([
                'postal_code'   => $row['postal_code'],
                'state'         => $row['state'],
                'district'      => $row['district'],
                'pickup_hub'    => $row['pickup_hub'],
                'hub_transfer'  => @$location->name,
                'hub_nickname'  => $row['hub_transfer_id'],
                'status'        => "1",
                'created_at'    => time(),
            ]);
        }else{
            $zoneUpdate = Zone::where('postal_code', $row['postal_code'])->update([
                'state'          =>  $row['state'],
                'district'       =>  $row['district'],
                'pickup_hub'     =>  $row['pickup_hub'],
                'hub_transfer'   =>  @$location->name,
                'hub_nickname'   =>  $row['hub_transfer_id'],
                'updated_at'     =>  time(),
            ]);
        }

    }
}