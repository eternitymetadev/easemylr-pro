<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use App\Models\Role;
use App\Models\ConsignmentNote;
use Auth;
use DB;

class Report2Export implements FromQuery, WithHeadings
{
    protected $startdate;
    protected $enddate;
    protected $baseclient_id;
    protected $regclient_id;

    function __construct($startdate, $enddate, $baseclient_id, $regclient_id)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->baseclient_id = $baseclient_id;
        $this->regclient_id = $regclient_id;
    }

    public function query()
    {
        $query = ConsignmentNote::
            where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
                'ConsignerDetail.GetZone',
                'ConsigneeDetail.GetZone',
                'ShiptoDetail.GetZone',
                'VehicleDetail:id,regn_no',
                'DriverDetail:id,name,fleet_id,phone',
                'ConsignerDetail.GetRegClient:id,name,baseclient_id',
                'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
                'VehicleType:id,name',
                'DrsDetail:consignment_no,drs_no,created_at'
            )->orderBy('id', 'asc');

        $authuser = Auth::user();
        $role_id = $authuser->role_id;
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        if ($role_id == 4) {
            $query->whereIn('regclient_id', $regclient);
        } elseif ($role_id != 1) {
            $query->whereIn('branch_id', $cc);
        }

        if (isset($this->startdate) && isset($this->enddate)) {
            $query->whereBetween('consignment_date', [$this->startdate, $this->enddate]);
        }

        if ($this->baseclient_id) {
            $query->whereHas('ConsignerDetail.GetRegClient.BaseClient', function ($q) {
                $q->where('id', $this->baseclient_id);
            });
        }

        if ($this->regclient_id) {
            $query->whereHas('ConsignerDetail.GetRegClient', function ($q) {
                $q->where('id', $this->regclient_id);
            });
        }

        // Return the query builder instance without using ->with()
        return $query;
    }

    public function headings(): array
    {
        return [
            // Add your headings here
        ];
    }
}
