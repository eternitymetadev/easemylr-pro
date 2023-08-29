<?php

namespace App\Exports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Session;
use Helper;

class DriverExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();
        $query = Driver::query();

        $driver = $query->orderby('created_at','DESC')->get();

        if($driver->count() > 0){
            foreach ($driver as $key => $value){  
                if($value->access_status == 0){
                    $access_status = 'Not Enabled';
                }else{
                    $access_status = 'Enabled';
                }
                $arr[] = [
                    'phone' => $value->phone,
                    'app_access' => $access_status,
                    'login_id' => @$value->login_id,
                    'login_password' => @$value->driver_password,
                    'license_number' => @$value->license_number,
                    'license_image' => @$value->license_image,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'phone',
            'App Access',
            'Login id',
            'Login Password',
            'license_number',
            'license_image',
        ];
    }
}
