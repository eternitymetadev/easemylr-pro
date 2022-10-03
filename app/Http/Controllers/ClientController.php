<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaseClient;
use App\Models\RegionalClient;
use App\Models\RegionalClientDetail;
use App\Models\ClientPriceDetail;
use App\Models\ConsignmentNote;
use App\Models\Zone;
use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientReportExport;
use Session;
use Config;
use Auth;
use DB;
use URL;
use Helper;
use Hash;
use Crypt;
use Validator;
use Illuminate\Support\Arr;

class ClientController extends Controller
{
    public $prefix;
    public $title;

    public function __construct()
    {
      $this->title =  "Clients";
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
        $query = BaseClient::query();
        $clients = $query->orderby('id','DESC')->get();
        return view('clients.client-list',['clients'=>$clients,'prefix'=>$this->prefix,'title'=>$this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function clientList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = BaseClient::query();
        $clients = $query->with('RegClients.Location')->orderby('id','DESC')->get();
        return view('clients.client-listpro',['clients'=>$clients,'prefix'=>$this->prefix,'title'=>$this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $this->pagetitle =  "Create";
        $locations = Helper::getLocations();

        return view('clients.create-client',['locations'=>$locations, 'prefix'=>$this->prefix, 'title'=>$this->title, 'pagetitle'=>$this->pagetitle]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'client_name' => 'required|unique:base_clients,client_name',
                // 'name' => 'required|unique:regional_clients,name',
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
            if(!empty($request->client_name)){
                $client['client_name']   = $request->client_name;
            }
            $client['status']     = "1";

            $saveclient = BaseClient::create($client); 
            $data = $request->all();

            if($saveclient)
            {
                if(!empty($request->data)){ 
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data ) { 
                        $save_data['baseclient_id'] = $saveclient->id;
                        $save_data['location_id'] = $save_data['location_id'];
                        $save_data['is_multiple_invoice'] = $save_data['is_multiple_invoice'];
                        $save_data['status'] = "1";
                        $saveregclients = RegionalClient::create($save_data);
                    }
                }
                
                $url    =   URL::to($this->prefix.'/clients');
                $response['success'] = true;
                $response['success_message'] = "Clients Added successfully";
                $response['error'] = false;
                $response['page'] = 'client-create';
                $response['redirect_url'] = $url;
            }else{
                $response['success'] = false;
                $response['error_message'] = "Can not created client please try again";
                $response['error'] = true;
            }
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);
    }

    public function regionalClients(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = RegionalClient::query();
        $regclients = $query->orderby('id','DESC')->get();
        return view('clients.regional-client-list',['regclients'=>$regclients,'prefix'=>$this->prefix, 'segment'=>$this->segment])->with('i', ($request->input('page', 1) - 1) * 5);
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
        $this->prefix = request()->route()->getPrefix();
        $this->pagetitle =  "Update";
        $id = decrypt($id); 
        $locations = Helper::getLocations();
        $getRegclients = RegionalClient::where('baseclient_id',$id)->get();
        $getClient = BaseClient::where('id',$id)->with('RegClients')->first();

        return view('clients.update-client')->with(['prefix'=>$this->prefix,'pagetitle'=>$this->pagetitle,'getClient'=>$getClient,'getRegclients'=>$getRegclients,'locations'=>$locations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function UpdateClient(Request $request)
    {
        // echo'<pre>'; print_r($request->all()); die;
        try { 
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name' => 'required',
                'client_name' => 'required',
            );
            $validator = Validator::make($request->all(),$rules);

            if($validator->fails())
            {
                $errors                 = $validator->errors();
                $response['success']    = false;
                $response['formErrors'] = true;
                $response['errors']     = $errors;
                return response()->json($response);
            }
            $checkbaseclientexist  = BaseClient::where('client_name','=',$request->client_name)
                    ->where('id','!=',$request->baseclient_id)
                    ->get();

            if(!$checkbaseclientexist->isEmpty()){
                $response['success'] = false;
                $response['error_message'] = "Base Client name already exists.";
                $response['baseclientupdateduplicate_error'] = true; 
                return response()->json($response);
            }
            $savebaseclient = BaseClient::where('id',$request->baseclient_id)->update(['client_name' => $request->client_name]);         
            
            if(!empty($request->data)){
                $get_data = $request->data;
                foreach ($get_data as $key => $save_data ) {
                    if(!empty($save_data['hidden_id'])){
                        $updatedata['baseclient_id'] = $request->baseclient_id;
                        $updatedata['status'] = "1";
                        $updatedata['name'] = $save_data['name'];
                        $updatedata['location_id'] = $save_data['location_id'];
                        $updatedata['is_multiple_invoice'] = $save_data['is_multiple_invoice'];
                        $hidden_id = $save_data['hidden_id'];                      
                        $saveregclients = RegionalClient::where('id',$hidden_id)->update($updatedata);
                      
                    }else{
                        $insertdata['baseclient_id'] = $request->baseclient_id;
                        $insertdata['location_id'] = $save_data['location_id'];
                        $insertdata['name'] = $save_data['name'];
                        $insertdata['is_multiple_invoice'] = $save_data['is_multiple_invoice'];
                        $insertdata['status'] = "1";
                        unset($save_data['hidden_id']);
                        $saveregclients = RegionalClient::create($insertdata);
                    }
                }
                $url  =  URL::to($this->prefix.'/clients');
                $response['page'] = 'client-update';
                $response['success'] = true;
                $response['success_message'] = "Client Updated Successfully";
                $response['error'] = false;
                $response['redirect_url'] = $url;
            }else{
                $response['success'] = false;
                $response['error_message'] = "Can not updated client please try again";
                $response['error'] = true;
            }

            DB::commit();
        }catch(Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false; 
        }

        return response()->json($response);
    }

    public function deleteClient(Request $request)
    {
        RegionalClient::where('id',$request->regclient_id)->delete();

        $response['success']         = true;
        $response['success_message'] = 'Regional Client deleted successfully';
        $response['error']           = false;
        return response()->json($response);
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
    public function createRegclientdetail(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = $request->id;
        $id = decrypt($id);
        $regclient_name = RegionalClient::where('id',$id)->select('id','name')->first();
        $zonestates = Zone::all()->unique('state')->pluck('state','id');
        
        return view('clients.add-regclientdetails',['prefix'=>$this->prefix,'zonestates'=>$zonestates,'regclient_name'=>$regclient_name]);
    }

    public function storeRegclientdetail(Request $request)
    {
        try{
            DB::beginTransaction();
            
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'client_name' => 'required|unique:base_clients,client_name',
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
            if(!empty($request->regclient_id)){
                $client['regclient_id']   = $request->regclient_id;
            }
            if(!empty($request->docket_price)){
                $client['docket_price']   = $request->docket_price;
            }
            $client['status']     = "1";

            $saveclient = RegionalClientDetail::create($client); 

            $data = $request->all();
            if($saveclient)
            {
                if(!empty($request->data)){ 
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data ) { 
                        $save_data['regclientdetail_id'] = $saveclient->id;
                        $save_data['status'] = "1";
                        $saveregclients = ClientPriceDetail::create($save_data);
                    }
                }
                
                $url    =   URL::to($this->prefix.'/reginal-clients');
                $response['success'] = true;
                $response['success_message'] = "Clients detail added successfully";
                $response['error'] = false;
                $response['page'] = 'client-create';
                $response['redirect_url'] = $url;
            }else{
                $response['success'] = false;
                $response['error_message'] = "Can not created client detail please try again";
                $response['error'] = true;
            }
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);
    }

    public function viewRegclientdetail($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);   
        $getClientDetail = RegionalClientDetail::where('regclient_id',$id)->with('RegClient','ClientPriceDetails')->first();
        
        return view('clients.view-regclient',['prefix'=>$this->prefix,'getClientDetail'=>$getClientDetail]);
    }

    public function editRegClientDetail($id){
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);

        $regclient_name = RegionalClient::where('id',$id)->select('id','name')->first();
        $zonestates = Zone::all()->unique('state')->pluck('state','id');
        $getClientDetail = RegionalClientDetail::where('regclient_id',$id)->with('RegClient','ClientPriceDetails')->first();
        
        return view('clients.update-regclientdetails',['prefix'=>$this->prefix,'zonestates'=>$zonestates,'regclient_name'=>$regclient_name, 'getClientDetail'=>$getClientDetail]);
    }

    //nurture client report
    public function clientReport(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        // $lastsevendays = \Carbon\Carbon::today()->subDays(7);
        // $date = Helper::yearmonthdate($lastsevendays);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $sessionperitem = Session::get('peritem');
        if(!empty($sessionperitem)){
            $perpage = $sessionperitem;
        }else{
            $perpage = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();
        $query = $query
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
                'VehicleType:id,name');

        if($request->ajax()){
            $query = ConsignmentNote::query();
            $query = $query
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
                    'VehicleType:id,name');

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
        
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $perpage = $peritem;
            }else{
                $perpage = Config::get('variable.PER_PAGE');
            }

            $startdate = $request->startdate;
            $enddate = $request->enddate;

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

            if(!empty($request->regclient)){
                $search = $request->regclient;
                $query = $query->where('regclient_id',$search);
            }
            
            if(isset($startdate) && isset($enddate)){
                // DB::enableQueryLog();
                $consignments = $query->whereBetween('consignment_date',[$startdate,$enddate])->orderby('created_at','DESC')->paginate($perpage);
                // dd(DB::getQueryLog());
            }else {
                $consignments = $query->orderBy('id','DESC')->paginate($perpage);
            }
            
            $html =  view('clients.client-report-ajax',['prefix'=>$this->prefix,'consignments' => $consignments,'perpage'=>$perpage])->render();

            return response()->json(['html' => $html]);
        }

        $regionalclients = RegionalClient::select('id','name','location_id')->get();
        $consignments = $query->orderBy('id','DESC')->paginate($perpage);
        
        return view('clients.client-report', ['consignments' => $consignments, 'regionalclients'=>$regionalclients, 'perpage'=>$perpage, 'prefix' => $this->prefix]);

    }

    public function getConsignmentClient(Request $request)
    {
        $getconsignments = ConsignmentNote::where('regclient_id',$request->regclient_id)->get();
        if($getconsignments)
        {
            $response['success'] = true;
            $response['error']   = false;
            $response['success_message'] = "Consignment data fetch successfully";
            $response['data_consignments'] = $getconsignments;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not fetch data please try again";
            $response['error'] = true;
        }

        return response()->json($response);
    }

    public function clientReportExport(Request $request)
    {
        return Excel::download(new ClientReportExport($request->regclient,$request->startdate,$request->enddate,$request->search), 'client_reports.csv');
    }


}
