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
use App\Models\Location;
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

            $query = $query->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail');

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

            $prsdata = $query->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail')->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html =  view('prs.prs-list-ajax',['prefix'=>$this->prefix,'prsdata' => $prsdata,'peritem'=>$peritem])->render();
            
            return response()->json(['html' => $html]);
        }

        $prsdata = $query->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner','VehicleDetail','DriverDetail')->orderBy('id','DESC')->paginate($peritem);
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

            $query = $query->with('ConsignerDetail:id,nick_name,city');

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
        $query = $query->with('ConsignerDetail:id,nick_name,city');

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
            $query = $query->with('PrsDriverTasks','PrsDriverTasks.PrsTaskItems');

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

        $query = $query->with('PrsDriverTasks','PrsDriverTasks.PrsTaskItems');

        $vehiclereceives  = $query->whereNotIn('status',[3])->orderBy('id','ASC')->paginate($peritem);
        $vehiclereceives  = $vehiclereceives->appends($request->query());
        // echo "<pre>"; print_r($vehiclereceives); die;
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
                    }else{
                        $consignmentsave['branch_id'] = $authuser->branch_id;
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
        
        if ($request->ajax()) {
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            $query = $query->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail');

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

            $prsdata = $query->where('status',3)->with('PrsRegClients.RegClient','VehicleDetail','DriverDetail')->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html =  view('prs.prs-paymentlist-ajax',['prefix'=>$this->prefix,'prsdata' => $prsdata,'peritem'=>$peritem])->render();
            
            return response()->json(['html' => $html]);
        }

        $prsdata = $query->where('status',3)->with('PrsRegClients.RegClient', 'PrsRegClients.RegConsigner.Consigner','VehicleDetail','DriverDetail')->orderBy('id','DESC')->paginate($peritem);
        $prsdata = $prsdata->appends($request->query());
        
        return view('prs.prs-paymentlist', ['prsdata' => $prsdata, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
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

            if ($authuser->role_id == 1) {
                $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 6) {
                $query = $query->whereIn('base_clients.id', $baseclient);
            } elseif ($authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }

            $query = $query->where(['status'=> 5,'prsitem_status'=>0])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail','PrsDetail');

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
        
        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $query = $query->where(['status'=> 5,'prsitem_status'=>0])->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail','PrsDetail');

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('prs.pickupload-list',['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem,'branchs'=>$branchs]);
    }

}