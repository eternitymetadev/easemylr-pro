<?php

namespace App\Exports;

use App\Models\Consignee;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use DB;
use Session;
use Helper;
use Auth;

class ConsigneeExport implements FromCollection, WithHeadings,ShouldQueue
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
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        
        $query = Consignee::with('Consigner','Zone');
        // $query = DB::table('consignees')->select('consignees.*', 'consigners.nick_name as consigner_id', 'zones.postal_code as postal_code')
        // ->join('consigners', 'consigners.id', '=', 'consignees.consigner_id')
        // ->join('zones', 'zones.postal_code', '=', 'consignees.postal_code');

        if($authuser->role_id == 1){
            $query = $query;
        }
        else if($authuser->role_id == 2 || $authuser->role_id == 3){
            $query = $query->whereHas('Consigner', function($query) use($cc){
                $query->whereIn('branch_id', $cc);
            });
        }
        else{
            $query = $query->whereHas('Consigner', function($query) use($regclient){
                $query->whereIn('regionalclient_id', $regclient);
            });
        }

        $consignee = $query->orderby('created_at','DESC')->get();

        if($consignee->count() > 0){
            foreach ($consignee as $key => $value){  

                if(!empty($value->Zone->name)){
                    $zone = $value->Zone->name;
                }else{
                    $zone = '';
                }

                if(!empty($value->Consigner->nick_name)){
                    $consigner = $value->Consigner->nick_name;
                }else{
                    $consigner = '';
                }

                if(!empty($value->dealer_type == '1')){
                    $dealer_type = 'Registered';
                }else{
                    $dealer_type = 'Unregistered';
                }

                $arr[] = [
                    'nick_name'     => $value->nick_name,
                    'legal_name'    => $value->legal_name,
                    'consigner_id'  => $value->consigner_id,
                    'contact_name'  => $value->contact_name,
                    'email'         => $value->email,
                    'dealer_type'   => $dealer_type,
                    'gst_number'    => $value->gst_number,
                    'phone'         => $value->phone,
                    'postal_code'   => @$value->postal_code,
                    'city'          => @$value->city,
                    'district'      => @$value->Zone->district,
                    'state_id'      => @$value->Zone->state,
                    'zone_id'       => @$zone,
                    'address_line1' => $value->address_line1,
                    'address_line2' => $value->address_line2,
                    'address_line3' => $value->address_line3,
                    'address_line4' => $value->address_line4,

                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'Consignee Nick Name',
            'Consignee Legal Name',
            'Consigner',
            'Contact Person Name',
            'Email',
            'Type Of Dealer',
            'GST Number',
            'Mobile No.',
            'PIN Code',
            'City',
            'District',
            'State',
            'Primary Zone',
            'Address Line 1',
            'Address Line 2',
            'Address Line 3',
            'Address Line 4',
        ];
    }
}