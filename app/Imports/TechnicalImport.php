<?php

namespace App\Imports;

use App\Models\Vendor;
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
        echo'<pre>'; print_r($row); die;
        
        return new Tmaster([
            'type'                   => 'Vendor',
            'vendor_no'              => $vendor_no,
            'name'                   => $row['vendor_name'],
            'email'                  => $row['email'],
           
        ]);
    }
}
