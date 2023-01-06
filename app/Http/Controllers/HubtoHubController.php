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
use Response;

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

        $get_lr = ConsignmentNote::whereIn('id', $consignmentId)->first();
        $get_tobranch = $get_lr->to_branch_id;

        $total_boxes = ConsignmentNote::whereIn('id', $consignmentId)->get();
        $box = array();
        foreach($total_boxes as $totalbox){
            $box[] = $totalbox->total_quantity;
        }
        $boxes = array_sum($box);

        foreach($consignmentId as $consignment){
            $savehrs = Hrs::create(['hrs_no' => $hrs_id_new, 'consignment_id' => $consignment, 'branch_id' => $cc,'to_branch_id' => $get_tobranch, 'total_hrs_quantity' => $boxes,'status' => 1]);
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

            $html = view('transportation.download-drs-list-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets,'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

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

         $hrsview = Hrs::with('ConsignmentDetail.ConsigneeDetail')->where('hrs_no', $id)->get();
        //  ->whereHas('ConsignmentDetail', function ($query){
        //     $query->where('status', '1');
        // })
         
        $result = json_decode(json_encode($hrsview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }

    public function updateVehicleHrs(Request $request)
    {

        $consignerId = $request->transaction_id;
        $cc = explode(',', $consignerId);
        $addvechileNo = $request->vehicle_id;
        $adddriverId = $request->driver_id;
        $vehicleType = $request->vehicle_type;
        $transporterName = $request->transporter_name;
        $purchasePrice = $request->purchase_price;
        $hrs_id = $request->hrs_id;

        $transaction = DB::table('hrs')->where('hrs_no', $hrs_id)->update(['vehicle_id' => $addvechileNo, 'driver_id' => $adddriverId, 'vehicle_type_id' => $vehicleType, 'transporter_name' => $transporterName, 'purchase_price' => $purchasePrice]);
        

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function getHrsSheetDetails(Request $request)
    {
        $id = $_GET['hrs_id'];
        $query = Hrs::query();
        $query = $query->where('hrs_no', $id)
            ->with('ConsignmentDetail.ConsigneeDetail')
            ->get();
        $result = json_decode(json_encode($query), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    public function addmoreLrHrs(Request $request)
    {

           $this->prefix = request()->route()->getPrefix();
             $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $query = ConsignmentNote::query();

            $query = $query->where(['h2h_check'=>'h2h','hrs_status'=>"2"])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail');

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
       
   

        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        $response['lrlist'] = $consignments;
        $response['success'] = true;
        $response['messages'] = 'successfully';
        return Response::json($response);

    }

    public function createdLrHrs(Request $request)
    {
        
        $hrs_no = $_POST['hrs_no'];
        $consignmentId = $_POST['consignmentID'];
        $authuser = Auth::user();
        $cc = $authuser->branch_id;

        foreach($consignmentId as $consignment){
            $savehrs = Hrs::create(['hrs_no' => $hrs_no, 'consignment_id' => $consignment, 'branch_id' => $cc,'status' => 1]);

        }
        
        ConsignmentNote::whereIn('id', $consignmentId )->update(['hrs_status' => 1]);

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function incomingHrs(Request $request)
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
                $query = $query->whereIn('to_branch_id', $cc);
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

            $html = view('hub-transportation.incoming-hrs-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets,'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

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
            $query = $query->whereIn('to_branch_id', $cc);
        }
        $hrssheets = $query->orderBy('id', 'DESC')->paginate($peritem);
        $hrssheets = $hrssheets->appends($request->query());

        return view('hub-transportation.incoming-hrs', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes]);

    }

    public function viewLrHrs(Request $request)
    {
        $id = $_GET['hrs_id'];

         $hrsview = Hrs::with('ConsignmentDetail.ConsigneeDetail')->where('hrs_no', $id)->get();
        $result = json_decode(json_encode($hrsview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }
    public function recevingHrsDetails(Request $request)
    {
        $id = $_GET['hrs_id'];

         $hrsview = Hrs::with('ConsignmentDetail')->where('hrs_no', $id)->get();
        $result = json_decode(json_encode($hrsview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }

    public function updateRecevingDetails(Request $request)
    {
        try {
            DB::beginTransaction();
                $hrs_no = $request->hrs_id;
                $receive_box = $request->receive_quantity;
                $remarks = $request->remarks;
                $consignment = $request->lr_no;
                $lr_no = explode(',', $consignment);

                Hrs::where('hrs_no', $hrs_no)->update(['total_receive_quantity' => $receive_box, 'remarks' => $remarks, 'receving_status' => 2]);

                ConsignmentNote::whereIn('id', $lr_no)->update(['hrs_status' => 3]);
                
            $response['success'] = true;
            $response['success_message'] = "Added successfully";
            $response['error'] = false;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }
}
