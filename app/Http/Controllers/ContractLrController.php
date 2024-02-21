<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consigner;
use App\Models\Location;
use App\Models\User;
use App\Models\Role;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\BranchAddress;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\RegionalClient;
use App\Models\ItemMaster;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Job;
use Carbon\Carbon;
use Validator;
use Response;
use Session;
use Storage;
use Helper;
use QrCode;
use Config;
use Auth;
use Mail;
use URL;
use DB;

class ContractLrController extends Controller
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
    public function index1(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = ConsignmentNote::query();

        if ($request->ajax()) {
            $authuser = Auth::user();

            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }
            if (isset($request->updatestatus)) {
                $lr_cancel = ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel, 'cancel_userid' => $authuser->id, 'delivery_status' => 'Cancel']);

                $url = $this->prefix . '/contract-lrs';
                $response['success'] = true;
                $response['success_message'] = "Consignment updated successfully";
                $response['error'] = false;
                $response['page'] = 'consignment-updateupdate';
                $response['redirect_url'] = $url;

                return response()->json($response);
            }

            
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);

            $query = $query->where('lr_type', 3)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail', 'JobDetail');

            if ($authuser->role_id == 1) {
                $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 8){
                $query;
            } else {
                $query = $query->where(function ($query) use ($cc) {
                    $query->whereIn('branch_id', $cc);
                });
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

            $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            $consignments = $consignments->appends($request->query());
            
            $html = view('contract-lr.consignment-list-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $query = $query->where('lr_type', 3)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail', 'JobDetail');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        }elseif($authuser->role_id == 8){
            $query;
        } else {
            $query = $query->whereIn('branch_id', $cc);
            // $query = $query->whereIn('branch_id', $cc)->orWhereIn('fall_in', $cc);
        }
        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('contract-lr.consignment-list', ['consignments' => $consignments, 'peritem' => $peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
    }
    
    public function index(Request $request)
    {
        $this->prefix = $request->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = ConsignmentNote::query()->where('lr_type', 3)
            ->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'VehicleDetail', 'DriverDetail', 'JobDetail');

        if ($request->ajax()) {
            if ($request->filled('resetfilter')) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            if ($request->filled('updatestatus')) {
                $authuser = Auth::user();
                $lr_cancel = ConsignmentNote::where('id', $request->id)->update([
                    'status' => $request->status,
                    'reason_to_cancel' => $request->reason_to_cancel,
                    'cancel_userid' => $authuser->id,
                    'delivery_status' => 'Cancel'
                ]);

                $url = $this->prefix . '/contract-lrs';
                $response = [
                    'success' => true,
                    'success_message' => "Consignment updated successfully",
                    'error' => false,
                    'page' => 'consignment-updateupdate',
                    'redirect_url' => $url
                ];

                return response()->json($response);
            }

            $authuser = Auth::user();
            $query = $this->applyRoleFilters($query, $authuser);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', "%$search%")
                        ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('ConsignerDetail', function ($query) use ($search) {
                            $query->where('nick_name', 'like', "%$search%");
                        })
                        ->orWhereHas('ConsigneeDetail', function ($query) use ($search) {
                            $query->where('nick_name', 'like', "%$search%");
                        });
                });
            }

            if ($request->filled('peritem')) {
                Session::put('peritem', $request->peritem);
            }

            $peritem = Session::get('peritem', Config::get('variable.PER_PAGE'));
            $consignments = $query->orderByDesc('id')->paginate($peritem);
            $consignments->appends($request->query());
            
            $html = view('contract-lr.consignment-list-ajax', [
                'prefix' => $this->prefix,
                'consignments' => $consignments,
                'peritem' => $peritem
            ])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $query = $this->applyRoleFilters($query, $authuser);
        $consignments = $query->orderByDesc('id')->paginate($peritem);
        $consignments->appends($request->query());

        return view('contract-lr.consignment-list', [
            'consignments' => $consignments,
            'peritem' => $peritem,
            'prefix' => $this->prefix,
            'segment' => $this->segment
        ]);
    }

    private function applyRoleFilters($query, $authuser)
    {
        switch ($authuser->role_id) {
            case 1:
                // No additional filters needed
                break;
            case 4:
            case 7:
                $query->whereIn('regclient_id', explode(',', $authuser->regionalclient_id));
                break;
            case 8:
                // No additional filters needed
                break;
            default:
                $query->whereIn('branch_id', explode(',', $authuser->branch_id));
                break;
        }

        return $query;
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
            $locations = Location::whereIn('id', $cc)->first();
            $branch_add = BranchAddress::get();
    
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
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['branch_id'] = $authuser->branch_id;
            // $consignmentsave['vehicle_type'] = $request->vehicle_type;
            // $consignmentsave['vehicle_id'] = $request->vehicle_id;
            // $consignmentsave['driver_id'] = $request->driver_id;
            
            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['delivery_status'] = "Started";
            $consignmentsave['lr_type'] = 3;  // lr-type =0-ftl, 1-ptl, 2-prs, 3-lr-contract
            $consignmentsave['status'] = 1;

            $regional_email = [];
            $regional_id = RegionalClient::where('id', $request->regclient_id)->first();
            if ($regional_id->is_email_sent == 1) {
                $regional_email[] = $regional_id->email;
            }
            $consigner_id = Consigner::where('id', $request->consigner_id)->first();
            if ($consigner_id->is_email_sent == 1) {
                $regional_email[] = $consigner_id->email;
            }
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
                            if (!empty($save_data['item_data'])) {
                                $qty_array = array();
                                $netwt_array = array();
                                $grosswt_array = array();
                                $chargewt_array = array();
                                foreach ($save_data['item_data'] as $key => $save_itemdata) {
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
            
            $mytime = Carbon::now('Asia/Kolkata'); 
            $currentdate = $mytime->toDateTimeString();
            // task created
            $respons = array(['consignment_id' => $saveconsignment->id, 'status' => 'Created','desc'=> 'Order Placed', 'location'=>$locations->name,'create_at' => $currentdate, 'type' => '2']);
            $respons_data = json_encode($respons);
            $create = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $respons_data, 'status' => 'Created', 'type' => '2']);
            // ==== end create

            // task assign
            $respons2 = array('consignment_id' => $saveconsignment->id, 'status' => 'Created','desc'=> 'Consignment Menifested at','location'=>$locations->name, 'create_at' => $currentdate, 'type' => '2');

            $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('id')->first();
            if(!empty($lastjob->response_data)){
                $st = json_decode($lastjob->response_data);
                array_push($st, $respons2);
                $sts = json_encode($st);

                $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Created', 'type' => '2']);
            }
            // ==== end started

            // task assign // commented at 19feb24 code working fine
            $respons3 = array('consignment_id' => $saveconsignment->id, 'status' => 'Assigned','desc'=> 'Out for Delivery','location'=>$locations->name, 'create_at' => $currentdate, 'type' => '2');

            $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $saveconsignment->id)->latest('id')->first();
            if(!empty($lastjob->response_data)){
                $st = json_decode($lastjob->response_data);
                array_push($st, $respons3);
                $sts = json_encode($st);

                $start = Job::create(['consignment_id' => $saveconsignment->id, 'response_data' => $sts, 'status' => 'Assigned', 'type' => '2']);
            }
            // ==== end started

            $url = $this->prefix . '/contract-lrs';
            $response['success'] = true;
            $response['success_message'] = "Consignment Added successfully";
            $response['error'] = false;
            $response['page'] = 'create-contractlr';
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

    public function contractBulkLrList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $regclient = explode(',', $authuser->regionalclient_id);
        $branchIds = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = ConsignmentNote::query();

        $query = $query->where('lr_type', 3)
            ->with('ConsignerDetail', 'ConsigneeDetail');

        if ($authuser->role_id == 4 || $authuser->role_id == 7) {
            $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id != 1) {
            $query->whereIn('branch_id', $branchIds);
        }
        $consignments = $query->orderBy('id', 'DESC')->get();

        return view('contract-lr.bulkLr-list', ['prefix' => $this->prefix, 'consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title]);
    }

    public function downloadBulkLr(Request $request)
    {
        $lrno = $request->checked_lr;
        $pdftype = $request->type;

        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::get();
        $locations = Location::whereIn('id', $cc)->first();

        if (is_null($lrno)) {
            return redirect()->back()->with('message', 'Data not found!');
        }
        
        foreach ($lrno as $key => $value) {

            $getdata = ConsignmentNote::where('id', $value)->with('ConsignmentItems', 'ConsignerDetail.GetZone', 'ConsigneeDetail.GetZone', 'ShiptoDetail.GetZone', 'VehicleDetail', 'DriverDetail')->first();
            $data = json_decode(json_encode($getdata), true);
            
            if ($data['consigner_detail']['legal_name'] != null) {
                $legal_name = '<b>' . $data['consigner_detail']['legal_name'] . '</b><br>';
            } else {
                $legal_name = '';
            }
            if ($data['consigner_detail']['address_line1'] != null) {
                $address_line1 = '' . $data['consigner_detail']['address_line1'] . '<br>';
            } else {
                $address_line1 = '';
            }
            if ($data['consigner_detail']['address_line2'] != null) {
                $address_line2 = '' . $data['consigner_detail']['address_line2'] . '<br>';
            } else {
                $address_line2 = '';
            }
            if ($data['consigner_detail']['address_line3'] != null) {
                $address_line3 = '' . $data['consigner_detail']['address_line3'] . '<br>';
            } else {
                $address_line3 = '';
            }
            if ($data['consigner_detail']['address_line4'] != null) {
                $address_line4 = '' . $data['consigner_detail']['address_line4'] . '<br><br>';
            } else {
                $address_line4 = '<br>';
            }
            if ($data['consigner_detail']['city'] != null) {
                $city = $data['consigner_detail']['city'] . ',';
            } else {
                $city = '';
            }
            if ($data['consigner_detail']['get_zone'] != null) {
                $district = $data['consigner_detail']['get_zone']['state'] . ',';
            } else {
                $district = '';
            }
            if ($data['consigner_detail']['postal_code'] != null) {
                $postal_code = $data['consigner_detail']['postal_code'] . '<br>';
            } else {
                $postal_code = '';
            }
            if ($data['consigner_detail']['gst_number'] != null) {
                $gst_number = 'GST No: ' . $data['consigner_detail']['gst_number'] . '<br>';
            } else {
                $gst_number = '';
            }
            if ($data['consigner_detail']['phone'] != null) {
                $phone = 'Phone No: ' . $data['consigner_detail']['phone'] . '<br>';
            } else {
                $phone = '';
            }

            $conr_add = $legal_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

            if (@$data['consignee_detail']['legal_name'] != null) {
                $nick_name = '<b>' . $data['consignee_detail']['legal_name'] . '</b><br>';
            } else {
                $nick_name = '';
            }
            if (@$data['consignee_detail']['address_line1'] != null) {
                $address_line1 = '' . $data['consignee_detail']['address_line1'] . '<br>';
            } else {
                $address_line1 = '';
            }
            if (@$data['consignee_detail']['address_line2'] != null) {
                $address_line2 = '' . $data['consignee_detail']['address_line2'] . '<br>';
            } else {
                $address_line2 = '';
            }
            if (@$data['consignee_detail']['address_line3'] != null) {
                $address_line3 = '' . $data['consignee_detail']['address_line3'] . '<br>';
            } else {
                $address_line3 = '';
            }
            if (@$data['consignee_detail']['address_line4'] != null) {
                $address_line4 = '' . $data['consignee_detail']['address_line4'] . '<br><br>';
            } else {
                $address_line4 = '<br>';
            }
            if (@$data['consignee_detail']['city'] != null) {
                $city = $data['consignee_detail']['city'] . ',';
            } else {
                $city = '';
            }
            if (@$data['consignee_detail']['get_zone'] != null) {
                $district = $data['consignee_detail']['get_zone']['state'] . ',';
            } else {
                $district = '';
            }
            if (@$data['consignee_detail']['postal_code'] != null) {
                $postal_code = $data['consignee_detail']['postal_code'] . '<br>';
            } else {
                $postal_code = '';
            }

            if (@$data['consignee_detail']['gst_number'] != null) {
                $gst_number = 'GST No: ' . $data['consignee_detail']['gst_number'] . '<br>';
            } else {
                $gst_number = '';
            }
            if (@$data['consignee_detail']['phone'] != null) {
                $phone = 'Phone No: ' . $data['consignee_detail']['phone'] . '<br>';
            } else {
                $phone = '';
            }

            $consnee_add = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

            if (@$data['shipto_detail']['legal_name'] != null) {
                $nick_name = '<b>' . $data['shipto_detail']['legal_name'] . '</b><br>';
            } else {
                $nick_name = '';
            }
            if (@$data['shipto_detail']['address_line1'] != null) {
                $address_line1 = '' . $data['shipto_detail']['address_line1'] . '<br>';
            } else {
                $address_line1 = '';
            }
            if (@$data['shipto_detail']['address_line2'] != null) {
                $address_line2 = '' . $data['shipto_detail']['address_line2'] . '<br>';
            } else {
                $address_line2 = '';
            }
            if (@$data['shipto_detail']['address_line3'] != null) {
                $address_line3 = '' . $data['shipto_detail']['address_line3'] . '<br>';
            } else {
                $address_line3 = '';
            }
            if (@$data['shipto_detail']['address_line4'] != null) {
                $address_line4 = '' . $data['shipto_detail']['address_line4'] . '<br><br>';
            } else {
                $address_line4 = '<br>';
            }
            if (@$data['shipto_detail']['city'] != null) {
                $city = $data['shipto_detail']['city'] . ',';
            } else {
                $city = '';
            }
            if (@$data['shipto_detail']['get_zone'] != null) {
                $district = $data['shipto_detail']['get_zone']['state'] . ',';
            } else {
                $district = '';
            }
            if (@$data['shipto_detail']['postal_code'] != null) {
                $postal_code = $data['shipto_detail']['postal_code'] . '<br>';
            } else {
                $postal_code = '';
            }
            if (@$data['shipto_detail']['gst_number'] != null) {
                $gst_number = 'GST No: ' . $data['shipto_detail']['gst_number'] . '<br>';
            } else {
                $gst_number = '';
            }
            if (@$data['shipto_detail']['phone'] != null) {
                $phone = 'Phone No: ' . $data['shipto_detail']['phone'] . '<br>';
            } else {
                $phone = '';
            }
            // if (@$data['is_salereturn'] != 1) {
                $shiptoadd = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;
            // } else {
            //     $shiptoadd = '';
            // }

            $generate_qrcode = QrCode::size(150)->generate('Eternity Forwarders Pvt. Ltd.');
            $output_file = '/qr-code/img-' . time() . '.svg';
            Storage::disk('public')->put($output_file, $generate_qrcode);
            $fullpath = storage_path('app/public/' . $output_file);
            
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
            if ($locations->id == 2 || $locations->id == 6 || $locations->id == 26) {
                $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[1]->name . ' </b></span><br />
            <b>' . $branch_add[1]->address . ',</b><br />
            <b>	' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . 'b</b><br />
            <b>GST No. : ' . $branch_add[1]->gst_number . '</b><br />';
            } else if ($locations->id == 32) {
                $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[2]->name . ' </b></span><br />
            <b>' . $branch_add[2]->address . ',</b><br />
            <b>	' . $branch_add[2]->district . ' - ' . $branch_add[2]->postal_code . ',' . $branch_add[2]->state . 'b</b><br />
            <b>GST No. : ' . $branch_add[2]->gst_number . '</b><br />';
            } else {
                $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[0]->name . ' </b></span><br />
                                <b>	Plot no: ' . $branch_add[0]->address . ',</b><br />
                                <b>	' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . 'b</b><br />
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
                ' . $shiptoadd . '
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

            $pay = public_path('assets/img/LOGO_Frowarders.jpg');
            $codStamp = public_path('assets/img/cod.png');
            $paidStamp = public_path('assets/img/paid.png');

            foreach ($pdftype as $i => $pdf) {

                if ($pdf == 1) {
                    $type = 'ORIGINAL';
                } elseif ($pdf == 2) {
                    $type = 'DUPLICATE';
                } elseif ($pdf == 3) {
                    $type = 'TRIPLICATE';
                } elseif ($pdf == 4) {
                    $type = 'QUADRUPLE';
                }
                if (!empty($data['consigner_detail']['get_zone'])) {
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
                bottom: 0;


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
                        </style>
                    <!-- style="border-collapse: collapse; width: 369px; height: 72px; background:#d2c5c5;"class="table2" -->
                    </head>
                    <body style="font-family:Arial Helvetica,sans-serif;">
                        <div class="container-flex" style="margin-bottom: 5px; margin-top: -30px;">
                            <table style="height: 70px;">
                                <tr>
                                <td class="a" style="font-size: 10px;">
                                ' . $branch_address . '
                                </td>

                                    <td class="a">
                                    <b>	Email & Phone</b><br />
                                    <b>	' . @$locations->email . '</b><br />
                                    ' . @$locations->phone . '<br />

                                    </td>
                                </tr>

                            </table>
                            <hr />
                            <table>
                                <tr>
                                    <td class="b">
                            <div class="ff" >
                                        <img src="' . $fullpath . '" alt="" class="imgu" />
                            </div>
                                    </td>
                                    <td>
                                        <div style="margin-top: -15px; text-align: center">
                                            <h2 style="margin-bottom: -16px">CONSIGNMENT NOTE</h2>
                                            <P>' . $type . '</P>
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
                                                <th class="mini-th mm">' . date('d-m-Y', strtotime($data['consignment_date'])) . '</th>';
                                                if ($data['is_salereturn'] == '1') {
                                                    $html .= '<th class="mini-th mm">' . @$data['shipto_detail']['city'] . '</th>
                                                            <th class="mini-th"> ' . @$data['consigner_detail']['city'] . '</th>';
                                                } else {
                                                    $html .= '<th class="mini-th mm"> ' . @$data['consigner_detail']['city'] . '</th>
                                                            <th class="mini-th">' . @$data['shipto_detail']['city'] . '</th>';
                                                }      

                                $html .= '</tr>
                                        </table>
                            </div>
                                    </td>
                                </tr>
                            </table>';
                if ($data['payment_type'] == 'To be Billed' || $data['payment_type'] == null) {
                    if (!empty($data['cod'])) {
                        $html .= ' <div class="loc">
                                    <table>
                                        <tr>
                                            <td valign="middle" style="position:relative; width: 200px">
                                            <img src="' . $codStamp . '" style="position:absolute;left: -2rem; top: -2rem; height: 100px; width: 140px; z-index: -1; opacity: 0.8" />
                                                <h2 style="margin-top:1.8rem; margin-left: 0.5rem; font-size: 1.7rem; text-align: center">
                                                <span style="font-size: 24px; line-height: 18px">Cash to Collect</span><br/>' . @$data['cod'] . '
                                                </h2>
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
                                </div>';
                    } else {
                        $html .= '   <div class="loc">
                                    <table>
                                        <tr>
                                            <td class="width_set">
                                                <div style="margin-left: 20px">';
                                                if ($data['is_salereturn'] == '1') {
                                                    $html .= ' <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['shipto_detail']['postal_code'] . ',' . @$data['shipto_detail']['city'] . ',' . @$data['shipto_detail']['get_zone']['state'] . '</b></i><div class="vl" ></div>
                                                <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i>';
                                            } else {
                                                $html .= ' <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i><div class="vl" ></div>
                                                                    <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['shipto_detail']['postal_code'] . ',' . @$data['shipto_detail']['city'] . ',' . @$data['shipto_detail']['get_zone']['state'] . '</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>';
                                            }
                                            $html .= ' <div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>
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
                                </div>';
                    }
                }

                if ($data['payment_type'] == 'To Pay') {
                    if (!empty($data['freight_on_delivery']) || !empty($data['cod'])) {
                        $total_cod_sum = @$data['freight_on_delivery']+@$data['cod'];

                        $html .= ' <div class="loc">
                                        <table>
                                            <tr>
                                                <td valign="middle" style="position:relative; width: 200px">
                                                <img src="' . $codStamp . '" style="position:absolute;left: -2rem; top: -2rem; height: 100px; width: 140px; z-index: -1; opacity: 0.8" />
                                                    <h2 style="margin-top:1.8rem; margin-left: 0.5rem; font-size: 1.7rem; text-align: center">
                                                    <span style="font-size: 24px; line-height: 18px">Cash to Collect</span><br/>' . $total_cod_sum . '
                                                    </h2>
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
                                    </div>';
                    } else {
                        $html .= '   <div class="loc">
                                        <table>
                                            <tr>
                                                <td class="width_set">
                                                    <div style="margin-left: 20px">';
                        if ($data['is_salereturn'] == 1) {
                            $html .= '<i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['shipto_detail']['postal_code'] . ',' . @$data['shipto_detail']['city'] . ',' . @$data['shipto_detail']['get_zone']['state'] . '</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>
                                <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i><div class="vl" ></div>';
                        } else {
                            $html .= '<i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i><div class="vl" ></div>
                                <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['shipto_detail']['postal_code'] . ',' . @$data['shipto_detail']['city'] . ',' . @$data['shipto_detail']['get_zone']['state'] . '</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>';
                        }
                        $html .= '</div>
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
                                    </div>';
                    }

                }

                if ($data['payment_type'] == 'Paid') {

                    $html .= ' <div class="loc">
                                        <table>
                                            <tr>
                                                <td valign="middle" style="position:relative; width: 200px">
                                                <img src="' . $paidStamp . '" style="position:absolute;left: 50%; transform: translateX(-40%); top: -2.5rem; height: 150px; width: 150px;" />
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
                                    </div>';

                }

                $html .= '<div class="container">
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
                                    <div>
                                        <div class="row">
                                                            <div class="col-sm-12 ">
                                                    <h4 style="margin-left:19px;"><b>Order Information</b></h4>
                                                        </div>
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
                                    </div>

                                    <div class="inputfiled">
                                    <table style="width: 690px;
                                    font-size: 10px; background-color:#e0dddc40;">
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
                                </table>
                                <table style=" border-collapse:collapse; width: 690px;height: 45px; font-size: 10px; background-color:#e0dddc40; text-align: center;" border="1" >';
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
                $html .= '      </table>
                                    <div>
                                        <table style="margin-top:0px;">
                                        <tr>
                                        <td width="50%" style="font-size: 13px;"><p style="margin-top:60px;"><b>Received the goods mentioned above in good conditions.</b><br><br>Receivers Name & Number:<br><br>Receiving Date & Time	:<br><br>Receiver Signature:<br><br></p></td>
                                        <td  width="50%"><p style="margin-left: 99px; margin-bottom:150px;"><b>For Eternity Forwarders Pvt.Ltd</b></p></td>
                                    </tr>
                                        </table>

                                    </div>
                            </div>

                    <!-- <div class="footer">
                                    <p style="text-align:center; font-size: 10px;">Terms & Conditions</p>
                                    <p style="font-size: 8px; margin-top: -5px">1. Eternity Solutons does not take any responsibility for damage,leakage,shortage,breakages,soliage by sun ran ,fire and any other damage caused.</p>
                                    <p style="font-size: 8px; margin-top: -5px">2. The goods will be delivered to Consignee only against,payment of freight or on confirmation of payment by the consignor. </p>
                                    <p style="font-size: 8px; margin-top: -5px">3. The delivery of the goods will have to be taken immediately on arrival at the destination failing which the  consignee will be liable to detention charges @Rs.200/hour or Rs.300/day whichever is lower.</p>
                                    <p style="font-size: 8px; margin-top: -5px">4. Eternity Solutons takes absolutely no responsibility for delay or loss in transits due to accident strike or any other cause beyond its control and due to breakdown of vehicle and for the consequence thereof. </p>
                                    <p style="font-size: 8px; margin-top: -5px">5. Any complaint pertaining the consignment note will be entertained only within 15 days of receipt of the meterial.</p>
                                    <p style="font-size: 8px; margin-top: -5px">6. In case of mismatch in e-waybill & Invoice of the consignor, Eternity Solutons will impose a penalty of Rs.15000/Consignment  Note in addition to the detention charges stated above. </p>
                                    <p style="font-size: 8px; margin-top: -5px">7. Any dispute pertaining to the consigment Note will be settled at chandigarh jurisdiction only.</p>
                    </div> -->
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
                $pdf->save(public_path() . '/bulk-contractLr/congn-' . $i . '-' . $value . '.pdf')->stream('congn-' . $i . '-' . $value . '.pdf');
                $pdf_name[] = 'congn-' . $i . '-' . $value . '.pdf';
            }
        }

        $pdfMerger = PDFMerger::init();
        foreach ($pdf_name as $pdf) {
            $pdfMerger->addPDF(public_path() . '/bulk-contractLr/' . $pdf);
        }
        $pdfMerger->merge();
        $pdfMerger->save("all.pdf", "browser");
        $file = new Filesystem;
        $file->cleanDirectory('pdf');
    }

    public function contractPodlist1(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

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
                ->where('lr_type', 3)
                ->with(
                    'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
                );

            if ($authuser->role_id == 1 || $authuser->role_id == 8) {
                $query = $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->where(function ($query) use ($cc) {
                    $query->whereIn('branch_id', $cc)->orWhere('to_branch_id', $cc);

                });
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {

                    $query->where('id', 'like', '%' . $search . '%');
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

            $startdate = $request->startdate;
            $enddate = $request->enddate;

            if (isset($startdate) && isset($enddate)) {
                $consignments = $query->whereBetween('consignment_date', [$startdate, $enddate])->orderby('created_at', 'DESC')->paginate($peritem);
            } else {
                $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            }

            $html = view('contract-lr.pod-list-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem])->render();
            // $consignments = $consignments->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query
            ->where('lr_type', 3)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
            );

        if ($authuser->role_id == 1 || $authuser->role_id == 8) {
            $query = $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->where(function ($query) use ($cc) {
                $query->whereIn('branch_id', $cc)->orWhere('to_branch_id', $cc);

            });
        }

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('contract-lr.pod-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'peritem' => $peritem]);
    }
    public function contractPodlist(Request $request)
    {
        $this->prefix = $request->route()->getPrefix();

        $sessionPerItem = Session::get('peritem', Config::get('variable.PER_PAGE'));
        $query = ConsignmentNote::query()->where('lr_type', 3)->with('ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount');

        if ($request->ajax()) {
            if ($request->has('resetfilter')) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authUser = Auth::user();
            $cc = explode(',', $authUser->branch_id);

            if (!empty($request->search)) {
                $search = str_replace("'", "", $request->search);
                $query->where('id', 'like', '%' . $search . '%');
            }

            if ($request->filled('peritem')) {
                Session::put('peritem', $request->peritem);
                $sessionPerItem = $request->peritem;
            }

            if ($request->filled('startdate') && $request->filled('enddate')) {
                $query->whereBetween('consignment_date', [$request->startdate, $request->enddate]);
            }

            $consignments = $query->orderByDesc('created_at')->paginate($sessionPerItem);
            $html = view('contract-lr.pod-list-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $sessionPerItem])->render();

            return response()->json(['html' => $html]);
        }

        $query = $this->roleFilters($query);
        $consignments = $query->orderByDesc('id')->paginate($sessionPerItem)->appends($request->query());

        return view('contract-lr.pod-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'peritem' => $sessionPerItem]);
    }

    private function roleFilters($query)
    {
        $authUser = Auth::user();
        if ($authUser->role_id == 1 || $authUser->role_id == 8) {
            return $query;
        } elseif ($authUser->role_id == 4) {
            $regClient = explode(',', $authUser->regionalclient_id);
            return $query->whereIn('regclient_id', $regClient);
        } else {
            $cc = explode(',', $authUser->branch_id);
            return $query->where(function ($query) use ($cc) {
                $query->whereIn('branch_id', $cc)->orWhereIn('to_branch_id', $cc);
            });
        }
    }
    
}
