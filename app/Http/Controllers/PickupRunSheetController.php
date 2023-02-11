<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PickupRunSheet;
use App\Models\PrsRegClient;
use App\Models\PrsRegConsigner;
use App\Models\PrsDrivertask;
use App\Models\PrsTaskItem;
use App\Models\RegionalClient;
use App\Models\Consigner;
use App\Models\Role;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleType;
use App\Models\PrsReceiveVehicle;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentSubItem;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\User;
use App\Models\PrsPaymentRequest;
use App\Models\PrsPaymentHistory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PrsExport;
use Carbon\Carbon;
use Helper;
use Validator;
use Config;
use Session;
use Auth;
use Crypt;
use DB;
use URL;


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
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            // $baseclient = explode(',', $authuser->baseclient_id);
            // $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);

            $query = $query->with('PrsRegClients.RegClient','PrsRegClients.RegConsigner.Consigner','VehicleDetail','DriverDetail');

            if ($authuser->role_id == 1) {
                $query;
            } else {
                $query = $query->whereIn('branch_id', $cc);
                }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('pickup_id', 'like', '%' . $search . '%')
                    ->orWhereHas('PrsRegClients.RegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('PrsRegClients.RegConsigner.Consigner',function( $query ) use($search,$searchT){
                        $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('DriverDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($driverquery)use($search,$searchT) {
                            $driverquery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('VehicleDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($vehiclequery)use($search,$searchT) {
                            $vehiclequery->where('regn_no', 'like', '%' . $search . '%');
                        });
                    });

                });
            }

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }

            $prsdata = $query->with('PrsRegClients.RegClient','PrsRegClients.RegConsigner.Consigner','VehicleDetail','DriverDetail')->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html =  view('prs.prs-list-ajax',['prefix'=>$this->prefix,'prsdata' => $prsdata,'peritem'=>$peritem,'segment' => $this->segment])->render();
            
            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->with('PrsRegClients.RegClient','PrsRegClients.RegConsigner.Consigner','VehicleDetail','DriverDetail');

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

        $prsdata = $query->orderBy('id','DESC')->paginate($peritem);
        $prsdata = $prsdata->appends($request->query());
        
        return view('prs.prs-list', ['prsdata' => $prsdata, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);

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
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);

        // if($authuser->role_id !=1){
        //     if($authuser->role_id ==2 || $role_id->id ==3){
        //         $regclients = RegionalClient::whereIn('location_id',$cc)->orderby('name','ASC')->get();
        //         $consigners = Consigner::whereIn('branch_id',$cc)->orderby('nick_name','ASC')->pluck('nick_name','id');
        //     }else{
        //         $regclients = RegionalClient::whereIn('id',$regclient)->orderby('name','ASC')->get();
        //         $consigners = Consigner::whereIn('regionalclient_id',$regclient)->orderby('nick_name','ASC')->pluck('nick_name','id');
        //     }
        // }else{
            $regclients = RegionalClient::where('status',1)->orderby('name','ASC')->get();
            $consigners = Consigner::where('status',1)->orderby('nick_name','ASC')->pluck('nick_name','id');
        // }
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();

        $locations = Location::select('id','name')->get();
        $hub_locations = Location::where('is_hub', '1')->select('id','name')->get();
        // dd($hub_locations);

        return view('prs.create-prs',['prefix'=>$this->prefix, 'regclients'=>$regclients,'locations'=>$locations,'hub_locations'=>$hub_locations, 'consigners'=>$consigners, 'vehicletypes'=>$vehicletypes, 'vehicles'=>$vehicles, 'drivers'=>$drivers]);
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
            $validator = Validator::make($request->all(),$rules);
        
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['validation']  = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
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
            if(!empty($request->vehicletype_id)){
                $prssave['vehicletype_id'] = $request->vehicletype_id;
            }
            if(!empty($request->vehicle_id)){
                $prssave['vehicle_id'] = $request->vehicle_id;
            }
            if(!empty($request->driver_id)){
                $prssave['driver_id'] = $request->driver_id;
            }

            $prssave['prs_date'] = $request->prs_date;
            $prssave['location_id'] = $request->location_id;
            $prssave['hub_location_id'] = $request->hub_location_id;
            $prssave['user_id'] = $authuser->id;
            $prssave['branch_id'] = $authuser->branch_id;
            $prssave['status'] = "1";
            
            $saveprs = PickupRunSheet::create($prssave);
            if($saveprs)
            {
                foreach($request->data as $key => $save_data){
                    $regclientsave['prs_id'] = $saveprs->id;
                    $regclientsave['regclient_id'] = $save_data['regclient_id'];
                    $regclientsave['status'] = "1";

                    $saveregclient = PrsRegClient::create($regclientsave);
                    if($saveregclient){
                        $data = array();
                        foreach($save_data['consigner_id'] as $cnr_data){
                            $data[] = [
                                'prs_regclientid' =>  $saveregclient->id,
                                'consigner_id' => $cnr_data,
                                'status' => '1',
                            ];
                        }
                        if($data){
                            $task_id = DB::table('prs_drivertasks')->select('task_id')->latest('task_id')->first();
                            if (empty($task_id) || $task_id == null) {
                                $task_id = 3800001;
                            } else {
                                $task_id = ($task_id->task_id) + 1;
                            }
                            foreach($data as $cnr){                             
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
                $url    =   URL::to($this->prefix.'/prs');
                $response['success'] = true;
                $response['success_message'] = "PRS Added successfully";
                $response['error'] = false;
                $response['page'] = 'prs-create';
                $response['redirect_url'] = $url;
            }else{
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
                if($request->prs_taskstatus == 1){
                    // update click on assigned status to acknowleged in driver task list
                    PrsDrivertask::where('id', $request->id)->update(['status' => '2']);
                }
                else{
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
                $query = $query->whereHas('PickupRunSheet', function($query) use($cc){
                    $query->whereIn('branch_id', $cc);
                });
                }

            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
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
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                            $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%')
                            ->orWhere('city', 'like', '%' . $search . '%');
                        });
                    });
                });
            }

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }

            $drivertasks = $query->orderBy('id', 'DESC')->paginate($peritem);
            $drivertasks = $drivertasks->appends($request->query());

            $html =  view('prs.driver-task-list-ajax',['prefix'=>$this->prefix,'drivertasks' => $drivertasks,'peritem'=>$peritem])->render();            

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
            $query = $query->whereHas('PickupRunSheet', function($query) use($cc){
                $query->whereIn('branch_id', $cc);
            });
            }

        $drivertasks  = $query->orderBy('id','DESC')->paginate($peritem);
        $drivertasks  = $drivertasks->appends($request->query());
        
        return view('prs.driver-task-list', ['drivertasks' => $drivertasks, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }

    // get list vehicle receive gate
    public function vehicleReceivegate(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PickupRunSheet::query();
        
        if ($request->ajax()) {
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }
            
            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            // $baseclient = explode(',', $authuser->baseclient_id);
            // $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            
            $query = $query->with('PrsDriverTasks','PrsDriverTasks.PrsTaskItems');

            if ($authuser->role_id == 1) {
                $query;
            } else {
                $query = $query->whereIn('branch_id', $cc);
                }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
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

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }

            $vehiclereceives = $query->whereNotIn('status',[3])->orderBy('id', 'ASC')->paginate($peritem);
            $vehiclereceives = $vehiclereceives->appends($request->query());
            
            $html =  view('prs.vehicle-receivegate-list-ajax',['prefix'=>$this->prefix,'vehiclereceives' => $vehiclereceives,'peritem'=>$peritem])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        
        $query = $query->with('PrsDriverTasks','PrsDriverTasks.PrsTaskItems');

        if ($authuser->role_id == 1) {
            $query;
        } else {
            $query = $query->whereIn('branch_id', $cc);
            }

        $vehiclereceives  = $query->whereNotIn('status',[3])->orderBy('id','ASC')->paginate($peritem);
        $vehiclereceives  = $vehiclereceives->appends($request->query());
        
        return view('prs.vehicle-receivegate-list', ['vehiclereceives' => $vehiclereceives, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }

    public function createTaskItem(Request $request)
    {
        // echo "<pre>"; print_r($request->all());die;
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'regclient_id' => 'required',
            );

            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['validation']  = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
                return response()->json($response);
            }            
            // insert prs driver task items
            if (!empty($request->data)) {
                $authuser = Auth::user();
                $getRegclient = Consigner::select('id', 'regionalclient_id')->where('id', $request->consigner_id)->first();

                $get_data = $request->data;
                foreach ($get_data as $key => $save_data) {
                    $save_data['drivertask_id'] = $request->drivertask_id;
                    $save_data['status'] = 1;
                    $save_data['user_id'] = $authuser->id;
                    $save_data['branch_id'] = $authuser->branch_id;

                    // upload invoice image
                    if (isset($save_data['invc_img'])){
                    // if($save_data['invc_img']){
                        $save_data['invoice_image'] = $save_data['invc_img']->getClientOriginalName();
                        $save_data['invc_img']->move(public_path('images/invoice_images'), $save_data['invoice_image']);
                    }
                    $savetaskitems = PrsTaskItem::create($save_data);
                    
                    // create order start
                    $today_date = Carbon::now();
                    $consignment_date = $today_date->format('Y-m-d');

                    $consignmentsave['regclient_id'] = $getRegclient->regionalclient_id;
                    $consignmentsave['consigner_id'] = $request->consigner_id;
                    $consignmentsave['consignment_date'] = $consignment_date;
                    $consignmentsave['user_id'] = $authuser->id;

                    if($authuser->role_id == 3){
                        $consignmentsave['branch_id'] = $request->branch_id;
                        $consignmentsave['fall_in'] = $request->branch_id;
                    }else{
                        $consignmentsave['branch_id'] = $authuser->branch_id;
                        $consignmentsave['fall_in'] = $authuser->branch_id;
                    }
                    $consignmentsave['status'] = 5;

                    if (!empty($request->vehicle_id)) {
                        $consignmentsave['delivery_status'] = "Started";
                    } else {
                        $consignmentsave['delivery_status'] = "Unassigned";
                    }
                    $consignmentsave['total_quantity'] = $savetaskitems->quantity;
                    $consignmentsave['total_weight'] = $savetaskitems->net_weight;
                    $consignmentsave['total_gross_weight'] = $savetaskitems->gross_weight;
                    $consignmentsave['prs_id'] = $request->prs_id;
                    $consignmentsave['prsitem_status'] = 1;
                    $consignmentsave['lr_type'] = 2;
                    if(empty($save_data['lr_id']) && (!empty($savetaskitems->invoice_no))){
                        $saveconsignment = ConsignmentNote::create($consignmentsave);
                    }else{
                        ConsignmentNote::where(['id'=> $save_data['lr_id']])->update(['prsitem_status'=>1]);
                        $saveconsignment = '';
                    }
                    
                    if($saveconsignment){
                        $save_data['consignment_id']    = $saveconsignment->id;
                        $save_data['quantity']          = $savetaskitems->quantity;
                        // $save_data['weight']            = $savetaskitems->net_weight;
                        // $save_data['gross_weight']      = $savetaskitems->gross_weight;
                        // $save_data['chargeable_weight'] = $savetaskitems->chargeable_weight;
                        // $save_data['order_id']          = $savetaskitems->order_id;
                        $save_data['invoice_no']        = $savetaskitems->invoice_no;
                        $save_data['invoice_date']      = $savetaskitems->invoice_date;
                        $save_data['status']            = 1;
                        $saveconsignmentitems = ConsignmentItem::create($save_data);                        

                        if($saveconsignmentitems){
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
                PrsDrivertask::where('id', $request->drivertask_id)->update(['status' => 3]);

                $countdrivertask_id = PrsDrivertask::where('prs_id', $request->prs_id)->count();
                $countdrivertask_status = PrsDrivertask::where(['prs_id'=> $request->prs_id, 'status'=>3])->count();
                if($countdrivertask_id == $countdrivertask_status){
                    PickupRunSheet::where('id', $request->prs_id)->update(['status'=> 2]);
                }
                   
                $url    =   URL::to($this->prefix.'/driver-tasks');
                $response['success'] = true;
                $response['success_message'] = "PRS task item Added successfully";
                $response['error'] = false;
                $response['page'] = 'create-prstaskitem';
                $response['redirect_url'] = $url;
            }else{
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
        $get_drivertasks = PrsDrivertask::where('prs_id',$request->prs_id)->with('ConsignerDetail:id,nick_name','PrsTaskItems')->get();
        // dd($get_drivertasks);
        $consinger_ids = explode(',',$request->consinger_ids);
        $consigners = Consigner::select('nick_name')->whereIn('id',$consinger_ids)->get();
        $cnr_data =json_decode(json_encode($consigners));

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
        $validator = Validator::make($request->all(),$rules);
    
        if($validator->fails())
        {
            $errors                  = $validator->errors();
            $response['success']     = false;
            $response['validation']  = false;
            $response['formErrors']  = true;
            $response['errors']      = $errors;
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
            if($savevehiclereceive){
                PrsDriverTask::where('prs_id', $savevehiclereceive->prs_id)->update(['status' => 4]);
                // PrsTaskItem::where('drivertask_id', $request->prs_id)->update(['status' => 2]);

                PickupRunSheet::where('id', $request->prs_id)->update(['status' => 3]);
                $url = URL::to($this->prefix.'/vehicle-receivegate');
                $response['success'] = true;
                $response['success_message'] = "PRS vehicle receive successfully";
                $response['error'] = false;
                $response['page'] = 'create-vehiclereceive';
                $response['redirect_url'] = $url;
            }else{
                $response['success'] = false;
                $response['error_message'] = "Can not PRS vehicle receive please try again";
                $response['error'] = true;
            }
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created PRS task item please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);

        $regclients = RegionalClient::where('status',1)->orderby('name','ASC')->get();
        $consigners = Consigner::where('status',1)->orderby('nick_name','ASC')->pluck('nick_name','id');
        // }
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();

        $locations = Location::select('id','name')->get();
        $hub_locations = Location::where('is_hub', '1')->select('id','name')->get();
        $getprs = PickupRunSheet::where('id',$id)->with('PrsRegClients','PrsRegClients.RegConsigner')->first();
        // echo "<pre>"; print_r($consigners); die;

        return view('prs.update-prs',['prefix'=>$this->prefix, 'getprs'=>$getprs, 'regclients'=>$regclients,'locations'=>$locations, 'hub_locations'=>$hub_locations, 'consigners'=>$consigners, 'vehicletypes'=>$vehicletypes, 'vehicles'=>$vehicles, 'drivers'=>$drivers]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    // get consigner on select regclient
    public function getConsigner(Request $request)
    {
        $getconsigners = Consigner::select('id','nick_name')->where('regionalclient_id', $request->regclient_id)->get();

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
        $getconsigners = ConsignmentNote::with('ConsignmentItems')->where(['consigner_id'=>$request->prsconsigner_id,'status'=> '5','prsitem_status'=>'0'])->orderBy('created_at', 'desc')->get();
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
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        $vendors = Vendor::with('Branch')->get();
        
        if ($request->ajax()) {
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            $query = $query->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail')
            ->where('request_status', 0)
            ->where('payment_status', 0);

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('pickup_id', 'like', '%' . $search . '%')
                    ->orWhereHas('PrsRegClients.RegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('PrsRegClients.RegConsigner.Consigner',function( $query ) use($search,$searchT){
                        $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('DriverDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($driverquery)use($search,$searchT) {
                            $driverquery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('VehicleDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($vehiclequery)use($search,$searchT) {
                            $vehiclequery->where('regn_no', 'like', '%' . $search . '%');
                        });
                    });

                });
            }

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }
            $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
            // $vehicles = Vehicle::select('vehicle_no')->distinct()->get();

            $prsdata = $query->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail')->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html =  view('prs.prs-paymentlist-ajax',['prefix'=>$this->prefix,'prsdata' => $prsdata, 'vehicles'=>$vehicles, 'peritem'=>$peritem])->render();
            
            return response()->json(['html' => $html]);
        }
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();

        $prsdata = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner','VehicleDetail','DriverDetail')->where('request_status', 0)
        ->where('payment_status', 0)->orderBy('id','DESC')->paginate($peritem);
        $prsdata = $prsdata->appends($request->query());
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        
        return view('prs.prs-paymentlist', ['prsdata' => $prsdata, 'vehicles'=>$vehicles,'vehicletype' => $vehicletypes, 'branchs' => $branchs,'vendors' => $vendors,'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
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
            if (isset($request->updatestatus)) {
                ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel]);

            $url = $this->prefix . '/orders';
            $response['success'] = true;
            $response['success_message'] = "Order updated successfully";
            $response['error'] = false;
            $response['page'] = 'order-statusupdate';
            $response['redirect_url'] = $url;

            return response()->json($response);
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
            
            $query = $query->where(['status'=> 5,'prsitem_status'=>0,'lr_type'=>1])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail','PrsDetail');

            if ($authuser->role_id == 1) {
                $query;
            } 
            elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 6) {
                $query = $query->whereIn('base_clients.id', $baseclient);
            } elseif ($authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } 
            else {
                $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc){
                    $query->whereIn('fall_in', $cc);
                });
                
                // $query = $query->whereIn('branch_id', $cc);
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

            $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            $consignments = $consignments->appends($request->query());

            $html = view('prs.pickupload-list-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem,'branchs'=>$branchs])->render();

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

        $query = $query->where(['status'=> 5,'prsitem_status'=>0,'lr_type'=>1])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail','PrsDetail');
        
        if ($authuser->role_id == 1) {
            $query;
        } 
        elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } 
        else {
            // $query = $query->whereIn('branch_id', $cc);
            $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc){
                $query->whereIn('fall_in', $cc);
            });
        }

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());
        
        return view('prs.pickupload-list',['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem,'branchs'=>$branchs]);
    }

    public function UpdatePrs(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'regclient_id' => 'required',
            );
            $validator = Validator::make($request->all(),$rules);
        
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['validation']  = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
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
            if(!empty($request->vehicletype_id)){
                $prssave['vehicletype_id'] = $request->vehicletype_id;
            }
            if(!empty($request->vehicle_id)){
                $prssave['vehicle_id'] = $request->vehicle_id;
            }
            if(!empty($request->driver_id)){
                $prssave['driver_id'] = $request->driver_id;
            }

            // $prssave['prs_date'] = $request->prs_date;
            $prssave['location_id'] = $request->location_id;
            $prssave['hub_location_id'] = $request->hub_location_id;
            $prssave['user_id'] = $authuser->id;
            $prssave['branch_id'] = $authuser->branch_id;
            $prssave['status'] = "1";
            
            $saveprs = PickupRunSheet::where('id',$request->prs_id)->update($prssave);
            if($saveprs)
            {
                $url    =   URL::to($this->prefix.'/prs');
                $response['success'] = true;
                $response['success_message'] = "PRS Updated successfully";
                $response['error'] = false;
                $response['page'] = 'prs-update';
                $response['redirect_url'] = $url;
            }else{
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
                    
                      $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt,'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign ,'payment_status' => 2, 'is_approve' => 0, 'status' => '1']);
  
                      PickupRunSheet::whereIn('pickup_id', $prsno)->update(['payment_status' => 2]);
                  } else {
                      $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                      if (!empty($getadvanced->balance)) {
                          $balance = $getadvanced->balance - $request->pay_amt;
                      } else {
                          $balance = 0;
                      }
                      $advance = $request->pay_amt;
  
                      $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt,'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign ,'payment_status' => 2, 'is_approve' => 0, 'status' => '1']);
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
  
                      $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt,'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'rm_id' => $authuser->rm_assign ,'payment_status' => 0, 'is_approve' => 1, 'status' => '1']);
  
                  } else {
                      $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                      if (!empty($getadvanced->balance)) {
                          $balance = $getadvanced->balance - $request->pay_amt;
                      } else {
                          $balance = 0;
                      }
                      $advance = $request->pay_amt;
                      // dd($advance);
  
                      $transaction = PrsPaymentRequest::create(['transaction_id' => $transaction_id_new, 'prs_no' => $prs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'current_paid_amt' => $request->final_payable_amount, 'amt_without_tds' => $request->pay_amt,'tds_deduct_balance' => $deduct_balance, 'branch_id' => $request->branch_id, 'user_id' => $user,'rm_id' => $authuser->rm_assign , 'payment_status' => 0, 'is_approve' => 1, 'status' => '1']);
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
          } elseif($authuser->role_id == 7){
            $query = $query->where('rm_id', $authuser->id);
          } else {

              $query = $query->whereIn('branch_id', $cc);
              
          }
          $prsRequests = $query->orderBy('id', 'DESC')->paginate($peritem);
          $prsRequests = $prsRequests->appends($request->query());
          $vendors = Vendor::with('Branch')->get();
          $vehicletype = VehicleType::select('id', 'name')->get();
          
  
          return view('prs.prs-request-list', ['peritem' => $peritem, 'prefix' => $this->prefix, 'prsRequests' => $prsRequests, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes, 'branchs' => $branchs, 'vendors' => $vendors,'vehicletype' => $vehicletype]);
  
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
        if($request->hrsAction == 1){
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
    }else{

           // ============ Request Rejected ================= //
           $prs_num = explode(',', $request->prs_no);
           PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['rejected_remarks' => $request->rejectedRemarks, 'payment_status' => 4 ]);
       
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
        // dd($request->transaction_id);

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
         // echo'<pre>'; print_r($request->all()); die;
         $authuser = Auth::user();
         $role_id = Role::where('id', '=', $authuser->role_id)->first();
         $cc = $authuser->branch_id;
         $user = $authuser->id;
         $bm_email = $authuser->email;
         $branch_name = Location::where('id', '=', $request->branch_id)->first();
 
         //deduct balance
         $deduct_balance = $request->payable_amount - $request->final_payable_amount ;
 
         $get_vehicle = PrsPaymentRequest::select('vehicle_no')->where('transaction_id', $request->transaction_id)->get();
         $sent_vehicle = array();
         foreach($get_vehicle as $vehicle){
               $sent_vehicle[] = $vehicle->vehicle_no;
         }
         $unique = array_unique($sent_vehicle);
         $sent_vehicle_no = implode(',', $unique);
         $url_header = $_SERVER['HTTP_HOST'];
         $prs = explode(',', $request->prs_no);
         if($authuser->is_payment == 0){
             
             $getadvanced = PrsPaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
             if (!empty($getadvanced->balance)) {
                 $balance = $getadvanced->balance - $request->payable_amount;
             } else {
                 $balance = 0;
             }
             $advance = $getadvanced->advanced + $request->payable_amount;
          
 
             PickupRunSheet::whereIn('pickup_id', $prs)->update(['payment_status' => 2]);
             
 
             PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance'=> $deduct_balance, 'current_paid_amt'=> $request->final_payable_amount ,'payment_status' => 2,'is_approve' => 0]);
 
             $new_response['success'] = true;
 
         }else{
 
         
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
 
                 PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance'=> $deduct_balance, 'current_paid_amt'=> $request->final_payable_amount ,'payment_status' => 2, 'is_approve' => 1]);
 
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
 
                 PrsPaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $request->payable_amount, 'balance' => $balance_amt, 'amt_without_tds' => $request->payable_amount, 'tds_deduct_balance'=> $deduct_balance, 'current_paid_amt'=> $request->final_payable_amount ,'payment_status' => 2]);
 
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

}