<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Consignee;
use Helper;

class UpdateLatitudeLongitude implements ToModel,WithHeadingRow
{
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        $postal_code = Consignee::where('postal_code', $row['postal_code'])->first();
        if(!empty($postal_code->postal_code)){
            Consignee::where('postal_code', $row['postal_code'])->update([
                'latitude'  =>  $row['latitude'],
                'longitude' =>  $row['longitude']
            ]);
        }
    }
    
}
