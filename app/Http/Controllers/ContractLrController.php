<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consigner;
use App\Models\Location;
use App\Models\Role;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\BranchAddress;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\Driver;
use App\Models\ItemMaster;
use App\Models\RegionalClient;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Validator;
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

            // if(!empty($request->vehicle_id) && $request->lr_type == 0){
            //     $getVehicle = Vehicle::where('id', $request->vehicle_id)->first();
            //     if($getVehicle){
            //         $drsVehicleIds = TransactionSheet::select('id','drs_no', 'vehicle_no')
            //             ->where('vehicle_no', $getVehicle->regn_no)
            //             ->whereDate('created_at', '>', '2023-12-20')
            //             ->whereNotIn('delivery_status', ['Successful', 'Cancel'])
            //             ->whereNotIn('status', [4, 0])
            //             ->where('is_started', 1)
            //             ->pluck('drs_no')
            //             ->unique()
            //             ->toArray();
            //         if($drsVehicleIds){
            //             $errorMessage = "Vehicle already assigned to DRS: " . implode(', ', $drsVehicleIds);
            //             return response()->json(['success' => false,'error_message' => $errorMessage]);
            //         }
            //     }
            // }
    
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
            $consignmentsave['delivery_status'] = "Unassigned";
            $consignmentsave['lr_type'] = 3;  // lr-type =0-ftl, 1-ptl, 2-prs, 3-lr-contract
            $consignmentsave['status'] = 7;  // status = 7 for lr-contract

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

            $url = $this->prefix . '/contract-lrs';
            $response['success'] = true;
            $response['success_message'] = "Consignment Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
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
