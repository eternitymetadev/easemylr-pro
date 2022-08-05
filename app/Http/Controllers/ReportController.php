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
            if($authuser->role_id == $role_id->id){
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->join('states', 'states.id', '=', 'consignees.state_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->get(['consignees.city']);
                    // echo'<pre>'; print_r($consignments); die;
                    
            }} else {
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet')
                    ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                    ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->join('states', 'states.id', '=', 'consignees.state_id')
                    ->get(['consignees.city']);

            }
        //echo'<pre>'; print_r($consignments); die;

        return view('consignments.consignment-reportAll', ['consignments' => $consignments, 'prefix' => $this->prefix]);

    }
    public function getFilterReportall(Request $request)
    {
     //echo'<pre>'; print_r($_POST); die;
     $query = ConsignmentNote::query();
     $authuser = Auth::user();
     $role_id = Role::where('id','=',$authuser->role_id)->first();
     $regclient = explode(',',$authuser->regionalclient_id);
     $cc = explode(',',$authuser->branch_id);
     if($authuser->role_id !=1){
         if($authuser->role_id == $role_id->id){
             $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet')
                 ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                 ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                 ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                 ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                 ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                 ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                 ->join('states', 'states.id', '=', 'consignees.state_id')
                 ->whereIn('consignment_notes.branch_id', $cc)
                 ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                 ->get(['consignees.city']);
                 // echo'<pre>'; print_r($consignments); die;
                 
         }} else {
             $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city', 'regional_clients.name as regional_name','base_clients.client_name as baseclient_name','drivers.name as drivers_name', 'drivers.phone as drivers_phone', 'drivers.fleet_id as fleet')
                 ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
                 ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
                 ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                 ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                 ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                 ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                 ->join('states', 'states.id', '=', 'consignees.state_id')
                 ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                 ->get(['consignees.city']);

         }
        //  echo'<pre>'; print_r($consignments); die;
          $response['fetch'] = $consignments;
          $response['success'] = true;
          $response['messages'] = 'Succesfully loaded';
          return Response::json($response);
    }
}
