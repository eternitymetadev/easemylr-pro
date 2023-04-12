<?php

namespace App\Http\Controllers;

use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentSubItem;
use App\Models\Driver;
use App\Models\ItemMaster;
use App\Models\Job;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Role;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Zone;
use Auth;
use Carbon\Carbon;
use DB;
use Helper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use QrCode;
use Storage;
use URL;
use Validator;

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

        $query = $query->whereIn('lr_type', [1, 2])->where('status', 5)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'PrsDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 6) {
            $query = $query->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {

            $query = $query->whereIn('branch_id', $cc)->orWhere(function ($query) use ($cc) {
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
        $getconsignments = ConsignmentNote::with('ConsignmentItem.ConsignmentSubItems', 'RegClient')->where('id', $id)->first();
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

        // if ($authuser->role_id == 1) {
        //     $branchs = Location::select('id', 'name')->get();
        // } elseif ($authuser->role_id == 2) {
        //     $branchs = Location::select('id', 'name')->where('id', $cc)->get();
        // } elseif ($authuser->role_id == 5) {
        //     $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        // } else {
        $branchs = Location::select('id', 'name')->get();
        // }

        return view('orders.update-order', ['prefix' => $this->prefix, 'getconsignments' => $getconsignments, 'consigners' => $consigners, 'consignees' => $consignees, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists, 'branchs' => $branchs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request)
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
            $branch_add = BranchAddress::get();
            $locations = Location::whereIn('id', $cc)->first();

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
            $consignmentsave['freight'] = $request->freight;
            $consignmentsave['freight_on_delivery'] = $request->freight_on_delivery;
            $consignmentsave['cod'] = $request->cod;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;

            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            // $consignmentsave['branch_id'] = $authuser->branch_id;

            $consignmentsave['edd'] = $request->edd;
            // $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['status'] = $status;

            $regional_email = [];
            $regional_id = RegionalClient::where('id', $request->regclient_id)->first();
            if ($regional_id->is_email_sent == 1) {
                $regional_email[] = $regional_id->email;
            }
            $consigner_id = Consigner::where('id', $request->consigner_id)->first();
            if ($consigner_id->is_email_sent == 1) {
                $regional_email[] = $consigner_id->email;
            }

            if ($request->lr_type == 1 || $request->lr_type == 2) {
                $consignee = Consignee::where('id', $request->consignee_id)->first();
                $consignee_pincode = $consignee->postal_code;

                $getpin_transfer = Zone::where('postal_code', $consignee_pincode)->first();

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

                $consignmentsave['to_branch_id'] = $get_branch_id;

                ///h2h branch check
                if ($location_name == $get_zonebranch) {
                    if (!empty($request->vehicle_id)) {
                        $consignmentsave['delivery_status'] = "Started";
                    } else {
                        $consignmentsave['delivery_status'] = "Unassigned";
                    }
                    $consignmentsave['hrs_status'] = 3;
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
                                $saveconsignmentitems = ConsignmentItem::where('id', $save_data['item_id'])->update($savedata);

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

                                            $savesubitems = ConsignmentSubItem::where('id', $save_itemdata['subitem_id'])->update($saveitemdata);
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
                                $saveconsignmentitems = ConsignmentItem::where('id', $save_data['item_id'])->update($savedata);
                            }
                        }
                    }
                } else {

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
                                $saveconsignmentitems = ConsignmentItem::where('id', $save_data['item_id'])->update($savedata);
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

                                            $savesubitems = ConsignmentSubItem::where('id', $save_itemdata['subitem_id'])->update($saveitemdata);

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
                                $saveconsignmentitems = ConsignmentItem::where('id', $save_data['item_id'])->update($savedata);
                            }
                        }
                    }
                }

            } else {
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
                            $saveconsignmentitems = ConsignmentItem::where('id', $save_data['item_id'])->update($savedata);
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

                                        $savesubitems = ConsignmentSubItem::where('id', $save_itemdata['subitem_id'])->update($saveitemdata);
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
                            $saveconsignmentitems = ConsignmentItem::where('id', $save_data['item_id'])->update($savedata);
                        }
                    }
                }
            }
            /////////////////// drs api push ////////////////////////////

            $consignment_id = $request->consignment_id;
            //  ======================== Send Email  ===================================//
            if (!empty($regional_email)) {
                $getdata = ConsignmentNote::where('id', $consignment_id)->with('ConsignmentItems', 'ConsignerDetail.GetZone', 'ConsigneeDetail.GetZone', 'ShiptoDetail.GetZone', 'VehicleDetail', 'DriverDetail')->first();
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

                if (isset($data['consignee_detail']['legal_name'])) {
                    $nick_name = '<b>' . $data['consignee_detail']['legal_name'] . '</b><br>';
                } else {
                    $nick_name = '';
                }
                if (isset($data['consignee_detail']['address_line1'])) {
                    $address_line1 = '' . $data['consignee_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if (isset($data['consignee_detail']['address_line2'])) {
                    $address_line2 = '' . $data['consignee_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if (isset($data['consignee_detail']['address_line3'])) {
                    $address_line3 = '' . $data['consignee_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if (isset($data['consignee_detail']['address_line4'])) {
                    $address_line4 = '' . $data['consignee_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if (isset($data['consignee_detail']['city'])) {
                    $city = $data['consignee_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if (isset($data['consignee_detail']['get_zone']['state'])) {
                    $district = $data['consignee_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if (isset($data['consignee_detail']['postal_code'])) {
                    $postal_code = $data['consignee_detail']['postal_code'] . '<br>';
                } else {
                    $postal_code = '';
                }

                if (isset($data['consignee_detail']['gst_number'])) {
                    $gst_number = 'GST No: ' . $data['consignee_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if (isset($data['consignee_detail']['phone'])) {
                    $phone = 'Phone No: ' . $data['consignee_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }

                $consnee_add = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

                if (isset($data['shipto_detail']['legal_name'])) {
                    $nick_name = '<b>' . $data['shipto_detail']['legal_name'] . '</b><br>';
                } else {
                    $nick_name = '';
                }
                if (isset($data['shipto_detail']['address_line1'])) {
                    $address_line1 = '' . $data['shipto_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if (isset($data['shipto_detail']['address_line2'])) {
                    $address_line2 = '' . $data['shipto_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if (isset($data['shipto_detail']['address_line3'])) {
                    $address_line3 = '' . $data['shipto_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if (isset($data['shipto_detail']['address_line4'])) {
                    $address_line4 = '' . $data['shipto_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if (isset($data['shipto_detail']['city'])) {
                    $city = $data['shipto_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if (isset($data['shipto_detail']['get_zone']['state'])) {
                    $district = $data['shipto_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if (isset($data['shipto_detail']['postal_code'])) {
                    $postal_code = $data['shipto_detail']['postal_code'] . '<br>';
                } else {
                    $postal_code = '';
                }
                if (isset($data['shipto_detail']['gst_number'])) {
                    $gst_number = 'GST No: ' . $data['shipto_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if (isset($data['shipto_detail']['phone'])) {
                    $phone = 'Phone No: ' . $data['shipto_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }

                $shiptoadd = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

                $generate_qrcode = QrCode::size(150)->generate('' . $consignment_id . '');
                $output_file = '/qr-code/img-' . time() . '.svg';
                Storage::disk('public')->put($output_file, $generate_qrcode);
                $fullpath = storage_path('app/public/' . $output_file);
                //  dd($generate_qrcode);
                $no_invoive = count($data['consignment_items']);

                if ($request->typeid == 1) {
                    $adresses = '<table width="100%">
                        <tr>
                            <td style="width:50%">' . $conr_add . '</td>
                            <td style="width:50%">' . $consnee_add . '</td>
                        </tr>
                    </table>';
                } else if ($request->typeid == 2) {
                    $adresses = '<table width="100%">
                            <tr>
                                <td style="width:33%">' . $conr_add . '</td>
                                <td style="width:33%">' . $consnee_add . '</td>
                                <td style="width:33%">' . $shiptoadd . '</td>
                            </tr>
                        </table>';
                }

                // get branch address
                if ($locations->id == 2 || $locations->id == 6 || $locations->id == 26) {
                    $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[1]->name . ' </b></span><br />
            <b>' . $branch_add[1]->address . ',</b><br />
            <b>	' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . '</b><br />
            <b>GST No. : ' . $branch_add[1]->gst_number . '</b><br />';
                } else {
                    $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[0]->name . ' </b></span><br />
            <b>	Plot no: ' . $branch_add[0]->address . ',</b><br />
            <b>	' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . '</b><br />
            <b>GST No. : ' . $branch_add[0]->gst_number . '</b><br />';
                }

                // relocate cnr cnee address check for sale to return case
                if ($data['is_salereturn'] == '1') {
                    $cnradd_heading = '<div class="container">
                <div>
                <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
                </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                ' . $consnee_add . '
                </p>
                </div>';
                    $cneadd_heading = '<div class="container">
                <div>
                <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
                </div>
                    <div style="margin-top: -11px;">
                    <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                    ' . $conr_add . '
                </p>
                </div>';
                    $shipto_address = '';
                } else {
                    $cnradd_heading = '<div class="container">
                <div>
                <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
                </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                ' . $conr_add . '
                </p>
                </div>';
                    $cneadd_heading = '<div class="container">
                <div>
                <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
                </div>
                    <div style="margin-top: -11px;">
                    <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                    ' . $consnee_add . '
                </p>
                </div>';
                    $shipto_address = '<td width="30%" style="vertical-align:top;>
                <div class="container">
                <div>
                <h5  style="margin-left:6px; margin-top: 0px">SHIP TO NAME & ADDRESS</h5><br>
                </div>
                    <div style="margin-top: -11px;">
                    <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                  ' . $shiptoadd . '
                </p>
                    </div>
                </td>';
                }

                $logo = public_path('assets/img/logo_2.png');
                $waterMark = public_path('assets/img/eternity-forwarders-logo-square.png');
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
                   bottom: 50px;
                   padding: 10px 2rem;

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
                @page {
                  margin-bottom: 0;
                  margin-right: 0;
                  margin-left: 0;
                }
                .businessInfo{
                    background: #F9B808;
                    font-size: 14px;
                    line-height: 20px;
                    padding: 6px;
                    text-align: center;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    height: 50px;
                    width: 100%;
                    margin-inline: 2rem;
                }


                        </style>
                    <!-- style="border-collapse: collapse; width: 369px; height: 72px; background:#d2c5c5;"class="table2" -->
                    </head>
                    <body style="font-family:Arial Helvetica,sans-serif;">
                    <img src="' . $waterMark . '" alt="" style="position:fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); opacity: 0.2; width: 500px; height: 500px; z-index: -1;" />
                        <div class="container-flex" style="margin-bottom: 5px; margin-top: -30px; padding: 0 2rem ">
                            <table style="height: 70px; margin-inline: 1rem;">
                                <tr>
                                <td class="a" style="font-size: 10px;">
                                ' . $branch_address . '
                                </td>

                                    <td class="a">
                                    <b>	Email & Phone</b><br />
                                    <b>	' . @$locations->email . '</b><br />
                                    ' . @$locations->phone . '<br />

                                    </td>
                                     <td>
                                     <img class="logoImg" src="' . $logo . '" style="width: 100%;"/>
                                     </td>
                                </tr>

                            </table>
                            <hr />
                            <table style="margin-inline: 1rem;">
                                <tr>
                                    <td class="b">
                                        <div class="ff" >
                                            <img src="' . $fullpath . '" alt="" class="imgu" />
                                        </div>
                                    </td>
                                    <td>
                                        <div style="margin-top: -15px; text-align: center">
                                            <h2 style="margin-bottom: -16px">CONSIGNMENT NOTE</h2>
                                            <P> Original </P>
                                        </div>
                                    <div class="mini-table1" style="background:#C0C0C0;">
                                        <table style=" border-collapse: collapse;" class="ee">
                                            <tr>
                                                <th class="mini-th mm nn">LR Number</th>
                                                <th class="mini-th mm nn">LR Date</th>
                                                <th class="mini-th mm nn">Dispatch</th>
                                                <th class="mini-th nn">Delivery</th>
                                            </tr>
                                            <tr>
                                                <th class="mini-th mm" >' . $data['id'] . '</th>
                                                <th class="mini-th mm">' . date('d-m-Y', strtotime($data['consignment_date'])) . '</th>
                                                <th class="mini-th mm"> ' . @$data['consigner_detail']['city'] . '</th>
                                                <th class="mini-th">' . @$data['consignee_detail']['city'] . '</th>

                                            </tr>
                                        </table>
                            </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="loc">
                                <table style="margin-inline: 1rem;">
                                    <tr>
                                        <td class="width_set">
                                            <div style="margin-left: 20px">
                                        <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i><div class="vl" ></div>

                                            <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consignee_detail']['postal_code'] . ',' . @$data['consignee_detail']['city'] . ',' . @$data['consignee_detail']['get_zone']['state'] . '</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>
                                            </div>
                                        </td>
                                        <td class="width_set">
                                            <table border="1px solid" class="table3">
                                                <tr>
                                                    <td width="40%" ><b style="margin-left: 7px;">Vehicle No</b></td>
                                                    <td>' . @$data['vehicle_detail']['regn_no'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="40%"><b style="margin-left: 7px;"> Driver Name</b></td>
                                                    <td>' . ucwords(@$data['driver_detail']['name']) . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="40%"><b style="margin-left: 7px;">Driver Number</b></td>
                                                    <td>' . ucwords(@$data['driver_detail']['phone']) . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="container">
                                    <div class="row">
                                        <div class="col-sm-12 ">
                                            <h4 style="margin-left:19px;"><b>Pickup and Drop Information</b></h4>
                                        </div>
                                    </div>
                                <table border="1" style=" border-collapse:collapse; width: 690px; ">
                                    <tr>
                                        <td width="30%" style="vertical-align:top; >
                                        ' . $cnradd_heading . '
                                        </td>
                                        <td width="30%" style="vertical-align:top;>
                                        ' . $cneadd_heading . '
                                        </td>
                                        ' . $shipto_address . '
                                    </tr>
                                </table>
                          </div>
                                <div class="row">
                                <div class="col-sm-12 ">
                                <h4 style="margin-left:19px;"><strong>Order Information</strong></h4>
                                </div>
                                </div>
                                <table border="1" style=" border-collapse:collapse; width: 690px;height: 48px; font-size: 10px; background-color:#e0dddc40;">
                                    <tr>
                                        <th>Number of invoice</th>
                                        <th>Item Description</th>
                                        <th>Mode of packing</th>
                                        <th>Total Quantity</th>
                                        <th>Total Net Weight</th>
                                        <th>Total Gross Weight</th>
                                    </tr>
                                    <tr>
                                        <th>' . $no_invoive . '</th>
                                        <th>' . $data['description'] . '</th>
                                        <th>' . $data['packing_type'] . '</th>
                                        <th>' . $data['total_quantity'] . '</th>
                                        <th>' . $data['total_weight'] . ' Kgs.</th>
                                        <th>' . $data['total_gross_weight'] . ' Kgs.</th>
                                    </tr>
                                </table>

                            <div class="inputfiled">
                                    <table style=" border-collapse:collapse; width: 690px;height: 45px; font-size: 10px; background-color:#e0dddc40; text-align: center;" border="1" >
                                         <tr>
                                              <th style="width:70px ">Order ID</th>
                                              <th style="width: 70px">Inv No</th>
                                              <th style="width: 70px">Inv Date</th>
                                              <th style="width:70px " >Inv Amount</th>
                                              <th style="width:70px ">E-way No</th>
                                              <th style="width: 70px">E-Way Date</th>
                                              <th style="width: 60px">Quantity</th>
                                              <th style="width:70px ">Net Weight</th>
                                              <th style="width:70px ">Gross Weight</th>
                                          </tr>
                                      ';
                $counter = 0;
                foreach ($data['consignment_items'] as $k => $dataitem) {
                    $counter = $counter + 1;
                    $html .= ' <tr>
                                                        <td style="width:70px ">' . $dataitem['order_id'] . '</td>
                                                        <td style="width: 70px">' . $dataitem['invoice_no'] . '</td>
                                                        <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['invoice_date']) . '</td>
                                                        <td style="width:70px ">' . $dataitem['invoice_amount'] . '</td>
                                                        <td style="width: 70px">' . $dataitem['e_way_bill'] . '</td>
                                                        <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['e_way_bill_date']) . '</td>
                                                        <td style="width:60px "> ' . $dataitem['quantity'] . '</td>
                                                        <td style="width:70px ">' . $dataitem['weight'] . ' Kgs. </td>
                                                        <td style="width:70px "> ' . $dataitem['gross_weight'] . ' Kgs.</td>
                                                   </tr>';
                }
                $html .= '</table>
                                <div>
                                        <table style="margin-top:0px;">
                                            <tr>
                                                <td width="50%" style="font-size: 13px;"><p style="margin-top:60px;"><b>Received the goods mentioned above in good conditions.</b><br><br>Receivers Name & Number:<br><br>Receiving Date & Time	:<br><br>Receiver Signature:<br><br></p></td>
                                                <td  width="50%"><p style="margin-left: 99px; margin-bottom:150px;"><b>For Eternity Forwarders Pvt.Ltd</b></p></td>
                                            </tr>
                                        </table>
                                    </div>
                                </table>
                            </div>
                            </div>

                            <div class="footer">
                                <p style="text-align:center; font-size: 10px;">Terms & Conditions</p>
                                <p style="font-size: 8px; margin-top: -5px">1. Eternity Solutons does not take any responsibility for damage,leakage,shortage,breakages,soilage by sun ran ,fire and any other damage caused.</p>
                                <p style="font-size: 8px; margin-top: -5px">2. The goods will be delivered to Consignee only against,payment of freight or on confirmation of payment by the consignor. </p>
                                <p style="font-size: 8px; margin-top: -5px">3. The delivery of the goods will have to be taken immediately on arrival at the destination failing which the  consignee will be liable to detention charges @Rs.200/hour or Rs.300/day whichever is lower.</p>
                                <p style="font-size: 8px; margin-top: -5px">4. Eternity Solutons takes absolutely no responsibility for delay or loss in transits due to accident strike or any other cause beyond its control and due to breakdown of vehicle and for the consequence thereof. </p>
                                <p style="font-size: 8px; margin-top: -5px">5. Any complaint pertaining the consignment note will be entertained only within 15 days of receipt of the material.</p>
                                <p style="font-size: 8px; margin-top: -5px">6. In case of mismatch in e-waybill & Invoice of the consignor, Eternity Solutions will impose a penalty of Rs.15000/Consignment  Note in addition to the detention charges stated above. </p>
                                <p style="font-size: 8px; margin-top: -5px">7. Any dispute pertaining to the consigment Note will be settled at chandigarh jurisdiction only.</p>
                           </div>
                            <div class="businessInfo">
                                Head Office: Plot No. B-014/03712, Prabhat, Zirakpur - 140603 | contact@eternityforwaders.com<br/>
                                CIN: U63030PB2021PTC053388
                           </div>
                        </div>
                        <!-- Optional JavaScript; choose one of the two! -->

                        <!-- Option 1: Bootstdap Bundle with Popper -->
                        <script
                            src="https://cdn.jsdelivr.net/npm/bootstdap@5.0.2/dist/js/bootstdap.bundle.min.js"
                            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                            crossorigin="anonymous"
                        ></script>

                        <!-- Option 2: Separate Popper and Bootstdap JS -->
                        <!--
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstdap@5.0.2/dist/js/bootstdap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKtdIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
                    -->
                    </body>
                </html>
                ';

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('legal', 'portrait');

                $data = ['Lr_No' => $consignment_id, 'consignor' => $data['consigner_detail']['legal_name'], 'consignee_name' => $data['consignee_detail']['legal_name'], 'consignee_pin' => $data['consignee_detail']['postal_code'], 'net_weigth' => $data['total_weight'], 'cases' => $data['total_quantity'], 'client' => $regional_id->name];
                $user['to'] = $regional_email;
                Mail::send('consignments.email-template', $data, function ($messges) use ($user, $pdf, $consignment_id) {
                    $messges->to($user['to']);
                    $messges->subject('Your Order has been picked & is ready to Ship : LR No. ' . $consignment_id . '');
                    $messges->attachData($pdf->output(), "LR .$consignment_id.pdf");

                });
            }
            // ================================end Send Email ============================= //
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
                    $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link, 'lr_mode' => 1]);
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
    public function updateOrderOld(Request $request)
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
            $consignmentsave['freight_on_delivery'] = $request->freight_on_delivery;
            $consignmentsave['freight'] = $request->freight;
            $consignmentsave['cod'] = $request->cod;
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
            $mytime = Carbon::now('Asia/Kolkata');
            $currentdate = $mytime->toDateTimeString();
            //===========================End drs lr ================================= //
            // if ($saveconsignment) {
            /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
            $get_driver_details = Driver::select('branch_id')->where('id', $request->driver_id)->first();

            // check app assign ========================================
            if (!empty($get_driver_details->branch_id)) {
                $driver_branch = explode(',', $get_driver_details->branch_id);
                if (in_array($authuser->branch_id, $driver_branch)) {
                    $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);

                    // task created
                    $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                    $respons_data = json_encode($respons);
                    $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create
                    // =================== task assign
                    $respons2 = array('consignment_id' => $saveconsignment->id, 'status' => 'Assigned', 'create_at' => $currentdate, 'type' => '2');

                    $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('consignment_id')->first();
                    $st = json_decode($lastjob->response_data);
                    array_push($st, $respons2);
                    $sts = json_encode($st);

                    $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Assigned', 'type' => '2']);
                    // ==== end started
                    $app_notify = $this->sendNotification($request->driver_id);
                } else {
                    // task created
                    $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                    $respons_data = json_encode($respons);
                    $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create
                }
                // if(!empty($request->driver_id)){
                //     $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);
                // }
            } else {
                // task created
                $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                $respons_data = json_encode($respons);
                $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                // ==== end create
            }

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

            $prs_regclientcheck = Regionalclient::where('id', $request->regclient_id)->first();
            if ($prs_regclientcheck->is_prs_pickup == 1) {
                $prsitem_status = '2';
                $status = '6'; //lr without pickup and without edit
                $hrs_status = '3';

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
                $prsitem_status = '0';
                $status = '5';
                $to_branch_id = null;
                $hrs_status = '2';

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
            // $consignmentsave['total_quantity'] = $request->total_quantity;
            // $consignmentsave['total_weight'] = $request->total_weight;
            // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
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
            $consignmentsave['prsitem_status'] = $prsitem_status;
            $consignmentsave['to_branch_id'] = $to_branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = $status;
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

            $consignmentsave['fall_in'] = $get_branch_id;

            ///h2h branch check
            if ($location_name == $get_zonebranch) {
                if (!empty($request->vehicle_id)) {
                    $consignmentsave['delivery_status'] = "Started";
                } else {
                    $consignmentsave['delivery_status'] = "Unassigned";
                }
                $consignmentsave['hrs_status'] = $hrs_status;
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
            } else {
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

            $mytime = Carbon::now('Asia/Kolkata');
            $currentdate = $mytime->toDateTimeString();
            //===========================End drs lr ================================= //
            // if ($saveconsignment) {
            /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
            $get_driver_details = Driver::select('branch_id')->where('id', $request->driver_id)->first();

            // check app assign ========================================
            if (!empty($get_driver_details->branch_id)) {
                $driver_branch = explode(',', $get_driver_details->branch_id);
                if (in_array($authuser->branch_id, $driver_branch)) {
                    $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);

                    // task created
                    $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                    $respons_data = json_encode($respons);
                    $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create
                    // =================== task assign
                    $respons2 = array('consignment_id' => $saveconsignment->id, 'status' => 'Assigned', 'create_at' => $currentdate, 'type' => '2');

                    $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('consignment_id')->first();
                    $st = json_decode($lastjob->response_data);
                    array_push($st, $respons2);
                    $sts = json_encode($st);

                    $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Assigned', 'type' => '2']);
                    // ==== end started
                    $app_notify = $this->sendNotification($request->driver_id);
                } else {
                    // task created
                    $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                    $respons_data = json_encode($respons);
                    $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create
                }
                // if(!empty($request->driver_id)){
                //     $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);
                // }
            } else {
                // task created
                $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                $respons_data = json_encode($respons);
                $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                // ==== end create
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

        $update_receve = ConsignmentNote::where('id', $request->lr_id)->update(['prsitem_status' => 2, 'prs_remarks' => $request->prs_remarks]);

          // =================== task assign ====== //
          $mytime = Carbon::now('Asia/Kolkata');
          $currentdate = $mytime->toDateTimeString();

          $respons2 = array('consignment_id' => $request->lr_id, 'status' => 'Prs Created', 'create_at' => $currentdate, 'type' => '2');

          $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $request->lr_id)->latest('consignment_id')->first();
          if (!empty($lastjob->response_data)) {
              $st = json_decode($lastjob->response_data);
              array_push($st, $respons2);
              $sts = json_encode($st);

              $start = Job::create(['consignment_id' => $request->lr_id, 'response_data' => $sts, 'status' => 'Prs Created', 'type' => '2']);
          }
          // ==== end started
         

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
        } elseif ($authuser->role_id == 4) {
            $data = $data->whereIn('consignment_notes.regclient_id', $regclient);
            // $data = $data->where('consignment_notes.user_id', $authuser->id);
        } elseif ($authuser->role_id == 6) {
            $data = $data->whereIn('base_clients.id', $baseclient);
        } elseif ($authuser->role_id == 7) {
            $data = $data->whereIn('regional_clients.id', $regclient);
        } else {
            $data = $data->whereIn('consignment_notes.branch_id', $cc);
        }
        $data = $data->where('consignment_notes.status', '5')->orderBy('id', 'DESC');

        $consignments = $data->get();

        if ($request->ajax()) {
            if (isset($request->updatestatus)) {
                ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel, 'delivery_status' => 'Cancel']);
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
            // $consignmentsave['consignment_date'] = $request->consignment_date;
            // $consignmentsave['payment_type'] = $request->payment_type;
            // $consignmentsave['freight'] = $request->freight;
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
            } else if ($request->booked_drs == 1) {

                $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode', 'vehicles.regn_no as regn_no', 'drivers.name as driver_name', 'drivers.phone as driver_phone')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $consignment_id)
                    ->first(['consignees.city']);
                $simplyfy = json_decode(json_encode($consignmentdrs), true);

                $transaction = DB::table('transaction_sheets')->where('consignment_no', $simplyfy['id'])->update(['total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'], 'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'delivery_status' => 'Assigned']);
            }
            //===========================End drs lr ================================= //
            /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/

            $mytime = Carbon::now('Asia/Kolkata');
            $currentdate = $mytime->toDateTimeString();
            //===========================End drs lr ================================= //
            // if ($saveconsignment) {
            /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
            if(!empty($request->driver_id)){
            $get_driver_details = Driver::select('access_status','branch_id')->where('id', $request->driver_id)->first();

            // check app assign ========================================
           if ($get_driver_details->access_status == 1) {
            if (!empty($get_driver_details->branch_id)) {
                $driver_branch = explode(',', $get_driver_details->branch_id);
                if (in_array($authuser->branch_id, $driver_branch)) {
                    $update = DB::table('consignment_notes')->where('id', $consignment_id)->update(['lr_mode' => 2]);

                    // // task created
                    // $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                    // $respons_data = json_encode($respons);
                    // $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // // ==== end create
                    // =================== task assign
                    $respons2 = array('consignment_id' => $consignment_id, 'status' => 'Assigned', 'create_at' => $currentdate, 'type' => '2');

                    $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $consignment_id)->latest('consignment_id')->first();
                    $st = json_decode($lastjob->response_data);
                    array_push($st, $respons2);
                    $sts = json_encode($st);

                    $start = Job::create(['consignment_id' => $consignment_id, 'response_data' => $sts, 'status' => 'Assigned', 'type' => '2']);
                    // ==== end started
                    $app_notify = $this->sendNotification($request->driver_id);
                } else {
                    // task created
                    // $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                    // $respons_data = json_encode($respons);
                    // $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                    // ==== end create
                }
                // if(!empty($request->driver_id)){
                //     $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);
                // }
            } else {
                // task created
                // $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created', 'create_at' => $currentdate, 'type' => '2']);
                // $respons_data = json_encode($respons);
                // $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
                // ==== end create
            }
        }
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
            // } else {
            //     $response['success'] = false;
            //     $response['error_message'] = "Can not updated consignment please try again";
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
    public function sendNotification($request)
    {

        $firebaseToken = Driver::where('id', $request)->whereNotNull('device_token')->pluck('device_token')->all();

        $SERVER_API_KEY = "AAAAd3UAl0E:APA91bFmxnV3YOAWBLrjOVb8n2CRiybMsXsXqKwDtYdC337SE0IRr1BTFLXWflB5VKD-XUjwFkS4v7I2XlRo9xmEYcgPOqrW0fSq255PzfmEwXurbxzyUVhm_jS37-mtkHFgLL3yRoXh";

        $data_json = ['type' => 'Assigned', 'status' => 1];

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "LR Assigned",
                "body" => "New LR assigned to you, please check",
            ],
            "data" => $data_json,
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        return $response;
    }

}
