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
use Helper;

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

    ////////////////////////
    public function printHrs(Request $request)
    {
        
        $id = $request->id;
        $transcationview = Hrs::with('ConsignmentDetail.ConsignerDetail.GetRegClient', 'ConsignmentDetail.consigneeDetail', 'ConsignmentItem','VehicleDetail','DriverDetail','Branch','ToBranch')
            ->whereHas('ConsignmentDetail', function ($q) {
                $q->where('status', '!=', 0);
            })
            ->where('hrs_no', $id)
            ->orderby('consignment_id', 'asc')->get();
        $simplyfy = json_decode(json_encode($transcationview), true);

        $total_quantity = 0;
        $total_weight = 0;
        foreach($simplyfy as $total)
        {
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
}
