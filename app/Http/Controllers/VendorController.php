<?php

namespace App\Http\Controllers;

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
use App\Models\Vehicle;
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
    }

    public function index()
    {
        $this->prefix = request()->route()->getPrefix();

        $vendors = Vendor::with('DriverDetail')->get();
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
            // echo'<pre>';print_r($request->all()); die;
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required',
                'transporter_name' => 'required',
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

            $vendor = DB::table('vendors')->select('vendor_no')->latest('vendor_no')->first();
            $vendor_no = json_decode(json_encode($vendor), true);
            if (empty($vendor_no) || $vendor_no == null) {
                $vendor_no = 10101;
            }else{
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
                $url = $this->prefix . '/consignments';
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

            if (isset($request->resetfilter)) {
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
                ->groupBy('drs_no');

            if ($authuser->role_id == 1) {
                $query = $query->with('ConsignmentDetail');
            } elseif ($authuser->role_id == 4) {
                $query = $query
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
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', '%' . $search . '%');
                        });

                });
            }

            if (isset($request->vehicle_no)) {
                $query = $query->where('vehicle_no', $request->vehicle_no);
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

            $paymentlist = $query->orderby('id', 'DESC')->paginate($peritem);

            $html = view('vendors.drs-paymentlist-ajax', ['prefix' => $this->prefix, 'paymentlist' => $paymentlist, 'peritem' => $peritem])->render();
            $paymentlist = $paymentlist->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $lastsevendays = \Carbon\Carbon::today()->subDays(7);
        $date = Helper::yearmonthdate($lastsevendays);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        // $query = $query
        //     ->with('ConsignmentDetail')
        //     ->groupBy('drs_no');

        $query = $query->whereIn('status', ['1', '0', '3'])
            ->where('request_status', 0)
            ->groupBy('drs_no');

        if ($authuser->role_id == 1) {
            $query = $query->with('ConsignmentDetail');
        } elseif ($authuser->role_id == 4) {
            $query = $query
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

        $paymentlist = $query->orderBy('id', 'DESC')->paginate($peritem);
        $paymentlist = $paymentlist->appends($request->query());
        $vehicles = Vehicle::select('id', 'regn_no')->get();

        $vendors = Vendor::all();
        return view('vendors.drs-paymentlist', ['prefix' => $this->prefix, 'paymentlist' => $paymentlist, 'vendors' => $vendors, 'peritem' => $peritem, 'vehicles' => $vehicles]);

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
        $response['get_status'] = $status;
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
         
        echo'<pre>'; print_r($request->all()); die;
        $drs = explode(',', $request->drs_no);
        $randm = rand();
        $pfu = 'ETF';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://stagging.finfect.biz/api/non_finvendors_payments',
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
                \"email\": \"$request->email\",
                \"transaction_id\": \"$request->transaction_id\"
                }]",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res_data = json_decode($response);
        // ============== Success Response
        if ($res_data->message == 'success') {

            if ($request->p_type == 'Balance') {

                $getadvanced = PaymentRequest::select('advanced', 'balance')->where('transaction_id', $request->transaction_id)->first();
                if (!empty($getadvanced->balance)) {
                    $balance = $getadvanced->balance - $request->payable_amount;
                } else {
                    $balance = 0;
                }
                $advance = $getadvanced->advanced + $request->payable_amount;

                TransactionSheet::whereIn('drs_no', $drs)->update(['payment_status' => 2]);

                PaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type,'advanced' => $advance, 'balance' => $balance, 'payment_status' => 2]);

                $bankdetails = array('acc_holder_name' => $request->name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $request->email);

                $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                $paymentresponse['transaction_id'] = $request->transaction_id;
                $paymentresponse['drs_no'] = $request->drs_no;
                $paymentresponse['bank_details'] = json_encode($bankdetails);
                $paymentresponse['purchase_amount'] = $request->claimed_amount;
                $paymentresponse['payment_type'] = $request->p_type;
                $paymentresponse['advance'] = $advance;
                $paymentresponse['balance'] = $balance;
                $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                $paymentresponse['payment_status'] = 2;

                $paymentresponse = PaymentHistory::create($paymentresponse);

            } else {

                $balance_amt = $request->claimed_amount - $request->payable_amount;
                //======== Payment History save =========//
                $bankdetails = array('acc_holder_name' => $request->name, 'account_no' => $request->acc_no, 'ifsc_code' => $request->ifsc, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name, 'email' => $request->email);

                $paymentresponse['refrence_transaction_id'] = $res_data->refrence_transaction_id;
                $paymentresponse['transaction_id'] = $request->transaction_id;
                $paymentresponse['drs_no'] = $request->drs_no;
                $paymentresponse['bank_details'] = json_encode($bankdetails);
                $paymentresponse['purchase_amount'] = $request->claimed_amount;
                $paymentresponse['payment_type'] = $request->p_type;
                $paymentresponse['advance'] = $request->payable_amount;
                $paymentresponse['balance'] = $balance_amt;
                $paymentresponse['tds_deduct_balance'] = $request->final_payable_amount;
                $paymentresponse['payment_status'] = 2;

                $paymentresponse = PaymentHistory::create($paymentresponse);

                PaymentRequest::where('transaction_id', $request->transaction_id)->update(['payment_type' => $request->p_type ,'advanced' => $request->payable_amount, 'balance' => $balance_amt, 'payment_status' => 2]);

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

            foreach ($simpl as $lr) {

                ConsignmentNote::where('id', $lr['consignment_no'])->where('status', '!=', 0)->update(['purchase_price' => $request->purchase_price]);

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

    public function importVendor(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $data = Excel::import(new VendorImport, request()->file('vendor_file'));
        $message = 'Vendors Imported Successfully';

        if ($data) {
            $response['success'] = true;
            $response['page'] = 'bulk-imports';
            $response['error'] = false;
            $response['success_message'] = $message;
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
        $drsno = explode(',', $request->drs_no);
        $consignment = TransactionSheet::whereIn('drs_no', $drsno)
            ->groupby('drs_no')
            ->get();

        $simplyfy = json_decode(json_encode($consignment), true);
        $no_of_digit = 7;
        $transactionId = DB::table('payment_requests')->select('transaction_id')->latest('transaction_id')->first();
        $transaction_id = json_decode(json_encode($transactionId), true);
        if (empty($transaction_id) || $transaction_id == null) {
            $transaction_id['transaction_id'] = 0;
        }
        $number = $transaction_id['transaction_id'] + 1;

        $i = 0;
        foreach ($simplyfy as $value) {
            $i++;
            $transaction = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);
            $drs_no = $value['drs_no'];
            $vendor_id = $request->vendor_name;
            $vehicle_no = $value['vehicle_no'];

            $transaction = PaymentRequest::create(['transaction_id' => $transaction, 'drs_no' => $drs_no, 'vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'total_amount' => $request->claimed_amount, 'payment_status' => 0, 'status' => '1']);
        }
        TransactionSheet::whereIn('drs_no', $drsno)->update(['request_status' => '1']);

        $url = $this->prefix . '/request-list';
        $response['success'] = true;
        $response['redirect_url'] = $url;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function requestList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $requestlists = PaymentRequest::with('VendorDetails')
            ->groupBy('transaction_id')
            ->get();
        $vendors = Vendor::all();

        return view('vendors.request-list', ['prefix' => $this->prefix, 'requestlists' => $requestlists, 'vendors' => $vendors]);
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
}
