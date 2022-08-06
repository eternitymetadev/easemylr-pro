<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaseClient;
use App\Models\RegionalClient;
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
                    $save_data['status'] = "1";
                    $saveregclients = RegionalClient::create($save_data);
                }
            }
            
            $url    =   URL::to($this->prefix.'/clients');
            $response['success'] = true;
            $response['success_message'] = "Clients Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'client-create';
            $response['redirect_url'] = $url;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created client please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    public function regionalClients(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = RegionalClient::query();
        $regclients = $query->orderby('id','DESC')->get();
        return view('clients.regional-client-list',['regclients'=>$regclients,'prefix'=>$this->prefix])->with('i', ($request->input('page', 1) - 1) * 5);
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
        try { 
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
            if(!empty($request->client_name)){
                $baseclient['client_name'] = $request->client_name;
            }
            $savebaseclient = BaseClient::where('id',$request->baseclient_id)->update($baseclient);
               
            if(!empty($request->data)){              
                $get_data = $request->data;
                foreach ($get_data as $key => $save_data ) {
                    if(!empty($save_data['hidden_id'])){
                        $updatedata['name'] = $save_data['name'];
                        $updatedata['baseclient_id'] = (int)$request->baseclient_id;
                        $updatedata['status'] = "1";
                        $hidden_id=$save_data['hidden_id'];
                        unset($save_data['hidden_id']);
                        $saveregclients = RegionalClient::where('id',$hidden_id)->update($updatedata);
                    }else{
                        $insertdata['name'] = $save_data['name'];
                        $insertdata['baseclient_id'] = (int)$request->baseclient_id;
                        $insertdata['location_id'] = $save_data['location_id'];
                        $insertdata['status'] = "1";
                        unset($save_data['hidden_id']);
                        $saveregclients = RegionalClient::create($insertdata);
                    }
                    
                    // if($save_data['isRegionalClientNull']==1){
                    //     $save_data['baseclient_id'] = $request->baseclient_id;
                    //     unset($save_data['hidden_id']);
                    //     unset($save_data['isRegionalClientNull']);
                    //     $saveregclients = RegionalClient::create($save_data);
                    // }else{
                        // $save_data['baseclient_id'] = $request->baseclient_id;
                        // $hidden_id=$save_data['hidden_id'];
                        // unset($save_data['hidden_id']);
                        // unset($save_data['isRegionalClientNull']);
                        // $saveregclients = RegionalClient::where('id',$hidden_id)->update($save_data);
                    // }
                }
                // dd($data);
                // RegionalClient::updateOrcreate($data);
            }
            $url  =  URL::to($this->prefix.'/clients');
            $response['page'] = 'client-update';
            $response['success'] = true;
            $response['success_message'] = "Client Updated Successfully";
            $response['error'] = false;
            $response['redirect_url'] = $url; 
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
}
