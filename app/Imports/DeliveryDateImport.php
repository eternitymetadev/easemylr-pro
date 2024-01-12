<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\ConsignmentNote;
use Helper;
use Auth;

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

        $authuser = Auth::user();
        
        $consignmentNote = ConsignmentNote::find($row['lr_no']);
        // if ($consignmentNote && empty($consignmentNote->delivery_date) && !empty($delivery_date)) {
            if ($consignmentNote && !empty($delivery_date)) {
            // dd($consignmentNote);
            $consignmentNote->update([
                'delivery_date' => $delivery_date,
                'delivery_status' => 'Successful',
                'signed_drs' => $row['pod_image'],
                'lr_mode' => 0,
                'pod_userid' => $authuser->id,
                'consignment_no'=>'By import',
            ]);
        }
    }

}

// public function model(array $row)
    // {
    //     $date_string = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['delivery_date']);
    //     $delivery_date = $date_string->format('Y-m-d');

    //     $authuser = Auth::user();
        
    //     $lr_dd = ConsignmentNote::where('id',$row['lr_no'])->update(['delivery_date'=> '']);
        
    //     if(empty($lr_dd->delivery_date)){
    //         if(!empty($delivery_date)){
    //             ConsignmentNote::where('id', $row['lr_no'])->update([
    //                 'delivery_date'  => $delivery_date,
    //                 'delivery_status' => 'Successful',
    //                 'signed_drs'    => $row['pod_image'],
    //                 'lr_mode'       => 0,
    //                 'pod_userid'    => $authuser->id,
    //             ]);
    //         }
    //     }
    // }