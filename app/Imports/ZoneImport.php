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
     
         $zone = DB::table('zones')->select('postal_code')->where('postal_code', $row['postal_code'])->first();
        if(empty($zone)){
            return new Zone([
                'primary_zone'  => $row['primary_zone'],
                'postal_code'   => (float)$row['postal_code'],
                'district'      => $row['district'],
                'state'         => $row['state'],
                'status'        => "1",
                'created_at'    => time(),
            ]);
        }else{
            $consigner = Zone::where('postal_code', $row['postal_code'])->update([
                'hub_transfer'    => $row['hub_transfer'],
                'updated_at'    => time(),
            ]);
        }

    }
}
