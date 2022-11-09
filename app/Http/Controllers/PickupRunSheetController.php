<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PickupRunSheet;
use App\Models\PrsDrivertask;
use App\Models\PrsTaskItem;
use App\Models\RegionalClient;
use App\Models\Consigner;
use App\Models\Role;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleType;
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
        $this->title = "Consignments";
        $this->segment = \Request::segment(2);
        $this->apikey = \Config::get('keys.api');
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

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
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

            $prsdata = $query->with('RegClient')->orderBy('id', 'DESC')->paginate($peritem);
            $prsdata = $prsdata->appends($request->query());

            $html =  view('prs.prs-list-ajax',['prefix'=>$this->prefix,'prsdata' => $prsdata,'peritem'=>$peritem])->render();
            

            return response()->json(['html' => $html]);
        }

        $prsdata = $query->with('VehicleDetail','DriverDetail')->orderBy('id','DESC')->paginate($peritem);
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

        if($authuser->role_id !=1){
            if($authuser->role_id ==2 || $role_id->id ==3){
                $regclients = RegionalClient::whereIn('location_id',$cc)->orderby('name','ASC')->get();
                $consigners = Consigner::whereIn('branch_id',$cc)->orderby('nick_name','ASC')->pluck('nick_name','id');
            }else{
                $regclients = RegionalClient::whereIn('id',$regclient)->orderby('name','ASC')->get();
                $consigners = Consigner::whereIn('regionalclient_id',$regclient)->orderby('nick_name','ASC')->pluck('nick_name','id');
            }
        }else{
            $regclients = RegionalClient::where('status',1)->orderby('name','ASC')->get();
            $consigners = Consigner::where('status',1)->orderby('nick_name','ASC')->pluck('nick_name','id');
        }
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();

        return view('prs.create-prs',['prefix'=>$this->prefix, 'regclients'=>$regclients, 'consigners'=>$consigners, 'vehicletypes'=>$vehicletypes, 'vehicles'=>$vehicles, 'drivers'=>$drivers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

        if(!empty($request->regclient_id)){
            $regclients = $request->regclient_id;
            $prssave['regclient_id'] = implode(',', $regclients);
        }

        if(!empty($request->consigner_id)){
            $consigners = $request->consigner_id;
            $prssave['consigner_id'] = implode(',', $consigners);
        }
        
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

        $prssave['user_id'] = $authuser->id;
        $prssave['branch_id'] = $authuser->branch_id;
        
        $prssave['status'] = "1";
        
        $saveprs = PickupRunSheet::create($prssave);

        if($saveprs)
        {
            $consigners = $saveprs->consigner_id;
            $consinger_ids  = explode(',',$consigners);
            // $consigner_count = count($consinger_ids);
            foreach($consinger_ids as $consigner){
                $prstask['prs_date'] = $saveprs->prs_date;
                $prstask['prs_id'] = $saveprs->id;
                $prstask['prsconsigner_id'] = $consigner;
                $prstask['status'] = "1";
                $saveprsdrivertasks = PrsDrivertask::create($prstask);
            }

            $url    =   URL::to($this->prefix.'/prs');
            $response['success'] = true;
            $response['success_message'] = "PRS Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'prs-create';
            $response['redirect_url'] = $url;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created PRS please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    public function driverTasks(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = PrsDrivertask::query();
        
        if ($request->ajax()) {
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                            $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('ConsigneeDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($cneequery)use($search,$searchT) {
                            $cneequery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    });

                });
            }

            $query = $query->with('ConsignerDetail:id,nick_name,city');

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }

            $drivertasks = $query->orderBy('id', 'ASC')->paginate($peritem);
            $drivertasks = $prsdata->appends($request->query());

            $html =  view('prs.driver-task-list-ajax',['prefix'=>$this->prefix,'drivertasks' => $drivertasks,'peritem'=>$peritem])->render();
            

            return response()->json(['html' => $html]);
        }

        $query = $query->with('ConsignerDetail:id,nick_name,city');

        $drivertasks  = $query->orderBy('id','ASC')->paginate($peritem);
        $drivertasks  = $drivertasks->appends($request->query());
        
        return view('prs.driver-task-list', ['drivertasks' => $drivertasks, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);

    }

    
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

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                            $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('ConsigneeDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($cneequery)use($search,$searchT) {
                            $cneequery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    });

                });
            }

            $query = $query->with('PrsDriverTasks,VehicleDetail');

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }

            $vehiclereceives = $query->orderBy('id', 'ASC')->paginate($peritem);
            $vehiclereceives = $prsdata->appends($request->query());
            
            $html =  view('prs.vehicle-receivegate-list-ajax',['prefix'=>$this->prefix,'vehiclereceives' => $vehiclereceives,'peritem'=>$peritem])->render();
            

            return response()->json(['html' => $html]);
        }

        $query = $query->with('PrsDriverTasks.PrsTaskItems');

        $vehiclereceives  = $query->orderBy('id','ASC')->paginate($peritem);
        $vehiclereceives  = $vehiclereceives->appends($request->query());
        
        return view('prs.vehicle-receivegate-list', ['vehiclereceives' => $vehiclereceives, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }

    public function createTaskItem(Request $request)
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

        // insert prs driver task items
        if (!empty($request->data)) {
            $get_data = $request->data;
            foreach ($get_data as $key => $save_data) {
                $save_data['drivertask_id'] = $request->drivertask_id;
                $save_data['status'] = 1;
                $save_data['user_id'] = $authuser->id;
                $save_data['branch_id'] = $authuser->branch_id;
                $savetaskitems = PrsTaskItem::create($save_data);

                if($savetaskitems){
                    PrsDrivertask::where('id', $request->drivertask_id)->update(['status' => 2]);

                    $countdrivertask_id = PrsDrivertask::where('prs_id', $request->prs_id)->count();
                    $countdrivertask_status = PrsDrivertask::where('status',2)->count();
                    // dd($countdrivertask_id);
                    if($countdrivertask_id == $countdrivertask_status){
                        PickupRunSheet::where('id', $request->prs_id)->update(['status' => 3]);
                    }

                    $url    =   URL::to($this->prefix.'/driver-tasks');
                    $response['success'] = true;
                    $response['success_message'] = "PRS task item Added successfully";
                    $response['error'] = false;
                    // $response['resetform'] = true;
                    $response['page'] = 'create-prstaskitem';
                    $response['redirect_url'] = $url;
                }else{
                    $response['success'] = false;
                    $response['error_message'] = "Can not created PRS task item please try again";
                    $response['error'] = true;
                }
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
    public function edit($id)
    {
        //
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
    public function getConsigner(Request $request){
        $getconsigners = Consigner::select('id','nick_name')->whereIn('regionalclient_id', $request->regclient_id)->get();

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

}
