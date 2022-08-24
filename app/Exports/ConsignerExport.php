<?php

namespace App\Exports;

use App\Models\Consigner;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use DB;
use Session;
use Helper;
use Auth;

class ConsignerExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();
        $query = Consigner::query();

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id); 
        $cc = explode(',',$authuser->branch_id);

        // $consigner = $query->with('State','RegClient','Branch')->orderby('created_at','DESC')->get();

        $query = DB::table('consigners')->select('consigners.*', 'regional_clients.name as regional_clientname', 'states.name as state_id')
                ->leftjoin('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->leftjoin('states', 'states.id', '=', 'consigners.state_id');

        if($authuser->role_id == 1){
            $query = $query;
        }
        else if($authuser->role_id == 2 || $authuser->role_id == 3){
            $query = $query->whereIn('consigners.branch_id', $cc);
        }
        else{
            $query = $query->whereIn('consigners.regionalclient_id', $regclient);
        }

        $consigners = $query->orderby('created_at','DESC')->get();

        if($consigners->count() > 0){
            foreach ($consigners as $key => $value){  
                if(!empty($value->State)){
                    if(!empty($value->State->name)){
                      $state = $value->State->name;
                    }else{
                      $state = '';
                    }
                }else{
                    $state = '';
                }
                if(!empty($value->RegClient)){
                    if(!empty($value->RegClient->name)){
                      $regClient = $value->RegClient->name;
                    }else{
                      $regClient = '';
                    }
                }else{
                    $regClient = '';
                }

                if(!empty($value->Branch)){
                    if(!empty($value->Branch->name)){
                      $location_name = $value->Branch->name;
                    }else{
                      $location_name = '';
                    }
                }else{
                    $location_name = '';
                }
                

                $arr[] = [
                    'id' => $value->id,
                    'nick_name' => $value->nick_name,
                    'legal_name' => $value->legal_name,
                    'gst_number' => $value->gst_number,
                    'contact_name' => $value->contact_name,
                    'phone' => $value->phone,
                    'branch_id' => $location_name,
                    'regionalclient_id' => @$regClient,
                    'email' => $value->email,
                    'address_line1' => $value->address_line1,
                    'address_line2' => $value->address_line2,
                    'address_line3' => $value->address_line3,
                    'address_line4' => $value->address_line4,
                    'postal_code' => $value->postal_code,
                    'city' => $value->city,
                    'district' => $value->district,
                    'postal_code' => $value->postal_code,
                    'state_id' => $state,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'id',
            'Consigner Nick Name',
            'Consigner Legal Name',
            'GST Number',            
            'Contact Person Name',
            'Mobile No.',
            'Location Name',
            'Regional Client Name',
            'Email',
            'Address Line1',
            'Address Line2',
            'Address Line3',
            'Address Line4',
            'PIN Code',
            'City',
            'District',
            'State',
        ];
    }
}
