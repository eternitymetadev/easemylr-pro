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
    protected $startdate;
    protected $enddate;
    protected $search;
    
    function __construct($startdate,$enddate,$search) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->search = $search;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        
        $query = ConsignmentNote::query();

        $query = $query->where(['status' => 5, 'prsitem_status' => 0, 'lr_type' => 1])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'PrsDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4 || $authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } else {
            $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc) {
                $query->whereIn('fall_in', $cc);
            });
        }

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $search = $this->search;

        if (!empty($search)) {
            $search = $search;
            $searchT = str_replace("'", "", $search);
            $query->where(function ($query) use ($search, $searchT) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ConsignerDetail', function ($query) use ($search, $searchT) {
                        $query->where(function ($cnrquery) use ($search, $searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('ConsigneeDetail', function ($query) use ($search, $searchT) {
                        $query->where(function ($cneequery) use ($search, $searchT) {
                            $cneequery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    });
            });
        }

        if(isset($startdate) && isset($enddate)){
            $query = $query->whereBetween('consignment_date',[$startdate,$enddate]);                
        }

        $consignments = $query->orderBy('id', 'DESC')->get();
        
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
