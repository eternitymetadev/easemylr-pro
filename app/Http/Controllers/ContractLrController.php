<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consigner;
use App\Models\Location;
use App\Models\Role;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Driver;
use App\Models\ItemMaster;
use App\Models\RegionalClient;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Validator;
use Storage;
use Helper;
use QrCode;
use Auth;
use Mail;
use URL;
use DB;

class ContractLrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createContractLr()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        if ($authuser->role_id == 2 || $authuser->role_id == 3) {
            if ($authuser->role_id == $role_id->id) {
                $consigners = Consigner::select('id', 'nick_name')->whereIn('branch_id', $cc)->get();
            } else {
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else if ($authuser->role_id != 2 || $authuser->role_id != 3) {
            if ($authuser->role_id != 1) {
                $consigners = Consigner::select('id', 'nick_name')->whereIn('regionalclient_id', $regclient)->get();
            } else {
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else {
            $consigners = Consigner::select('id', 'nick_name')->get();
        }

        if ($authuser->role_id == 1) {
            $branchs = Location::select('id', 'name')->get();
        } elseif ($authuser->role_id == 2) {
            $branchs = Location::select('id', 'name')->where('id', $cc)->get();
        } elseif ($authuser->role_id == 5) {
            $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        } else {
            $branchs = Location::select('id', 'name')->get();
        }

        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $itemlists = ItemMaster::where('status', '1')->get();

        /////////////////////////////Bill to regional clients //////////////////////////
        $regionalclient = RegionalClient::select('id', 'name', 'location_id')->get();
        
        return view('contract-lr.create-lrcontract', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists, 'branchs' => $branchs]);
    }

    // store contractLr
    public function storeContractLr(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'consigner_id' => 'required',
                'consignee_id' => 'required',
                'ship_to_id' => 'required',
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
            $cc = explode(',', $authuser->branch_id);
            $location = Location::whereIn('id',$cc)->first();

            $prs_regclientcheck = Regionalclient::where('id', $request->regclient_id)->first();
            if ($prs_regclientcheck->is_prs_pickup == 1) {
                
                $consignee = Consignee::where('id', $request->consignee_id)->first();
                $consignee_pincode = $consignee->postal_code;

                $getpin_transfer = Zone::where('postal_code', $consignee_pincode)->first();

                $get_location = Location::where('id', $authuser->branch_id)->first();
                $chk_h2h_branch = $get_location->with_h2h;
                $location_name = $get_location->name;

                if (!empty($getpin_transfer->hub_transfer)) {
                    $get_branch = Location::where('name', $getpin_transfer->hub_transfer)->first();
                    $get_branch_id_to = $get_branch->id;
                    $get_zonebranch = $getpin_transfer->hub_transfer;
                } else {
                    $get_branch_id_to = $authuser->branch_id;
                    $get_zonebranch = $location_name;
                }
                $to_branch_id = $get_branch_id_to;
                $get_branch_id = $authuser->branch_id;
            } else {
                $to_branch_id = null;

                $consigner = Consigner::where('id', $request->consigner_id)->first();
                $consigner_pincode = $consigner->postal_code;
                if (empty($consigner_pincode)) {
                    $response['success'] = false;
                    $response['error_message'] = "Postal Code Not Found";
                    $response['error'] = true;
                    return response()->json($response);
                }
                $getpin_transfer = Zone::where('postal_code', $consigner_pincode)->first();

                $get_location = Location::where('id', $authuser->branch_id)->first();
                $chk_h2h_branch = $get_location->with_h2h;
                $location_name = $get_location->name;

                if (!empty($getpin_transfer->hub_transfer)) {
                    $get_branch = Location::where('name', $getpin_transfer->hub_transfer)->first();
                    $get_branch_id = $get_branch->id;
                    $get_zonebranch = $getpin_transfer->hub_transfer;
                } else {
                    $get_branch_id = $authuser->branch_id;
                    $get_zonebranch = $location_name;
                }
            }

            $consignmentsave['regclient_id'] = $request->regclient_id;
            $consignmentsave['consigner_id'] = $request->consigner_id;
            $consignmentsave['consignee_id'] = $request->consignee_id;
            $consignmentsave['ship_to_id'] = $request->ship_to_id;
            $consignmentsave['is_salereturn'] = $request->is_salereturn;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['payment_type'] = $request->payment_type;
            $consignmentsave['description'] = $request->description;
            $consignmentsave['packing_type'] = $request->packing_type;
            $consignmentsave['dispatch'] = $request->dispatch;
            $consignmentsave['freight'] = $request->freight;
            $consignmentsave['freight_on_delivery'] = $request->freight_on_delivery;
            $consignmentsave['cod'] = $request->cod;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;
            // $consignmentsave['prsitem_status'] = $prsitem_status;
            // $consignmentsave['to_branch_id'] = $to_branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = 7;  // status = 7 for lr contract
            $consignmentsave['lr_type'] = $request->lr_type;  // lr_type = 3 for lr contract
            $consignmentsave['delivery_status'] = "Unassigned";

            ///h2h branch check
            if ($location_name == $get_zonebranch) {
                $consignmentsave['h2h_check'] = 'lm';
                ///same location check
                if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {

                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            if($save_data['invoice_amount'] != '' || $save_data['invoice_amount'] != null){
                                $save_data['invoice_amount'] = $save_data['invoice_amount'];
                            }else{
                                $save_data['invoice_amount'] = 0;
                            }
                            $saveconsignmentitems = ConsignmentItem::create($save_data);

                            if ($saveconsignmentitems) {
                                // dd($save_data['item_data']);
                                if (!empty($save_data['item_data'])) {
                                    $qty_array = array();
                                    $netwt_array = array();
                                    $grosswt_array = array();
                                    $chargewt_array = array();
                                    foreach ($save_data['item_data'] as $key => $save_itemdata) {
                                        // echo "<pre>"; print_r($save_itemdata); die;
                                        $qty_array[] = $save_itemdata['quantity'];
                                        $netwt_array[] = $save_itemdata['net_weight'];
                                        $grosswt_array[] = $save_itemdata['gross_weight'];
                                        $chargewt_array[] = $save_itemdata['chargeable_weight'];

                                        $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                                        $save_itemdata['status'] = 1;

                                        $savesubitems = ConsignmentSubItem::create($save_itemdata);
                                    }

                                    $quantity_sum = array_sum($qty_array);
                                    $netwt_sum = array_sum($netwt_array);
                                    $grosswt_sum = array_sum($grosswt_array);
                                    $chargewt_sum = array_sum($chargewt_array);

                                    ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);

                                    ConsignmentNote::where('id', $saveconsignment->id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                                }
                            }
                        }

                    }
                } else {
                    $consignmentsave['total_quantity'] = $request->total_quantity;
                    $consignmentsave['total_weight'] = $request->total_weight;
                    $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                    $consignmentsave['total_freight'] = $request->total_freight;
                    $saveconsignment = ConsignmentNote::create($consignmentsave);

                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            if($save_data['invoice_amount'] != '' || $save_data['invoice_amount'] != null){
                                $save_data['invoice_amount'] = $save_data['invoice_amount'];
                            }else{
                                $save_data['invoice_amount'] = 0;
                            }
                            $saveconsignmentitems = ConsignmentItem::create($save_data);
                        }
                    }
                }
            } else {
                $consignmentsave['h2h_check'] = 'h2h';

                if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            if($save_data['invoice_amount'] != '' || $save_data['invoice_amount'] != null){
                                $save_data['invoice_amount'] = $save_data['invoice_amount'];
                            }else{
                                $save_data['invoice_amount'] = 0;
                            }
                            $saveconsignmentitems = ConsignmentItem::create($save_data);

                            if ($saveconsignmentitems) {
                                // dd($save_data['item_data']);
                                if (!empty($save_data['item_data'])) {
                                    $qty_array = array();
                                    $netwt_array = array();
                                    $grosswt_array = array();
                                    $chargewt_array = array();
                                    foreach ($save_data['item_data'] as $key => $save_itemdata) {
                                        // echo "<pre>"; print_r($save_itemdata); die;
                                        $qty_array[] = $save_itemdata['quantity'];
                                        $netwt_array[] = $save_itemdata['net_weight'];
                                        $grosswt_array[] = $save_itemdata['gross_weight'];
                                        $chargewt_array[] = $save_itemdata['chargeable_weight'];

                                        $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                                        $save_itemdata['status'] = 1;

                                        $savesubitems = ConsignmentSubItem::create($save_itemdata);
                                    }

                                    $quantity_sum = array_sum($qty_array);
                                    $netwt_sum = array_sum($netwt_array);
                                    $grosswt_sum = array_sum($grosswt_array);
                                    $chargewt_sum = array_sum($chargewt_array);

                                    ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);

                                    ConsignmentNote::where('id', $saveconsignment->id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                                }
                            }
                        }
                    }
                } else {
                    $consignmentsave['total_quantity'] = $request->total_quantity;
                    $consignmentsave['total_weight'] = $request->total_weight;
                    $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                    $consignmentsave['total_freight'] = $request->total_freight;
                    $saveconsignment = ConsignmentNote::create($consignmentsave);

                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            if($save_data['invoice_amount'] != '' || $save_data['invoice_amount'] != null){
                                $save_data['invoice_amount'] = $save_data['invoice_amount'];
                            }else{
                                $save_data['invoice_amount'] = 0;
                            }
                            $saveconsignmentitems = ConsignmentItem::create($save_data);
                        }
                    }
                }
            }

            $mytime = Carbon::now('Asia/Kolkata');
            $currentdate = $mytime->toDateTimeString();
            //===========================End drs lr ================================= //
            // if ($saveconsignment) {
            /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
            $get_driver_details = Driver::select('branch_id')->where('id', $request->driver_id)->first();

            //========= check app assign =================//
            if (!empty($get_driver_details->branch_id)) {
                $driver_branch = explode(',', $get_driver_details->branch_id);
                if (in_array($authuser->branch_id, $driver_branch)) {
                    $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);

                    //========= task created =================//
                    $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'desc'=>'Order Placed','create_at' => $currentdate,'location'=>$location->name, 'type' => '2']);
                    $respons_data = json_encode($respons);
                    $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create =================//
                    // ================= task assign =================//
                    $respons2 = array('consignment_id' => $saveconsignment->id, 'status' => 'Menifested','desc'=>'Consignment Menifested at', 'create_at' => $currentdate,'location'=>$location->name, 'type' => '2');

                    $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('id')->first();
                    if(!empty($lastjob->response_data)){
                        $st = json_decode($lastjob->response_data);
                        array_push($st, $respons2);
                        $sts = json_encode($st);

                        $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Menifested', 'type' => '2']);
                    }
                    // ==== end started
                    $app_notify = $this->sendNotification($request->driver_id);
                } else {
                    // task created
                    $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'desc'=>'Order Placed','create_at' => $currentdate,'location'=>$location->name, 'type' => '2']);
                    $respons_data = json_encode($respons);
                    $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create
                }
                // if(!empty($request->driver_id)){
                //     $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);
                // }
            } else {
                // task created //
                $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'desc'=>'Order Placed','create_at' => $currentdate,'location'=>$location->name, 'type' => '2']);
                $respons_data = json_encode($respons);
                $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                // ==== end create===//
                $pickup_location = Location::where('id',$saveconsignment->fall_in)->first();
                // ================= task assign =================//
                $respons2 = array('consignment_id' => $saveconsignment->id, 'status' => 'Menifested','desc'=>'Consignment Menifested at', 'create_at' => $currentdate,'location'=>$pickup_location->name, 'type' => '2');

                $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('id')->first();
                if(!empty($lastjob->response_data)){
                    $st = json_decode($lastjob->response_data);
                    array_push($st, $respons2);
                    $sts = json_encode($st);

                    $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Menifested', 'type' => '2']);
                }
                // ==== end started
                
            }

            $url = $this->prefix . '/orders';
            $response['success'] = true;
            $response['success_message'] = "Order Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'create-consignment';
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
}
