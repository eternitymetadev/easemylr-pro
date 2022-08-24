<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Location;
use App\Models\TransactionSheet;
use App\Models\Vehicle;
use App\Models\Role;
use App\Models\VehicleType;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Auth;
use DB;
use QrCode;
use Storage;
use Validator;
use DataTables;
use Helper;
use Response;

class ReportController extends Controller
{
    public function consignmentReportsAll()
    {
        $this->prefix = request()->route()->getPrefix();

        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id == 4){
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet','ship.nick_name as ship_nick', 'ship.city as ship_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('consignees as ship', 'ship.id', '=', 'consignment_notes.ship_to_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->where('consignment_notes.user_id', $authuser->id)
                    ->get(['consignees.city']);
                    // dd($consignments);
            }else{
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet','ship.nick_name as ship_nick','ship.city as ship_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('consignees as ship', 'ship.id', '=', 'consignment_notes.ship_to_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->get(['consignees.city']);
            }
        } else {
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet','ship.nick_name as ship_nick','ship.city as ship_city')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('consignees as ship', 'ship.id', '=', 'consignment_notes.ship_to_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->get(['consignees.city']);
            }
        return view('consignments.consignment-reportAll', ['consignments' => $consignments, 'prefix' => $this->prefix]);
    }
    public function getFilterReportall(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id == 4){
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet','ship.nick_name as ship_nick','ship.city as ship_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('consignees as ship', 'ship.id', '=', 'consignment_notes.ship_to_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->where('consignment_notes.user_id', $authuser->id)
                    ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                    ->get(['consignees.city']);
            }else{
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet','ship.nick_name as ship_nick','ship.city as ship_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('consignees as ship', 'ship.id', '=', 'consignment_notes.ship_to_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                    ->get(['consignees.city']);
            }
        } else {
            $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet','ship.nick_name as ship_nick','ship.city as ship_city')
                ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                ->join('consignees as ship', 'ship.id', '=', 'consignment_notes.ship_to_id')
                ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                ->get(['consignees.city']);
        }
        $response['fetch'] = $consignments;
        $response['success'] = true;
        $response['messages'] = 'Succesfully loaded';
        return Response::json($response);
    }
       // =============================Admin Report ============================= //
       
    public function adminReport1(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
      
            $query = Consigner::query();
            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $regclient = explode(',',$authuser->regionalclient_id); 
            $cc = explode(',',$authuser->branch_id);
          
                $consigners = DB::table('consigners')->select('consigners.*', 'regional_clients.name as regional_clientname','base_clients.client_name as baseclient_name', 'states.name as state_id','consignees.nick_name as consignee_nick_name', 'consignees.contact_name as consignee_contact_name', 'consignees.phone as consignee_phone', 'consignees.postal_code as consignee_postal_code', 'consignees.district as consignee_district','consignees.state_id as consignee_states','consigne_stat.name as consignee_state')
                ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                ->join('consignees', 'consignees.consigner_id', '=', 'consigners.id')
                ->leftjoin('states', 'states.id', '=', 'consigners.state_id')
                ->leftjoin('states as consigne_stat', 'consigne_stat.id', '=', 'consignees.state_id')
                ->get();
                

        return view('consignments.admin-report1',["prefix" => $this->prefix,'adminrepo' => $consigners]);
    }

    public function adminReport2(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        
        $lr_data = DB::table('consignment_notes')->select('consignment_notes.*','consigners.nick_name as consigner_nickname','regional_clients.name as regional_client_name','base_clients.client_name as base_client_name', 'locations.name as locations_name')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->leftjoin('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                ->join('locations', 'locations.id', '=', 'regional_clients.location_id')
                ->get();
                

        return view('consignments.admin-report2',["prefix" => $this->prefix, 'lr_data' => $lr_data]);
    }

}
