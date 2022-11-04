<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Consigner;
use App\Models\State;
use App\Models\Location;
use App\Models\RegionalClient;

class ConsignerImport implements ToModel,WithHeadingRow
{
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // $getState = State::where('name',$row['state'])->first();
        $getregClient = RegionalClient::where('id',$row['regional_client_id'])->first();

        if(!empty($getregClient)){
            $regclient = $getregClient->id;
        }
        else{
            $regclient = 'N/A';
        }

        if(!empty($row['location_id'])){
            $location = $row['location_id'];
        }
        else{
            $location = 'N/A';
        }
        
        // if(!empty($getState)){
        //     $state = $getState->id;
        // }
        // else{
        //     $state = 'N/A';
        // }

        $consigner = Consigner::where('nick_name', $row['nick_name'])->where('regionalclient_id', $row['regional_client_id'])->first();
        if(empty($consigner)){
            
            return new Consigner([
                'nick_name'    => $row['nick_name'],
                'legal_name'   => $row['legal_name'],
                'gst_number'   => $row['gst_number'],
                'contact_name' => $row['contact_name'],
                'phone'        => (float)$row['phone'],
                'regionalclient_id' => $regclient,
                'branch_id'    => $location,
                'email'        => $row['email'],
                'address_line1'=> $row['address_line1'],
                'address_line2'=> $row['address_line2'],
                'address_line3'=> $row['address_line3'],
                'address_line4'=> $row['address_line4'], 
                'city'         => $row['city'],
                'district'     => $row['district'],
                'postal_code'  => $row['postal_code'],
                'state_id'     => $row['state'],
                'status'       => 1,
                'created_at'   => time(),
            ]);
        }else{
            $consigner = Consigner::where('nick_name', $row['nick_name'])->where('regionalclient_id', $row['regional_client_id'])->update([
                'nick_name'    => $row['nick_name'],
                'legal_name'   => $row['legal_name'],
                'gst_number'   => $row['gst_number'],
                'contact_name' => $row['contact_name'],
                'phone'        => (float)$row['phone'],
                'regionalclient_id' => $regclient,
                'branch_id'    => $location,
                'email'        => $row['email'],
                'address_line1'=> $row['address_line1'],
                'address_line2'=> $row['address_line2'],
                'address_line3'=> $row['address_line3'],
                'address_line4'=> $row['address_line4'], 
                'city'         => $row['city'],
                'district'     => $row['district'],
                'postal_code'  => $row['postal_code'],
                'state_id'     => $row['state'],
            ]);
        }
    }
}
