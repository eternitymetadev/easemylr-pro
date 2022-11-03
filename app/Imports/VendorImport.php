<?php

namespace App\Imports;

use App\Models\Driver;
use App\Models\Vendor;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendorImport implements ToModel, WithHeadingRow//ToCollection

{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
         $check_ifsc = strlen($row['ifsc_code']);

         if($check_ifsc == 11){
        $driver = Driver::select('id')->where('name', $row['driver_name'])->where('phone', $row['driver_number'])->first();

        $other_details = array('transporter_name' => $row['transporter_name'], 'contact_person_number' => $row['transporter_name']);

        $bankdetails = array('acc_holder_name' => $row['account_holder_name'], 'account_no' => $row['account_no'], 'ifsc_code' => $row['ifsc_code'], 'bank_name' => $row['bank_name'], 'branch_name' => $row['branch_name']);

        $no_of_digit = 5;
        $vendor = DB::table('vendors')->select('vendor_no')->latest('vendor_no')->first();
        $vendor_no = json_decode(json_encode($vendor), true);
        if (empty($vendor_no) || $vendor_no == null) {
            $vendor_no['vendor_no'] = 0;
        }
        $number = $vendor_no['vendor_no'] + 1;
        $vendor_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);

        if($row['declaration_available'] == 'Yes'){
            $declaration = 1;
        }else if($row['declaration_available'] == 'No'){
            $declaration = 2;
        }else{
            $declaration = 0;
        }

        $vendor_check = Vendor::where('name', '=', $row['vendor_name'])->first();
        if(empty($vendor_check)){

        return new Vendor([
            'type'                   => 'Vendor',
            'vendor_no'              => $vendor_no,
            'name'                   => $row['vendor_name'],
            'email'                  => $row['email'],
            'other_details'          => json_encode($other_details),
            'bank_details'           => json_encode($bankdetails),
            'driver_id'              => @$driver->id,
            'pan'                    => $row['pan'],
            'vendor_type'            => $row['vendor_type'],
            'declaration_available'  => $declaration,
            'tds_rate'               => $row['tds_rate'],
            'branch_id'              => $row['branch_id'],
            'gst_register'           => $row['gst_register'],
            'gst_no'                 => $row['gst_number'],
            'is_acc_verified'        => 0,
            'is_active'              => 1,
            'created_at'             => time(),
        ]);
    }
}

    }
}
