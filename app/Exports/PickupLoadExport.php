<?php

namespace App\Exports;

use App\Models\ConsignmentNote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Session;
use Helper;
use App\Models\Role;
use Carbon\Carbon;
use DateTime;
use Auth;

class PickupLoadExport implements FromCollection, WithHeadings,ShouldQueue
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
        $query = ConsignmentNote::query();

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc) {
                $query->whereIn('fall_in', $cc);
            });
        }

        $consignments = $query->where(['status' => 5, 'prsitem_status' => 0, 'lr_type' => 1])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'PrsDetail')->orderBy('id', 'DESC')->get();
        
        if($consignments->count() > 0){
            foreach ($consignments as $consignment){
                
                $arr[] = [
                    'pickup_branch' => @$consignment->ConsignerDetail->GetBranch->name,
                    'booking_branch' => @$consignment->Branch->name,
                    'lr_no' => @$consignment->id,
                    'lr_date' => @$consignment->consignment_date,
                    'client' => @$consignment->ConsignerDetail->GetRegClient->name,
                    'consigner' => @$consignment->ConsignerDetail->nick_name, 
                    'pincode' => @$consignment->ConsignerDetail->postal_code,
                    'city' => @$consignment->ConsignerDetail->city,
                    'quantity' => @$consignment->total_quantity,
                    'net_weight' => @$consignment->total_weight,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'Pickup Branch',
            'Booking Branch',
            'LR No',
            'LR Date',
            'Client',
            'Consigner',
            'PIN',
            'City',
            'Quantity',
            'Net Weight',
        ];
    }
}
