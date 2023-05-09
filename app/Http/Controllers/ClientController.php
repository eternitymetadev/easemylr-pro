<?php

namespace App\Http\Controllers;

use App\Exports\ClientReportExport;
use App\Models\BaseClient;
use App\Models\ClientPriceDetail;
use App\Models\ConsignmentNote;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\RegionalClientDetail;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Auth;
use Config;
use DB;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use URL;
use Validator;

class ClientController extends Controller
{
    public $prefix;
    public $title;

    public function __construct()
    {
        $this->title = "Clients";
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
        $clients = $query->orderby('id', 'DESC')->get();
        return view('clients.client-list', ['clients' => $clients, 'prefix' => $this->prefix, 'title' => $this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function clientList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = BaseClient::query();
        $clients = $query->with('RegClients.Location')->orderby('id', 'DESC')->get();
        return view('clients.client-listpro', ['clients' => $clients, 'prefix' => $this->prefix, 'title' => $this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $this->pagetitle = "Create";
        $locations = Helper::getLocations();

        return view('clients.create-client', ['locations' => $locations, 'prefix' => $this->prefix, 'title' => $this->title, 'pagetitle' => $this->pagetitle]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'client_name' => 'required|unique:base_clients,client_name',
                // 'name' => 'required|unique:regional_clients,name',
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
            if (!empty($request->client_name)) {
                $client['client_name'] = $request->client_name;
            }

            // ======= gst upload
            $gstupload = $request->file('upload_gst');
            $path = Storage::disk('s3')->put('clients', $gstupload);
            $gst_img_path_save = Storage::disk('s3')->url($path);

            //  ======= pan upload
            $panupload = $request->file('upload_pan');
            $pan_path = Storage::disk('s3')->put('clients', $panupload);
            $pan_img_path_save = Storage::disk('s3')->url($pan_path);

            // // ======= tan upload
            $tanupload = $request->file('upload_tan');
            $tan_path = Storage::disk('s3')->put('clients', $tanupload);
            $tan_img_path_save = Storage::disk('s3')->url($tan_path);

            // // ======= moa upload
            $moaupload = $request->file('upload_moa');
            $moa_path = Storage::disk('s3')->put('clients', $moaupload);
            $moa_img_path_save = Storage::disk('s3')->url($moa_path);

            $client['tan'] = $request->tan;
            $client['gst_no'] = $request->gst_no;
            $client['pan'] = $request->pan;
            $client['upload_gst'] = $gst_img_path_save;
            $client['upload_pan'] = $pan_img_path_save;
            $client['upload_tan'] = $tan_img_path_save;
            $client['upload_moa'] = $moa_img_path_save;
            $client['status'] = "1";

            $saveclient = BaseClient::create($client);
            // $data = $request->all();

            // if($saveclient)
            // {
            //     if(!empty($request->data)){
            //         $get_data = $request->data;
            //         foreach ($get_data as $key => $save_data ) {
            //             $save_data['baseclient_id'] = $saveclient->id;
            //             $save_data['location_id'] = $save_data['location_id'];
            //             $save_data['is_multiple_invoice'] = $save_data['is_multiple_invoice'];
            //             $save_data['is_prs_pickup'] = $save_data['is_prs_pickup'];
            //             $save_data['email'] = $save_data['email'];
            //             $save_data['is_email_sent'] = $save_data['is_email_sent'];
            //             $save_data['status'] = "1";
            //             $saveregclients = RegionalClient::create($save_data);
            //         }
            //     }

            $url = URL::to($this->prefix . '/clients');
            $response['success'] = true;
            $response['success_message'] = "Clients Added successfully";
            $response['error'] = false;
            $response['page'] = 'client-create';
            $response['redirect_url'] = $url;
            // }else{
            //     $response['success'] = false;
            //     $response['error_message'] = "Can not created client please try again";
            //     $response['error'] = true;
            // }
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
        $regclients = $query->with('Location')->orderby('id', 'DESC')->get();
        return view('clients.regional-client-list', ['regclients' => $regclients, 'prefix' => $this->prefix, 'segment' => $this->segment])->with('i', ($request->input('page', 1) - 1) * 5);
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
        $this->pagetitle = "Update";
        $id = decrypt($id);
        $locations = Helper::getLocations();
        $getRegclients = RegionalClient::where('baseclient_id', $id)->get();
        $getClient = BaseClient::where('id', $id)->with('RegClients')->first();

        return view('clients.update-client')->with(['prefix' => $this->prefix, 'pagetitle' => $this->pagetitle, 'getClient' => $getClient, 'getRegclients' => $getRegclients, 'locations' => $locations]);
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
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name' => 'required',
                'client_name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }
            $checkbaseclientexist = BaseClient::where('client_name', '=', $request->client_name)
                ->where('id', '!=', $request->baseclient_id)
                ->get();

            if (!$checkbaseclientexist->isEmpty()) {
                $response['success'] = false;
                $response['error_message'] = "Base Client name already exists.";
                $response['baseclientupdateduplicate_error'] = true;
                return response()->json($response);
            }
            $savebaseclient = BaseClient::where('id', $request->baseclient_id)->update(['client_name' => $request->client_name]);

            if (!empty($request->data)) {
                $get_data = $request->data;
                foreach ($get_data as $key => $save_data) {
                    if (!empty($save_data['hidden_id'])) {
                        $updatedata['baseclient_id'] = $request->baseclient_id;
                        $updatedata['status'] = "1";
                        $updatedata['name'] = $save_data['name'];
                        $updatedata['location_id'] = $save_data['location_id'];
                        $updatedata['is_multiple_invoice'] = $save_data['is_multiple_invoice'];
                        $updatedata['is_prs_pickup'] = $save_data['is_prs_pickup'];
                        $updatedata['email'] = $save_data['email'];
                        $updatedata['is_email_sent'] = $save_data['is_email_sent'];
                        $hidden_id = $save_data['hidden_id'];
                        $saveregclients = RegionalClient::where('id', $hidden_id)->update($updatedata);

                    } else {
                        $insertdata['baseclient_id'] = $request->baseclient_id;
                        $insertdata['location_id'] = $save_data['location_id'];
                        $insertdata['name'] = $save_data['name'];
                        $insertdata['is_multiple_invoice'] = $save_data['is_multiple_invoice'];
                        $insertdata['is_prs_pickup'] = $save_data['is_prs_pickup'];
                        $insertdata['email'] = $save_data['email'];
                        $insertdata['is_email_sent'] = $save_data['is_email_sent'];
                        $insertdata['status'] = "1";
                        unset($save_data['hidden_id']);
                        $saveregclients = RegionalClient::create($insertdata);
                    }
                }
                $url = URL::to($this->prefix . '/clients');
                $response['page'] = 'client-update';
                $response['success'] = true;
                $response['success_message'] = "Client Updated Successfully";
                $response['error'] = false;
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not updated client please try again";
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

    public function deleteClient(Request $request)
    {
        RegionalClient::where('id', $request->regclient_id)->delete();

        $response['success'] = true;
        $response['success_message'] = 'Regional Client deleted successfully';
        $response['error'] = false;
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
        $regclient_name = RegionalClient::where('id', $id)->select('id', 'name')->first();
        $zonestates = Zone::all()->unique('state')->pluck('state', 'id');

        return view('clients.add-regclientdetails', ['prefix' => $this->prefix, 'zonestates' => $zonestates, 'regclient_name' => $regclient_name]);
    }

    public function storeRegclientdetail(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'data.*.from_state.to_state' => 'distinct',
                // 'client_name' => 'required|unique:base_clients,client_name',
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

            // echo "<pre>"; print_r($request->all());die;
            // $check_fromtostate = ClientPriceDetail::where('regclientdetail_id'=>$request->regclient_id)->whereIn('id','!=',$request->consigner_id)->get();

            // if(!$check_fromtostate->isEmpty()){
            //     $response['success'] = false;
            //     $response['error_message'] = "From and To state already exists.";
            //     $response['fromto_state_error'] = true;
            //     return response()->json($response);
            // }

            if (!empty($request->regclient_id)) {
                $client['regclient_id'] = $request->regclient_id;
            }
            if (!empty($request->docket_price)) {
                $client['docket_price'] = $request->docket_price;
            }
            $client['status'] = "1";

            $saveclient = RegionalClientDetail::create($client);

            $data = $request->all();
            if ($saveclient) {
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
                        $save_data['regclientdetail_id'] = $saveclient->id;
                        $save_data['status'] = "1";
                        $saveregclients = ClientPriceDetail::create($save_data);
                    }
                }

                $url = URL::to($this->prefix . '/reginal-clients');
                $response['success'] = true;
                $response['success_message'] = "Clients detail added successfully";
                $response['error'] = false;
                $response['page'] = 'clientdetail-create';
                $response['redirect_url'] = $url;
            } else {
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
        $getClientDetail = RegionalClientDetail::where('regclient_id', $id)->with('RegClient', 'ClientPriceDetails')->first();

        return view('clients.view-regclient', ['prefix' => $this->prefix, 'getClientDetail' => $getClientDetail]);
    }

    public function editRegClientDetail($id)
    {
        
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $regclient_name = RegionalClient::where('id', $id)->first();
        $zonestates = Zone::all()->unique('state')->pluck('state', 'id');
        $locations = Location::all();
        $base_clients = BaseClient::all();



        return view('clients.update-regclientdetails', ['prefix' => $this->prefix, 'zonestates' => $zonestates, 'regclient_name' => $regclient_name, 'locations' => $locations, 'base_clients' => $base_clients]);
    }

    public function updateRegclientdetail(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //   'client_name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }

            // $payment_term = implode(',', $request->payment_term);

            $regionalupdate['baseclient_id'] = $request->base_client_id;
            $regionalupdate['name'] = $request->name;
            $regionalupdate['regional_client_nick_name'] = $request->regional_client_nick_name;
            $regionalupdate['email'] = $request->email;
            $regionalupdate['phone'] = $request->phone;
            $regionalupdate['gst_no'] = $request->gst_no;
            $regionalupdate['pan'] = $request->pan;
            $regionalupdate['is_multiple_invoice'] = $request->is_multiple_invoice;
            $regionalupdate['is_prs_pickup'] = $request->is_prs_pickup;
            $regionalupdate['is_email_sent'] = $request->is_email_sent;
            $regionalupdate['location_id'] = $request->branch_id;
            // $regionalupdate['upload_gst'] = $gst_img_path_save;
            // $regionalupdate['upload_pan'] = $pan_img_path_save;
            // $regionalupdate['payment_term'] = $payment_term;
            // $regionalupdate['status']       = $request->status;

            RegionalClient::where('id', $request->regclientdetail_id)->update($regionalupdate);
            $url = URL::to($this->prefix . '/reginal-clients');

            $response['success'] = true;
            $response['success_message'] = "Regional Client Updated Successfully";
            $response['error'] = false;
            $response['page'] = 'clientdetail-update';
            $response['redirect_url'] = $url;


            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }

        return response()->json($response);
    }

    // public function updateRegclientdetail(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $this->prefix = request()->route()->getPrefix();
    //         $rules = array(
    //             //   'client_name' => 'required',
    //         );
    //         $validator = Validator::make($request->all(), $rules);

    //         if ($validator->fails()) {
    //             $errors = $validator->errors();
    //             $response['success'] = false;
    //             $response['formErrors'] = true;
    //             $response['errors'] = $errors;
    //             return response()->json($response);
    //         }

    //         $saveClientDetail = RegionalClientDetail::where('id', $request->regclientdetail_id)->update(['docket_price' => $request->docket_price]);

    //         if (!empty($request->data)) {
    //             $get_data = $request->data;

    //             foreach ($get_data as $key => $save_data) {
    //                 if (!empty($save_data['hidden_id'])) {
    //                     $updatedata['regclientdetail_id'] = $request->regclientdetail_id;
    //                     $updatedata['status'] = "1";
    //                     $updatedata['from_state'] = $save_data['from_state'];
    //                     $updatedata['to_state'] = $save_data['to_state'];
    //                     $updatedata['price_per_kg'] = $save_data['price_per_kg'];
    //                     $updatedata['open_delivery_price'] = $save_data['open_delivery_price'];
    //                     $hidden_id = $save_data['hidden_id'];
    //                     $saveregclients = ClientPriceDetail::where('id', $hidden_id)->update($updatedata);
    //                 } else {
    //                     $insertdata['regclientdetail_id'] = $request->regclientdetail_id;
    //                     $insertdata['status'] = "1";
    //                     $insertdata['from_state'] = $save_data['from_state'];
    //                     $insertdata['to_state'] = $save_data['to_state'];
    //                     $insertdata['price_per_kg'] = $save_data['price_per_kg'];
    //                     $insertdata['open_delivery_price'] = $save_data['open_delivery_price'];
    //                     unset($save_data['hidden_id']);
    //                     $saveclientPriceDeatil = ClientPriceDetail::create($insertdata);
    //                 }
    //             }
    //             $url = URL::to($this->prefix . '/reginal-clients');
    //             $response['page'] = 'clientdetail-update';
    //             $response['success'] = true;
    //             $response['success_message'] = "Client detail Updated Successfully";
    //             $response['error'] = false;
    //             $response['redirect_url'] = $url;
    //         } else {
    //             $response['success'] = false;
    //             $response['error_message'] = "Can not updated client detial please try again";
    //             $response['error'] = true;
    //         }

    //         DB::commit();
    //     } catch (Exception $e) {
    //         $response['error'] = false;
    //         $response['error_message'] = $e;
    //         $response['success'] = false;
    //         $response['redirect_url'] = $url;
    //     }

    //     return response()->json($response);
    // }

    //nurture client report
    public function clientReport(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $sessionperitem = Session::get('peritem');
        if (!empty($sessionperitem)) {
            $peritem = $sessionperitem;
        } else {
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query
                ->where('status', '!=', 5)
                ->with(
                    'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
                );

            if ($request->peritem) {
                Session::put('peritem', $request->peritem);
            }

            $peritem = Session::get('peritem');
            if (!empty($peritem)) {
                $peritem = $peritem;
            } else {
                $peritem = Config::get('variable.PER_PAGE');
            }

            if (!empty($request->regclient)) {
                $search = $request->regclient;
                $query = $query->where('regclient_id', $search);
            }

            $startdate = $request->startdate;
            $enddate = $request->enddate;

            if (isset($startdate) && isset($enddate)) {
                $consignments = $query->whereBetween('consignment_date', [$startdate, $enddate])->orderby('created_at', 'DESC')->paginate($peritem);
            } else {
                $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            }
            $consignments = $consignments->appends($request->query());

            $html = view('clients.client-report-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }

        $query = $query
            ->where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
            );

        $regionalclients = RegionalClient::select('id', 'name', 'location_id')->get();

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('clients.client-report', ['consignments' => $consignments, 'regionalclients' => $regionalclients, 'peritem' => $peritem, 'prefix' => $this->prefix]);
    }

    public function getConsignmentClient(Request $request)
    {
        $getconsignments = ConsignmentNote::where('regclient_id', $request->regclient_id)->get();
        if ($getconsignments) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = "Consignment data fetch successfully";
            $response['data_consignments'] = $getconsignments;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch data please try again";
            $response['error'] = true;
        }

        return response()->json($response);
    }

    public function clientReportExport(Request $request)
    {
        return Excel::download(new ClientReportExport($request->regclient, $request->startdate, $request->enddate), 'nurtureclient_reports.csv');
    }
/////////
    public function createRegionalClient()
    {
        $this->prefix = request()->route()->getPrefix();
        $locations = Location::all();
        $base_clients = BaseClient::all();

        return view('clients.create-regional_client', ['locations' => $locations, 'prefix' => $this->prefix, 'title' => $this->title, 'base_clients' => $base_clients]);
    }

    public function generateRegionalName(Request $request)
    {

        $getBaseClient = BaseClient::where('id', $request->base_client)->first();
        $getlocation = Location::where('id', $request->branch_id)->first();

        $generate_regional = $getBaseClient->client_name . '-' . $getlocation->name;

        $response['success'] = true;
        $response['generate_regional'] = $generate_regional;

        return response()->json($response);

    }
    public function updateGenerateRegionalName(Request $request)
    {

        $getBaseClient = BaseClient::where('id', $request->base_client)->first();
        $getlocation = Location::where('id', $request->branch_id)->first();

        $generate_regional = $getBaseClient->client_name . '-' . $getlocation->name;

        $response['success'] = true;
        $response['generate_regional'] = $generate_regional;

        return response()->json($response);

    }

    public function storeRegionalClient(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'client_name' => 'required|unique:base_clients,client_name',
                'name' => 'required|unique:regional_clients,name',
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

            // ======= gst upload
            $gstupload = $request->file('upload_gst');
            $path = Storage::disk('s3')->put('clients', $gstupload);
            $gst_img_path_save = Storage::disk('s3')->url($path);

            //  ======= pan upload
            $panupload = $request->file('upload_pan');
            $pan_path = Storage::disk('s3')->put('clients', $panupload);
            $pan_img_path_save = Storage::disk('s3')->url($pan_path);

            // $payment_term = implode(',', $request->payment_term);

            $client['baseclient_id'] = $request->base_client_id;
            $client['name'] = $request->name;
            $client['regional_client_nick_name'] = $request->regional_client_nick_name;
            $client['email'] = $request->email;
            $client['phone'] = $request->phone;
            $client['gst_no'] = $request->gst_no;
            $client['pan'] = $request->pan;
            $client['is_multiple_invoice'] = $request->is_multiple_invoice;
            $client['is_prs_pickup'] = $request->is_prs_pickup;
            $client['is_email_sent'] = $request->is_email_sent;
            $client['location_id'] = $request->branch_id;
            $client['upload_gst'] = $gst_img_path_save;
            $client['upload_pan'] = $pan_img_path_save;
            // $client['payment_term'] = $payment_term;
            $client['status'] = "1";

            $saveclient = RegionalClient::create($client);

            $url = URL::to($this->prefix . '/clients');
            $response['success'] = true;
            $response['success_message'] = "Clients Added successfully";
            $response['error'] = false;
            $response['page'] = 'client-create';
            $response['redirect_url'] = $url;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);
    }

    public function editBaseClient(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $baseClient = BaseClient::where('id', $request->id)->first();

        return view('clients.update-baseclient', ['prefix' => $this->prefix, 'baseClient' => $baseClient]);
    }
    // =========
    public function updateBaseClient(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'client_name' => 'required|unique:base_clients,client_name',
                // 'name' => 'required|unique:regional_clients,name',
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
            if (!empty($request->client_name)) {
                $client['client_name'] = $request->client_name;
            }

             // ======= gst upload //
            if (!empty($request->file('upload_gst'))) {
                $gstupload = $request->file('upload_gst');
                $path = Storage::disk('s3')->put('clients', $gstupload);
                $gst_img_path_save = Storage::disk('s3')->url($path);
                $client['upload_gst'] = $gst_img_path_save;
            }
    
            //  ======= pan upload //
            if (!empty($request->file('upload_pan'))) {
                $panupload = $request->file('upload_pan');
                $pan_path = Storage::disk('s3')->put('clients', $panupload);
                $pan_img_path_save = Storage::disk('s3')->url($pan_path);
                $client['upload_pan'] = $pan_img_path_save;
            }
    
            // ======= tan upload //
            if (!empty($request->file('upload_tan'))) {
                $tanupload = $request->file('upload_tan');
                $tan_path = Storage::disk('s3')->put('clients', $tanupload);
                $tan_img_path_save = Storage::disk('s3')->url($tan_path);
                $client['upload_tan'] = $tan_img_path_save;
            }
    
            // ======= moa upload //
            if (!empty($request->file('upload_moa'))) {
                $moaupload = $request->file('upload_moa');
                $moa_path = Storage::disk('s3')->put('clients', $moaupload);
                $moa_img_path_save = Storage::disk('s3')->url($moa_path);
                $client['upload_moa'] = $moa_img_path_save;
            }

            $client['tan'] = $request->tan;
            $client['gst_no'] = $request->gst_no;
            $client['pan'] = $request->pan;
            $client['status'] = "1";

            $saveclient = BaseClient::where('id',$request->base_client)->update($client);

            $url = URL::to($this->prefix . '/clients');
            $response['success'] = true;
            $response['success_message'] = "Clients Updated successfully";
            $response['error'] = false;
            $response['page'] = 'client-create';
            $response['redirect_url'] = $url;
       
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);
    }

}