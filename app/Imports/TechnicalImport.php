<?php

namespace App\Imports;

use App\Models\TechnicalMaster;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TechnicalImport implements ToModel, WithHeadingRow//ToCollection

{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
       
        $check_technical_name = TechnicalMaster::where('technical_formula', $row['technical_formula'])->first();
        if(empty($check_technical_name)){
        return new TechnicalMaster([
            'technical_formula'      => $row['technical_formula'],
       
        ]);
    }
    }
}
