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
       
        return new TechnicalMaster([
            'technical_formula'      => $row['technical_formula'],
       
        ]);
    }
}
