<?php

namespace App\Http\Controllers;

use App\Exports\PaymentReportExport;
use App\Exports\exportDrsWiseReport;
use App\Exports\VendorExport;
use App\Imports\VendorImport;
use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Location;
use App\Models\PaymentHistory;
use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\TransactionSheet;
use App\Models\User;
use App\Models\VehicleType;
use App\Models\Vendor;
use Auth;
use Config;
use DB;
use Excel;
use Helper;
use Illuminate\Http\Request;
use Session;
use URL;
use Validator;

class VendorController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Secondary Reports";
        $this->segment = \Request::segment(2);
        $this->req_link = \Config::get('req_api_link.req');
    }

    public function index()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $query = Vendor::query();
        $query = $query->with('DriverDetail', 'Branch');
        if ($authuser->role_id == 2) {
            $query = $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }
        $vendors = $query->get();

        return view('vendors.vendor-list', ['prefix' => $this->prefix, 'vendors' => $vendors]);
    }
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);

        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();

        if ($authuser->role_id == 1) {
            $branchs = Location::select('id', 'name')->get();
        } elseif ($authuser->role_id == 2) {
            $branchs = Location::select('id', 'name')->where('id', $cc)->get();
        } elseif ($authuser->role_id == 5) {
            $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        } else {
            $branchs = Location::select('id', 'name')->get();
        }

        return view('vendors.create-vendor', ['prefix' => $this->prefix, 'drivers' => $drivers, 'branchs' => $branchs]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required|unique:vendors',
                'ifsc_code' => 'required|min:11',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['error_message'] = $errors;
                return response()->json($response);
            }
            /// pan check
            $pancheck = Vendor::where('vendor_type', $request->vendor_type)->where('pan', $request->pan)->first();
            if ($pancheck) {

                $response['success'] = false;
                $response['pan_check'] = true;
                $response['errors'] = "Pan no. and vendor type already exists";
                return response()->json($response);
            }

            $panupload = $request->file('pan_upload');
            if (!empty($panupload)) {
                $panfile = $panupload->getClientOriginalName();
                $panupload->move(public_path('drs/uploadpan'), $panfile);
            } else {
                $panfile = null;
            }

            $cheaque = $request->file('cancel_cheaque');
            if (!empty($cheaque)) {
                $cheaquefile = $cheaque->getClientOriginalName();
                $cheaque->move(public_path('drs/cheaque'), $cheaquefile);
            } else {
                $cheaquefile = null;
            }

            $dec_file = $request->file('declaration_file');
            if (!empty($dec_file)) {
                $decl_file = $dec_file->getClientOriginalName();
                $dec_file->move(public_path('drs/declaration'), $decl_file);
            } else {
                $decl_file = null;
            }
            /////declaration file check
            if ($request->decalaration_available == 1) {
                if (empty($decl_file)) {
                    $response['success'] = false;
                    $response['decl_check'] = true;
                    $response['errors'] = "please upload declaration file";
                    return response()->json($response);
                }
            }

            $vendor = DB::table('vendors')->select('vendor_no')->latest('vendor_no')->first();
            $vendor_no = json_decode(json_encode($vendor), true);
            if (empty($vendor_no) || $vendor_no == null) {
                $vendor_no = 10101;
            } else {
                $vendor_no = $vendor_no['vendor_no'] + 1;
            }

            $bankdetails = array('acc_holder_name' => $request->acc_holder_name, 'account_no' => $request->account_no, 'ifsc_code' => $request->ifsc_code, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name);

            $otherdetail = array('transporter_name' => $request->transporter_name, 'contact_person_number' => $request->contact_person_number);

            $vendorsave['type'] = 'Vendor';
            $vendorsave['vendor_no'] = $vendor_no;
            $vendorsave['name'] = $request->name;
            $vendorsave['email'] = $request->email;
            $vendorsave['driver_id'] = $request->driver_id;
            $vendorsave['bank_details'] = json_encode($bankdetails);
            $vendorsave['pan'] = $request->pan;
            $vendorsave['upload_pan'] = $panfile;
            $vendorsave['cancel_cheaque'] = $cheaquefile;
            $vendorsave['other_details'] = json_encode($otherdetail);
            $vendorsave['vendor_type'] = $request->vendor_type;
            $vendorsave['declaration_available'] = $request->decalaration_available;
            $vendorsave['declaration_file'] = $decl_file;
            $vendorsave['tds_rate'] = $request->tds_rate;
            $vendorsave['branch_id'] = $request->branch_id;
            $vendorsave['gst_register'] = $request->gst_register;
            $vendorsave['gst_no'] = $request->gst_no;
            $vendorsave['is_acc_verified'] = 0;
            $vendorsave['is_active'] = 1;

            $savevendor = Vendor::create($vendorsave);

            if ($savevendor) {
                $url = $this->prefix . '/vendor-list';
                $response['success'] = true;
                $response['success_message'] = "Vendor Added successfully";
                $response['error'] = false;
                $response['redirect_url'] = $url;

            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not created Vendor please try again";
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

    public function paymentList(Request $request)
    {

        $this->prefix = request()->route()->getPrefix();

        $sessionperitem = Session::get('peritem');
        if (!empty($sessionperitem)) {
            $peritem = $sessionperitem;
        } else {
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = TransactionSheet::query();

        if ($request->ajax()) {
            $searchids = [];

            if (isset($request->resetfilter)) {
                Session::forget('searchvehicle');
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);

            $lastsevendays = \Carbon\Carbon::today()->subDays(7);
            $date = Helper::yearmonthdate($lastsevendays);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query->whereIn('status', ['1', '0', '3'])
                ->where('request_status', 0)
                ->where('payment_status', '=', 0)
                ->groupBy('drs_no');

            if ($authuser->role_id == 1) {
                $query = $query->with('ConsignmentDetail');
            } elseif ($authuser->role_id == 4) {
                $query = $query
                    ->with('ConsignmentDetail.vehicletype')
                    ->whereHas('ConsignmentDetail', function ($query) use ($regclient) {
                        $query->whereIn('regclient_id', $regclient);
                    });
            } elseif ($authuser->role_id == 5) {
                $query = $query->with('ConsignmentDetail');
            } elseif ($authuser->role_id == 6) {
                $query = $query
                    ->whereHas('ConsignmentDetail', function ($query) use ($baseclient) {
                        $query->whereIn('base_clients.id', $baseclient);
                    });
            } elseif ($authuser->role_id == 7) {
                $query = $query
                    ->whereHas('ConsignmentDetail.ConsignerDetail.RegClient', function ($query) use ($baseclient) {
                        $query->whereIn('id', $regclient);
                    });
            } else {
                $query = $query->with('ConsignmentDetail')->whereIn('branch_id', $cc);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('vehicle_no', 'like', '%' . $search . '%')
                        ->orWhere('drs_no', 'like', '%' . $search . '%');

                });
            }

            /// search with vehicle no

            if ($request->searchvehicle) {
                Session::put('searchvehicle', $request->searchvehicle);
            }
            $searchvehicle = Session::get('searchvehicle');
            if (isset($searchvehicle)) {
                $query = $query->whereIn('vehicle_no', $searchvehicle);
            }

            // if (isset($request->vehicle_no)) {
            //     $query = $query->where('vehicle_no', $request->vehicle_no);
            // }

            if ($request->peritem) {
                Session::put('peritem', $request->peritem);
            }

            $peritem = Session::get('peritem');
            if (!empty($peritem)) {
                $peritem = $peritem;
            } else {
                $peritem = Config::get('variable.PER_PAGE');
            }

            $vehicles = TransactionSheet::select('vehicle_no')->distinct()->get();

            $paymentlist = $query->orderby('id', 'DESC')->paginate($peritem);

            $html = view('vendors.drs-paymentlist-ajax', ['prefix' => $this->prefix, 'paymentlist' => $paymentlist, 'peritem' => $peritem, 'vehicles' => $vehicles])->render();
            $paymentlist = $paymentlist->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();

        $lastsevendays = \Carbon\Carbon::today()->subDays(7);
        $date = Helper::yearmonthdate($lastsevendays);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        // $query = $query
        //     ->with('ConsignmentDetail')
        //     ->groupBy('drs_no');

        $query = $query->whereIn('status', ['1', '0', '3'])
            ->where('request_status', 0)
            ->where('payment_status', '=', 0)
            ->groupBy('drs_no');

        if ($authuser->role_id == 1) {
            $query = $query->with('ConsignmentDetail');
        } elseif ($authuser->role_id == 4) {
            $query = $query
                ->with('ConsignmentDetail.vehicletype')
                ->whereHas('ConsignmentDetail', function ($query) use ($regclient) {
                    $query->whereIn('regclient_id', $regclient);
                });
        } elseif ($authuser->role_id == 5) {
            $query = $query->with('ConsignmentDetail');
        } elseif ($authuser->role_id == 6) {
            $query = $query
                ->whereHas('ConsignmentDetail', function ($query) use ($baseclient) {
                    $query->whereIn('base_clients.id', $baseclient);
                });
        } elseif ($authuser->role_id == 7) {
            $query = $query
                ->whereHas('ConsignmentDetail.ConsignerDetail.RegClient', function ($query) use ($baseclient) {
                    $query->whereIn('id', $regclient);
                });
        } else {

            $query = $query->with('ConsignmentDetail')->whereIn('branch_id', $cc);
        }

        $vehicles = TransactionSheet::select('vehicle_no')->distinct()->get();

        $paymentlist = $query->orderBy('id', 'DESC')->paginate($peritem);
        $paymentlist = $paymentlist->appends($request->query());
        // $vehicles    = Vehicle::select('id', 'regn_no')->get();
        $vehicletype = VehicleType::select('id', 'name')->get();
        $vendors = Vendor::with('Branch')->get();

        return view('vendors.drs-paymentlist', ['prefix' => $this->prefix, 'paymentlist' => $paymentlist, 'vendors' => $vendors, 'peritem' => $peritem, 'vehicles' => $vehicles, 'vehicletype' => $vehicletype, 'branchs' => $branchs]);
    }

    public function getdrsdetails(Request $request)
    {

        $drs = TransactionSheet::with('ConsignmentDetail')->whereIn('drs_no', $request->drs_no)->first();

        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no', $request->drs_no)->get();

        $getlr_deldate = ConsignmentNote::select('delivery_date')->where('status', '!=', 0)->whereIn('id', $get_lrs)->get();
        $total_deldate = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->where('delivery_date', '!=', null)->count();
        $total_empty = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->where('delivery_date', '=', null)->count();

        $total_lr = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->count();

        if ($total_deldate == $total_lr) {
            $status = "Successful";
        } elseif ($total_lr == $total_empty) {
            $status = "Started";
        } else {
            $status = "Partial Delivered";
        }

        $response['get_data'] = $drs;
        $response['status'] = $status;
        $response['success'] = true;
        $response['error_message'] = "find data";
        return response()->json($response);
    }

    public function vendorbankdetails(Request $request)
    {

        $vendors = Vendor::where('id', $request->vendor_id)->first();

        // if($vendors->is_acc_verified == 1){
        $response['vendor_details'] = $vendors;
        $response['success'] = true;
        $response['message'] = "verified account";
        // }else{
        //     $response['success'] = false;
        //     $response['message'] = "Account not verified";
        // }

        return response()->json($response);
    }

    public function createPaymentRequest(Request $request)
    {
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = $authuser->branch_id;
        $user = $authuser->id;
        $bm_email = $authuser->email;
        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        //deduct balance
        $deduct_balance = $request->payable_amount - $request->final_payable_amount ;

        $get_vehicle = PaymentRequest::select('vehicle_no')->where('transaction_id', $request->transaction_id)->get();
        $sent_vehicle = array();
        foreach($get_vehicle as $vehicle){
              $sent_vehicle[] = $vehicle->vehicle_no;
        }
        $unique = array_unique($sent_vehicle);
        $sent_vehicle_no = implode(',', $unique);

        $url_header = $_SERVER['HTTP_HOST'];
        $drs = explode(',', $request->drs_no);
        $pfu = 'ETF';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->req_link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "[{
            \"unique_code\": \"$request->vendor_no\",
            \"name\": \"$request->name\",
            \"acc_no\": \"$request->acc_no\",
            \"beneficiary_name\": \"$request->beneficiary_name\",
            \"ifsc\": \"$request->ifsc\",
            \"bank_name\": \"$request->bank_name\",
            \"baddress\": \"$request->branch_name\",
            \"payable_amount\": \"$request->final_payable_amount\",
            \"claimed_amount\": \"$request->claimed_amount\",
            \"pfu\": \"$pfu\",
            \"ptype\": \"$request->p_type\",
            \"email\": \"$bm_email\",
            \"terid\": \"$request->transaction_id\",
            \"branch\": \"$branch_name->nick_name\",
            \"pan\": \"$request->pan\",
            \"amt_deducted\": \"$deduct_balance\",
            \"vehicle\": \"$sent_vehicle_no\",
            \"txn_route\": \"DRS\"
            }]",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Access-Control-Request-Headers:' . $url_header,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res_data = json_decode($response);
        // $cc = 'success';
        // ============== Success Response
        if ($res_data->message == 'success') {

            if ($request->p_type == 'Balance' || $request->p_type == 'Fully') {

                $getadvanced = PaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
                if (!empty($getadvanced->balance)) {
                    $balance = $getadvanced->balance - $request->payable_amount;
                } else {
                    $balance = 0;
                }
                $advance = $getadvanced->advanced + $request->payable_amount;

                TransactionSheet::whereIn('drs_no', $drs)->update(['payment_status' => 2]);

                PaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'payment_status' => 2]);

                $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                $paymentresponse['transaction_id'] = $request->transaction_id;
                $paymentresponse['drs_no'] = $request->drs_no;
                $paymentresponse['bank_details'] = json_encode($bankdetails);
                $paymentresponse['purchase_amount'] = $request->claimed_amount;
                $paymentresponse['payment_type'] = $request->p_type;
                $paymentresponse['advance'] = $advance;
                $paymentresponse['balance'] = $balance;
                $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                $paymentresponse['current_paid_amt'] = $request->payable_amount;
                $paymentresponse['payment_status'] = 2;

                $paymentresponse = PaymentHistory::create($paymentresponse);

            } else {

                $balance_amt = $request->claimed_amount - $request->payable_amount;
                //======== Payment History save =========//
                $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                $paymentresponse['transaction_id'] = $request->transaction_id;
                $paymentresponse['drs_no'] = $request->drs_no;
                $paymentresponse['bank_details'] = json_encode($bankdetails);
                $paymentresponse['purchase_amount'] = $request->claimed_amount;
                $paymentresponse['payment_type'] = $request->p_type;
                $paymentresponse['advance'] = $request->payable_amount;
                $paymentresponse['balance'] = $balance_amt;
                $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                $paymentresponse['current_paid_amt'] = $request->payable_amount;
                $paymentresponse['payment_status'] = 2;

                $paymentresponse = PaymentHistory::create($paymentresponse);

                PaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type, 'advanced' => $request->payable_amount, 'balance' => $balance_amt, 'payment_status' => 2]);

                TransactionSheet::whereIn('drs_no', $drs)->update(['payment_status' => 2]);
            }

            $new_response['success'] = true;
            $new_response['message'] = $res_data->message;

        } else {

            $new_response['message'] = $res_data->message;
            $new_response['success'] = false;

        }

        return response()->json($new_response);
    }

    public function view_vendor_details(Request $request)
    {
        $vendors = Vendor::with('DriverDetail')->where('id', $request->vendor_id)->first();

        $response['view_details'] = $vendors;
        $response['success'] = true;
        $response['message'] = "verified account";
        return response()->json($response);

    }
    public function update_purchase_price(Request $request)
    {
        try {
            DB::beginTransaction();
            $getlr = TransactionSheet::select('consignment_no')->where('drs_no', $request->drs_no)->get();
            $simpl = json_decode(json_encode($getlr), true);
            TransactionSheet::where('drs_no', $request->drs_no)->update(['purchase_amount' => $request->purchase_price]);

            foreach ($simpl as $lr) {

                ConsignmentNote::where('id', $lr['consignment_no'])->where('status', '!=', 0)->update(['purchase_price' => $request->purchase_price, 'vehicle_type' => $request->vehicle_type]);

            }
            $response['success'] = true;
            $response['success_message'] = "Price Added successfully";
            $response['error'] = false;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);

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

    public function importVendor(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $rows = Excel::toArray([], request()->file('vendor_file'));
        $data = $rows[0];

        $chng = $this->rewrap($data);
        $ignore_vendor = array();
        foreach ($chng as $val) {
            $ifsc_code = $val['ifsc_code'];
            $check_length = strlen($ifsc_code);

            if ($check_length != 11) {
                $ignore_vendor[] = ['vendor' => $val['vendor_name'], 'ifsc_code' => $val['ifsc_code']];
            }
        }
        $ignorecount = count($ignore_vendor);

        $data = Excel::import(new VendorImport, request()->file('vendor_file'));
        $message = 'Vendors Imported Successfully';

        if ($data) {
            $response['success'] = true;
            $response['page'] = 'bulk-imports';
            $response['error'] = false;
            $response['success_message'] = $message;
            $response['ignore_vendor'] = $ignore_vendor;
            $response['ignorecount'] = $ignorecount;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import consignees please try again";
        }
        return response()->json($response);
    }

    public function exportVendor(Request $request)
    {
        return Excel::download(new VendorExport, 'vendordata.csv');
    }

    public function checkAccValid(Request $request)
    {
        $checkacc = Vendor::select('bank_details')->get();
        foreach ($checkacc as $check) {

            $acc = json_decode($check->bank_details);
            if (!empty($request->acc_no)) {
                if ($acc->account_no == $request->acc_no) {

                    $response['success'] = false;
                    $response['error'] = false;
                    $response['success_message'] = 'Account already exists';
                    return response()->json($response);
                }
            }

        }
        $response['success'] = true;
        $response['error'] = false;
        $response['success_message'] = 'done';
        return response()->json($response);

    }

    public function viewdrsLr(Request $request)
    {

        $id = $_GET['drs_lr'];
        $transcationview = TransactionSheet::select('*')->with('ConsignmentDetail', 'ConsignmentItem')->where('drs_no', $id)
            ->whereHas('ConsignmentDetail', function ($query) {
                $query->where('status', '1');
            })
            ->orderby('order_no', 'asc')->get();
        $result = json_decode(json_encode($transcationview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    // Edit Vendor====================== //
    public function editViewVendor(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);

        $getvendor = Vendor::where('id', $request->id)->first();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        if ($authuser->role_id == 1) {
            $branchs = Location::select('id', 'name')->get();
        } elseif ($authuser->role_id == 2) {
            $branchs = Location::select('id', 'name')->where('id', $cc)->get();
        } elseif ($authuser->role_id == 5) {
            $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();
        } else {
            $branchs = Location::select('id', 'name')->get();
        }

        return view('vendors.edit-vendor', ['prefix' => $this->prefix, 'getvendor' => $getvendor, 'drivers' => $drivers, 'branchs' => $branchs]);
    }

    public function updateVendor(Request $request)
    {
        try {

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }

            $bankdetails = array('acc_holder_name' => $request->acc_holder_name, 'account_no' => $request->account_no, 'ifsc_code' => $request->ifsc_code, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name);

            $otherdetail = array('transporter_name' => $request->transporter_name, 'contact_person_number' => $request->contact_person_number);

            $vendorsave['type'] = 'Vendor';
            // $vendorsave['vendor_no'] = $vendor_no;
            $vendorsave['name'] = $request->name;
            $vendorsave['email'] = $request->email;
            $vendorsave['driver_id'] = $request->driver_id;
            $vendorsave['bank_details'] = json_encode($bankdetails);
            $vendorsave['pan'] = $request->pan;
            // $vendorsave['upload_pan'] = $panfile;
            // $vendorsave['cancel_cheaque'] = $cheaquefile;
            $vendorsave['other_details'] = json_encode($otherdetail);
            $vendorsave['vendor_type'] = $request->vendor_type;
            $vendorsave['declaration_available'] = $request->decalaration_available;
            // $vendorsave['declaration_file'] = $decl_file;
            $vendorsave['tds_rate'] = $request->tds_rate;
            $vendorsave['branch_id'] = $request->branch_id;
            $vendorsave['gst_register'] = $request->gst_register;
            $vendorsave['gst_no'] = $request->gst_no;

            Vendor::where('id', $request->vendor_id)->update($vendorsave);

            $response['success'] = true;
            $response['success_message'] = "Vendor Updated Successfully";
            $response['error'] = false;
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);

    }
    // ==================CreatePayment Request =================
    public function createPaymentRequestVendor(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $url_header = $_SERVER['HTTP_HOST'];

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = $authuser->branch_id;
        $user = $authuser->id;
        $bm_email = $authuser->email;

        $branch_name = Location::where('id', '=', $request->branch_id)->first();

        //deduct balance
        $deduct_balance = $request->pay_amt - $request->final_payable_amount ;

        $drsno = explode(',', $request->drs_no);
        $consignment = TransactionSheet::whereIn('drs_no', $drsno)
            ->groupby('drs_no')
            ->get();
        $simplyfy = json_decode(json_encode($consignment), true);
        $transactionId = DB::table('payment_requests')->select('transaction_id')->latest('transaction_id')->first();
        $transaction_id = json_decode(json_encode($transactionId), true);
        if (empty($transaction_id) || $transaction_id == null) {
            $transaction_id_new = 70000101;
        } else {
            $k = $transaction_id['transaction_id'];
            $s = $k . ''; // or: $s = (string)$k;
            $a = $s[0];
            if ($a == 7) {
                $transaction_id_new = $transaction_id['transaction_id'] + 1;
            } else {
                $transaction_id_new = 70000101;
            }
        }

        $i = 0;
        $sent_vehicle = array();
        foreach ($simplyfy as $value) {
            $i++;
            $drs_no = $value['drs_no'];
            $vendor_id = $request->vendor_name;
            $vehicle_no = $value['vehicle_no'];
            $sent_vehicle[] = $value['vehicle_no'];

            if ($request->p_type == 'Advance') {
                $balance_amt = $request->claimed_amount - $request->pay_amt;

                $transaction = PaymentRequest::create(['transaction_id' => $transaction_id_new, 'drs_no' => $drs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'branch_id' => $request->branch_id, 'user_id' => $user, 'payment_status' => 0, 'status' => '1']);
            } else {
                $getadvanced = PaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                if (!empty($getadvanced->balance)) {
                    $balance = $getadvanced->balance - $request->pay_amt;
                } else {
                    $balance = 0;
                }
                $advance = $request->pay_amt;
                // dd($advance);

                $transaction = PaymentRequest::create(['transaction_id' => $transaction_id_new, 'drs_no' => $drs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'payment_type' => $request->p_type, 'total_amount' => $request->claimed_amount, 'advanced' => $advance, 'balance' => $balance, 'branch_id' => $request->branch_id, 'user_id' => $user, 'payment_status' => 0, 'status' => '1']);
            }

        }
        $unique = array_unique($sent_vehicle);
        $sent_venicle_no = implode(',', $unique);
        

        TransactionSheet::whereIn('drs_no', $drsno)->update(['request_status' => '1']);
        // ============== Sent to finfect
        $pfu = 'ETF';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->req_link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "[{
            \"unique_code\": \"$request->vendor_no\",
            \"name\": \"$request->v_name\",
            \"acc_no\": \"$request->acc_no\",
            \"beneficiary_name\": \"$request->beneficiary_name\",
            \"ifsc\": \"$request->ifsc\",
            \"bank_name\": \"$request->bank_name\",
            \"baddress\": \"$request->branch_name\",
            \"payable_amount\": \"$request->final_payable_amount\",
            \"claimed_amount\": \"$request->claimed_amount\",
            \"pfu\": \"$pfu\",
            \"ptype\": \"$request->p_type\",
            \"email\": \"$bm_email\",
            \"terid\": \"$transaction_id_new\",
            \"branch\": \"$branch_name->nick_name\",
            \"vehicle\": \"$sent_venicle_no\",
            \"pan\": \"$request->pan\",
            \"amt_deducted\": \"$deduct_balance\",
            \"txn_route\": \"DRS\"
            }]",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Access-Control-Request-Headers:' . $url_header,

            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res_data = json_decode($response);
        // ============== Success Response
        // $cs = 'success';
        // echo'<pre>'; print_r($res_data); die;
        if ($res_data->message == 'success') {

            if ($request->p_type == 'Fully') {
                $getadvanced = PaymentRequest::select('advanced', 'balance')->where('transaction_id', $transaction_id_new)->first();
                if (!empty($getadvanced->balance)) {
                    $balance = $getadvanced->balance - $request->pay_amt;
                } else {
                    $balance = 0;
                }
                $advance = $request->pay_amt;

                TransactionSheet::whereIn('drs_no', $drsno)->update(['payment_status' => 2]);

                PaymentRequest::where('transaction_id', $transaction_id_new)->update(['payment_type' => $request->p_type, 'advanced' => $advance, 'balance' => $balance, 'payment_status' => 2]);

                $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                $paymentresponse['transaction_id'] = $transaction_id_new;
                $paymentresponse['drs_no'] = $request->drs_no;
                $paymentresponse['bank_details'] = json_encode($bankdetails);
                $paymentresponse['purchase_amount'] = $request->claimed_amount;
                $paymentresponse['payment_type'] = $request->p_type;
                $paymentresponse['advance'] = $advance;
                $paymentresponse['balance'] = $balance;
                $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                $paymentresponse['current_paid_amt'] = $request->pay_amt;
                $paymentresponse['payment_status'] = 2;

                $paymentresponse = PaymentHistory::create($paymentresponse);

            } else {

                $balance_amt = $request->claimed_amount - $request->pay_amt;
                //======== Payment History save =========//
                $bankdetails = array('acc_holder_name' => $request->beneficiary_name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $bm_email);

                $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                $paymentresponse['transaction_id'] = $transaction_id_new;
                $paymentresponse['drs_no'] = $request->drs_no;
                $paymentresponse['bank_details'] = json_encode($bankdetails);
                $paymentresponse['purchase_amount'] = $request->claimed_amount;
                $paymentresponse['payment_type'] = $request->p_type;
                $paymentresponse['advance'] = $request->pay_amt;
                $paymentresponse['balance'] = $balance_amt;
                $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                $paymentresponse['current_paid_amt'] = $request->pay_amt;
                $paymentresponse['payment_status'] = 2;

                $paymentresponse = PaymentHistory::create($paymentresponse);
                PaymentRequest::where('transaction_id', $transaction_id_new)->update(['payment_type' => $request->p_type, 'advanced' => $request->pay_amt, 'balance' => $balance_amt, 'payment_status' => 2]);

                TransactionSheet::whereIn('drs_no', $drsno)->update(['payment_status' => 2]);
            }

            $new_response['success'] = true;
            $new_response['message'] = $res_data->message;

        } else {

            $new_response['message'] = $res_data->message;
            $new_response['success'] = false;

        }

        $url = $this->prefix . '/request-list';
        $new_response['redirect_url'] = $url;
        $new_response['success_message'] = "Data Imported successfully";

        return response()->json($new_response);

    }

    public function requestList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $branchs = Location::select('id', 'name')->whereIn('id', $cc)->get();

        if ($authuser->role_id == 2) {
            $requestlists = PaymentRequest::with('VendorDetails', 'Branch')
                ->where('branch_id', $cc)
                ->where('payment_status', '!=', 0)
                ->groupBy('transaction_id')
                ->get();
        } else {
            $requestlists = PaymentRequest::with('VendorDetails', 'Branch')
                ->where('payment_status', '!=', 0)
                ->groupBy('transaction_id')
                ->get();
        }

        $vendors = Vendor::all();
        $vehicletype = VehicleType::select('id', 'name')->get();

        return view('vendors.request-list', ['prefix' => $this->prefix, 'requestlists' => $requestlists, 'vendors' => $vendors, 'vehicletype' => $vehicletype, 'branchs' => $branchs]);
    }

    public function getVendorReqDetails(Request $request)
    {
        $req_data = PaymentRequest::with('VendorDetails')->where('transaction_id', $request->trans_id)
            ->groupBy('transaction_id')->get();

        $getdrs = PaymentRequest::select('drs_no')->where('transaction_id', $request->trans_id)
            ->get();
        $simply = json_decode(json_encode($getdrs), true);
        foreach ($simply as $value) {
            $store[] = $value['drs_no'];
        }
        $drs_no = implode(',', $store);
// ==================================
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no', $store)->get();

        $getlr_deldate = ConsignmentNote::select('delivery_date')->where('status', '!=', 0)->whereIn('id', $get_lrs)->get();
        $total_deldate = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->where('delivery_date', '!=', null)->count();
        $total_empty = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->where('delivery_date', '=', null)->count();

        $total_lr = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->count();

        if ($total_deldate == $total_lr) {
            $status = "Successful";
        } elseif ($total_lr == $total_empty) {
            $status = "Started";
        } else {
            $status = "Partial Delivered";
        }

        $response['status'] = $status;
        $response['req_data'] = $req_data;
        $response['drs_no'] = $drs_no;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function showDrs(Request $request)
    {
        $getdrs = PaymentRequest::select('drs_no')->where('transaction_id', $request->trans_id)->get();
        // dd($request->transaction_id);

        $response['getdrs'] = $getdrs;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);
    }

    public function editPurchasePrice(Request $request)
    {
        $drs_price = TransactionSheet::with('ConsignmentDetail')->where('drs_no', $request->drs_no)->first();
        $vehicletype = VehicleType::select('id', 'name')->get();

        $response['drs_price'] = $drs_price;
        $response['vehicletype'] = $vehicletype;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);
    }

    public function updatePurchasePriceVehicleType(Request $request)
    {
        try {
            DB::beginTransaction();
            $getlr = TransactionSheet::select('consignment_no')->where('drs_no', $request->drs_no)->get();
            $simpl = json_decode(json_encode($getlr), true);
            TransactionSheet::where('drs_no', $request->drs_no)->update(['purchase_amount' => $request->purchase_price]);

            foreach ($simpl as $lr) {

                ConsignmentNote::where('id', $lr['consignment_no'])->where('status', '!=', 0)->update(['purchase_price' => $request->purchase_price, 'vehicle_type' => $request->vehicle_type]);

            }
            $response['success'] = true;
            $response['success_message'] = "Price Added successfully";
            $response['error'] = false;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);

    }

    public function getBalanceAmount(Request $request)
    {
        $getbalance = PaymentRequest::where('transaction_id', $request->transaction_id)->first();

        $response['getbalance'] = $getbalance;
        $response['success'] = true;
        $response['success_message'] = "get balance";
        return response()->json($response);
    }

    public function check_paid_status()
    {
        ini_set('max_execution_time', 0); // 0 = Unlimited
        $get_data_db = DB::table('payment_requests')->select('transaction_id', 'payment_type')->whereIn('payment_status', [2, 3])->get()->toArray();
        $size = sizeof($get_data_db);

        for ($i = 0; $i < $size; $i++) {
            $trans_id = $get_data_db[$i]->transaction_id;
            $p_type = $get_data_db[$i]->payment_type;

            $url = 'https://finfect.biz/api/get_payment_response_drs/' . $trans_id;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            if ($response) {
                $received_data = json_decode($response);
                $status_code = $received_data->status_code;
                if ($status_code == 2) {
                    if ($p_type == 'Fully' || $p_type == 'Balance') {

                        $update_status = PaymentRequest::where('transaction_id', $trans_id)->update(['payment_status' => 1]);

                        PaymentHistory::where('transaction_id', $trans_id)->where('payment_status', 2)->update(['payment_status' => 1, 'finfect_status' => $received_data->status, 'paid_amt' => $received_data->amount, 'bank_refrence_no' => $received_data->bank_refrence_no, 'payment_date' => $received_data->payment_date]);

                        $get_drs = PaymentRequest::select('drs_no')->where('transaction_id', $trans_id)->get();

                        foreach ($get_drs as $drs) {
                            TransactionSheet::where('drs_no', $drs->drs_no)->where('payment_status', 2)->update(['payment_status' => 1]);
                        }
                    } else {
                        $update_status = PaymentRequest::where('transaction_id', $trans_id)->update(['payment_status' => 3]);

                        PaymentHistory::where('transaction_id', $trans_id)->where('payment_status', 2)->update(['payment_status' => 3, 'finfect_status' => $received_data->status, 'paid_amt' => $received_data->amount, 'bank_refrence_no' => $received_data->bank_refrence_no, 'payment_date' => $received_data->payment_date]);

                        $get_drs = PaymentRequest::select('drs_no')->where('transaction_id', $trans_id)->get();

                        foreach ($get_drs as $drs) {
                            TransactionSheet::where('drs_no', $drs->drs_no)->where('payment_status', 2)->update(['payment_status' => 3]);
                        }

                    }
                }
            }
        }
        return 1;
    }

    // public function paymentReportView(Request $request)
    // {
    //     $this->prefix = request()->route()->getPrefix();
    //     $authuser = Auth::user();
    //     $role_id = Role::where('id', '=', $authuser->role_id)->first();
    //     $cc = explode(',', $authuser->branch_id);

    //     if ($authuser->role_id == 2) {
    //         $payment_lists = PaymentRequest::with('Branch', 'TransactionDetails.ConsignmentNote.RegClient', 'VendorDetails', 'PaymentHistory', 'TransactionDetails.ConsignmentNote.ConsignmentItems', 'TransactionDetails.ConsignmentNote.vehicletype', 'TransactionDetails.ConsignmentNote.ShiptoDetail')
    //             ->where('branch_id', $cc)
    //             ->get();
    //     } else {
    //         $payment_lists = PaymentRequest::with('Branch', 'TransactionDetails.ConsignmentNote.RegClient', 'VendorDetails', 'PaymentHistory', 'TransactionDetails.ConsignmentNote.ConsignmentItems', 'TransactionDetails.ConsignmentNote.vehicletype', 'TransactionDetails.ConsignmentNote.ShiptoDetail')
    //             ->get();
    //     }
    //     $simp =

    //     return view('vendors.payment-report-view', ['prefix' => $this->prefix, 'payment_lists' => $payment_lists]);
    // }

    public function paymentReportView(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);
        $query = PaymentHistory::with('PaymentRequest.Branch', 'PaymentRequest.TransactionDetails.ConsignmentNote.RegClient', 'PaymentRequest.VendorDetails', 'PaymentRequest.TransactionDetails.ConsignmentNote.ConsignmentItems', 'PaymentRequest.TransactionDetails.ConsignmentNote.vehicletype');

        if ($authuser->role_id == 2) {
            $query->whereHas('PaymentRequest', function ($query) use ($cc) {
                $query->whereIn('branch_id', $cc);
            });
        } else {
            $query = $query;
        }
        $payment_lists = $query->groupBy('transaction_id')->get();
        return view('vendors.payment-report-view', ['prefix' => $this->prefix, 'payment_lists' => $payment_lists]);
    }

    public function exportPaymentReport(Request $request)
    {
        return Excel::download(new PaymentReportExport, 'PaymentReport.xlsx');
    }

    public function handshakeReport(Request $request)
    {

        $this->prefix = request()->route()->getPrefix();
        $paymentreports = PaymentRequest::with('VendorDetails', 'Branch')
            ->groupBy('transaction_id')
            ->get();

        return view('vendors.handshake-report', ['prefix' => $this->prefix, 'paymentreports' => $paymentreports]);
    }

    public function drsWiseReport(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $cc = explode(',', $authuser->branch_id);

        $query = PaymentRequest::with('PaymentHistory','Branch', 'TransactionDetails.ConsignmentNote.RegClient', 'VendorDetails', 'TransactionDetails.ConsignmentNote.vehicletype');
        if ($authuser->role_id == 2) {
                $query->whereIn('branch_id', $cc);
        } else {
            $query = $query;
        }
        $drswiseReports = $query->where('payment_status', '!=', 0)->get();
            
        return view('vendors.drswise-payment-report', ['prefix' => $this->prefix, 'drswiseReports' => $drswiseReports]);
    }

    public function exportdrsWiseReport(Request $request)
    {
        return Excel::download(new exportDrsWiseReport, 'DrsWise-PaymentReport.xlsx');
    }

}
