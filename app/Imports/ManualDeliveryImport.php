<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\ConsignmentNote;
use Helper;

class ManualDeliveryImport implements ToModel,WithHeadingRow
{
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {        
        $lr_no = ConsignmentNote::where('id', $row['lr_no'])->first();

        if(!empty($lr_no->job_id)){
            ConsignmentNote::where('id', $row['lr_no'])->update(['job_id' => NULL]);
        }
    }
}
