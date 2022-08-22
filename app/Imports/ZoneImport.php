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
        // $zone = DB::table('zones')->select('license_number')->where('license_number', $row['license_number'])->first();
        // if(empty($zone)){
            return new Zone([
                'primary_zone'  => $row['primary_zone'],
                'postal_code'   => (float)$row['postal_code'],
                'district'      => $row['district'],
                'state'         => $row['state'],
                'status'        => "1",
                'created_at'    => time(),
            ]);
        // }

    }
}
