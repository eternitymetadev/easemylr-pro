<?php

namespace App\Exports;

use App\models\SecondaryAvailStock;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\TransactionSheet;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Vehicle;
use App\Models\Role;
use App\Models\VehicleType;
use App\Models\User;
use Session;
use Helper;
use Auth;
use DateTime;
use DB;

class DrsPaymentExport implements FromCollection, WithHeadings, ShouldQueue
{
    protected $startdate;
    protected $enddate;
    protected $search_vehicle;
    protected $select_vehicle;

    function __construct($startdate,$enddate,$search_vehicle,$select_vehicle) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->search_vehicle = $search_vehicle;
        $this->select_vehicle = $select_vehicle;
    }

    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();

        $query = TransactionSheet::query();

        $startdate = $this->startdate;
        $enddate = $this->enddate;
        $search_vehicle = $this->search_vehicle;
        $select_vehicle = $this->select_vehicle;
        
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        
        $query = $query->whereIn('status', ['1', '0', '3'])
            ->where('request_status', 0)
            ->where('payment_status', '=', 0)
            ->groupBy('drs_no');

        if ($authuser->role_id == 1) {
            $query = $query->with('ConsignmentDetail');
        } elseif ($authuser->role_id == 4) {
            $query = $query
                ->with('ConsignmentDetail.vehicletype')
                ->whereHas('ConsignmentDetail', function ($query) use ($regclient) {
                    $query->whereIn('regclient_id', $regclient);
                });
        } elseif ($authuser->role_id == 5) {
            $query = $query->with('ConsignmentDetail');
        } elseif ($authuser->role_id == 6) {
            $query = $query
                ->whereHas('ConsignmentDetail', function ($query) use ($baseclient) {
                    $query->whereIn('base_clients.id', $baseclient);
                });
        } elseif ($authuser->role_id == 7) {
            $query = $query
                ->whereHas('ConsignmentDetail.ConsignerDetail.RegClient', function ($query) use ($baseclient) {
                    $query->whereIn('id', $regclient);
                });
        } else {

            $query = $query->whereIn('branch_id', $cc)->whereHas('ConsignmentDetail', function ($query) {
                $query->where('status', '!=', 0);
            });
        }

        if(isset($startdate) && isset($enddate)){
            $query = $query->whereBetween('created_at',[$startdate,$enddate]);                
        }

        if (!empty($request->search_vehicle)) {
            $search = $request->search_vehicle;
            $searchT = str_replace("'", "", $search);
            $query->where(function ($query) use ($search, $searchT) {
                $query->where('vehicle_no', 'like', '%' . $search . '%')
                    ->orWhere('drs_no', 'like', '%' . $search . '%');

            });
        }

        if (isset($select_vehicle)) {
            $query = $query->whereIn('vehicle_no', $select_vehicle);
        }
        
        $paymentlist = $query->orderBy('id','ASC')->get();
        
        if($paymentlist->count() > 0){
            foreach ($paymentlist as $key => $list){
                if (!empty(@$list->ConsignmentDetail->purchase_price)) {
                    $purchase_price = @$list->ConsignmentDetail->purchase_price;
                }else {
                    $purchase_price = "Add Amount";
                }

                // $date = new DateTime($list->created_at, new DateTimeZone('GMT-7'));
                // $date->setTimezone(new DateTimeZone('IST'));

                // drs status
                if ($list->status == 0) {
                    $drs_status = 'Cancelled';
                }else{
                    if (empty($list->vehicle_no) || empty($list->driver_name) || empty($list->driver_no)) {
                        $drs_status = 'No Status';
                    }else {
                        $drs_status = Helper::getdeleveryStatus(@$list->drs_no);
                    }
                }                
                
                $arr[] = [
                    'purchase_price'   => @$purchase_price,
                    'vehicle_type'     => @$list->ConsignmentDetail->vehicletype->name,
                    'drs_no'           => 'DRS-'.@$list->drs_no,
                    // 'drs_date'         => @$date->format('d-m-Y'),
                    'drs_date'         => @$list->created_at->format('d-m-Y'),
                    'drs_status'       => @$drs_status,
                    'total_lr'         => Helper::countdrslr(@$list->drs_no),
                    'vehicle_no'       => @$list->vehicle_no,
                    'gross_wt'         => Helper::totalGrossWeight(@$list->drs_no),
                    'total_wt'         => Helper::totalWeight(@$list->drs_no),
                    'quantity'         => Helper::totalQuantity(@$list->drs_no),
                    'driver_name'      => @$list->driver_name,                    
                ];
            }
        }
        return collect($arr);
    }

    public function headings(): array
    {
        return [
            'Purchase Price',
            'Vehicle Type',
            'Drs No',
            'Drs Date',
            'Drs Status',
            'Total LR',
            'Vehicle No',
            'Gross Wt.',
            'Total Wt.',
            'Quantity', 
            'Driver Name',
        ];
    }
}