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
    protected $failedLRs = [];

    public function getFailedLRs()
    {
        return $this->failedLRs;
    }
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $date_string = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['delivery_date']);
        $getDelivery_date = $date_string->format('Y-m-d');

        $authuser = Auth::user();        
        $consignmentNote = ConsignmentNote::find($row['lr_no']);

        if ($consignmentNote && !empty($getDelivery_date) && ($consignmentNote->delivery_status == 'Started' || $consignmentNote->delivery_status == 'Successful')
        && ($consignmentNote->consignment_date <= $getDelivery_date)) {
            if($consignmentNote->delivery_date){
                $delivery_date = $consignmentNote->delivery_date;
            }else{
                $delivery_date = $getDelivery_date;
            }
            // dd($consignmentNote);
            $consignmentNote->update([
                'delivery_date' => $delivery_date,
                'delivery_status' => 'Successful',
                'signed_drs' => $row['pod_image'],
                'lr_mode' => 0,
                'pod_userid' => $authuser->id,
                'consignment_no'=>'By import',
            ]);
        } else {
            // LR ID without a valid POD, store it in the failedLRs array
            $this->failedLRs[] = $row['lr_no'];
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