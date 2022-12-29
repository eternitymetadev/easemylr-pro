<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Hrs;
use DB;
use Auth;
use App\Models\ConsignmentNote;
use Config;
use Session;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Driver;

class HubtoHubController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Secondary Reports";
        $this->segment = \Request::segment(2);
        $this->req_link = \Config::get('req_api_link.req');
    }

   public function hubtransportation()
   {
       $this->prefix = request()->route()->getPrefix();

       return view('hub-transportation.hub-transportation', ['prefix' => $this->prefix]);
   }

   public function hrsList()
   {
            $this->prefix = request()->route()->getPrefix();
            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $query = ConsignmentNote::query();

            $query = $query->where('h2h_check', 'h2h')->where('hrs_status',2)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail');

            if ($authuser->role_id == 1) {  
                $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }
            $consignments = $query->orderBy('id', 'DESC')->get();

            return view('hub-transportation.hrs-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title]);
   }

   public function createHrs(Request $request)
    {

        $consignmentId = $_POST['consignmentID'];
        $authuser = Auth::user();
        $cc = $authuser->branch_id;


        $hrsid = DB::table('hrs')->select('hrs_no')->latest('hrs_no')->first();
        $hrs_id = json_decode(json_encode($hrsid), true);
        if (empty($hrs_id) || $hrs_id == null) {
            $hrs_id_new = 4000101;
        } else {
                $hrs_id_new = $hrs_id['hrs_no'] + 1;
        }

        foreach($consignmentId as $consignment){
            $savehrs = Hrs::create(['hrs_no' => $hrs_id_new, 'consignment_id' => $consignment, 'branch_id' => $cc,'status' => 1]);

        }

        ConsignmentNote::whereIn('id', $consignmentId )->update(['hrs_status' => 1]);


        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function hrsSheet(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = Hrs::query();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query->with('ConsignmentDetail','VehicleDetail','DriverDetail')->whereIn('status', ['1', '0', '3'])
                ->groupBy('hrs_no');

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
                $query = $query
                    ->whereHas('ConsignmentDetail.ConsignerDetail.RegClient', function ($query) use ($baseclient) {
                        $query->whereIn('id', $regclient);
                    });
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('hrs_no', 'like', '%' . $search . '%');
                });
            }

            if ($request->peritem) {
                Session::put('peritem', $request->peritem);
            }

            $peritem = Session::get('peritem');
            if (!empty($peritem)) {
                $peritem = $peritem;
            } else {
                $peritem = Config::get('variable.PER_PAGE');
            }

            $hrssheets = $query->orderBy('id', 'DESC')->paginate($peritem);
            $hrssheets = $hrssheets->appends($request->query());

            $html = view('consignments.download-drs-list-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets,'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('ConsignmentDetail','VehicleDetail','DriverDetail')
            ->whereIn('status', ['1', '0', '3'])
            ->groupBy('hrs_no');

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
        $hrssheets = $query->orderBy('id', 'DESC')->paginate($peritem);
        $hrssheets = $hrssheets->appends($request->query());

        return view('hub-transportation.hrs-sheet', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes]);

    }
    public function view_saveHrsDetails(Request $request)
    {
        $id = $_GET['hrs_id'];
        
         $hrsview = Hrs::with('ConsignmentDetail')->where('hrs_no', $id)->get();
        //  ->whereHas('ConsignmentDetail', function ($query){
        //     $query->where('status', '1');
        // })
         
        $result = json_decode(json_encode($hrsview), true);
        echo'<pre>'; print_r($result); die;

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }
}
