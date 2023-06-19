<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Hrs;
use App\Models\HrsPaymentHistory;
use App\Models\HrsPaymentRequest;
use App\Models\Location;
use App\Models\LrRoute;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Vendor;
use Auth;
use Config;
use DB;
use Helper;
use Illuminate\Http\Request;
use Response;
use Session;
use Carbon\Carbon;
use DateTime;
use App\Models\Job;

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

        $query = $query->where('h2h_check', 'h2h')->where('hrs_status', 2)->whereNotIn('status', ['5', '0'])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereRaw("SUBSTRING_INDEX(route_branch_id, ',', -1) = ?", [$authuser->branch_id]); 
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

        $total_boxes = ConsignmentNote::whereIn('id', $consignmentId)->get();
        $box = array();
        foreach ($total_boxes as $totalbox) {
            $box[] = $totalbox->total_quantity;
        }
        $boxes = array_sum($box);
        // $get_lr = ConsignmentNote::whereIn('id', $consignmentId)->first();
        // $get_tobranch = $get_lr->to_branch_id;
        foreach ($consignmentId as $key => $value) {
            $get_route = LrRoute::where('lr_id', $value)->first();

            $get_previous_hub = Hrs::where('consignment_id', $value)->orderBy('id', 'desc')->first();
            $all_route = explode(',', $get_route->route);

            if (!empty($get_previous_hub)) {
                $current_branch = array_search($get_previous_hub->to_branch_id, $all_route);
                $next_branch = $all_route[$current_branch + 1];

                $savehrs = Hrs::create(['hrs_no' => $hrs_id_new, 'consignment_id' => $value, 'branch_id' => $get_previous_hub->to_branch_id, 'to_branch_id' => $next_branch, 'total_hrs_quantity' => $boxes, 'status' => 1]);
            } else {
                $current_branch = array_search($cc, $all_route);
                $next_branch = $all_route[$current_branch + 1];

                $savehrs = Hrs::create(['hrs_no' => $hrs_id_new, 'consignment_id' => $value, 'branch_id' => $cc, 'to_branch_id' => $next_branch, 'total_hrs_quantity' => $boxes, 'status' => 1]);
            }
        }

        // foreach ($consignmentId as $consignment) {
        //     $savehrs = Hrs::create(['hrs_no' => $hrs_id_new, 'consignment_id' => $consignment, 'branch_id' => $cc, 'to_branch_id' => $get_route, 'total_hrs_quantity' => $boxes, 'status' => 1]);
        // }

        ConsignmentNote::whereIn('id', $consignmentId)->update(['hrs_status' => 1]);

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

            $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')->whereIn('status', ['1', '0', '3'])
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

            $html = view('transportation.download-drs-list-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')
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
        //  echo'<pre>'; print_r($request->all()); die;
        $consignerId = $request->lr_id;
        $cc = explode(',', $consignerId);
        
        $addvechileNo = $request->vehicle_id;
        $adddriverId = $request->driver_id;
        $vehicleType = $request->vehicle_type;
        $transporterName = $request->transporter_name;
        $purchasePrice = $request->purchase_price;
        $hrs_id = $request->hrs_id;

        $transaction = DB::table('hrs')->where('hrs_no', $hrs_id)->update(['vehicle_id' => $addvechileNo, 'driver_id' => $adddriverId, 'vehicle_type_id' => $vehicleType, 'transporter_name' => $transporterName, 'purchase_price' => $purchasePrice]);

        $mytime = Carbon::now('Asia/Kolkata');
        $currentdate = $mytime->toDateTimeString();

        foreach ($cc as $c_id) {
            // =================== task assign
            $respons2 = array('consignment_id' => $c_id, 'status' => 'Hub Transfer', 'create_at' => $currentdate, 'type' => '2');

            $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $c_id)->orderBy('id','DESC')->first();
            $st = json_decode($lastjob->response_data);
            array_push($st, $respons2);
            $sts = json_encode($st);
           

            $start = Job::create(['consignment_id' => $c_id, 'response_data' => $sts, 'status' => 'Hub Transfer', 'type' => '2']);
            // ==== end started
        }

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

        $query = $query->where(['h2h_check' => 'h2h', 'hrs_status' => "2"])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereIn('fall_in', $cc);
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

        foreach ($consignmentId as $consignment) {
            $savehrs = Hrs::create(['hrs_no' => $hrs_no, 'consignment_id' => $consignment, 'branch_id' => $cc, 'status' => 1]);

        }

        ConsignmentNote::whereIn('id', $consignmentId)->update(['hrs_status' => 1]);

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

            $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')->whereIn('status', ['1', '0', '3'])
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

            $html = view('hub-transportation.incoming-hrs-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')
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
            $authuser = Auth::user();

            $hrs_no = $request->hrs_id;
            $receive_box = $request->receive_quantity;
            $remarks = $request->remarks;
            $consignment = $request->lr_no;
            $lr_no = explode(',', $consignment);

            foreach ($lr_no as $lr) {

                $check_route = LrRoute::where('lr_id', $lr)->first();
                $check_route_hub = explode(',', $check_route->route);

                $get_last_branch = end($check_route_hub);

                $get_last_route = ConsignmentNote::where('id', $lr)->first();
                $get_last_route_branch = $get_last_route->route_branch_id;
                $route_line = $get_last_route_branch . ',' . $authuser->branch_id;

                if ($authuser->branch_id == $get_last_branch) {
                    ConsignmentNote::where('id', $lr)->update(['hrs_status' => 3, 'route_branch_id' => $route_line]);
                } else {
                    $get_last_route = ConsignmentNote::where('id', $lr)->first();
                    $get_last_route_branch = $get_last_route->route_branch_id;
                    $route_line = $get_last_route_branch . ',' . $authuser->branch_id;

                    ConsignmentNote::where('id', $lr)->update(['route_branch_id' => $route_line,'hrs_status' => 2]);
                }

            }

            Hrs::where('hrs_no', $hrs_no)->update(['total_receive_quantity' => $receive_box, 'remarks' => $remarks, 'receving_status' => 2]);

            $mytime = Carbon::now('Asia/Kolkata');
        $currentdate = $mytime->toDateTimeString();

        foreach ($lr_no as $c_id) {
            // =================== task assign
            $respons2 = array('consignment_id' => $c_id, 'status' => 'Received Hub', 'create_at' => $currentdate, 'type' => '2');

            $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $c_id)->orderBy('id','DESC')->first();
            $st = json_decode($lastjob->response_data);
            array_push($st, $respons2);
            $sts = json_encode($st);
           

            $start = Job::create(['consignment_id' => $c_id, 'response_data' => $sts, 'status' => 'Received Hub', 'type' => '2']);
            // ==== end started

        }
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

    ////////////////////////
    public function printHrs(Request $request)
    {

        $id = $request->id;
        $transcationview = Hrs::with('ConsignmentDetail.ConsignerDetail.GetRegClient', 'ConsignmentDetail.consigneeDetail', 'ConsignmentItem', 'VehicleDetail', 'DriverDetail', 'Branch', 'ToBranch')
            ->whereHas('ConsignmentDetail', function ($q) {
                $q->where('status', '!=', 0);
            })
            ->where('hrs_no', $id)
            ->orderby('consignment_id', 'asc')->get();
        $simplyfy = json_decode(json_encode($transcationview), true);

        $total_quantity = 0;
        $total_weight = 0;
        foreach ($simplyfy as $total) {
            $total_quantity += $total['consignment_detail']['total_quantity'];
            $total_weight += $total['consignment_detail']['total_weight'];
        }

        $no_of_deliveries = count($simplyfy);
        $details = $simplyfy[0];
        $pay = public_path('assets/img/LOGO_Frowarders.jpg');

        $drsDate = date('d-m-Y', strtotime($details['created_at']));
        $html = '<html>
        <head>
        <title>Document</title>
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
          <style>
          table,
          th,
          td {
              border: 0px solid black;
              border-collapse: collapse;
              text-align: left;
          }
          .drs_t,
          .drs_r,
          .drs_d,
          .drs_h {
              border: 1px solid black;
              border-collapse: collapse;
              text-align: left;
          }
            @page { margin: 100px 25px; }
            header { position: fixed; top: -60px; left: 0px; right: 0px; height: 200px; }
            footer { position: fixed; bottom: -105px; left: 0px; right: 0px;  height: 100px; }
           /* p { page-break-after: always; }
            p:last-child { page-break-after: never; } */
            * {
                box-sizing: border-box;
              }


              .column {
                float: left;
                width: 14.33%;
                padding: 5px;
                height: auto;
              }


              .row:after {
                content: "";
                display: table;
                clear: both;
              }
              .dd{
                margin-left: 0px;
              }

          </style>
        </head>
        <body style="font-size:13px; font-family:Arial Helvetica,sans-serif;">
                    <header><div class="row" style="display:flex;">
                    <div class="column"  style="width: 493px;">
                        <h1 class="dd">Hub Run Sheet</h1>
                        <div  class="dd">
                        <table class="drs_t" style="width:100%">
                            <tr class="drs_r">
                                <th class="drs_h">HRS No. & Date</th>
                                <th class="drs_h">DRS-' . $details['hrs_no'] . '</th>
                                <th class="drs_h">Hrs Date</th>
                                <th class="drs_h">' . $drsDate . '</th>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">HRS Created</td>
                                <td class="drs_d">' . $details['branch']['name'] . '</td>
                                <td class="drs_d">Receiving</td>
                                <td class="drs_d">' . $details['to_branch']['name'] . '</td>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">No. of LRs</td>
                                <td class="drs_d">' . $no_of_deliveries . '</td>
                                <td class="drs_d">Vehicle No.</td>
                                <td class="drs_d">' . $details['vehicle_detail']['regn_no'] . '</td>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">Total Quantity</td>
                                <td class="drs_d">' . $total_quantity . '</td>
                                <td class="drs_d">Driver Name</td>
                                <td class="drs_d">' . @$details['driver_detail']['name'] . '</td>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">Total Weight</td>
                                <td class="drs_d">' . $total_weight . '</td>
                                <td class="drs_d">Driver No.</td>
                                <td class="drs_d">' . @$details['driver_detail']['phone'] . '</td>
                            </tr>
                        </table>
                    </div>

                    </div>
                     <div class="column" style="margin-left: 56px;">
                        <img src="' . $pay . '" class="imga" style = "width: 170px; height: 80px; margin-top:30px;">
                    </div>
                </div>
                <br>
                <div id="content"><div class="row" style="border: 1px solid black;">
                <div class="column" style="width:125px;">
                    <h4 style="margin: 0px;"> Bill to Client</h4>
                    <h4 style="margin: 0px;">LR Details:</h4>
                </div>
                <div class="column" style="width:200px;">
                    <h4 style="margin: 0px;">Consignee Name & Mobile Number</h4>
                </div>
                <div class="column" style="width:125px;">
                    <h4 style="margin: 0px;">Delivery City, </h4>
                    <h4 style="margin: 0px;"> Dstt & PIN</h4>

                    </div>
                    <div class="column">
                    <h4 style="margin: 0px;">Shipment Details</h4>
                    </div>
                    <div class="column" style="width:170px;">
                    <h4 style="margin: 0px; ">Stamp & Signature of Receiver</h4>
                    </div>
                </div>
                </div>
                </header>
                    <footer><div class="row">
                    <div class="col-sm-12" style="margin-left: 0px;">
                        <p>Head Office:Forwarders private Limited</p>
                        <p style="margin-top:-13px;">Add:Plot No.B-014/03712,prabhat,Zirakpur-140603</p>
                        <p style="margin-top:-13px;">Phone:07126645510 email:contact@eternityforwarders.com</p>
                    </div>
                </div></footer>
                    <main style="margin-top:190px;">';
        $i = 0;
        $total_Boxes = 0;
        $total_weight = 0;

        foreach ($simplyfy as $dataitem) {
            //    echo'<pre>'; print_r($dataitem); die;

            $i++;
            if ($i % 5 == 0) {
                $html .= '<div style="page-break-before: always; margin-top:180px;"></div>';
            }

            $total_Boxes += $dataitem['consignment_detail']['total_quantity'];
            $total_weight += $dataitem['consignment_detail']['total_weight'];
            //echo'<pre>'; print_r($dataitem['consignment_no']); die;
            $html .= '
                <div class="row" style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; margin-bottom: -10px;">

                    <div class="column" style="width:125px;">
                       <p style="margin-top:0px;">' . $dataitem['consignment_detail']['consigner_detail']['get_reg_client']['name'] . '</p>
                        <p style="margin-top:-8px;">' . $dataitem['consignment_id'] . '</p>
                        <p style="margin-top:-13px;">' . Helper::ShowDayMonthYear($dataitem['consignment_detail']['consignment_date']) . '</p>
                    </div>
                    <div class="column" style="width:200px;">
                        <p style="margin-top:0px;">' . $dataitem['consignment_detail']['consignee_detail']['nick_name'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['consignment_detail']['consignee_detail']['phone'] . '</p>

                    </div>
                    <div class="column" style="width:125px;">
                        <p style="margin-top:0px;">' . $dataitem['consignment_detail']['consignee_detail']['city'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['consignment_detail']['consignee_detail']['district'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['consignment_detail']['consignee_detail']['postal_code'] . '</p>

                      </div>
                      <div class="column" >
                        <p style="margin-top:0px;">Boxes:' . $dataitem['consignment_detail']['total_quantity'] . '</p>
                        <p style="margin-top:-13px;">Wt:' . $dataitem['consignment_detail']['total_gross_weight'] . '</p>
                        <p style="margin-top:-13px;">EDD:' . Helper::ShowDayMonthYear($dataitem['consignment_detail']['edd']) . '</p>
                      </div>
                      <div class="column" style="width:170px;">
                        <p></p>
                      </div>
                  </div>';
            $html .= '<div class="row" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; margin-top: 0px;">';
            //echo'<pre>'; print_r($chunk); die;
            $html .= ' <div class="column" style="width:230px; margin-top: -10px;">';
            $html .= '<table class="neworder" style="margin-top: -10px;"><tr style="border:0px;"><td style="width: 190px; padding:6px;"><span style="font-weight: bold;">Order ID</span></td><td style="width: 190px;"><span style="font-weight: bold;">Invoice No</span></td></tr></table>';
            $itm_no = 0;
            foreach ($dataitem['consignment_item'] as $cc) {
                $itm_no++;

                $html .= '  <table style="border:0; margin-top: -7px;"><tr><td style="width: 190px; padding:3px;">' . $itm_no . '.  ' . $cc['order_id'] . '</td><td style="width: 190px; padding:3px;">' . $itm_no . '.  ' . $cc['invoice_no'] . '</td></tr></table>';

            }
            $html .= '</div> ';

            $html .= '</div>

                <br>';

        }

        $html .= '</main>
        </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('print.pdf');

    }

    // ============================   HRS PAYMENT ========================= //
    public function hrsPaymentList(Request $request)
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

            $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')->whereIn('status', ['1', '0', '3'])
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

            $html = view('transportation.download-drs-list-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')
            ->where('request_status', 0)
            ->where('payment_status', '=', 0)
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
        $vendors = Vendor::with('Branch')->get();
        $vehicletype = VehicleType::select('id', 'name')->get();

        return view('hub-transportation.hrs-payment-list', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletype' => $vehicletype, 'branchs' => $branchs, 'vendors' => $vendors]);

    }
    /////////////////////// hrs payment list page ///////////////////////////
    public function viewhrsLr(Request $request)
    {

        $id = $_GET['hrs_lr'];

        $transcationview = Hrs::select('*')->with('ConsignmentDetail.ConsigneeDetail', 'ConsignmentItem')->where('hrs_no', $id)
        // ->whereHas('ConsignmentDetail', function ($query) {
        //     $query->where('status', '1');
        // })
            ->orderby('id', 'asc')->get();
        $result = json_decode(json_encode($transcationview), true);
        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data fetch successfully";
        echo json_encode($response);

    }

    ////
    public function getHrsdetails(Request $request)
    {
        $hrs_statsu = Hrs::with('ConsignmentDetail')->whereIn('hrs_no', $request->hrs_no)->first();

        $response['get_data'] = $hrs_statsu;
        $response['success'] = true;
        $response['error_message'] = "find data";
        return response()->json($response);
    }

    ////
    public function outgoingHrs(Request $request)
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

            $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')->whereIn('status', ['1', '0', '3'])
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

            $html = view('hub-transportation.outgoing-hrs-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')
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

        return view('hub-transportation.outgoing-hrs', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes]);

    }
    // ==================CreatePayment Request =================
    public function createHrsPayment(Request $request)
    {

        $this->prefix = request()->route()->getPrefix();
        $url_header = $_SERVER['HTTP_HOST'];

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = $authuser->branch_id;
        $user = $authuser->id;
        $bm_email = $authuser->email;

        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        $deduct_balance = $request->pay_amt - $request->final_payable_amount;

        $hrsno = explode(',', $request->hrs_no);
        $consignment = Hrs::with('VehicleDetail')->whereIn('hrs_no', $hrsno)
            ->groupby('hrs_no')
            ->get();
        $simplyfy = json_decode(json_encode($consignment), true);
        $transactionId = DB::table('hrs_payment_requests')->select('transaction_id')->latest('transaction_id')->first();
        $transaction_id = json_decode(json_encode($transactionId), true);
        if (empty($transaction_id) || $transaction_id == null) {
            $transaction_id_new = 80000101;
        } else {
            $transaction_id_new = $transaction_id['transaction_id'] + 1;
        }

        // ============ Check Request Permission =================
        if ($authuser->is_payment == 0) {
            $i = 0;
            $sent_vehicle = array();
            foreach ($simplyfy as $value) {

                $i++;
                $hrs_no = $value['hrs_no'];
                $vendor_id = @$request->vendor_name;
                $vehicle_no = @$value['vehicle_detail']['regn_no'];
                $sent_vehicle[] = @$value['vehicle_detail']['regn_no'];

                if ($request->p_type == 'Advance') {
                    $balance_amt = $request->claimed_amount - $request->pay_amt;

                    $transaction = HrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'hrs_no' => $hrs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 2, 'is_approve' => 0, 'status' => '1']);

                    Hrs::whereIn('hrs_no', $hrsno)->update(['payment_status' => 2]);
                } else {
                    $getadvanced = HrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->pay_amt;
                    } else {
                        $balance = 0;
                    }
                    $advance = $request->pay_amt;
                    // dd($advance);

                    $transaction = HrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'hrs_no' => $hrs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 2, 'is_approve' => 0, 'status' => '1']);
                    Hrs::whereIn('hrs_no', $hrsno)->update(['payment_status' => 2]);

                }

            }

            $unique = array_unique($sent_vehicle);
            $sent_venicle_no = implode(',', $unique);
            Hrs::whereIn('hrs_no', $hrsno)->update(['request_status' => '1']);

            $url = $this->prefix . '/hrs-request-list';
            $new_response['success'] = true;
            $new_response['redirect_url'] = $url;
            $new_response['success_message'] = "Data Imported successfully";

        } else {
            $i = 0;
            $sent_vehicle = array();
            foreach ($simplyfy as $value) {

                $i++;
                $hrs_no = $value['hrs_no'];
                $vendor_id = $request->vendor_name;
                $vehicle_no = $value['vehicle_detail']['regn_no'];
                $sent_vehicle[] = $value['vehicle_detail']['regn_no'];

                if ($request->p_type == 'Advance') {
                    $balance_amt = $request->claimed_amount - $request->pay_amt;

                    $transaction = HrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'hrs_no' => $hrs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 0, 'is_approve' => 1, 'status' => '1']);

                } else {
                    $getadvanced = HrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->pay_amt;
                    } else {
                        $balance = 0;
                    }
                    $advance = $request->pay_amt;
                    // dd($advance);

                    $transaction = HrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'hrs_no' => $hrs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 0, 'is_approve' => 1, 'status' => '1']);
                }

            }

            $unique = array_unique($sent_vehicle);
            $sent_venicle_no = implode(',', $unique);
            Hrs::whereIn('hrs_no', $hrsno)->update(['request_status' => '1']);

            $checkduplicateRequest = HrsPaymentHistory::where('transaction_id', $transaction_id_new)->where('payment_status', 2)->first();
            if (empty($checkduplicateRequest)) {
                // ============== Sent to finfect
                $pfu = 'ETF';
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $this->req_link,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => "[{
                \"unique_code\": \"$request->vendor_no\",
                \"name\": \"$request->v_name\",
                \"acc_no\": \"$request->acc_no\",
                \"beneficiary_name\": \"$request->beneficiary_name\",
                \"ifsc\": \"$request->ifsc\",
                \"bank_name\": \"$request->bank_name\",
                \"baddress\": \"$request->branch_name\",
                \"payable_amount\": \"$request->final_payable_amount\",
                \"claimed_amount\": \"$request->claimed_amount\",
                \"pfu\": \"$pfu\",
                \"ptype\": \"$request->p_type\",
                \"email\": \"$bm_email\",
                \"terid\": \"$transaction_id_new\",
                \"branch\": \"$branch_name->name\",
                \"vehicle\": \"$sent_venicle_no\",
                \"pan\": \"$request->pan\",
                \"amt_deducted\": \"$deduct_balance\",
                \"txn_route\": \"HRS\"
                }]",
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Access-Control-Request-Headers:' . $url_header,

                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $res_data = json_decode($response);
                // ============== Success Response
                if ($res_data->message == 'success') {

                    if ($request->p_type == 'Fully') {
                        $getadvanced = HrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                        if (!empty($getadvanced->balance)) {
                            $balance = $getadvanced->balance - $request->pay_amt;
                        } else {
                            $balance = 0;
                        }
                        $advance = $request->pay_amt;

                        Hrs::whereIn('hrs_no', $hrsno)->update(['payment_status' => 2]);

                        HrsPaymentRequest::where('transaction_id', $transaction_id_new)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'payment_status' => 2]);

                        $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                        $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                        $paymentresponse['transaction_id'] = $transaction_id_new;
                        $paymentresponse['hrs_no'] = $request->hrs_no;
                        $paymentresponse['bank_details'] = json_encode($bankdetails);
                        $paymentresponse['purchase_amount'] = $request->claimed_amount;
                        $paymentresponse['payment_type'] = $request->p_type;
                        $paymentresponse['advance'] = $advance;
                        $paymentresponse['balance'] = $balance;
                        $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                        $paymentresponse['current_paid_amt'] = $request->pay_amt;
                        $paymentresponse['payment_status'] = 2;

                        $paymentresponse = HrsPaymentHistory::create($paymentresponse);

                    } else {

                        $balance_amt = $request->claimed_amount - $request->pay_amt;
                        //======== Payment History save =========//
                        $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                        $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                        $paymentresponse['transaction_id'] = $transaction_id_new;
                        $paymentresponse['hrs_no'] = $request->hrs_no;
                        $paymentresponse['bank_details'] = json_encode($bankdetails);
                        $paymentresponse['purchase_amount'] = $request->claimed_amount;
                        $paymentresponse['payment_type'] = $request->p_type;
                        $paymentresponse['advance'] = $request->pay_amt;
                        $paymentresponse['balance'] = $balance_amt;
                        $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                        $paymentresponse['current_paid_amt'] = $request->pay_amt;
                        $paymentresponse['payment_status'] = 2;

                        $paymentresponse = HrsPaymentHistory::create($paymentresponse);
                        HrsPaymentRequest::where('transaction_id', $transaction_id_new)->update(['payment_type' => $request->p_type, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'payment_status' => 2]);

                        Hrs::whereIn('hrs_no', $hrsno)->update(['payment_status' => 2]);
                    }

                    $new_response['success'] = true;
                    $new_response['message'] = $res_data->message;

                } else {

                    $new_response['message'] = $res_data->message;
                    $new_response['success'] = false;

                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    //$paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $transaction_id_new;
                    $paymentresponse['hrs_no'] = $request->hrs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->pay_amt;
                    $paymentresponse['payment_status'] = 4;

                    $paymentresponse = HrsPaymentHistory::create($paymentresponse);

                }
                $url = $this->prefix . '/hrs-request-list';
                $new_response['redirect_url'] = $url;
                $new_response['success_message'] = "Request Sent Successfully";
            } else {
                $new_response['error'] = false;
                $new_response['success_message'] = "Request Already Sent";
            }

        }

        return response()->json($new_response);

    }

    ///////////
    public function hrsRequestList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = HrsPaymentRequest::query();
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

            $query = $query->with('ConsignmentDetail', 'VehicleDetail', 'DriverDetail')->whereIn('status', ['1', '0', '3'])
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

            $html = view('transportation.download-drs-list-ajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrssheets' => $hrssheets, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('Branch', 'User')
            ->whereIn('status', ['1', '0', '3'])
            ->groupBy('transaction_id');

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
        } elseif ($authuser->role_id == 3) {
            $query = $query->where('rm_id', $authuser->id);
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }
        $hrsRequests = $query->orderBy('id', 'DESC')->paginate($peritem);
        $hrsRequests = $hrsRequests->appends($request->query());
        $vendors = Vendor::with('Branch')->get();
        $vehicletype = VehicleType::select('id', 'name')->get();

        return view('hub-transportation.hrs-request-list', ['peritem' => $peritem, 'prefix' => $this->prefix, 'hrsRequests' => $hrsRequests, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes, 'branchs' => $branchs, 'vendors' => $vendors, 'vehicletype' => $vehicletype]);

    }

    public function getVendorReqDetailsHrs(Request $request)
    {
        $req_data = HrsPaymentRequest::with('VendorDetails')->where('transaction_id', $request->transaction_id)
            ->groupBy('transaction_id')->get();

        $gethrs = HrsPaymentRequest::select('hrs_no')->where('transaction_id', $request->transaction_id)
            ->get();
        $simply = json_decode(json_encode($gethrs), true);
        foreach ($simply as $value) {
            $store[] = $value['hrs_no'];
        }
        $hrs_no = implode(',', $store);

        $response['req_data'] = $req_data;
        $response['hrs_no'] = $hrs_no;
        $response['success'] = true;
        $response['success_message'] = "Approver successfully";
        return response()->json($response);

    }

    // ============ Rm Approver Pusher =========================
    public function rmApproverRequest(Request $request)
    {

        $authuser = User::where('id', $request->user_id)->first();
        $bm_email = $authuser->email;
        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        //deduct balance
        // $deduct_balance = $request->payable_amount - $request->final_payable_amount;

        if ($request->hrsAction == 1) {
            $get_vehicle = HrsPaymentRequest::select('vehicle_no')->where('transaction_id', $request->transaction_id)->get();
            $sent_vehicle = array();
            foreach ($get_vehicle as $vehicle) {
                $sent_vehicle[] = $vehicle->vehicle_no;
            }
            $unique = array_unique($sent_vehicle);
            $sent_vehicle_no = implode(',', $unique);

            $url_header = $_SERVER['HTTP_HOST'];
            $drs = explode(',', $request->drs_no);
            $pfu = 'ETF';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->req_link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => "[{
            \"unique_code\": \"$request->vendor_no\",
            \"name\": \"$request->name\",
            \"acc_no\": \"$request->acc_no\",
            \"beneficiary_name\": \"$request->beneficiary_name\",
            \"ifsc\": \"$request->ifsc\",
            \"bank_name\": \"$request->bank_name\",
            \"baddress\": \"$request->branch_name\",
            \"payable_amount\": \"$request->final_payable_amount\",
            \"claimed_amount\": \"$request->claimed_amount\",
            \"pfu\": \"$pfu\",
            \"ptype\": \"$request->p_type\",
            \"email\": \"$bm_email\",
            \"terid\": \"$request->transaction_id\",
            \"branch\": \"$branch_name->name\",
            \"pan\": \"$request->pan\",
            \"amt_deducted\": \"$request->amt_deducted\",
            \"vehicle\": \"$sent_vehicle_no\",
            \"txn_route\": \"HRS\"
            }]",
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Access-Control-Request-Headers:' . $url_header,
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $res_data = json_decode($response);
            // $cc = 'success';
            // ============== Success Response
            if ($res_data->message == 'success') {

                if ($request->p_type == 'Balance' || $request->p_type == 'Fully') {

                    $getadvanced = HrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->payable_amount;
                    } else {
                        $balance = 0;
                    }
                    $advance = $getadvanced->advanced;

                    HrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_status' => 2, 'is_approve' => 1]);

                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['hrs_no'] = $request->hrs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $advance;
                    $paymentresponse['balance'] = $balance;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = HrsPaymentHistory::create($paymentresponse);

                } else {

                    $balance_amt = $request->claimed_amount - $request->payable_amount;
                    //======== Payment History save =========//
                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['hrs_no'] = $request->hrs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $request->payable_amount;
                    $paymentresponse['balance'] = $balance_amt;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = HrsPaymentHistory::create($paymentresponse);

                    HrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_status' => 2, 'is_approve' => 1]);
                }

                $new_response['success'] = true;
                $new_response['message'] = $res_data->message;

            } else {
                $new_response['message'] = $res_data->message;
                $new_response['success'] = false;
            }

        } else {
            // ============ Request Rejected ================= //
            $hrs_num = explode(',', $request->hrs_no);
            HrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['rejected_remarks' => $request->rejectedRemarks, 'payment_status' => 4]);

            Hrs::whereIn('hrs_no', $hrs_num)->update(['payment_status' => 0, 'request_status' => 0]);

            $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

            $paymentresponse['transaction_id'] = $request->transaction_id;
            $paymentresponse['hrs_no'] = $request->hrs_no;
            $paymentresponse['bank_details'] = json_encode($bankdetails);
            $paymentresponse['purchase_amount'] = $request->claimed_amount;
            $paymentresponse['payment_type'] = $request->p_type;
            $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
            $paymentresponse['current_paid_amt'] = $request->payable_amount;
            $paymentresponse['payment_status'] = 4;

            $paymentresponse = HrsPaymentHistory::create($paymentresponse);

            $new_response['message'] = 'Request Rejected';
            $new_response['success'] = true;

        }

        return response()->json($new_response);

    }
    ////
    public function updatePurchasePriceHrs(Request $request)
    {
        try {
            DB::beginTransaction();

            Hrs::where('hrs_no', $request->hrs_no)->update(['purchase_price' => $request->purchase_price, 'vehicle_type_id' => $request->vehicle_type]);

            $response['success'] = true;
            $response['success_message'] = "Price Added successfully";
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
    ///////////
    public function getSecondPaymentDetails(Request $request)
    {

        $req_data = HrsPaymentRequest::with('VendorDetails')->where('transaction_id', $request->trans_id)
            ->groupBy('transaction_id')->get();

        $getdrs = HrsPaymentRequest::select('hrs_no')->where('transaction_id', $request->trans_id)
            ->get();
        $simply = json_decode(json_encode($getdrs), true);
        foreach ($simply as $value) {
            $store[] = $value['hrs_no'];
        }
        $hrs_no = implode(',', $store);

        $response['req_data'] = $req_data;
        $response['hrs_no'] = $hrs_no;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    //////////////////////
    public function createSecondPaymentRequest(Request $request)
    {
        // echo'<pre>'; print_r($request->all()); die;
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = $authuser->branch_id;
        $user = $authuser->id;
        $bm_email = $authuser->email;
        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        //deduct balance
        $deduct_balance = $request->payable_amount - $request->final_payable_amount;

        $get_vehicle = HrsPaymentRequest::select('vehicle_no')->where('transaction_id', $request->transaction_id)->get();
        $sent_vehicle = array();
        foreach ($get_vehicle as $vehicle) {
            $sent_vehicle[] = $vehicle->vehicle_no;
        }
        $unique = array_unique($sent_vehicle);
        $sent_vehicle_no = implode(',', $unique);
        $url_header = $_SERVER['HTTP_HOST'];
        $hrs = explode(',', $request->hrs_no);
        if ($authuser->is_payment == 0) {

            $getadvanced = HrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
            if (!empty($getadvanced->balance)) {
                $balance = $getadvanced->balance - $request->payable_amount;
            } else {
                $balance = 0;
            }
            $advance = $getadvanced->advanced + $request->payable_amount;

            Hrs::whereIn('hrs_no', $hrs)->update(['payment_status' => 2]);

            HrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance' => $deduct_balance, 'current_paid_amt' => $request->final_payable_amount, 'payment_status' => 2, 'is_approve' => 0]);

            $new_response['success'] = true;

        } else {

            $pfu = 'ETF';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->req_link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => "[{
            \"unique_code\": \"$request->vendor_no\",
            \"name\": \"$request->name\",
            \"acc_no\": \"$request->acc_no\",
            \"beneficiary_name\": \"$request->beneficiary_name\",
            \"ifsc\": \"$request->ifsc\",
            \"bank_name\": \"$request->bank_name\",
            \"baddress\": \"$request->branch_name\",
            \"payable_amount\": \"$request->final_payable_amount\",
            \"claimed_amount\": \"$request->claimed_amount\",
            \"pfu\": \"$pfu\",
            \"ptype\": \"$request->p_type\",
            \"email\": \"$bm_email\",
            \"terid\": \"$request->transaction_id\",
            \"branch\": \"$branch_name->name\",
            \"pan\": \"$request->pan\",
            \"amt_deducted\": \"$deduct_balance\",
            \"vehicle\": \"$sent_vehicle_no\",
            \"txn_route\": \"HRS\"
            }]",
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Access-Control-Request-Headers:' . $url_header,
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $res_data = json_decode($response);
            // $cc = 'success';
            // ============== Success Response
            if ($res_data->message == 'success') {

                if ($request->p_type == 'Balance' || $request->p_type == 'Fully') {

                    $getadvanced = HrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->payable_amount;
                    } else {
                        $balance = 0;
                    }
                    $advance = $getadvanced->advanced + $request->payable_amount;

                    Hrs::whereIn('hrs_no', $hrs)->update(['payment_status' => 2]);

                    HrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance' => $deduct_balance, 'current_paid_amt' => $request->final_payable_amount, 'payment_status' => 2, 'is_approve' => 1]);

                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['hrs_no'] = $request->hrs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $advance;
                    $paymentresponse['balance'] = $balance;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = HrsPaymentHistory::create($paymentresponse);

                } else {

                    $balance_amt = $request->claimed_amount - $request->payable_amount;
                    //======== Payment History save =========//
                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['hrs_no'] = $request->hrs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $request->payable_amount;
                    $paymentresponse['balance'] = $balance_amt;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = HrsPaymentHistory::create($paymentresponse);

                    HrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $request->payable_amount, 'balance' => $balance_amt, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance' => $deduct_balance, 'current_paid_amt' => $request->final_payable_amount, 'payment_status' => 2]);

                    Hrs::whereIn('hrs_no', $drs)->update(['payment_status' => 2]);
                }

                $new_response['success'] = true;
                $new_response['message'] = $res_data->message;

            } else {

                $new_response['message'] = $res_data->message;
                $new_response['success'] = false;

            }
        }
        return response()->json($new_response);
    }

    public function showHrs(Request $request)
    {
        $gethrs = HrsPaymentRequest::select('hrs_no')->where('transaction_id', $request->trans_id)->get();

        $response['gethrs'] = $gethrs;
        $response['success'] = true;
        $response['success_message'] = "HRS Fetched successfully";
        return response()->json($response);
    }
    /////////////////
    public function editPurchasePriceHrs(Request $request)
    {

        $hrs_price = Hrs::with('ConsignmentDetail')->where('hrs_no', $request->hrs_no)->first();
        $vehicletype = VehicleType::select('id', 'name')->get();

        $response['hrs_price'] = $hrs_price;
        $response['vehicletype'] = $vehicletype;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);
    }
    public function updatePurchasePriceVehicleTypeHrs(Request $request)
    {
        try {
            DB::beginTransaction();

            Hrs::where('hrs_no', $request->hrs_no)->update(['purchase_price' => $request->purchase_price, 'vehicle_type_id' => $request->vehicle_type]);

            $response['success'] = true;
            $response['success_message'] = "Price Added successfully";
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

    public function removeHrs(Request $request)
    {
        //
        $consignmentId = $_GET['consignment_id'];
        $consigner = DB::table('consignment_notes')->where('id', $consignmentId)->update(['hrs_status' => '2']);
        $transac = DB::table('hrs')->where('consignment_id', $consignmentId)->delete();

        $response['success'] = true;
        $response['success_message'] = "hrs remove successful";
        echo json_encode($response);
    }

}
