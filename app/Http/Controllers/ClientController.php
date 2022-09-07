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
        // dd($regclient_name);
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

    //nurture client report
    public function clientReport()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $regionalclients = RegionalClient::get();

        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);

        $query = ConsignmentNote::query();

        // if ($request->ajax()) {
        //     if(!empty($request->search)){
        //         $search = $request->search; 
        //         $query->where(function ($query)use($search) {
        //               $query->where('name', 'like', '%' . $search . '%')
        //                     ->orWhereHas('Country', function ($countryquery) use ($search) {
        //                       $countryquery->where('name', 'like', '%' . $search . '%');
        //                     })
        //                     ->orWhereHas('Region', function ($regionquery) use ($search) {
        //                       $regionquery->where('name', 'like', '%' . $search . '%');
        //                     })
        //                     ->orWhere('website', 'like', '%' . $search . '%');
        //         });
        //     }
        // }

        // if($authuser->role_id !=1){
        //     if($authuser->role_id == 4){
        //         $query = $query->where('user_id', $authuser->id)->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->orderBy('id','DESC')->get();         
        //     }else{ 
        //         $query = $query->whereIn('branch_id', $cc)->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->orderBy('id','DESC')->get();
        //     }
        // } else {
            $query = $query->with('ConsignmentItems', 'ConsignerDetail.GetState', 'ConsigneeDetail.GetState', 'ShiptoDetail.GetState', 'VehicleDetail', 'DriverDetail')->orderBy('id','DESC')->get();
        // }
        
        $consignments = json_decode(json_encode($query), true);
        // echo "<pre>"; print_r($consignments); die;
        return view('clients.client-report', ['consignments' => $consignments, 'regionalclients'=>$regionalclients, 'prefix' => $this->prefix]);

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
            // $response['redirect_url'] = URL::to($this->prefix.'/consigners');
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not fetch data please try again";
            $response['error'] = true;
        }

        return response()->json($response);
    }


}
