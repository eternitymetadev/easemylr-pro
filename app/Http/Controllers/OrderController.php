<?php

namespace App\Http\Controllers;

use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentSubItem;
use App\Models\Driver;
use App\Models\ItemMaster;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Role;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Auth;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use URL;
use Validator;
use App\Models\Zone;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->title = "Order Booking";
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
        // $peritem = 20;
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->whereIn('lr_type',[1,2])->where('status', 5)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail','PrsDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
          
            $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc){
                $query->whereIn('fall_in', $cc)->where('status', 5);
            });
        }
        // $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
        //     ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
        //     ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id');

        // if ($authuser->role_id == 1) {
        //     $data;
        // }
        // elseif($authuser->role_id ==4){
        //     $data = $data->whereIn('consignment_notes.regclient_id', $regclient);
        //     // $data = $data->where('consignment_notes.user_id', $authuser->id);
        // }
        // elseif($authuser->role_id ==6){
        //     $data = $data->whereIn('base_clients.id', $baseclient);
        // } elseif ($authuser->role_id == 7) {
        //     $data = $data->whereIn('regional_clients.id', $regclient);
        // } else {
        //     $data = $data->whereIn('consignment_notes.branch_id', $cc);
        // }
        // $data = $data->where('consignment_notes.status','5')->orderBy('id', 'DESC');

        $consignments = $query->orderBy('id', 'DESC')->get();

        if ($request->ajax()) {
            if (isset($request->updatestatus)) {
                ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel]);
            }

            $url = $this->prefix . '/orders';
            $response['success'] = true;
            $response['success_message'] = "Order updated successfully";
            $response['error'] = false;
            $response['page'] = 'order-statusupdate';
            $response['redirect_url'] = $url;

            return response()->json($response);
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

        return view('orders.order-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title, 'branchs' => $branchs])
            ->with('i', ($request->input('page', 1) - 1) * 5);
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

        if ($authuser->role_id == 2 || $authuser->role_id == 3) {
            $branch = $authuser->branch_id;
            $branch_loc = explode(',', $branch);
            $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name', 'location_id')->get();

        } elseif ($authuser->role_id == 4) {
            $reg = $authuser->regionalclient_id;
            $regional = explode(',', $reg);
            $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name', 'location_id')->get();

        } else {
            $regionalclient = RegionalClient::select('id', 'name', 'location_id')->get();
        }

        return view('orders.create-order', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists, 'branchs' => $branchs]);
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

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
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
            // $consignmentsave['total_quantity'] = $request->total_quantity;
            // $consignmentsave['total_weight'] = $request->total_weight;
            // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            // $consignmentsave['total_freight'] = $request->total_freight;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;

            $consignmentsave['branch_id'] = $request->branch_id;
            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = 5;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }
            // if ($saveconsignment) {
            // insert consignment items
            if($request->invoice_check == 1 || $request->invoice_check == 2){
                $saveconsignment = ConsignmentNote::create($consignmentsave);
            if (!empty($request->data)) {
                $get_data = $request->data;
                foreach ($get_data as $key => $save_data) {
                    $save_data['consignment_id'] = $saveconsignment->id;
                    $save_data['status'] = 1;
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
        }else{

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
                    $saveconsignmentitems = ConsignmentItem::create($save_data);
                }
            }
        }
            $url = $this->prefix . '/orders';
            $response['success'] = true;
            $response['success_message'] = "Order Created successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'create-consignment';
            $response['redirect_url'] = $url;
            // } else {
            //     $response['success'] = false;
            //     $response['error_message'] = "Can not created order please try again";
            //     $response['error'] = true;
            // }
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
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $getconsignments = ConsignmentNote::with('ConsignmentItem.ConsignmentSubItems')->where('id', $id)->first();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     if ($authuser->role_id == $role_id->id) {
        //         $consigners = Consigner::whereIn('branch_id', $cc)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     } else {
        //         $consigners = Consigner::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     }
        // } else if ($authuser->role_id != 2 || $authuser->role_id != 3) {
        //     if ($authuser->role_id != 1) {
        //         $consigners = Consigner::whereIn('regionalclient_id', $regclient)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     } else {
        //         $consigners = Consigner::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     }
        // } else {
            $consigners = Consigner::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        // }
        // $consignees = Consignee::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        $consignees = Consignee::select('id', 'nick_name')->where(['consigner_id' => $getconsignments->consigner_id])->get();
        // dd($consignees);

        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $itemlists = ItemMaster::where('status', '1')->get();
        ////////////// Bill to regional clients //////////////

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     $branch = $authuser->branch_id;
        //     $branch_loc = explode(',', $branch);
        //     $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name')->get();
        // } elseif ($authuser->role_id == 4) {
        //     $reg = $authuser->regionalclient_id;
        //     $regional = explode(',', $reg);
        //     $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name')->get();
        // } else {
            $regionalclient = RegionalClient::select('id', 'name')->get();
        // }

        if ($authuser->role_id == 1) {
            $branchs = Location::select('id', 'name')->get();
        } elseif ($authuser->role_id == 2) {
            $branchs = Location::select('id', 'name')->where('id', $cc)->get();
        } elseif ($authuser->role_id == 5) {
            $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        } else {
            $branchs = Location::select('id', 'name')->get();
        }

        return view('orders.update-order', ['prefix' => $this->prefix, 'getconsignments' => $getconsignments, 'consigners' => $consigners, 'consignees' => $consignees, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists, 'branchs' => $branchs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request){
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

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
            }

            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }
            
            // $consignmentsave['regclient_id'] = $request->regclient_id;
            // $consignmentsave['consigner_id'] = $request->consigner_id;
            $consignmentsave['consignee_id'] = $request->consignee_id;
            $consignmentsave['ship_to_id'] = $request->ship_to_id;
            $consignmentsave['is_salereturn'] = $request->is_salereturn;
            // $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['payment_type'] = $request->payment_type;
            $consignmentsave['description'] = $request->description;
            $consignmentsave['packing_type'] = $request->packing_type;
            // $consignmentsave['dispatch'] = $request->dispatch;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            // $consignmentsave['branch_id'] = $authuser->branch_id;

            $consignmentsave['edd'] = $request->edd;
            // $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['status'] = $status;

            if($request->lr_type == 1){  
                $consignee = Consignee::where('id', $request->consignee_id)->first();
                $consignee_pincode = $consignee->postal_code;

                $getpin_transfer = Zone::where('postal_code', $consignee_pincode)->first();

                $get_location = Location::where('id', $authuser->branch_id)->first();
                $chk_h2h_branch = $get_location->with_h2h;
                $location_name = $get_location->name;
                
                if(!empty($getpin_transfer->hub_transfer)){
                    $get_branch = Location::where('name', $getpin_transfer->hub_transfer)->first();
                    $get_branch_id = $get_branch->id;
                    $get_zonebranch = $getpin_transfer->hub_transfer;
                    }else{
                    $get_branch_id = $authuser->branch_id;
                    $get_zonebranch = $location_name;
                    }
               
                $consignmentsave['to_branch_id'] = $get_branch_id;

                    ///h2h branch check
                    if($location_name == $get_zonebranch){
                        if (!empty($request->vehicle_id)) {
                            $consignmentsave['delivery_status'] = "Started";
                        } else {
                            $consignmentsave['delivery_status'] = "Unassigned";
                        }
                        $consignmentsave['hrs_status'] = 2;
                        $consignmentsave['h2h_check'] = 'lm';
                        ///same location check
                        if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                            $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                            if (!empty($request->data)) {
                                $get_data = $request->data;
                                foreach ($get_data as $key => $save_data) {

                                    $savedata['consignment_id'] = $request->consignment_id;
                                    $savedata['order_id'] = $save_data['order_id'];
                                    $savedata['invoice_no'] = $save_data['invoice_no'];
                                    $savedata['invoice_date'] = $save_data['invoice_date'];
                                    $savedata['invoice_amount'] = $save_data['invoice_amount'];
                                    $savedata['e_way_bill'] = $save_data['e_way_bill'];
                                    $savedata['e_way_bill_date'] = $save_data['e_way_bill_date'];
                                    $savedata['status'] = 1;
                                    // unset($save_data['item_id']);
                                    $saveconsignmentitems = ConsignmentItem::where('id',$save_data['item_id'])->update($savedata);
                                    
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

                                                $saveitemdata['conitem_id'] = $save_data['item_id'];
                                                $saveitemdata['item'] = $save_itemdata['item'];
                                                $saveitemdata['quantity'] = $save_itemdata['quantity'];
                                                $saveitemdata['net_weight'] = $save_itemdata['net_weight'];
                                                $saveitemdata['gross_weight'] = $save_itemdata['gross_weight'];
                                                $saveitemdata['chargeable_weight'] = $save_itemdata['chargeable_weight'];
                                                $saveitemdata['status'] = 1;
                
                                                $savesubitems = ConsignmentSubItem::where('id',$save_itemdata['subitem_id'])->update($saveitemdata);
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
                            $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                
                            if (!empty($request->data)) {
                                $get_data = $request->data;
                                foreach ($get_data as $key => $save_data) {
                                    $savedata['consignment_id'] = $request->consignment_id;
                                    $savedata['order_id'] = $save_data['order_id'];
                                    $savedata['invoice_no'] = $save_data['invoice_no'];
                                    $savedata['invoice_date'] = $save_data['invoice_date'];
                                    $savedata['invoice_amount'] = $save_data['invoice_amount'];
                                    $savedata['e_way_bill'] = $save_data['e_way_bill'];
                                    $savedata['e_way_bill_date'] = $save_data['e_way_bill_date'];
                                    $savedata['quantity'] = $save_data['quantity'];
                                    $savedata['weight'] = $save_data['weight'];
                                    $savedata['gross_weight'] = $save_data['gross_weight'];
                                    $savedata['status'] = 1;
                                    // unset($save_data['item_id']);
                                    $saveconsignmentitems = ConsignmentItem::where('id',$save_data['item_id'])->update($savedata);
                                }
                            }
                        }
                    }else{
                     
                        $consignmentsave['h2h_check'] = 'h2h';
                        $consignmentsave['hrs_status'] = 2;

                        if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                            $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                            if (!empty($request->data)) {
                                $get_data = $request->data;
                                foreach ($get_data as $key => $save_data) {
                                    $savedata['consignment_id'] = $request->consignment_id;
                                    $savedata['order_id'] = $save_data['order_id'];
                                    $savedata['invoice_no'] = $save_data['invoice_no'];
                                    $savedata['invoice_date'] = $save_data['invoice_date'];
                                    $savedata['invoice_amount'] = $save_data['invoice_amount'];
                                    $savedata['e_way_bill'] = $save_data['e_way_bill'];
                                    $savedata['e_way_bill_date'] = $save_data['e_way_bill_date'];
                                    
                                    $savedata['status'] = 1;
                                    // unset($save_data['item_id']);
                                    $saveconsignmentitems = ConsignmentItem::where('id',$save_data['item_id'])->update($savedata);
                                    // $save_data['consignment_id'] = $saveconsignment->id;
                                    // $save_data['status'] = 1;
                                    // $saveconsignmentitems = ConsignmentItem::create($save_data);
                
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
                                                
                                                $saveitemdata['conitem_id'] = $save_data['item_id'];
                                                $saveitemdata['item'] = $save_itemdata['item'];
                                                $saveitemdata['quantity'] = $save_itemdata['quantity'];
                                                $saveitemdata['net_weight'] = $save_itemdata['net_weight'];
                                                $saveitemdata['gross_weight'] = $save_itemdata['gross_weight'];
                                                $saveitemdata['chargeable_weight'] = $save_itemdata['chargeable_weight'];
                                                $saveitemdata['status'] = 1;
                
                                                $savesubitems = ConsignmentSubItem::where('id',$save_itemdata['subitem_id'])->update($saveitemdata);

                                                // $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                                                // $save_itemdata['status'] = 1;
                
                                                // $savesubitems = ConsignmentSubItem::create($save_itemdata);
                                            }
                                            
                                            $quantity_sum = array_sum($qty_array);
                                            $netwt_sum = array_sum($netwt_array);
                                            $grosswt_sum = array_sum($grosswt_array);
                                            $chargewt_sum = array_sum($chargewt_array);
                
                                            ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);
                
                                            ConsignmentNote::where('id', $request->consignment_id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                                        }
                                    }
                                }
                            }
                        } else {
                            $consignmentsave['total_quantity'] = $request->total_quantity;
                            $consignmentsave['total_weight'] = $request->total_weight;
                            $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                            $consignmentsave['total_freight'] = $request->total_freight;
                            $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                
                            if (!empty($request->data)) {
                                $get_data = $request->data;
                                foreach ($get_data as $key => $save_data) {
                                    $savedata['consignment_id'] = $request->consignment_id;
                                    $savedata['order_id'] = $save_data['order_id'];
                                    $savedata['invoice_no'] = $save_data['invoice_no'];
                                    $savedata['invoice_date'] = $save_data['invoice_date'];
                                    $savedata['invoice_amount'] = $save_data['invoice_amount'];
                                    $savedata['e_way_bill'] = $save_data['e_way_bill'];
                                    $savedata['e_way_bill_date'] = $save_data['e_way_bill_date'];
                                    $savedata['quantity'] = $save_data['quantity'];
                                    $savedata['weight'] = $save_data['weight'];
                                    $savedata['gross_weight'] = $save_data['gross_weight'];
                                    $savedata['status'] = 1;
                                    // unset($save_data['item_id']);
                                    $saveconsignmentitems = ConsignmentItem::where('id',$save_data['item_id'])->update($savedata);
                                }
                            }
                        }
                    }
               
            }else{
                //regular same flow 
                //h2h branch check
             if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    // dd($get_data);
                    foreach ($get_data as $key => $save_data) {
                        $savedata['consignment_id'] = $request->consignment_id;
                        $savedata['order_id'] = $save_data['order_id'];
                        $savedata['invoice_no'] = $save_data['invoice_no'];
                        $savedata['invoice_date'] = $save_data['invoice_date'];
                        $savedata['invoice_amount'] = $save_data['invoice_amount'];
                        $savedata['e_way_bill'] = $save_data['e_way_bill'];
                        $savedata['e_way_bill_date'] = $save_data['e_way_bill_date'];
                        $savedata['status'] = 1;
                        // unset($save_data['item_id']);
                        $saveconsignmentitems = ConsignmentItem::where('id',$save_data['item_id'])->update($savedata);
                        // dd($saveconsignmentitems);
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

                                    $saveitemdata['conitem_id'] = $save_data['item_id'];
                                    $saveitemdata['item'] = $save_itemdata['item'];
                                    $saveitemdata['quantity'] = $save_itemdata['quantity'];
                                    $saveitemdata['net_weight'] = $save_itemdata['net_weight'];
                                    $saveitemdata['gross_weight'] = $save_itemdata['gross_weight'];
                                    $saveitemdata['chargeable_weight'] = $save_itemdata['chargeable_weight'];
                                    $saveitemdata['status'] = 1;
    
                                    $savesubitems = ConsignmentSubItem::where('id',$save_itemdata['subitem_id'])->update($saveitemdata);
                                }
                                $quantity_sum = array_sum($qty_array);
                                $netwt_sum = array_sum($netwt_array);
                                $grosswt_sum = array_sum($grosswt_array);
                                $chargewt_sum = array_sum($chargewt_array);
    
                                ConsignmentItem::where('id', $save_data['item_id'])->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);
    
                                ConsignmentNote::where('id', $request->consignment_id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                            }
                        }
                    }
                }
            } else {
                $consignmentsave['total_quantity'] = $request->total_quantity;
                $consignmentsave['total_weight'] = $request->total_weight;
                $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                $consignmentsave['total_freight'] = $request->total_freight;
                $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
    
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
                        $savedata['consignment_id'] = $request->consignment_id;
                        $savedata['order_id'] = $save_data['order_id'];
                        $savedata['invoice_no'] = $save_data['invoice_no'];
                        $savedata['invoice_date'] = $save_data['invoice_date'];
                        $savedata['invoice_amount'] = $save_data['invoice_amount'];
                        $savedata['e_way_bill'] = $save_data['e_way_bill'];
                        $savedata['e_way_bill_date'] = $save_data['e_way_bill_date'];
                        $savedata['status'] = 1;
                        $saveconsignmentitems = ConsignmentItem::where('id',$save_data['item_id'])->update($savedata);
                    }
                }
            }
            }
/////////////////// drs api push ////////////////////////////

             $consignment_id = $request->consignment_id;
             //===================== Create DRS in LR ================================= //
     
             if (!empty($request->vehicle_id)) {
                 $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode', 'vehicles.regn_no as regn_no', 'drivers.name as driver_name', 'drivers.phone as driver_phone')
                     ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                     ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                     ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                     ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                     ->where('consignment_notes.id', $consignment_id)
                     ->first(['consignees.city']);
                 $simplyfy = json_decode(json_encode($consignmentdrs), true);
     
                 $no_of_digit = 5;
                 $drs = DB::table('transaction_sheets')->select('drs_no')->latest('drs_no')->first();
                 $drs_no = json_decode(json_encode($drs), true);
                 if (empty($drs_no) || $drs_no == null) {
                     $drs_no['drs_no'] = 0;
                 }
                 $number = $drs_no['drs_no'] + 1;
                 $drs_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);
     
                 $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $simplyfy['id'], 'consignee_id' => $simplyfy['consignee_name'], 'consignment_date' => $simplyfy['consignment_date'], 'branch_id' => $authuser->branch_id, 'city' => $simplyfy['city'], 'pincode' => $simplyfy['pincode'], 'total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'], 'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'order_no' => '1', 'delivery_status' => 'Assigned', 'status' => '1']);
             }
             //===========================End drs lr ================================= //
             // if ($saveconsignment) {
     
                 /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
                 $vn = $consignmentsave['vehicle_id'];
                 $lid = $request->consignment_id;
                 $lrdata = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name', 'consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
                     ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                     ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                     ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                     ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                     ->where('consignment_notes.id', $lid)
                     ->get();
                 $simplyfy = json_decode(json_encode($lrdata), true);
                 //echo "<pre>";print_r($simplyfy);die;
                 //Send Data to API
     
                 if (($request->edd) >= $request->consignment_date) {
                     if (!empty($vn) && !empty($simplyfy[0]['team_id']) && !empty($simplyfy[0]['fleet_id'])) {
                         $createTask = $this->createTookanTasks($simplyfy);
                         $json = json_decode($createTask[0], true);
                         $job_id = $json['data']['job_id'];
                         $tracking_link = $json['data']['tracking_link'];
                         $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
                     }
                 }
            
                $url = $this->prefix . '/consignments';
                $response['success'] = true;
                $response['success_message'] = "Consignment Added successfully";
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

    ////////////////////
    public function updateOrderOld(Request $request){
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

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
            }

            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }
            
            // $consignmentsave['regclient_id'] = $request->regclient_id;
            // $consignmentsave['consigner_id'] = $request->consigner_id;
            // $consignmentsave['consignee_id'] = $request->consignee_id;
            // $consignmentsave['ship_to_id'] = $request->ship_to_id;
            // $consignmentsave['is_salereturn'] = $request->is_salereturn;
            // $consignmentsave['consignment_date'] = $request->consignment_date;
            // $consignmentsave['payment_type'] = $request->payment_type;
            // $consignmentsave['description'] = $request->description;
            // $consignmentsave['packing_type'] = $request->packing_type;
            // $consignmentsave['dispatch'] = $request->dispatch;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            // $consignmentsave['branch_id'] = $authuser->branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['status'] = $status;
            

            // $consignment_id = $saveconsignment->id;

            // if ($saveconsignment) {
            // insert consignment items
            if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                // if (!empty($request->data)) {
                //     $get_data = $request->data;
                //     foreach ($get_data as $key => $save_data) {

                //         $save_data['consignment_id'] = $saveconsignment->id;
                //         $save_data['status'] = 1;
                //         $saveconsignmentitems = ConsignmentItem::create($save_data);

                //         if ($saveconsignmentitems) {
                //             if (!empty($save_data['item_data'])) {
                //                 $qty_array = array();
                //                 $netwt_array = array();
                //                 $grosswt_array = array();
                //                 $chargewt_array = array();
                //                 foreach ($save_data['item_data'] as $key => $save_itemdata) {
                //                     // echo "<pre>"; print_r($save_itemdata); die;
                //                     $qty_array[] = $save_itemdata['quantity'];
                //                     $netwt_array[] = $save_itemdata['net_weight'];
                //                     $grosswt_array[] = $save_itemdata['gross_weight'];
                //                     $chargewt_array[] = $save_itemdata['chargeable_weight'];

                //                     $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                //                     $save_itemdata['status'] = 1;

                //                     $savesubitems = ConsignmentSubItem::create($save_itemdata);
                //                 }
                //                 $quantity_sum = array_sum($qty_array);
                //                 $netwt_sum = array_sum($netwt_array);
                //                 $grosswt_sum = array_sum($grosswt_array);
                //                 $chargewt_sum = array_sum($chargewt_array);

                //                 ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);

                //                 ConsignmentNote::where('id', $saveconsignment->id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                //             }
                //         }
                //     }

                // }
            } else {
                $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
                // $consignmentsave['total_quantity'] = $request->total_quantity;
                // $consignmentsave['total_weight'] = $request->total_weight;
                // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                // $consignmentsave['total_freight'] = $request->total_freight;
                // $saveconsignment = ConsignmentNote::create($consignmentsave);

                // if (!empty($request->data)) {
                //     $get_data = $request->data;
                //     foreach ($get_data as $key => $save_data) {
                //         $save_data['consignment_id'] = $saveconsignment->id;
                //         $save_data['status'] = 1;
                //         $saveconsignmentitems = ConsignmentItem::create($save_data);
                //     }
                // }
            }

            $consignment_id = $request->consignment_id;
            //===================== Create DRS in LR ================================= //

            if (!empty($request->vehicle_id)) {
                $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode', 'vehicles.regn_no as regn_no', 'drivers.name as driver_name', 'drivers.phone as driver_phone')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $consignment_id)
                    ->first(['consignees.city']);
                $simplyfy = json_decode(json_encode($consignmentdrs), true);

                $no_of_digit = 5;
                $drs = DB::table('transaction_sheets')->select('drs_no')->latest('drs_no')->first();
                $drs_no = json_decode(json_encode($drs), true);
                if (empty($drs_no) || $drs_no == null) {
                    $drs_no['drs_no'] = 0;
                }
                $number = $drs_no['drs_no'] + 1;
                $drs_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);

                $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $simplyfy['id'], 'consignee_id' => $simplyfy['consignee_name'], 'consignment_date' => $simplyfy['consignment_date'], 'branch_id' => $authuser->branch_id, 'city' => $simplyfy['city'], 'pincode' => $simplyfy['pincode'], 'total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'], 'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'order_no' => '1', 'delivery_status' => 'Assigned', 'status' => '1']);
            }
            //===========================End drs lr ================================= //
            if ($saveconsignment) {

                /****** PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   *******/
                $vn = $consignmentsave['vehicle_id'];
                $lid = $request->consignment_id;
                $lrdata = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name', 'consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $lid)
                    ->get();
                $simplyfy = json_decode(json_encode($lrdata), true);
                //echo "<pre>";print_r($simplyfy);die;
                //Send Data to API

                if (($request->edd) >= $request->consignment_date) {
                    if (!empty($vn) && !empty($simplyfy[0]['team_id']) && !empty($simplyfy[0]['fleet_id'])) {
                        $createTask = $this->createTookanTasks($simplyfy);
                        $json = json_decode($createTask[0], true);
                        $job_id = $json['data']['job_id'];
                        $tracking_link = $json['data']['tracking_link'];
                        $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
                    }
                }

                $url = $this->prefix . '/orders';
                $response['success'] = true;
                $response['success_message'] = "Order Added successfully";
                $response['error'] = false;
                // $response['resetform'] = true;
                $response['page'] = 'update-order';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not created order please try again";
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

    //++++++++++++++++++++++ Tookan API Push +++++++++++++++++++++++++++++++++++//

    public function createTookanTasks($taskDetails)
    {

        //echo "<pre>";print_r($taskDetails);die;

        foreach ($taskDetails as $task) {

            $td = '{
                "api_key": "' . $this->apikey . '",
                "order_id": "' . $task['consignment_no'] . '",
                "job_description": "DRS-' . $task['id'] . '",
                "customer_email": "' . $task['email'] . '",
                "customer_username": "' . $task['consignee_name'] . '",
                "customer_phone": "' . $task['phone'] . '",
                "customer_address": "' . $task['pincode'] . ',' . $task['city'] . ',India",
                "latitude": "",
                "longitude": "",
                "job_delivery_datetime": "' . $task['edd'] . ' 21:00:00",
                "custom_field_template": "Template_1",
                "meta_data": [
                    {
                        "label": "Invoice Amount",
                        "data": "' . $task['invoice_amount'] . '"
                    },
                    {
                        "label": "Quantity",
                        "data": "' . $task['total_weight'] . '"
                    }
                ],
                "team_id": "' . $task['team_id'] . '",
                "auto_assignment": "1",
                "has_pickup": "0",
                "has_delivery": "1",
                "layout_type": "0",
                "tracking_link": 1,
                "timezone": "-330",
                "fleet_id": "' . $task['fleet_id'] . '",
                "notify": 1,
                "tags": "",
                "geofence": 0
            }';

            //echo "<pre>";print_r($td);echo "</pre>";die;

            //die;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.tookanapp.com/v2/create_task',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $td,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            ));

            $response[] = curl_exec($curl);

            curl_close($curl);

        }
        //echo "<pre>";print_r($response);echo "</pre>";die;
        return $response;

    }
    public function rewrap(array $input)
    {
        $key_names = array_shift($input);
        $output = array();
        foreach ($input as $index => $inner_array) {
            $output[] = array_combine($key_names, $inner_array);
        }
        return $output;
    }

    public function importOrderBooking(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);

        $rows = Excel::toArray([], request()->file('order_file'));
        $data = $rows[0];
        $excelarray = $this->rewrap($data);
        $datas = array();
        $orderId = array();
        $today = Date('Y-m-d');
        $i = 0;
        foreach ($excelarray as $element) {
            $date_string = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($element['invoice_date']);
            $inv_date = $date_string->format('Y-m-d');
            $eway_date_string = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($element['eway_date']);
            $eway_date = $eway_date_string->format('Y-m-d');
            $i++;

            if ($i == 1) {

                $get_regional = RegionalClient::where('name', $element['billing_client'])->first();
                $get_consigner = Consigner::where('nick_name', $element['consigner_id'])->first();
                $get_consignee = Consignee::where('nick_name', $element['consignee_id'])->first();
                if (!empty($get_regional) && !empty($get_consigner) && !empty($get_consignee)) {
                    $regional_id = $get_regional->id;
                    $consigner_id = $get_consigner->id;
                    $consignee_id = $get_consignee->id;

                    $consignmentsave['regclient_id'] = $regional_id;
                    $consignmentsave['consigner_id'] = $consigner_id;
                    $consignmentsave['consignee_id'] = $consignee_id;
                    $consignmentsave['consignment_date'] = $today;
                    $consignmentsave['payment_type'] = $element['payment_term'];
                    $consignmentsave['branch_id'] = $request->branch_id;
                    $consignmentsave['user_id'] = $authuser->id;
                    $consignmentsave['status'] = 5;

                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    $consignment_id = $saveconsignment->id;

                    $consignmentItem['order_id'] = $element['order_id'];
                    $consignmentItem['invoice_no'] = $element['invoice_no'];
                    $consignmentItem['invoice_date'] = $inv_date;
                    $consignmentItem['invoice_amount'] = $element['invoice_value'];
                    $consignmentItem['e_way_bill'] = $element['eway_bill'];
                    $consignmentItem['e_way_bill_date'] = $eway_date;
                    $consignmentItem['consignment_id'] = $consignment_id;

                    $saveconsignmentItem = ConsignmentItem::create($consignmentItem);
                    $consignment_item = $saveconsignmentItem->id;
                }

            }
            $get_regional = RegionalClient::where('name', $element['billing_client'])->first();
            $get_consigner = Consigner::where('nick_name', $element['consigner_id'])->first();
            $get_consignee = Consignee::where('nick_name', $element['consignee_id'])->first();
            if (!empty($get_regional) && !empty($get_consigner) && !empty($get_consignee)) {
                $regional_id = $get_regional->id;
                $consigner_id = $get_consigner->id;
                $consignee_id = $get_consignee->id;

                $query = ConsignmentNote::query();
                $query = $query->where('branch_id', $request->branch_id)
                // $checkorder = ConsignmentItem::where('order_id', $element['order_id'])->latest('order_id')->first();
                    ->whereHas('ConsignmentItem', function ($query) use ($element) {
                        $query->where('order_id', $element['order_id'])->latest('order_id');
                    });
                $checkorder = $query->first();

                if (!empty($checkorder)) {
                    $get_item = ItemMaster::where('brand_name', $element['material_description'])->first();
                    $item_id = $get_item->id;

                    $consignmentSubItem['item'] = $item_id;
                    $consignmentSubItem['quantity'] = $element['quantity'];
                    $consignmentSubItem['net_weight'] = $element['net_weight'];
                    $consignmentSubItem['gross_weight'] = $element['gross_weight'];
                    $consignmentSubItem['chargeable_weight'] = $element['chargeble_weight'];
                    $consignmentSubItem['conitem_id'] = $consignment_item;
                    $saveconsignmentsub = ConsignmentSubItem::create($consignmentSubItem);
                    $subid = $saveconsignmentsub->id;

                    $lastestid = ConsignmentSubItem::where('id', $subid)->latest('conitem_id')->first();
                    $qty = $lastestid->quantity;
                    $weight = $lastestid->net_weight;
                    $gross = $lastestid->gross_weight;
                    $chargable = $lastestid->chargeable_weight;

                    $checkquantity = ConsignmentItem::where('id', $consignment_item)->latest('id')->first();
                    if (!empty($checkquantity)) {

                        $finalquantity = $checkquantity->quantity + $qty;
                        $finalweight = $checkquantity->weight + $weight;
                        $finalgross = $checkquantity->gross_weight + $gross;
                        $finalchargable = $checkquantity->chargeable_weight + $chargable;
                    } else {
                        $finalquantity = $qty;
                        $finalweight = $weight;
                        $finalgross = $gross;
                        $finalchargable = $chargable;
                    }
                    $itm = ConsignmentItem::where('id', $consignment_item)->update(['quantity' => $finalquantity, 'weight' => $finalweight, 'gross_weight' => $finalgross, 'chargeable_weight' => $finalchargable]);

                    $consign_itm = ConsignmentNote::where('id', $consignment_id)->update(['total_quantity' => $finalquantity, 'total_weight' => $finalweight, 'total_gross_weight' => $finalgross]);
                }

            } else {

                $get_regional = RegionalClient::where('name', $element['billing_client'])->first();
                $get_consigner = Consigner::where('nick_name', $element['consigner_id'])->first();
                $get_consignee = Consignee::where('nick_name', $element['consignee_id'])->first();
                if (!empty($get_regional) && !empty($get_consigner) && !empty($get_consignee)) {
                    $regional_id = $get_regional->id;
                    $consigner_id = $get_consigner->id;
                    $consignee_id = $get_consignee->id;

                    $consignmentsave['regclient_id'] = $regional_id;
                    $consignmentsave['consigner_id'] = $consigner_id;
                    $consignmentsave['consignee_id'] = $consignee_id;
                    $consignmentsave['consignment_date'] = $today;
                    $consignmentsave['payment_type'] = $element['payment_term'];
                    $consignmentsave['branch_id'] = $request->branch_id;
                    $consignmentsave['user_id'] = $authuser->id;
                    $consignmentsave['status'] = 5;
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    $consignment_id = $saveconsignment->id;

                    $consignmentItem['order_id'] = $element['order_id'];
                    $consignmentItem['invoice_no'] = $element['invoice_no'];
                    $consignmentItem['invoice_date'] = $inv_date;
                    $consignmentItem['invoice_amount'] = $element['invoice_value'];
                    $consignmentItem['e_way_bill'] = $element['eway_bill'];
                    $consignmentItem['e_way_bill_date'] = $eway_date;
                    $consignmentItem['consignment_id'] = $consignment_id;
                    $saveconsignmentItem = ConsignmentItem::create($consignmentItem);
                    $consignment_item = $saveconsignmentItem->id;

                    $get_item = ItemMaster::where('brand_name', $element['material_description'])->first();
                    $item_id = $get_item->id;
                    $consignmentSubItem['item'] = $item_id;
                    $consignmentSubItem['quantity'] = $element['quantity'];
                    $consignmentSubItem['net_weight'] = $element['net_weight'];
                    $consignmentSubItem['gross_weight'] = $element['gross_weight'];
                    $consignmentSubItem['chargeable_weight'] = $element['chargeble_weight'];
                    $consignmentSubItem['conitem_id'] = $consignment_item;
                    $saveconsignmentsubitm = ConsignmentSubItem::create($consignmentSubItem);
                    $subid = $saveconsignmentsubitm->id;

                    $lastestid = ConsignmentSubItem::where('id', $subid)->latest('conitem_id')->first();
                    $qty = $lastestid->quantity;
                    $weight = $lastestid->net_weight;
                    $gross = $lastestid->gross_weight;
                    $chargable = $lastestid->chargeable_weight;

                    $checkquantity = ConsignmentItem::where('id', $consignment_item)->latest('id')->first();
                    if (!empty($checkquantity)) {
                        $finalquantity = $checkquantity->quantity + $qty;
                        $finalweight = $checkquantity->weight + $weight;
                        $finalgross = $checkquantity->gross_weight + $gross;
                        $finalchargable = $checkquantity->chargeable_weight + $chargable;
                    } else {
                        $finalquantity = $qty;
                        $finalweight = $weight;
                        $finalgross = $gross;
                        $finalchargable = $chargable;
                    }
                    $itm = ConsignmentItem::where('id', $consignment_item)->update(['quantity' => $finalquantity, 'weight' => $finalweight, 'gross_weight' => $finalgross, 'chargeable_weight' => $finalchargable]);

                    $consign_itm = ConsignmentNote::where('id', $consignment_id)->update(['total_quantity' => $finalquantity, 'total_weight' => $finalweight, 'total_gross_weight' => $finalgross]);
                }
            }
        }
        $message = "Data imported Successfully";
        if ($data) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import consignees please try again";
        }
        return response()->json($response);
    }

    public function getBillClient(Request $request)
    {

        $getregionals = RegionalClient::where('location_id', $request->branch_id)->get();

        if ($getregionals) {
            $response['success'] = true;
            $response['success_message'] = "Regional Client list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getregionals;

        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch Regional list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    // ========================== Order Book FTL and Ptl ========================= //
    
    public function orderBookFtl()
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

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     $branch = $authuser->branch_id;
        //     $branch_loc = explode(',', $branch);
        //     $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name', 'location_id')->get();

        // } elseif ($authuser->role_id == 4) {
        //     $reg = $authuser->regionalclient_id;
        //     $regional = explode(',', $reg);
        //     $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name', 'location_id')->get();

        // } else {
            $regionalclient = RegionalClient::select('id', 'name', 'location_id')->get();
        // }

        return view('orders.order-book-ftl', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists, 'branchs' => $branchs]);
    }

    public function storeFtlOrder(Request $request)
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

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
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
            // $consignmentsave['total_quantity'] = $request->total_quantity;
            // $consignmentsave['total_weight'] = $request->total_weight;
            // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            // $consignmentsave['total_freight'] = $request->total_freight;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;
            // $consignmentsave['branch_id'] = $request->branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = 5;
            $consignmentsave['lr_type'] = $request->lr_type;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            // $consignee = Consignee::where('id', $request->consignee_id)->first();
            // $consignee_pincode = $consignee->postal_code;
           
            // $getpin_transfer = Zone::where('postal_code', $consignee_pincode)->first();
            // $get_zonebranch = $getpin_transfer->hub_transfer;
            // $get_branch = Location::where('name', $get_zonebranch)->first();
            // $consignmentsave['to_branch_id'] = $get_branch->id;

            // $get_location = Location::where('id', $authuser->branch_id)->first();
            // $chk_h2h_branch = $get_location->with_h2h;
            // $location_name = $get_location->name;

            
             if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                $saveconsignment = ConsignmentNote::create($consignmentsave);
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
    
                        $save_data['consignment_id'] = $saveconsignment->id;
                        $save_data['status'] = 1;
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
                        $saveconsignmentitems = ConsignmentItem::create($save_data);
                    }
                }
            } ///////////////////////////////////////// drs api push/////////////////////////////////////////////

             $consignment_id = $saveconsignment->id;
            
                $url = $this->prefix . '/reserve-lr';
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

    public function orderBookPtl()
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

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     $branch = $authuser->branch_id;
        //     $branch_loc = explode(',', $branch);
        //     $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name', 'location_id')->get();

        // } elseif ($authuser->role_id == 4) {
        //     $reg = $authuser->regionalclient_id;
        //     $regional = explode(',', $reg);
        //     $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name', 'location_id')->get();

        // } else {
            $regionalclient = RegionalClient::select('id', 'name', 'location_id')->get();
        // }

        return view('orders.order-book-ptl', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists, 'branchs' => $branchs]);
    }

    public function storePtlOrder(Request $request)
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

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
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
            // $consignmentsave['total_quantity'] = $request->total_quantity;
            // $consignmentsave['total_weight'] = $request->total_weight;
            // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            // $consignmentsave['total_freight'] = $request->total_freight;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = 5;
            $consignmentsave['lr_type'] = $request->lr_type;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            // $consignee = Consignee::where('id', $request->consignee_id)->first();
            // $consignee_pincode = $consignee->postal_code;
            // if(empty($consignee_pincode))
            // {
            //     $response['success'] = false;
            //     $response['error_message'] = "Postal Code Not Found";
            //     $response['error'] = true;
            //     return response()->json($response);
            // }

            $consigner = Consigner::where('id', $request->consigner_id)->first();
            $consigner_pincode = $consigner->postal_code;
            if(empty($consigner_pincode))
            {
                $response['success'] = false;
                $response['error_message'] = "Postal Code Not Found";
                $response['error'] = true;
                return response()->json($response);
            }
           
            $getpin_transfer = Zone::where('postal_code', $consigner_pincode)->first();
            

            $get_location = Location::where('id', $authuser->branch_id)->first();
            $chk_h2h_branch = $get_location->with_h2h;
            $location_name = $get_location->name;

            if(!empty($getpin_transfer->hub_transfer)){ 
            $get_branch = Location::where('name', $getpin_transfer->hub_transfer)->first();
            $get_branch_id = $get_branch->id;
            $get_zonebranch = $getpin_transfer->hub_transfer;
            }else{
            $get_branch_id = $authuser->branch_id;
            $get_zonebranch = $location_name;
            }
            $consignmentsave['fall_in'] = $get_branch_id;

             ///h2h branch check
             if($location_name == $get_zonebranch){
                if (!empty($request->vehicle_id)) {
                    $consignmentsave['delivery_status'] = "Started";
                } else {
                    $consignmentsave['delivery_status'] = "Unassigned";
                }
                $consignmentsave['hrs_status'] = 2;
                $consignmentsave['h2h_check'] = 'lm';
                ///same location check
                if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
        
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
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
                            $saveconsignmentitems = ConsignmentItem::create($save_data);
                        }
                    }
                }
            }else{
             
                $consignmentsave['h2h_check'] = 'h2h';
                $consignmentsave['hrs_status'] = 2;

                if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
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
                            $saveconsignmentitems = ConsignmentItem::create($save_data);
                        }
                    }
                }
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
    public function prsReceiveMaterial(Request $request)
    {

        $update_receve = ConsignmentNote::where('id', $request->lr_id)->update(['prsitem_status' => 2,'prs_remarks' => $request->prs_remarks]);

        if ($update_receve) {
            $response['success'] = true;
            $response['success_message'] = "Status updated successfully";
            $response['error'] = false;

        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not update  please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    public function reserveLr(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        // $peritem = 20;
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            ->where('lr_type', 0);

        if ($authuser->role_id == 1) {
            $data;
        }
        elseif($authuser->role_id ==4){
            $data = $data->whereIn('consignment_notes.regclient_id', $regclient);
            // $data = $data->where('consignment_notes.user_id', $authuser->id);
        }
        elseif($authuser->role_id ==6){
            $data = $data->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $data = $data->whereIn('regional_clients.id', $regclient);
        } else {
            $data = $data->whereIn('consignment_notes.branch_id', $cc);
        }
        $data = $data->where('consignment_notes.status','5')->orderBy('id', 'DESC');

        $consignments = $data->get();

        if ($request->ajax()) {
            if (isset($request->updatestatus)) {
                ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel]);
            }

            $url = $this->prefix . '/orders';
            $response['success'] = true;
            $response['success_message'] = "Order updated successfully";
            $response['error'] = false;
            $response['page'] = 'order-statusupdate';
            $response['redirect_url'] = $url;

            return response()->json($response);
        }
        return view('orders.reserve-lr', ['consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    // ==========   Reserve LR =================== //
    public function editReserveLr($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $getconsignments = ConsignmentNote::where('id', $id)->first();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     if ($authuser->role_id == $role_id->id) {
        //         $consigners = Consigner::whereIn('branch_id', $cc)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     } else {
        //         $consigners = Consigner::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     }
        // } else if ($authuser->role_id != 2 || $authuser->role_id != 3) {
        //     if ($authuser->role_id != 1) {
        //         $consigners = Consigner::whereIn('regionalclient_id', $regclient)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     } else {
        //         $consigners = Consigner::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        //     }
        // } else {
            $consigners = Consigner::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        // }
        $consignees = Consignee::orderby('nick_name', 'ASC')->pluck('nick_name', 'id');

        $getconsignment = Location::select('id', 'name', 'consignment_no')->whereIn('id', $cc)->latest('id')->first();
        if (!empty($getconsignment->consignment_no)) {
            $con_series = $getconsignment->consignment_no;
        } else {
            $con_series = '';
        }

        $cn = ConsignmentNote::select('id', 'consignment_no', 'branch_id')->whereIn('branch_id', $cc)->latest('id')->first();
        if ($cn) {
            if (!empty($cn->consignment_no)) {
                $cc = explode('-', $cn->consignment_no);
                $getconsignmentno = @$cc[1] + 1;
                $consignmentno = $cc[0] . '-' . $getconsignmentno;
            } else {
                $consignmentno = $con_series . '-1';
            }
        } else {
            $consignmentno = $con_series . '-1';
        }

        if (empty($consignmentno)) {
            $consignmentno = "";
        }
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        ////////////// Bill to regional clients //////////////

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     $branch = $authuser->branch_id;
        //     $branch_loc = explode(',', $branch);
        //     $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name')->get();

        // } elseif ($authuser->role_id == 4) {
        //     $reg = $authuser->regionalclient_id;
        //     $regional = explode(',', $reg);
        //     $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name')->get();

        // } else {
            $regionalclient = RegionalClient::select('id', 'name')->get();
        // }

        return view('orders.update-reserve', ['prefix' => $this->prefix, 'getconsignments' => $getconsignments, 'consigners' => $consigners, 'consignees' => $consignees, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'consignmentno' => $consignmentno, 'drivers' => $drivers, 'regionalclient' => $regionalclient]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReserveLr(Request $request)
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

            if ($request->booked_drs == 0) {
                if (empty($request->vehicle_id)) {
                    $status = '2';
                } else {
                    $status = '1';
                }
            } else {
                $status = '1';
            }

            $getconsignment = Location::select('id', 'name', 'consignment_no')->whereIn('id', $cc)->latest('id')->first();
            if (!empty($getconsignment->consignment_no)) {
                $con_series = $getconsignment->consignment_no;
            } else {
                $con_series = '';
            }
            // $con_series = $getconsignment->consignment_no;
            $cn = ConsignmentNote::select('id', 'consignment_no', 'branch_id')->whereIn('branch_id', $cc)->latest('id')->first();
            if ($cn) {
                if (!empty($cn->consignment_no)) {
                    $cc = explode('-', $cn->consignment_no);
                    $getconsignmentno = @$cc[1] + 1;
                    $consignmentno = $cc[0] . '-' . $getconsignmentno;
                } else {
                    $consignmentno = $con_series . '-1';
                }
            } else {
                $consignmentno = $con_series . '-1';
            }
            $consignmentsave['regclient_id'] = $request->regclient_id;
            $consignmentsave['consigner_id'] = $request->consigner_id;
            $consignmentsave['consignee_id'] = $request->consignee_id;
            $consignmentsave['ship_to_id'] = $request->ship_to_id;
            $consignmentsave['consignment_no'] = $consignmentno;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['payment_type'] = $request->payment_type;
            $consignmentsave['freight'] = $request->freight;
            $consignmentsave['description'] = $request->description;
            $consignmentsave['packing_type'] = $request->packing_type;
            $consignmentsave['dispatch'] = $request->dispatch;
            $consignmentsave['total_quantity'] = $request->total_quantity;
            $consignmentsave['total_weight'] = $request->total_weight;
            $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            if ($authuser->role_id != 3) {
                $consignmentsave['branch_id'] = $authuser->branch_id;
            }
            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = $status;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            $saveconsignment = ConsignmentNote::where('id', $request->consignment_id)->update($consignmentsave);
            $consignment_id = $request->consignment_id;
            //===================== Create DRS in LR ================================= //
            if ($request->booked_drs == 0) {
                if (!empty($request->vehicle_id)) {
                    $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode', 'vehicles.regn_no as regn_no', 'drivers.name as driver_name', 'drivers.phone as driver_phone')
                        ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                        ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                        ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                        ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                        ->where('consignment_notes.id', $consignment_id)
                        ->first(['consignees.city']);
                    $simplyfy = json_decode(json_encode($consignmentdrs), true);
                    //echo'<pre>'; print_r($simplyfy); die;

                    $no_of_digit = 5;
                    $drs = DB::table('transaction_sheets')->select('drs_no')->latest('drs_no')->first();
                    $drs_no = json_decode(json_encode($drs), true);
                    if (empty($drs_no) || $drs_no == null) {
                        $drs_no['drs_no'] = 0;
                    }
                    $number = $drs_no['drs_no'] + 1;
                    $drs_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);

                    $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $simplyfy['id'], 'consignee_id' => $simplyfy['consignee_name'], 'consignment_date' => $simplyfy['consignment_date'], 'branch_id' => $authuser->branch_id, 'city' => $simplyfy['city'], 'pincode' => $simplyfy['pincode'], 'total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'], 'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'order_no' => '1', 'delivery_status' => 'Assigned', 'status' => '1']);
                }
            }else if($request->booked_drs == 1){

                $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode', 'vehicles.regn_no as regn_no', 'drivers.name as driver_name', 'drivers.phone as driver_phone')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                ->where('consignment_notes.id', $consignment_id)
                ->first(['consignees.city']);
            $simplyfy = json_decode(json_encode($consignmentdrs), true);

            $transaction = DB::table('transaction_sheets')->where('consignment_no',$simplyfy['id'])->update(['total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'], 'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'delivery_status' => 'Assigned']);
            }
            //===========================End drs lr ================================= //
            if ($saveconsignment) {

                /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
                $vn = $consignmentsave['vehicle_id'];
                $lid = $request->consignment_id;
                $lrdata = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name', 'consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $lid)
                    ->get();
                $simplyfy = json_decode(json_encode($lrdata), true);
                //echo "<pre>";print_r($simplyfy);die;
                //Send Data to API
                $get_data = ConsignmentNote::where('id', $request->consignment_id)->first();

                if (!empty($vn) && !empty($simplyfy[0]['team_id']) && !empty($simplyfy[0]['fleet_id'])) {
                    $createTask = $this->createTookanTasks($simplyfy);
                    $json = json_decode($createTask[0], true);
                    $job_id = $json['data']['job_id'];
                    $tracking_link = $json['data']['tracking_link'];
                    $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
                }
                // insert consignment items
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
                        $save_data['consignment_id'] = $request->consignment_id;
                        $save_data['status'] = 1;
                        $saveconsignmentitems = ConsignmentItem::create($save_data);
                    }
                }
                $url = URL::to($this->prefix . '/consignments');
                $response['success'] = true;
                $response['success_message'] = "Order Updated successfully";
                $response['error'] = false;
                $response['page'] = 'update-order';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not updated consignment please try again";
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

}