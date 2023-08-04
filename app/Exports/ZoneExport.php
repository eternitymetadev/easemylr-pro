<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use App\Models\Zone;
use Session;
use Helper;

class ZoneExport implements FromCollection, WithHeadings,ShouldQueue
{
    protected $state_name;

    function __construct($state_name) {
        $this->state_name = $state_name;
    }
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = Zone::query();
        $query = $query->with('Branch','GetLocation');

        $str = $this->state_name;
        if($str){
            $state_name = explode(" ",$str);
            $query = $query->whereIn('state', $state_name);
        }
        
        $zone = $query->orderby('created_at','DESC')->get();

        if($zone->count() > 0){
            foreach ($zone as $key => $value){
                $arr[] = [
                    'postal_code'   => @$value->postal_code,
                    'district'      => @$value->district,
                    'state'         => @$value->state,
                    'city'          => @$value->city,
                    'pickup_hub'    => @$value->GetLocation->name,
                    'hub_transfer'  => @$value->hub_transfer,
                    // 'hub_nickname'  => @$value->Branch->hub_nickname,
                    // 'primary_zone' => $value->primary_zone,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'Postal Code',
            'District',
            'State',
            'City',
            'Pickup Hub',
            'Delivery Hub',
            // 'delivery_hub_nickname',
            // 'primary_zone',
        ];
    }
}