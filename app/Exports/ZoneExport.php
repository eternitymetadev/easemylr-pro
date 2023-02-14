<?php

namespace App\Exports;

use App\Models\Zone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Session;
use Helper;

class ZoneExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();
        $query = Zone::query();

        $zone = $query->orderby('created_at','DESC')->get();

        if($zone->count() > 0){
            foreach ($zone as $key => $value){
                $arr[] = [
                    'postal_code' => $value->postal_code,
                    'district' => $value->district,
                    'state' => $value->state,
                    'hub_transfer' => $value->hub_transfer,
                    'primary_zone' => $value->primary_zone,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'postal_code',
            'district',
            'state',
            'hub_transfer',
            'primary_zone',
        ];
    }
}
