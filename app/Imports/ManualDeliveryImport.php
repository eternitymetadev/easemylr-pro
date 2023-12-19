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
    // For shadow app change mode app to manual now its not need because shadow app not used
    public function model(array $row)
    {        
        $lr_no = ConsignmentNote::where('id', $row['lr_no'])->first();
        // job_id in consignment_notes table value come from eternity app now not use 
        if(!empty($lr_no->job_id)){
            ConsignmentNote::where('id', $row['lr_no'])->update(['lr_mode' => 0]);
        }
    }
}
