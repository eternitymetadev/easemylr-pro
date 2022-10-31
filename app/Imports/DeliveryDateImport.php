<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\ConsignmentNote;
use Helper;

class DeliveryDateImport implements ToModel,WithHeadingRow
{
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $date_string = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['delivery_date']);
        $delivery_date = $date_string->format('Y-m-d');
        
        $lr_no = ConsignmentNote::where('id',$row['lr_no'])->first();

        if(empty($lr_no->delivery_date)){
            ConsignmentNote::where('id', $row['lr_no'])->update([
                'delivery_date'  => $delivery_date,
                'delivery_status'    => 'Successful',
            ]);
        }
    }
}
