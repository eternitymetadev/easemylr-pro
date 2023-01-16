<?php

namespace App\Imports;

use App\Models\ItemMaster;
use App\Models\TechnicalMaster;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemUpload implements ToModel, WithHeadingRow//ToCollection

{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        
        $technical_formula = TechnicalMaster::where('technical_formula', '=', $row['technical_name'])->first();
        if(!empty($technical_formula)){
           $technical_id = $technical_formula->id;
        }else{
            $technical_id = Null;
        }
        return new ItemMaster([
            'technical_id'          => $technical_id,
            'technical_formula'      => $row['technical_name'],
            'erp_mat_code'          => $row['erp_mat_code'],
            'manufacturer'      => $row['manufacturer'],
            'brand_name'         => $row['brand_name'],
            'net_weight'       => $row['net_wt'],
            'gross_weight'    => $row['g_wt'],
            'chargable_weight'  => $row['c_wt']
       
        ]);
    
    }
}
