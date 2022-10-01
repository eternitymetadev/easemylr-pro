<?php

namespace App\Http\Controllers;

use App\Exports\VendorExport;
use App\Imports\VendorImport;
use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Role;
use App\Models\TransactionSheet;
use App\Models\User;
use App\Models\Vendor;
use Auth;
use Config;
use DB;
use Excel;
use Helper;
use Illuminate\Http\Request;
use Session;
use Validator;

class VendorController extends Controller
{
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

            $no_of_digit = 5;
            $vendor = DB::table('vendors')->select('vendor_no')->latest('vendor_no')->first();
            $vendor_no = json_decode(json_encode($vendor), true);
            if (empty($vendor_no) || $vendor_no == null) {
                $vendor_no['vendor_no'] = 0;
            }
            $number = $vendor_no['vendor_no'] + 1;
            $vendor_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);

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

            // $query = $query
            //     ->with('ConsignmentDetail')
            //     ->groupBy('drs_no');

            $query = $query->whereIn('status', ['1', '0', '3'])
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
            }elseif ($authuser->role_id == 6) {
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

        $vendors = Vendor::all();
        return view('vendors.drs-paymentlist', ['prefix' => $this->prefix, 'paymentlist' => $paymentlist, 'vendors' => $vendors, 'peritem' => $peritem]);

    }

    public function getdrsdetails(Request $request)
    {

        $drs = TransactionSheet::with('ConsignmentDetail')->where('drs_no', $request->drsno)->first();

        $response['get_data'] = $drs;
        $response['success'] = true;
        $response['error_message'] = "Can not find data";
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
                \"payable_amount\": \"$request->payable_amount\",
                \"claimed_amount\": \"$request->claimed_amount\",
                \"ax_id\": \"$request->drs_no\",
                \"pfu\": \"$pfu\",
                \"ax_voucher_code\": \"$request->drs_no\",
                \"txn_route\": \"DRS\",
                \"ptype\": \"$request->p_type\",
                \"email\": \"$request->email\",
                \"terid\": \"$request->drs_no\"
                }]",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // $res_data = json_decode($response);
        echo $response;

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

    // Edit Vendor====================== //
    public function editViewVendor(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $getvendor = Vendor::where('id', $request->id)->first();

        return view('vendors.edit-vendor', ['prefix' => $this->prefix, 'getvendor' => $getvendor]);
    }
}
