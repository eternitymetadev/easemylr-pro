<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Consignee;
use Helper;

class ConsigneePhoneImport implements ToModel,WithHeadingRow
{
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {        
        $consignee = Consignee::where('id', $row['consignee_id'])->first();
        
        if(!empty($consignee)){
            Consignee::where('id', $row['consignee_id'])->update(['phone' => $row['contact_no']]);
        }
    }
}
