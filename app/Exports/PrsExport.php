<?php

namespace App\Exports;

use App\Models\PickupRunSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Session;
use Helper;
use App\Models\Role;
use Auth;

class PrsExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
       
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $query = PickupRunSheet::query();

        if ($authuser->role_id == 1) {
            $query;
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }
       
        $prs_report = $query->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail','Consignment')->orderBy('id', 'DESC')->get();
//  echo "<pre>"; print_r($prs_report);exit;

        if($prs_report->count() > 0){
            foreach ($prs_report as $key => $value){  
                $reg_client_name = array();
                foreach($value->PrsRegClients as $regclients)
                {
                $reg_client_name[]=$regclients->RegClient->name;
                }
                // for($i=0;$i<sizeof($reg_client_name);$i++)
                // {
                //     $string[]=$reg_client_name[$i];
                // }
                
                $consigner_name = array();
               $reg_client=implode(',',$reg_client_name);
               foreach($value->PrsRegClients as $regcnrs)
               foreach($regcnrs->RegConsigner as $regcnr)
               {
                $consigner_name[]= $regcnr->Consigner->nick_name ;
               }

              $pickup_point=implode(',',$consigner_name);
             
                $arr[] = [
                    'pickup_id' => $value->pickup_id,
                    'regional_client' => $reg_client,
                    'pickup_points' => $pickup_point,
                    'date' => $value->prs_date,
                    'vehicle_no' => @$value->VehicleDetail->regn_no,
                    'driver_name' => @$value->DriverDetail->name,
                    'quantity' => @$value->Consignment->total_quantity,
                    'net_weight' => @$value->Consignment->total_weight,
                    'gross_weight' => @$value->Consignment->total_gross_weight,
                    // 'status' => $value->status,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'Pickup ID',
            'Regional Client',
            'Pickup Points',
            'Date',
            'Vehicle Number',
            'Driver Name',
            'Quantity',
            'Net Weight',
            'Gross Weight',
        ];
    }
}
