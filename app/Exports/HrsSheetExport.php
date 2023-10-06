<?php

namespace App\Exports;

use App\Models\Hrs;
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

class HrsSheetExport implements FromCollection, WithHeadings,ShouldQueue
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
        $query = Hrs::query();

        if ($authuser->role_id == 1) {
            $query = $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query
                ->whereHas('ConsignmentDetail', function ($query) use ($regclient) {
                    $query->whereIn('regclient_id', $regclient);
                });
        } elseif ($authuser->role_id == 6) {
            $query = $query
                ->whereHas('ConsignmentDetail', function ($query) use ($baseclient) {
                    $query->whereIn('base_clients.id', $baseclient);
                });
        } elseif ($authuser->role_id == 7) {
            $query = $query->with('ConsignmentDetail')->whereIn('regional_clients.id', $regclient);
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }
       
        $hrssheets = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')
        ->whereIn('status', ['1', '0', '3'])
        ->groupBy('hrs_no')
        ->orderBy('id', 'DESC')->get();
        
        if($hrssheets->count() > 0){
            foreach ($hrssheets as $hrssheet){
                $date = new DateTime($hrssheet->created_at, new \DateTimeZone('GMT-7'));
                $date->setTimezone(new \DateTimeZone('IST'));

                //received status
                if($hrssheet->receving_status == 1){
                    $received_status = 'Outgoing';
                }else{
                    $received_status = 'Received Hub';
                }

                //payment status
                if($hrssheet->payment_status == 0){
                    $payment_status = 'unpaid';
                }elseif($hrssheet->payment_status == 1){
                    $payment_status = 'Paid';
                }elseif($hrssheet->payment_status == 2){
                    $payment_status = 'Sent';
                }elseif($hrssheet->payment_status == 3){
                    $payment_status = 'Partial Paid';
                }else{
                    $payment_status = 'Unknown';
                }
                                
                $arr[] = [
                    'hrs_no' => $hrssheet->hrs_no,
                    'created_date' => @$date->format('Y-m-d'),
                    'hub_transferbranch' => @$hrssheet->ToBranch->name,
                    'vehicle_no' => @$hrssheet->VehicleDetail->regn_no,
                    'driver_name' => @$hrssheet->DriverDetail->name,
                    'driver_phone' => @$hrssheet->DriverDetail->phone, 
                    'received_status' => @$received_status,
                    'payment_status' => @$payment_status,
                ];
            }
        }                 
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'Hrs No',
            'Hrs Date',
            'Hub Transfer',
            'Vehicle Number',
            'Driver Name',
            'Driver Phone',
            'Received Status',
            'Payment Status',
        ];
    }
}
