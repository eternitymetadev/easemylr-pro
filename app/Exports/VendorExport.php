<?php

namespace App\Exports;

use App\Models\Vendor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;
use App\Models\Role;

class VendorExport implements FromCollection, WithHeadings, ShouldQueue
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(6000);
        $arr = array();

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $query = Vendor::query();
        $query = $query->with('DriverDetail', 'Branch');
        if ($authuser->role_id == 2) {
            $query = $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }
        $vendordata = $query->orderBy('id', 'ASC')->get();

        if ($vendordata->count() > 0) {
            foreach ($vendordata as $key => $value) {
                $bank_detail = json_decode($value->bank_details);
                $other_details = json_decode($value->other_details);

                if ($value->declaration_available == 1) {
                    $declaration = 'Yes';
                } else {
                    $declaration = 'No';
                }

                $arr[] = [
                    'vendor_no' => $value->vendor_no,
                    'name' => $value->name,
                    'email' => @$value->email,
                    'driver' => @$value->DriverDetail->name,
                    'transporter_name' => @$other_details->transporter_name,
                    'contact_person_number' => @$other_details->contact_person_number,
                    'acc_holder_name' => $bank_detail->acc_holder_name,
                    'account_no' => @$bank_detail->account_no,
                    'ifsc_code' => @$bank_detail->ifsc_code,
                    'bank_name' => @$bank_detail->bank_name,
                    'branch_name' => @$bank_detail->branch_name,
                    'pan' => @$value->pan,
                    'vendor_type' => @$value->vendor_type,
                    'declaration_available' => @$declaration,
                    'tds_rate' => @$value->tds_rate,
                    'gst_no' => @$value->gst_no,
                    'gst_register' => @$value->gst_register,
                    'branch_id' => @$value->Branch->name,

                ];
            }
        }
        return collect($arr);

    }
    public function headings(): array
    {
        return [
            'Vendor No',
            'Name',
            'Email',
            'Driver',
            'Transporter Name',
            'Contact Number',
            'Account Holder Name',
            'Account Number',
            'Ifsc  Code',
            'Bank Name',
            'Branch Name',
            'Pan',
            'Vendor Type',
            'Declaration Available',
            'Tds Rate',
            'Gst No',
            'Gst Register',
            'Branch Location',

        ];
    }
}
