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
use App\Models\User;
use App\Exports\MisReportExport;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Auth; 
use DB;
use QrCode;
use Storage;
use Validator;
use DataTables;
use Helper;
use Response;
use Excel;

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
        $lastsevendays = \Carbon\Carbon::today()->subDays(7);
        $date = Helper::yearmonthdate($lastsevendays);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        if($authuser->role_id ==1)
        {
            $query = $query
            ->where('consignment_date', '>=', $date)
            ->where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
                'ConsignerDetail:regionalclient_id,id,nick_name,city,postal_code,district,state_id',
                'ConsignerDetail.GetState:id,name',
                'ConsigneeDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ConsigneeDetail.GetState:id,name', 
                'ShiptoDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ShiptoDetail.GetState:id,name',
                'VehicleDetail:id,regn_no', 
                'DriverDetail:id,name,fleet_id,phone', 
                'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
                'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
                'vehicletype:id,name')
            ->orderBy('id','DESC')->get();
        }elseif($authuser->role_id == 4){
            $query = $query
            ->where('consignment_date', '>=', $date)
            ->where('status', '!=', 5)
            ->whereIn('user_id', [$authuser->id, $user->id])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();                

        }else{
            $query = $query->where('status', '!=', 5)
            ->where('branch_id', $cc)
            ->where('consignment_date', '>=', $date)
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();
        } 
        $consignments = json_decode(json_encode($query), true);
        // echo "<pre>", print_r($consignments); die;
        return view('consignments.consignment-reportAll', ['consignments' => $consignments, 'prefix' => $this->prefix]);
    }
    public function getFilterReportall(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        if($authuser->role_id ==1){
            // $query = $query
            // ->where('status', '!=', 5)
            // ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            // ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();

            $query = $query
            ->where('status', '!=', 5)
            ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount',
                'ConsignerDetail:regionalclient_id,id,nick_name,city,postal_code,district,state_id',
                'ConsignerDetail.GetState:id,name',
                'ConsigneeDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ConsigneeDetail.GetState:id,name', 
                'ShiptoDetail:id,consigner_id,nick_name,city,postal_code,district,state_id',
                'ShiptoDetail.GetState:id,name',
                'VehicleDetail:id,regn_no', 
                'DriverDetail:id,name,fleet_id,phone', 
                'ConsignerDetail.GetRegClient:id,name,baseclient_id', 
                'ConsignerDetail.GetRegClient.BaseClient:id,client_name',
                'vehicletype:id,name')
            ->orderBy('id','DESC')->get();
        }elseif($authuser->role_id == 4){
            $query = $query->whereIn('user_id', [$authuser->id, $user->id])
            ->where('status', '!=', 5)
            ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();  

        }else{
            $query = $query->whereIn('branch_id', $cc)
            ->where('status', '!=', 5)
            ->whereBetween('consignment_date', [$_POST['first_date'], $_POST['last_date']])
            ->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->get();
        }
        $consignments = json_decode(json_encode($query), true);

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

    // =============================================================================//
    // ==========================Custom Mis Reports ================================//

    public function misreport()
    {
        $this->prefix = request()->route()->getPrefix();

        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id ==1)
        {
            $consignments = $query->where('consignment_notes.status', '!=', 5)->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->paginate(20);
        }elseif($authuser->role_id == 4){
            $consignments = $query->where('consignment_notes.status', '!=', 5)->where('user_id', $authuser->id)->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->paginate(20);                
        }else{
            $consignments = $query->where('consignment_notes.status', '!=', 5)->where('branch_id', $cc)->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient', 'ConsignerDetail.GetRegClient.BaseClient','vehicletype')->orderBy('id','DESC')->paginate(20);
        } 
        // $consignments = json_decode(json_encode($query), true);
        return view('consignments.custom-mis2', ['consignments' => $consignments, 'prefix' => $this->prefix]);
    }

    public function exportExcelmisreport2()
    {
       
        return Excel::download(new MisReportExport, 'mis report.csv');
    }
}
