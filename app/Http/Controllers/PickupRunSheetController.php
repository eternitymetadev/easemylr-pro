<?php

namespace App\Http\Controllers;

use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentSubItem;
use App\Models\Driver;
use App\Models\Location;
use App\Models\PickupRunSheet;
use App\Models\PrsDrivertask;
use App\Models\PrsPaymentHistory;
use App\Models\PrsPaymentRequest;
use App\Models\PrsReceiveVehicle;
use App\Models\PrsRegClient;
use App\Models\PrsTaskItem;
use App\Models\RegionalClient;
use App\Models\BranchAddress;
use App\Models\Role;
use App\Models\User;
use App\Models\Hrs;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Vendor;
use App\Models\Job;
use App\Exports\PrsExport;
use App\Exports\PickupLoadExport;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Storage;
use URL;
use Helper;
use Validator;
use QrCode;
use DateTime;

class PickupRunSheetController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "PRS";
        $this->segment = \Request::segment(2);
        $this->req_link = \Config::get('req_api_link.req');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PickupRunSheet::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $cc = explode(',', $authuser->branch_id);

            $query = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner', 'VehicleDetail', 'DriverDetail');

            if ($authuser->role_id != 1) {
                $query = $query->whereIn('branch_id', $cc);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('pickup_id', 'like', '%' . $search . '%')
                        ->orWhereHas('PrsRegClients.RegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('PrsRegClients.RegConsigner.Consigner', function ($query) use ($search, $searchT) {
                            $query->where(function ($cnrquery) use ($search, $searchT) {
                                $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('DriverDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($driverquery) use ($search, $searchT) {
                                $driverquery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('phone', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('VehicleDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($vehiclequery) use ($search, $searchT) {
                                $vehiclequery->where('regn_no', 'like', '%' . $search . '%');
                            });
                        });

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

            $prsdata = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner', 'VehicleDetail', 'DriverDetail')->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html = view('prs.prs-list-ajax', ['prefix' => $this->prefix, 'prsdata' => $prsdata, 'peritem' => $peritem, 'segment' => $this->segment])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner', 'VehicleDetail', 'DriverDetail');

        if ($authuser->role_id == 1) {
            $query;
        }
        //  elseif ($authuser->role_id == 4) {
        //     $query = $query->whereIn('regclient_id', $regclient);
        // } elseif ($authuser->role_id == 7) {
        //     $query = $query->whereIn('regclient_id', $regclient);
        // }
        else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $prsdata = $query->orderBy('id', 'DESC')->paginate($peritem);
        $prsdata = $prsdata->appends($request->query());

        return view('prs.prs-list', ['prsdata' => $prsdata, 'peritem' => $peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        // if($authuser->role_id !=1){
        //     if($authuser->role_id ==2 || $role_id->id ==3){
        //         $regclients = RegionalClient::whereIn('location_id',$cc)->orderby('name','ASC')->get();
        //         $consigners = Consigner::whereIn('branch_id',$cc)->orderby('nick_name','ASC')->pluck('nick_name','id');
        //     }else{
        //         $regclients = RegionalClient::whereIn('id',$regclient)->orderby('name','ASC')->get();
        //         $consigners = Consigner::whereIn('regionalclient_id',$regclient)->orderby('nick_name','ASC')->pluck('nick_name','id');
        //     }
        // }else{
        $regclients = RegionalClient::where('status', 1)->orderby('name', 'ASC')->get();
        $consigners = Consigner::where('status', 1)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        // }
        
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        $locations = Location::select('id', 'name')->get();
        $hub_locations = Location::where('is_hub', '1')->select('id', 'name')->get();
        
        return view('prs.create-prs', ['prefix' => $this->prefix, 'regclients' => $regclients, 'locations' => $locations, 'hub_locations' => $hub_locations, 'consigners' => $consigners, 'vehicletypes' => $vehicletypes, 'vehicles' => $vehicles, 'drivers' => $drivers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'regclient_id' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }
            $authuser = Auth::user();
            $pickup_id = DB::table('pickup_run_sheets')->select('pickup_id')->latest('pickup_id')->first();
            $pickup_id = json_decode(json_encode($pickup_id), true);
            if (empty($pickup_id) || $pickup_id == null) {
                $pickup_id = 2900001;
            } else {
                $pickup_id = $pickup_id['pickup_id'] + 1;
            }

            $prssave['pickup_id'] = $pickup_id;
            if (!empty($request->vehicletype_id)) {
                $prssave['vehicletype_id'] = $request->vehicletype_id;
            }
            if (!empty($request->vehicle_id)) {
                $prssave['vehicle_id'] = $request->vehicle_id;
            }
            if (!empty($request->driver_id)) {
                $prssave['driver_id'] = $request->driver_id;
            }

            $prssave['prs_date'] = $request->prs_date;
            $prssave['location_id'] = $request->location_id;
            $prssave['hub_location_id'] = $request->hub_location_id;
            $prssave['user_id'] = $authuser->id;
            $prssave['branch_id'] = $authuser->branch_id;
            $prssave['status'] = "1";

            $saveprs = PickupRunSheet::create($prssave);
            if ($saveprs) {
                foreach ($request->data as $key => $save_data) {
                    $regclientsave['prs_id'] = $saveprs->id;
                    $regclientsave['regclient_id'] = $save_data['regclient_id'];
                    $regclientsave['status'] = "1";

                    $saveregclient = PrsRegClient::create($regclientsave);
                    if ($saveregclient) {
                        $data = array();
                        foreach ($save_data['consigner_id'] as $cnr_data) {
                            $data[] = [
                                'prs_regclientid' => $saveregclient->id,
                                'consigner_id' => $cnr_data,
                                'status' => '1',
                            ];
                        }
                        if ($data) {
                            $task_id = DB::table('prs_drivertasks')->select('task_id')->latest('task_id')->first();
                            if (empty($task_id) || $task_id == null) {
                                $task_id = 3800001;
                            } else {
                                $task_id = ($task_id->task_id) + 1;
                            }
                            foreach ($data as $cnr) {
                                $prstask['task_id'] = $task_id;
                                $prstask['prs_date'] = $saveprs->prs_date;
                                $prstask['prs_id'] = $saveprs->id;
                                $prstask['prsconsigner_id'] = $cnr['consigner_id'];
                                $prstask['status'] = "1";

                                $savedrivertask = PrsDrivertask::create($prstask);
                                $task_id = $savedrivertask['task_id'] + 1;
                            }
                        }
                        $saveregcnr = $saveregclient->RegConsigner()->insert($data);
                    }
                }
                $url = URL::to($this->prefix . '/prs');
                $response['success'] = true;
                $response['success_message'] = "PRS Added successfully";
                $response['error'] = false;
                $response['page'] = 'prs-create';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not created PRS please try again";
                $response['error'] = true;
            }

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    public function driverTasks(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PrsDrivertask::query();

        if ($request->ajax()) {
            if (isset($request->prsdrivertask_status)) {
                if ($request->prs_taskstatus == 1) {
                    // update click on assigned status to acknowleged in driver task list
                    PrsDrivertask::where('id', $request->id)->update(['status' => '2']);
                } else {
                    // update on statuschange action btn in driver task list
                    PrsDrivertask::where('id', $request->id)->update(['status' => '4']);
                }

                $url = $this->prefix . '/driver-tasks';
                $response['success'] = true;
                $response['success_message'] = "Driver task status updated successfully";
                $response['error'] = false;
                $response['page'] = 'drivertsak-update';
                $response['redirect_url'] = $url;

                return response()->json($response);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);

            $query = $query->with('ConsignerDetail:id,nick_name,city');

            if ($authuser->role_id == 1) {
                $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->whereHas('PickupRunSheet', function ($query) use ($cc) {
                    $query->whereIn('branch_id', $cc);
                });
            }

            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('task_id', 'like', '%' . $search . '%')
                        ->orWhereHas('PickupId', function ($regclientquery) use ($search) {
                            $regclientquery->where('pickup_id', 'like', '%' . $search . '%')
                                ->orWhereHas('DriverDetail', function ($query) use ($search) {
                                    $query->where(function ($driverquery) use ($search) {
                                        $driverquery->where('name', 'like', '%' . $search . '%')
                                            ->orWhere('phone', 'like', '%' . $search . '%');
                                    });
                                })
                                ->orWhereHas('VehicleDetail', function ($query) use ($search) {
                                    $query->where(function ($vehiclequery) use ($search) {
                                        $vehiclequery->where('regn_no', 'like', '%' . $search . '%');
                                    });
                                });
                        })
                        ->orWhereHas('ConsignerDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cnrquery) use ($search, $searchT) {
                                $cnrquery->where('nick_name', 'like', '%' . $search . '%')
                                    ->orWhere('city', 'like', '%' . $search . '%');
                            });
                        });
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

            $drivertasks = $query->orderBy('id', 'DESC')->paginate($peritem);
            $drivertasks = $drivertasks->appends($request->query());

            $html = view('prs.driver-task-list-ajax', ['prefix' => $this->prefix, 'drivertasks' => $drivertasks, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->with('ConsignerDetail:id,nick_name,city');

        if ($authuser->role_id == 1) {
            $query;
        }
        // elseif ($authuser->role_id == 4) {
        //     $query = $query->whereIn('regclient_id', $regclient);
        // } elseif ($authuser->role_id == 7) {
        //     $query = $query->whereIn('regclient_id', $regclient);
        // }
        else {
            $query = $query->whereHas('PickupRunSheet', function ($query) use ($cc) {
                $query->whereIn('branch_id', $cc);
            });
        }

        $drivertasks = $query->orderBy('id', 'DESC')->paginate($peritem);
        $drivertasks = $drivertasks->appends($request->query());

        return view('prs.driver-task-list', ['drivertasks' => $drivertasks, 'peritem' => $peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }

    // get list vehicle receive gate
    public function vehicleReceivegate(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PickupRunSheet::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $cc = explode(',', $authuser->branch_id);

            $query = $query->with('PrsDriverTasks', 'PrsDriverTasks.PrsTaskItems');

            if ($authuser->role_id == 1) {
                $query;
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('pickup_id', 'like', '%' . $search . '%')
                        ->orWhereHas('VehicleDetail', function ($vehiclequery) use ($search) {
                            $vehiclequery->where('regn_no', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('DriverDetail', function ($driverquery) use ($search) {
                            $driverquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('VehicleType', function ($vehtypequery) use ($search) {
                            $vehtypequery->where('name', 'like', '%' . $search . '%');
                        });
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

            $vehiclereceives = $query->whereNotIn('status', [3])->orderBy('id', 'ASC')->paginate($peritem);
            $vehiclereceives = $vehiclereceives->appends($request->query());

            $html = view('prs.vehicle-receivegate-list-ajax', ['prefix' => $this->prefix, 'vehiclereceives' => $vehiclereceives, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->with('PrsDriverTasks', 'PrsDriverTasks.PrsTaskItems');

        if ($authuser->role_id == 1) {
            $query;
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $vehiclereceives = $query->whereNotIn('status', [3])->orderBy('id', 'ASC')->paginate($peritem);
        $vehiclereceives = $vehiclereceives->appends($request->query());

        return view('prs.vehicle-receivegate-list', ['vehiclereceives' => $vehiclereceives, 'peritem' => $peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }

    public function createTaskItem(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'regclient_id' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }
            // insert prs driver task items
            if (!empty($request->data)) {
                $authuser = Auth::user();
                $cc = explode(',', $authuser->branch_id);
                $location = Location::whereIn('id', $cc)->first();

                $getRegclient = Consigner::select('id', 'regionalclient_id')->where('id', $request->consigner_id)->first();

                $get_data = $request->data;
                foreach ($get_data as $key => $save_data) {
                    $save_data['drivertask_id'] = $request->drivertask_id;
                    $save_data['status'] = 1;
                    $save_data['user_id'] = $authuser->id;
                    $save_data['branch_id'] = $authuser->branch_id;

                    // upload invoice image
                    if (isset($save_data['invc_img'])) {
                        // Get the original filename
                        $originalFilename = uniqid() . '_' . $save_data['invc_img']->getClientOriginalName();
                    
                        // Upload the file to AWS S3
                        if (Storage::disk('s3')->putFileAs('prs_invoices', $save_data['invc_img'], $originalFilename)) {
                            // Update the 'invoice_image' field with the original filename
                            $imagePath = explode('/', $originalFilename);
                            $save_data['invoice_image'] = end($imagePath);
                        }
                    }

                    // old path (public_path('images/invoice_images')
                    
                    $savetaskitems = PrsTaskItem::create($save_data);

                    // create order start
                    $today_date = Carbon::now();
                    $consignment_date = $today_date->format('Y-m-d');

                    $consignmentsave['regclient_id'] = @$getRegclient->regionalclient_id;
                    $consignmentsave['consigner_id'] = $request->consigner_id;
                    $consignmentsave['consignment_date'] = $consignment_date;
                    $consignmentsave['user_id'] = $authuser->id;

                    if ($authuser->role_id == 3) {
                        $consignmentsave['branch_id'] = $request->branch_id;
                        $consignmentsave['fall_in'] = $request->branch_id;
                    } else {
                        $consignmentsave['branch_id'] = $authuser->branch_id;
                        $consignmentsave['fall_in'] = $authuser->branch_id;
                    }
                    $consignmentsave['status'] = 5;

                    // if (!empty($request->vehicle_id)) {
                    //     $consignmentsave['delivery_status'] = "Started";
                    // } else {
                    $consignmentsave['delivery_status'] = "Unassigned";
                    // }
                    $consignmentsave['total_quantity'] = $savetaskitems->quantity;
                    $consignmentsave['total_weight'] = $savetaskitems->net_weight;
                    $consignmentsave['total_gross_weight'] = $savetaskitems->gross_weight;
                    $consignmentsave['prs_id'] = $request->prs_id;
                    $consignmentsave['prsitem_status'] = 1;
                    $consignmentsave['lr_type'] = 2;
                    if (empty($save_data['lr_id']) && (!empty($savetaskitems->invoice_no))) {
                        $saveconsignment = ConsignmentNote::create($consignmentsave);

                        $mytime = Carbon::now('Asia/Kolkata');
                        $currentdate = $mytime->toDateTimeString();
                        
                        if($saveconsignment){
                            // task created //
                            $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'desc'=>'Order Placed','create_at' => $currentdate,'location'=>$location->name, 'type' => '2']);
                            $respons_data = json_encode($respons);
                            $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                            // ==== end create===//
                            
                            // ================= task assign =================//
                            $respons2 = array('consignment_id' => $saveconsignment->id, 'status' => 'Menifested','desc'=>'Consignment Menifested at', 'create_at' => $currentdate,'location'=>$location->name, 'type' => '2');
                            $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('id')->first();
                            if(!empty($lastjob->response_data)){
                                $st = json_decode($lastjob->response_data);
                                array_push($st, $respons2);
                                $sts = json_encode($st);

                                $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Menifested', 'type' => '2']);
                            }

                            // task created //
                            $respons3 = array('consignment_id' => $saveconsignment->id, 'status' => 'Prs Created', 'desc'=>'Pickup Scheduled','create_at' => $currentdate,'location'=>$location->name, 'type' => '2');
                            
                            $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('id')->first();
                            if(!empty($lastjob->response_data)){
                                $st = json_decode($lastjob->response_data);
                                array_push($st, $respons3);
                                $sts = json_encode($st);

                                $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Prs Created', 'type' => '2']);
                            }
                            // ==== end created===//
                        }                        
                    } else {
                        ConsignmentNote::where(['id' => $save_data['lr_id']])->update(['prsitem_status' => 1, 'prs_id' =>$request->prs_id]);
                        $saveconsignment = '';
                    }

                    if ($saveconsignment) {
                        $save_data['consignment_id'] = $saveconsignment->id;
                        $save_data['quantity'] = $savetaskitems->quantity;
                        // $save_data['weight']            = $savetaskitems->net_weight;
                        // $save_data['gross_weight']      = $savetaskitems->gross_weight;
                        // $save_data['chargeable_weight'] = $savetaskitems->chargeable_weight;
                        // $save_data['order_id']          = $savetaskitems->order_id;
                        $save_data['invoice_no'] = $savetaskitems->invoice_no;
                        $save_data['invoice_date'] = $savetaskitems->invoice_date;
                        $save_data['status'] = 1;
                        $saveconsignmentitems = ConsignmentItem::create($save_data);

                        if ($saveconsignmentitems) {
                            $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                            $save_itemdata['quantity'] = $saveconsignmentitems->quantity;
                            // $save_itemdata['net_weight'] = $saveconsignmentitems->weight;
                            // $save_itemdata['gross_weight'] = $saveconsignmentitems->gross_weight;
                            $save_itemdata['status'] = 1;
                            $savesubitems = ConsignmentSubItem::create($save_itemdata);
                        }
                    }
                    // end create order
                }
                $pickup_Date = Carbon::now()->format('Y-m-d');
                PrsDrivertask::where('id', $request->drivertask_id)->update(['pickup_Date'=>$pickup_Date,'status' => 3]);

                $countdrivertask_id = PrsDrivertask::where('prs_id', $request->prs_id)->count();
                $countdrivertask_status = PrsDrivertask::where(['prs_id' => $request->prs_id, 'status' => 3])->count();
                if ($countdrivertask_id == $countdrivertask_status) {
                    PickupRunSheet::where('id', $request->prs_id)->update(['status' => 2]);
                }

                $url = URL::to($this->prefix . '/driver-tasks');
                $response['success'] = true;
                $response['success_message'] = "PRS task item Added successfully";
                $response['error'] = false;
                $response['page'] = 'create-prstaskitem';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not created PRS task item please try again";
                $response['error'] = true;
            }
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    // get cnr count in receive vehicle list on receive vehicle action btn
    public function getVehicleItem(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $get_drivertasks = PrsDrivertask::where('prs_id', $request->prs_id)->with('ConsignerDetail:id,nick_name', 'PrsTaskItems')->get();
        
        $consinger_ids = explode(',', $request->consinger_ids);
        $consigners = Consigner::select('nick_name')->whereIn('id', $consinger_ids)->get();
        $cnr_data = json_decode(json_encode($consigners));

        if ($cnr_data) {
            $response['success'] = true;
            $response['success_message'] = "Consigner fetch successfully";
            $response['error'] = false;
            $response['data'] = $get_drivertasks;
            $response['data_prsid'] = $request->prs_id;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consigner please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    // store receive vehicle item from modal submit
    public function createReceiveVehicle(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            // 'regclient_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['success'] = false;
            $response['validation'] = false;
            $response['formErrors'] = true;
            $response['errors'] = $errors;
            return response()->json($response);
        }

        $authuser = Auth::user();
        if (!empty($request->data)) {
            $get_data = $request->data;
            // echo "<pre>"; print_r($get_data); die;
            foreach ($get_data as $key => $save_data) {
                $save_data['prs_id'] = $request->prs_id;
                $save_data['status'] = 1;
                $save_data['user_id'] = $authuser->id;
                $save_data['branch_id'] = $authuser->branch_id;
                $saveitem_data = $save_data['item_id'];
                $saveitem_ids = explode(',', $saveitem_data);

                $savevehiclereceive = PrsReceiveVehicle::create($save_data);
                PrsTaskItem::whereIn('drivertask_id', $saveitem_ids)->update(['status' => 2]);

            }
            if ($savevehiclereceive) {
                PrsDriverTask::where('prs_id', $savevehiclereceive->prs_id)->update(['status' => 4]);
                // PrsTaskItem::where('drivertask_id', $request->prs_id)->update(['status' => 2]);

                PickupRunSheet::where('id', $request->prs_id)->update(['status' => 3]);
                $url = URL::to($this->prefix . '/vehicle-receivegate');
                $response['success'] = true;
                $response['success_message'] = "PRS vehicle receive successfully";
                $response['error'] = false;
                $response['page'] = 'create-vehiclereceive';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not PRS vehicle receive please try again";
                $response['error'] = true;
            }
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not created PRS task item please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($prs_id)
    {
        $id = decrypt($prs_id);
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $regclients = RegionalClient::where('status', 1)->orderby('name', 'ASC')->get();
        $consigners = Consigner::where('status', 1)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');

        $getprs = PickupRunSheet::where('id', $id)->with('PrsRegClients', 'PrsRegClients.RegConsigner')->first();
       
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        $locations = Location::select('id', 'name')->get();
        $hub_locations = Location::where('is_hub', '1')->select('id', 'name')->get();
        $getprs = PickupRunSheet::where('id', $id)->with('PrsRegClients', 'PrsRegClients.RegConsigner')->first();
        
        return view('prs.update-prs', ['prefix' => $this->prefix, 'getprs' => $getprs, 'regclients' => $regclients, 'locations' => $locations, 'hub_locations' => $hub_locations, 'consigners' => $consigners, 'vehicletypes' => $vehicletypes, 'vehicles' => $vehicles, 'drivers' => $drivers]);
    }

    // get consigner on select regclient
    public function getConsigner(Request $request)
    {
        $getconsigners = Consigner::select('id', 'nick_name')->where('regionalclient_id', $request->regclient_id)->get();

        if ($getconsigners) {
            $response['success'] = true;
            $response['success_message'] = "Consigner list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getconsigners;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consigner list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    // get consigner on select regclient
    public function getlrItems(Request $request)
    {
        $getconsigners = ConsignmentNote::with('ConsignmentItems')->where(['consigner_id' => $request->prsconsigner_id, 'status' => '5', 'prsitem_status' => '0'])->orderBy('created_at', 'desc')->get();
        // echo'<pre>'; print_r(json_decode($getconsigners)); die;
        if ($getconsigners) {
            $response['success'] = true;
            $response['success_message'] = "Consigner list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getconsigners;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consigner list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new PrsExport, 'prs.csv');
    }

    public function paymentList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PickupRunSheet::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $query = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner', 'VehicleDetail', 'DriverDetail')->where('request_status', 0)
            ->where('payment_status', 0);

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('pickup_id', 'like', '%' . $search . '%')
                        ->orWhereHas('PrsRegClients.RegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('PrsRegClients.RegConsigner.Consigner', function ($query) use ($search, $searchT) {
                            $query->where(function ($cnrquery) use ($search, $searchT) {
                                $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('DriverDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($driverquery) use ($search, $searchT) {
                                $driverquery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('phone', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('VehicleDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($vehiclequery) use ($search, $searchT) {
                                $vehiclequery->where('regn_no', 'like', '%' . $search . '%');
                            });
                        });

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
            $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
            // $vehicles = Vehicle::select('vehicle_no')->distinct()->get();
            $authuser = Auth::user();
            $cc = explode(',', $authuser->branch_id); 

            if ($authuser->role_id == 1) {
                $query;
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }

            $prsdata = $query->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html = view('prs.prs-paymentlist-ajax', ['prefix' => $this->prefix, 'prsdata' => $prsdata, 'vehicles' => $vehicles, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        
        $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        $vendors = Vendor::with('Branch')->get();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        $query = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner', 'VehicleDetail', 'DriverDetail')->where('request_status', 0)
            ->where('payment_status', 0);

        if ($authuser->role_id == 1) {
            $query;
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $prsdata = $query->orderBy('id', 'DESC')->paginate($peritem);
        $prsdata = $prsdata->appends($request->query());

        return view('prs.prs-paymentlist', ['prsdata' => $prsdata, 'vehicles' => $vehicles, 'vehicletype' => $vehicletypes, 'branchs' => $branchs, 'vendors' => $vendors, 'peritem' => $peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }

    public function pickupLoads(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = ConsignmentNote::query();

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

            if ($authuser->role_id == 1) {
                $branchs = Location::select('id', 'name')->get();
            } elseif ($authuser->role_id == 2) {
                $branchs = Location::select('id', 'name')->where('id', $cc)->get();
            } elseif ($authuser->role_id == 5) {
                $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
            } else {
                $branchs = Location::select('id', 'name')->get();
            }

            $query = $query->where(['status' => 5, 'prsitem_status' => 0, 'lr_type' => 1])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'PrsDetail');

            if ($authuser->role_id == 1) {
                $query;
            } elseif ($authuser->role_id == 4 || $authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 6) {
                $query = $query->whereIn('base_clients.id', $baseclient);
            } else {
                $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc) {
                    $query->whereIn('fall_in', $cc);
                });
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('ConsignerDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cnrquery) use ($search, $searchT) {
                                $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('ConsigneeDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cneequery) use ($search, $searchT) {
                                $cneequery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        });
                });
                // ->orWhereHas('ConsignmentItem',function( $query ) use($search,$searchT){
                //     $query->where(function ($invcquery)use($search,$searchT) {
                //         $invcquery->where('invoice_no', 'like', '%' . $search . '%');
                //     });
                // });

                // });
            }

            $startdate = $request->startdate;
            $enddate = $request->enddate;

            if(isset($startdate) && isset($enddate)){
                $query = $query->whereBetween('consignment_date',[$startdate,$enddate]);                
            }

            $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            $consignments = $consignments->appends($request->query());

            $html = view('prs.pickupload-list-ajax', ['prefix' => $this->prefix, 'segment' => $this->segment, 'consignments' => $consignments, 'peritem' => $peritem, 'branchs' => $branchs])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        if ($authuser->role_id == 1) {
            $branchs = Location::select('id', 'name')->get();
        } elseif ($authuser->role_id == 2) {
            $branchs = Location::select('id', 'name')->where('id', $cc)->get();
        } elseif ($authuser->role_id == 5) {
            $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        } else {
            $branchs = Location::select('id', 'name')->get();
        }

        $query = $query->where(['status' => 5, 'prsitem_status' => 0, 'lr_type' => 1])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'PrsDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4 || $authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } else {
            $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc) {
                $query->whereIn('fall_in', $cc);
            });
        }

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('prs.pickupload-list', ['prefix' => $this->prefix, 'segment' => $this->segment, 'consignments' => $consignments, 'peritem' => $peritem, 'branchs' => $branchs]);
    }

    //export PickupLoad list
    public function exportPickupLoad(Request $request)
    {
        return Excel::download(new PickupLoadExport($request->startdate, $request->enddate,$request->search), 'pickup-load.csv');
    }

    public function UpdatePrs(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'regclient_id' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }
            $authuser = Auth::user();
            // $pickup_id = DB::table('pickup_run_sheets')->select('pickup_id')->latest('pickup_id')->first();
            // $pickup_id = json_decode(json_encode($pickup_id), true);
            // if (empty($pickup_id) || $pickup_id == null) {
            //     $pickup_id = 2900001;
            // } else {
            //     $pickup_id = $pickup_id['pickup_id'] + 1;
            // }

            // $prssave['pickup_id'] = $pickup_id;
            if (!empty($request->vehicletype_id)) {
                $prssave['vehicletype_id'] = $request->vehicletype_id;
            }
            if (!empty($request->vehicle_id)) {
                $prssave['vehicle_id'] = $request->vehicle_id;
            }
            if (!empty($request->driver_id)) {
                $prssave['driver_id'] = $request->driver_id;
            }

            // $prssave['prs_date'] = $request->prs_date;
            $prssave['location_id'] = $request->location_id;
            $prssave['hub_location_id'] = $request->hub_location_id;
            $prssave['user_id'] = $authuser->id;
            $prssave['branch_id'] = $authuser->branch_id;
            $prssave['status'] = "1";

            $saveprs = PickupRunSheet::where('id', $request->prs_id)->update($prssave);
            if ($saveprs) {
                $url = URL::to($this->prefix . '/prs');
                $response['success'] = true;
                $response['success_message'] = "PRS Updated successfully";
                $response['error'] = false;
                $response['page'] = 'prs-update';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not updated PRS please try again";
                $response['error'] = true;
            }

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }
    // ================= ADD PURCHASE AMT ============= //
    public function updatePurchasePricePrs(Request $request)
    {
        try {
            DB::beginTransaction();
            PickupRunSheet::where('pickup_id', $request->prs_no)->update(['purchase_amount' => $request->purchase_price, 'vehicletype_id' => $request->vehicle_type]);

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

    public function getPrsdetails(Request $request)
    {
        $prs_statsu = PickupRunSheet::whereIn('pickup_id', $request->prs_no)->first();

        $response['get_data'] = $prs_statsu;
        $response['success'] = true;
        $response['error_message'] = "find data";
        return response()->json($response);
    }

    // ==================CreatePayment Request =================
    public function createPrsPayment(Request $request)
    {

        $this->prefix = request()->route()->getPrefix();
        $url_header = $_SERVER['HTTP_HOST'];

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = $authuser->branch_id;
        $user = $authuser->id;
        $bm_email = $authuser->email;

        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        //deduct balance
        $deduct_balance = $request->pay_amt - $request->final_payable_amount;

        $prsno = explode(',', $request->prs_no);

        $consignment = PickupRunSheet::with('VehicleDetail')->whereIn('pickup_id', $prsno)
            ->groupby('pickup_id')
            ->get();
        $simplyfy = json_decode(json_encode($consignment), true);
        $transactionId = DB::table('prs_payment_requests')->select('transaction_id')->latest('transaction_id')->first();
        $transaction_id = json_decode(json_encode($transactionId), true);
        if (empty($transaction_id) || $transaction_id == null) {
            $transaction_id_new = 90000101;
        } else {
            $transaction_id_new = $transaction_id['transaction_id'] + 1;
        }
        // ============ Check Request Permission =================
        if ($authuser->is_payment == 0) {
            $i = 0;
            $sent_vehicle = array();
            foreach ($simplyfy as $value) {

                $i++;
                $prs_no = $value['pickup_id'];
                $vendor_id = $request->vendor_name;
                $vehicle_no = @$value['vehicle_detail']['regn_no'];
                $sent_vehicle[] = @$value['vehicle_detail']['regn_no'];

                if ($request->p_type == 'Advance') {
                    $balance_amt = $request->claimed_amount - $request->pay_amt;

                    $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 2, 'is_approve' => 0, 'status' => '1']);

                    PickupRunSheet::whereIn('pickup_id', $prsno)->update(['payment_status' => 2]);
                } else {
                    $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->pay_amt;
                    } else {
                        $balance = 0;
                    }
                    $advance = $request->pay_amt;

                    $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 2, 'is_approve' => 0, 'status' => '1']);
                    PickupRunSheet::whereIn('pickup_id', $prsno)->update(['payment_status' => 2]);

                }

            }

            $unique = array_unique($sent_vehicle);
            $sent_venicle_no = implode(',', $unique);
            PickupRunSheet::whereIn('pickup_id', $prsno)->update(['request_status' => '1']);

            $url = $this->prefix . '/prs-request-list';
            $new_response['success'] = true;
            $new_response['redirect_url'] = $url;
            $new_response['success_message'] = "Data Imported successfully";

        } else {
            $i = 0;
            $sent_vehicle = array();
            foreach ($simplyfy as $value) {

                $i++;
                $prs_no = $value['pickup_id'];
                $vendor_id = $request->vendor_name;
                $vehicle_no = $value['vehicle_detail']['regn_no'];
                $sent_vehicle[] = $value['vehicle_detail']['regn_no'];

                if ($request->p_type == 'Advance') {
                    $balance_amt = $request->claimed_amount - $request->pay_amt;

                    $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 0, 'is_approve' => 1, 'status' => '1']);

                } else {
                    $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->pay_amt;
                    } else {
                        $balance = 0;
                    }
                    $advance = $request->pay_amt;
                    // dd($advance);

                    $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt, 'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign, 'payment_status' => 0, 'is_approve' => 1, 'status' => '1']);
                }

            }

            $unique = array_unique($sent_vehicle);
            $sent_venicle_no = implode(',', $unique);
            PickupRunSheet::whereIn('pickup_id', $prsno)->update(['request_status' => '1']);

            $checkduplicateRequest = PrsPaymentHistory::where('transaction_id', $transaction_id_new)->where('payment_status', 2)->first();
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
                  \"txn_route\": \"PRS\"
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
                        $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                        if (!empty($getadvanced->balance)) {
                            $balance = $getadvanced->balance - $request->pay_amt;
                        } else {
                            $balance = 0;
                        }
                        $advance = $request->pay_amt;

                        PickupRunSheet::whereIn('pickup_id', $prsno)->update(['payment_status' => 2]);

                        PrsPaymentRequest::where('transaction_id', $transaction_id_new)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'payment_status' => 2]);

                        $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                        $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                        $paymentresponse['transaction_id'] = $transaction_id_new;
                        $paymentresponse['prs_no'] = $request->prs_no;
                        $paymentresponse['bank_details'] = json_encode($bankdetails);
                        $paymentresponse['purchase_amount'] = $request->claimed_amount;
                        $paymentresponse['payment_type'] = $request->p_type;
                        $paymentresponse['advance'] = $advance;
                        $paymentresponse['balance'] = $balance;
                        $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                        $paymentresponse['current_paid_amt'] = $request->pay_amt;
                        $paymentresponse['payment_status'] = 2;

                        $paymentresponse = PrsPaymentHistory::create($paymentresponse);

                    } else {

                        $balance_amt = $request->claimed_amount - $request->pay_amt;
                        //======== Payment History save =========//
                        $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                        $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                        $paymentresponse['transaction_id'] = $transaction_id_new;
                        $paymentresponse['prs_no'] = $request->prs_no;
                        $paymentresponse['bank_details'] = json_encode($bankdetails);
                        $paymentresponse['purchase_amount'] = $request->claimed_amount;
                        $paymentresponse['payment_type'] = $request->p_type;
                        $paymentresponse['advance'] = $request->pay_amt;
                        $paymentresponse['balance'] = $balance_amt;
                        $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                        $paymentresponse['current_paid_amt'] = $request->pay_amt;
                        $paymentresponse['payment_status'] = 2;

                        $paymentresponse = PrsPaymentHistory::create($paymentresponse);
                        PrsPaymentRequest::where('transaction_id', $transaction_id_new)->update(['payment_type' => $request->p_type, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'payment_status' => 2]);

                        PickupRunSheet::whereIn('pickup_id', $prsno)->update(['payment_status' => 2]);
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
                $url = $this->prefix . '/prs-request-list';
                $new_response['redirect_url'] = $url;
                $new_response['success_message'] = "Request Sent Successfully";
            } else {
                $new_response['error'] = false;
                $new_response['success_message'] = "Request Already Sent";
            }

        }

        return response()->json($new_response);

    }

    public function prsRequestList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PrsPaymentRequest::query();
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
        $prsRequests = $query->orderBy('id', 'DESC')->paginate($peritem);
        $prsRequests = $prsRequests->appends($request->query());
        $vendors = Vendor::with('Branch')->get();
        $vehicletype = VehicleType::select('id', 'name')->get();

        return view('prs.prs-request-list', ['peritem' => $peritem, 'prefix' => $this->prefix, 'prsRequests' => $prsRequests, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes, 'branchs' => $branchs, 'vendors' => $vendors, 'vehicletype' => $vehicletype]);

    }
    /// RM aprover
    public function getVendorReqDetailsPrs(Request $request)
    {
        $req_data = PrsPaymentRequest::with('VendorDetails')->where('transaction_id', $request->transaction_id)
            ->groupBy('transaction_id')->get();

        $gethrs = PrsPaymentRequest::select('prs_no')->where('transaction_id', $request->transaction_id)
            ->get();
        $simply = json_decode(json_encode($gethrs), true);
        foreach ($simply as $value) {
            $store[] = $value['prs_no'];
        }
        $prs_no = implode(',', $store);

        $response['req_data'] = $req_data;
        $response['prs_no'] = $prs_no;
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
            $get_vehicle = PrsPaymentRequest::select('vehicle_no')->where('transaction_id', $request->transaction_id)->get();
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
            \"txn_route\": \"PRS\"
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

                    $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->payable_amount;
                    } else {
                        $balance = 0;
                    }
                    $advance = $getadvanced->advanced;

                    PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_status' => 2, 'is_approve' => 1]);

                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['prs_no'] = $request->prs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $advance;
                    $paymentresponse['balance'] = $balance;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = PrsPaymentHistory::create($paymentresponse);

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

                    $paymentresponse = PrsPaymentHistory::create($paymentresponse);

                    PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_status' => 2, 'is_approve' => 1]);
                }

                $new_response['success'] = true;
                $new_response['message'] = $res_data->message;

            } else {
                $new_response['message'] = $res_data->message;
                $new_response['success'] = false;
            }
        } else {

            // ============ Request Rejected ================= //
            $prs_num = explode(',', $request->prs_no);
            PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['rejected_remarks' => $request->rejectedRemarks, 'payment_status' => 4]);

            PickupRunSheet::whereIn('pickup_id', $prs_num)->update(['payment_status' => 0, 'request_status' => 0]);

            $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

            $paymentresponse['transaction_id'] = $request->transaction_id;
            $paymentresponse['prs_no'] = $request->prs_no;
            $paymentresponse['bank_details'] = json_encode($bankdetails);
            $paymentresponse['purchase_amount'] = $request->claimed_amount;
            $paymentresponse['payment_type'] = $request->p_type;
            $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
            $paymentresponse['current_paid_amt'] = $request->payable_amount;
            $paymentresponse['payment_status'] = 4;

            $paymentresponse = PrsPaymentHistory::create($paymentresponse);

            $new_response['message'] = 'Request Rejected';
            $new_response['success'] = true;
        }

        return response()->json($new_response);

    }
    public function showPrs(Request $request)
    {
        $getprs = PrsPaymentRequest::select('prs_no')->where('transaction_id', $request->trans_id)->get();

        $response['getprs'] = $getprs;
        $response['success'] = true;
        $response['success_message'] = "Prs transaction Ids";
        return response()->json($response);
    }

    public function getSecondPaymentDetailsPrs(Request $request)
    {

        $req_data = PrsPaymentRequest::with('VendorDetails')->where('transaction_id', $request->trans_id)
            ->groupBy('transaction_id')->get();

        $getdrs = PrsPaymentRequest::select('prs_no')->where('transaction_id', $request->trans_id)
            ->get();
        $simply = json_decode(json_encode($getdrs), true);
        foreach ($simply as $value) {
            $store[] = $value['prs_no'];
        }
        $prs_no = implode(',', $store);

        $response['req_data'] = $req_data;
        $response['prs_no'] = $prs_no;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    //////////////////////
    public function createSecondPaymentRequestPrs(Request $request)
    {
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = $authuser->branch_id;
        $user = $authuser->id;
        $bm_email = $authuser->email;
        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        //deduct balance
        $deduct_balance = $request->payable_amount - $request->final_payable_amount;

        $get_vehicle = PrsPaymentRequest::select('vehicle_no')->where('transaction_id', $request->transaction_id)->get();
        $sent_vehicle = array();
        foreach ($get_vehicle as $vehicle) {
            $sent_vehicle[] = $vehicle->vehicle_no;
        }
        $unique = array_unique($sent_vehicle);
        $sent_vehicle_no = implode(',', $unique);
        $url_header = $_SERVER['HTTP_HOST'];
        $prs = explode(',', $request->prs_no);
        if ($authuser->is_payment == 0) {

            $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
            if (!empty($getadvanced->balance)) {
                $balance = $getadvanced->balance - $request->payable_amount;
            } else {
                $balance = 0;
            }
            $advance = $getadvanced->advanced + $request->payable_amount;

            PickupRunSheet::whereIn('pickup_id', $prs)->update(['payment_status' => 2]);

            PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance' => $deduct_balance, 'current_paid_amt' => $request->final_payable_amount, 'payment_status' => 2, 'is_approve' => 0]);

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
             \"txn_route\": \"PRS\"
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

                    $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
                    if (!empty($getadvanced->balance)) {
                        $balance = $getadvanced->balance - $request->payable_amount;
                    } else {
                        $balance = 0;
                    }
                    $advance = $getadvanced->advanced + $request->payable_amount;

                    PickupRunSheet::whereIn('pickup_id', $prs)->update(['payment_status' => 2]);

                    PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance' => $deduct_balance, 'current_paid_amt' => $request->final_payable_amount, 'payment_status' => 2, 'is_approve' => 1]);

                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['prs_no'] = $request->prs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $advance;
                    $paymentresponse['balance'] = $balance;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = PrsPaymentHistory::create($paymentresponse);

                } else {

                    $balance_amt = $request->claimed_amount - $request->payable_amount;
                    //======== Payment History save =========//
                    $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                    $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                    $paymentresponse['transaction_id'] = $request->transaction_id;
                    $paymentresponse['prs_no'] = $request->prs_no;
                    $paymentresponse['bank_details'] = json_encode($bankdetails);
                    $paymentresponse['purchase_amount'] = $request->claimed_amount;
                    $paymentresponse['payment_type'] = $request->p_type;
                    $paymentresponse['advance'] = $request->payable_amount;
                    $paymentresponse['balance'] = $balance_amt;
                    $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                    $paymentresponse['current_paid_amt'] = $request->payable_amount;
                    $paymentresponse['payment_status'] = 2;

                    $paymentresponse = PrsPaymentHistory::create($paymentresponse);

                    PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $request->payable_amount, 'balance' => $balance_amt, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance' => $deduct_balance, 'current_paid_amt' => $request->final_payable_amount, 'payment_status' => 2]);

                    PickupRunSheet::whereIn('pickup_id', $prs)->update(['payment_status' => 2]);
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

    // print LR for prs lr view
    public function prsPrintLR($lr_id)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::get();
        $locations = Location::with('GstAddress')->whereIn('id', $cc)->first();

        $getdata = ConsignmentNote::where('id', $lr_id)->with('ConsignmentItems', 'ConsignerDetail.GetZone','ConsignerDetail.GetRegClient', 'VehicleDetail', 'DriverDetail','PrsDetail','PrsDetail.VehicleDetail','PrsDetail.DriverDetail')->first();
        $data = json_decode(json_encode($getdata), true);
        
        if (isset($data['consigner_detail']['legal_name'])) {
            $legal_name = '<b>' . $data['consigner_detail']['legal_name'] . '</b><br>';
        } else {
            $legal_name = '';
        }
        if (isset($data['consigner_detail']['address_line1'])) {
            $address_line1 = '' . $data['consigner_detail']['address_line1'] . '<br>';
        } else {
            $address_line1 = '';
        }
        if (isset($data['consigner_detail']['address_line2'])) {
            $address_line2 = '' . $data['consigner_detail']['address_line2'] . '<br>';
        } else {
            $address_line2 = '';
        }
        if (isset($data['consigner_detail']['address_line3'])) {
            $address_line3 = '' . $data['consigner_detail']['address_line3'] . '<br>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['consigner_detail']['address_line4'])) {
            $address_line4 = '' . $data['consigner_detail']['address_line4'] . '<br><br>';
        } else {
            $address_line4 = '<br>';
        }
        if (isset($data['consigner_detail']['city'])) {
            $city = $data['consigner_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['consigner_detail']['get_zone']['state'])) {
            $district = $data['consigner_detail']['get_zone']['state'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['consigner_detail']['postal_code'])) {
            $postal_code = $data['consigner_detail']['postal_code'] . '<br>';
        } else {
            $postal_code = '';
        }
        if (isset($data['consigner_detail']['gst_number'])) {
            $gst_number = 'GST No: ' . $data['consigner_detail']['gst_number'] . '<br>';
        } else {
            $gst_number = '';
        }
        if (isset($data['consigner_detail']['phone'])) {
            $phone = 'Phone No: ' . $data['consigner_detail']['phone'] . '<br>';
        } else {
            $phone = '';
        }

        $conr_add = $legal_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

        $generate_qrcode = QrCode::size(150)->generate('' . $lr_id . '');
        $output_file = '/qr-code/img-' . time() . '.svg';
        Storage::disk('public')->put($output_file, $generate_qrcode);
        $fullpath = storage_path('app/public/' . $output_file);
        //  dd($generate_qrcode);
        $no_invoive = count($data['consignment_items']);

        // get branch address
        if ($data['branch_id'] == 2 || $data['branch_id'] == 6 || $data['branch_id'] == 26) {
            $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[1]->name . ' </b></span><br />
        <b>' . $branch_add[1]->address . ',</b><br />
        <b>	' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . '</b><br />
        <b>GST No. : ' . $branch_add[1]->gst_number . '</b><br />';
        } else if ($data['branch_id'] == 32) {
            $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[2]->name . ' </b></span><br />
        <b>' . $branch_add[2]->address . ',</b><br />
        <b>	' . $branch_add[2]->district . ' - ' . $branch_add[2]->postal_code . ',' . $branch_add[2]->state . '</b><br />
        <b>GST No. : ' . $branch_add[2]->gst_number . '</b><br />';
        } else {
            $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[0]->name . ' </b></span><br />
        <b>	Plot no: ' . $branch_add[0]->address . ',</b><br />
        <b>	' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . '</b><br />
        <b>GST No. : ' . $branch_add[0]->gst_number . '</b><br />';
        }

        // relocate cnr cnee address check for sale to return case

        $consignmentItems = $data['consignment_items']; // Assuming $data is your array

        $invoiceNumbers = '';
        foreach ($consignmentItems as $item) {
            if (isset($item['invoice_no'])) {
                $invoiceNumbers .= $item['invoice_no'] . '/';
            }
        }

        // Remove the trailing '/' if it exists
        $invoiceNumbers = rtrim($invoiceNumbers, '/');
        
            $lrInvces = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">Invoice No</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            ' . @$invoiceNumbers . '
            </p>
            </div>';
            $billingClient = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">Bill to Client</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            ' . @$data['consigner_detail']['get_reg_client']['name'] . '
            </p>
            </div>';
            $cnradd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            ' . $conr_add . '
            </p>
            </div>';
            $expectedPickup = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">Expected Pickup Details</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                 No. of Boxes - '.$data['total_quantity'].'<br> Net Waight(Kg) - '. $data['total_weight'] .'
            </p>
            </div>';
            $actualPickup = '<td width="30%" style="vertical-align:top;>
            <div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">Actual Pickup Details</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                No of Boxes - <br>
                Net Waight(Kg) -
                 
            </p>
                </div>
            </td>';

        $pay = public_path('assets/img/LOGO_Frowarders.jpg');
        $codStamp = public_path('assets/img/cod.png');
        $paidStamp = public_path('assets/img/paid.png');
        $waterMark = public_path('assets/img/demo.png');

        for ($i = 1; $i < 3; $i++) {
            if ($i == 1) {$type = 'ORIGINAL';} elseif ($i == 2) {$type = 'DUPLICATE';}
            if (!empty($data['consigner_detail']['get_zone']['state'])) {
                $cnr_state = $data['consigner_detail']['get_zone']['state'];
            } else {
                $cnr_state = '';
            }

            $html = '<!DOCTYPE html>
            <html lang="en">
                <head>
                    <!-- Required meta tags -->
                    <meta charset="utf-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1" />

                    <!-- Bootstdap CSS -->

                    <style>
                        * {
                            box-sizing: border-box;
                        }
                        label {
                            padding: 12px 12px 12px 0;
                            display: inline-block;
                        }

                        /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
                        @media screen and (max-width: 600px) {
                        }
                        img {
                            width: 120px;
                            height: 60px;
                        }
                        .a {
                            width: 290px;
                            font-size: 11px;
                        }
                        td.b {
                            width: 238px;
                            margin: auto;
                        }
                        .width_set{
                            width:200px;
                        }
                        img.imgu {
                            margin-left: 58px;
                            height:100px;
                        }
                        .loc {
                                margin-bottom: -8px;
                                margin-top: 27px;
                            }
                            .table3 {
                border-collapse: collapse;
                width: 378px;
                height: 84px;
                margin-left: 71px;
            }
                  .footer {
               position: fixed;
               left: 0;
               bottom: 0;


            }
            .vl {
                border-left: solid;
                height: 18px;
                margin-left: 3px;
            }
            .ff{
              margin-top: 26px;
            }
            .relative {
              position: relative;
              left: 30px;
            }
            .mini-table1{

                border: 1px solid;
                border-radius: 13px;
                width: 429px;
                height: 72px;

            }
            .mini-th{
              width:90px;
              font-size: 12px;
            }
            .ee{
                margin:auto;
                margin-top:12px;
            }
            .nn{
              border-bottom:1px solid;
            }
            .mm{
            border-right:1px solid;
            padding:4px;
            }
            html { -webkit-print-color-adjust: exact; }
            .td_style{
                text-align: left;
                padding: 8px;
                color: #627429;
            }
                    </style>
                <!-- style="border-collapse: collapse; width: 369px; height: 72px; background:#d2c5c5;"class="table2" -->
                </head>
                <body style="font-family:Arial Helvetica,sans-serif;">
                <!-- <img src="' . $waterMark . '" alt="" style="position:fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); opacity: 0.2; width: 500px; height: 500px; z-index: -1;" /> -->
                    <div class="container-flex" style="margin-bottom: 5px; margin-top: -30px;">
                        <table style="height: 70px;">
                            <tr>
                            <td class="a" style="font-size: 10px;">
                            ' . $branch_address . '
                            </td>

                                <td class="a">
                                <b>	Email & Phone</b><br />
                                <b>	' . @$locations->email . '</b><br />
                                ' . @$locations->phone . '<br />

                                </td>
                            </tr>

                        </table>
                        <hr />
                        <table>
                            <tr>
                                <td class="b">
                                    <div class="ff" >
                                        <img src="' . $fullpath . '" alt="" class="imgu" />
                                    </div>
                                </td>
                                <td>
                                    <div style="margin-top: -15px; text-align: center">
                                        <h2 style="margin-bottom: -16px">CONSIGNMENT NOTE</h2>
                                        <P>' . $type . '</P>
                                    </div>
                                    <div class="mini-table1" style="background:#C0C0C0;">
                                        <table style=" border-collapse: collapse; width: 96%" class="ee">
                                            <tr>
                                                <th class="mini-th mm nn">PRS Number</th>
                                                <th class="mini-th mm nn">LR Number</th>
                                                <th class="mini-th nn">LR Date</th>
                                            </tr>
                                            <tr>
                                                <th class="mini-th mm" >' . @$data['prs_detail']['pickup_id'] . '</th>
                                                <th class="mini-th mm" >' . $data['id'] . '</th>
                                                <th class="mini-th">' . date('d-m-Y', strtotime($data['consignment_date'])) . '</th>';
                                               
                                    $html .= '</tr>
                                                </table>
                                            </div>
                                                </td>
                                            </tr>
                                            </table>';
                                            
                
                    $html .= '  <div class="loc">
                                <table>
                                    <tr>
                                        <td class="width_set">
                                            <div style="margin-left: 20px">';
                                            
                                            $html .= ' <i class="fa-solid fa-location-dot" style="font-size: 12px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i>';
                    $html .= ' </div>
                                        </td>
                                        <td class="width_set">
                                            <table border="1px solid" class="table3">
                                                <tr>
                                                    <td width="40%" ><b style="margin-left: 7px;">Vehicle No</b></td>
                                                    <td>' . @$data['prs_detail']['vehicle_detail']['regn_no'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="40%"><b style="margin-left: 7px;"> Driver Name</b></td>
                                                    <td>' . ucwords(@$data['prs_detail']['driver_detail']['name']) . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="40%"><b style="margin-left: 7px;">Driver Number</b></td>
                                                    <td>' . ucwords(@$data['prs_detail']['driver_detail']['phone']) . '</td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>';

            $html .= '<div class="container">
                                <div class="row">
                                    <div class="col-sm-12 ">
                                        <h4 style="margin-left:19px;"><b>Pickup and Drop Information</b></h4>
                                    </div>
                                </div>
                            <table border="1" style=" border-collapse:collapse; width: 100%; ">
                                <tr>
                                    <td width="30%" style="vertical-align:top; >
                                    ' . $lrInvces . '
                                    </td>
                                    <td width="30%" style="vertical-align:top; >
                                    ' . $billingClient . '
                                    </td>
                                    <td width="30%" style="vertical-align:top; >
                                    ' . $cnradd_heading . '
                                    </td>
                                    <td width="30%" style="vertical-align:top;>
                                    ' . $expectedPickup . '
                                    </td>
                                    ' . $actualPickup . '
                                </tr>
                            </table>
                        </div>
                        <div>
                            <div class="inputfiled">';

            $html .= ' <div>
                            <table style="width: 100%; margin-top:0px;">
                                <tr>
                                    <td width="50%" style="font-size: 13px;">
                                        <p style="">
                                            <strong>Driver Eternity</strong><br><br>
                                            Receivers Name & Number:<br><br>
                                            Receiving Date & Time	:
                                        </p>
                                    </td>
                                    <td width="50%; vertical-align: top; text-align: right">
                                        <p style="">
                                            <strong>Consignor Sign & Stamp</strong>
                                        </p>    
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    </div>
                </body>
            </html>
            ';
            
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            $pdf->setPaper('legal', 'portrait');
            $pdf->save(public_path() . '/prs-pdf/prs-' . $i . '.pdf')->stream('prs-' . $i . '.pdf');
            $pdf_name[] = 'prs-' . $i . '.pdf';
        }
        $pdfMerger = PDFMerger::init();
        foreach ($pdf_name as $pdf) {
            $pdfMerger->addPDF(public_path() . '/prs-pdf/' . $pdf);
        }
        $pdfMerger->merge();
        $pdfMerger->save("all.pdf", "browser");
        $file = new Filesystem;
        $file->cleanDirectory('pdf');
    }

    public function prsPrint($prsId)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = PickupRunSheet::query();
        $query = $query->with('Consignments','PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner', 'VehicleDetail', 'DriverDetail')
        ->whereHas('Consignments', function ($q) {
            $q->where('status', '!=', 0);
        })
        ->where('id', $prsId);
        // ->whereIn('status', ['1', '3','4']);
    
        if ($authuser->role_id != 1) {
            $query = $query->whereIn('branch_id', $cc);
        }
        $getPrs = $query->orderby('id', 'asc')->first();        

        $pay = public_path('assets/img/LOGO_Frowarders.jpg');

        $date = new DateTime($getPrs->created_at, new \DateTimeZone('GMT-7'));
        
        $date->setTimezone(new \DateTimeZone('IST'));
        $prsDate = $date->format('d-m-Y');
        $lrCount = count($getPrs->Consignments);

        $consignmentsArray = $getPrs->Consignments->toArray();
        // Extracting the 'total_quantity' column values
        $totalQuantities = array_column($consignmentsArray, 'total_quantity');
        // Calculating the sum of 'total_quantity' values
        $sumTotalQuantity = array_sum($totalQuantities);

        // Extracting the 'total_quantity' column values
        $totalWeights = array_column($consignmentsArray, 'total_weight');
        // Calculating the sum of 'total_quantity' values
        $sumTotalWeight = array_sum($totalWeights);

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
                        <h1 class="dd">Pickup Run Sheet</h1>
                        <div  class="dd">
                        <table class="drs_t" style="width:100%">
                            <tr class="drs_r">
                                <th class="drs_h">Pickup No.</th>
                                <th class="drs_h">' . $getPrs->pickup_id . '</th>
                                <th class="drs_h">Vehicle No.</th>
                                <th class="drs_h">' . @$getPrs->VehicleDetail->regn_no . '</th>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">PRS Date</td>
                                <td class="drs_d">' . $prsDate . '</td>
                                <td class="drs_d">Driver Name</td>
                                <td class="drs_d">' . @$getPrs->DriverDetail->name . '</td>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">No. of LRs</td>
                                <td class="drs_d"> '.@$lrCount.' </td>
                                <td class="drs_d">Driver No.</td>
                                <td class="drs_d">' . @$getPrs->DriverDetail->phone . '</td>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">Total Boxes</td>
                                <td class="drs_d">'. @$sumTotalQuantity .'</td>
                                <td class="drs_d">Total Weights</td>
                                <td class="drs_d">'. @$sumTotalWeight .' </td>
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
                        <h4 style="margin: 0px;">Consignor Name & Mobile Number</h4>
                    </div>
                    <div class="column" style="width:125px;">
                        <h4 style="margin: 0px;">Consignor City, </h4>
                        <h4 style="margin: 0px;"> Dstt & PIN</h4>
                    </div>
                    <div class="column">
                        <h4 style="margin: 0px;">Expected Pickup Details</h4>
                    </div>
                    <div class="column" style="width:170px;">
                        <h4 style="margin: 0px; ">Actual Pickup Details</h4>
                    </div>
                </div>
                </div>
                </header>
                    
                    <main style="margin-top:150px;">';
        $i = 0;
       
        foreach ($getPrs->Consignments as $dataitem) {
            //    echo'<pre>'; print_r($dataitem->ConsignerDetail->GetRegClient->name); die;

            $i++;
            if ($i % 5 == 0) {
                $html .= '<div style="page-break-before: always; margin-top:160px;"></div>';
            }

            $html .= '
                <div class="row" style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; margin-bottom: -10px;">

                    <div class="column" style="width:125px;">
                       <p style="margin-top:0px;">' . $dataitem->ConsignerDetail->GetRegClient->name . '</p>
                        <p style="margin-top:-8px;">' . $dataitem->id . '</p>
                        <p style="margin-top:-13px;">' . Helper::ShowDayMonthYear($dataitem->consignment_date) . '</p>
                    </div>
                    <div class="column" style="width:200px;">
                        <p style="margin-top:0px;">' . @$dataitem->ConsignerDetail->nick_name . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem->ConsignerDetail->phone . '</p>

                    </div>
                    <div class="column" style="width:125px;">
                        <p style="margin-top:0px;">' . @$dataitem->ConsignerDetail->city . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem->ConsignerDetail->district . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem->ConsignerDetail->postal_code . '</p>

                      </div>
                      <div class="column" >
                        <p style="margin-top:0px;">Boxes: ' . $dataitem->total_quantity . '</p>
                        <p></p>
                        <p style="margin-top:-13px;">Wt(Kg): ' . $dataitem->total_weight . '</p>
                      </div>
                      <div class="column" style="width:170px;">
                      <p style="margin-top:0px;">Boxes: </p>
                      <p></p>
                      <p style="margin-top:-13px;">Wt(Kg): </p>
                      </div>
                  </div>';
            $html .= '<div class="row" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; margin-top: 0px;">';
            //echo'<pre>'; print_r($chunk); die;
            $html .= ' <div class="column" style="width:230px; margin-top: -10px;">';
            $html .= '<table class="neworder" style="margin-top: -10px;"><tr style="border:0px;"><td style="width: 190px; padding:6px;"><span style="font-weight: bold;">Order ID</span></td><td style="width: 190px;"><span style="font-weight: bold;">Invoice No</span></td></tr></table>';
            $itm_no = 0;
            foreach ($dataitem->ConsignmentItems as $cc) {
                // echo'<pre>'; print_r($cc->order_id); die;
                $itm_no++;

                $html .= '  <table style="border:0; margin-top: -7px;"><tr><td style="width: 190px; padding:3px;">' . $itm_no . '.  ' . $cc->order_id . '</td><td style="width: 190px; padding:3px;">' . $itm_no . '.  ' . $cc->invoice_no . '</td></tr></table>';
            }
            $html .= '</div> ';

            $html .= '</div>

                <br>';

        }

        $html .= '</main>
        <table style="width: 100%; margin-top: 10px">
            <tr>
                <td>Driver Eternity & Date</td>
                <td style="text-align: right">Consignor Sign & Stamp</td>
            </tr>
        </table>
        </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('print.pdf');

    }

}
