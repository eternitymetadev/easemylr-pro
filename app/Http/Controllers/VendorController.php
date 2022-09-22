<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\TransactionSheet;
use App\Models\ConsignmentNote;
use DB;
use Validator;


class VendorController extends Controller
{
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        return view('vendors.create-vendor',['prefix' => $this->prefix]);
    }

    public function store(Request $request)
    {
        try {
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
            if(!empty($panupload)){
                $panfile = $panupload->getClientOriginalName();
                $panupload->move(public_path('drs/uploadpan'), $panfile);
            }else{
                $panfile = NULL;
            }
        
            $cheaque = $request->file('cancel_cheaque');
            if(!empty($cheaque)){
                $cheaquefile = $cheaque->getClientOriginalName();
                $cheaque->move(public_path('drs/cheaque'), $cheaquefile);
            }else{
                $cheaquefile = NULL;
            }

            $no_of_digit = 5;
            $vendor = DB::table('vendors')->select('vendor_no')->latest('vendor_no')->first();
            $vendor_no = json_decode(json_encode($vendor), true);
            if (empty($vendor_no) || $vendor_no == null) {
                $vendor_no['vendor_no'] = 0;
            }
            $number = $vendor_no['vendor_no'] + 1;
            $vendor_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);
             
            $bankdetails = array('acc_holder_name'=> $request->acc_holder_name, 'account_no' => $request->account_no, 'ifsc_code' => $request->ifsc_code, 'bank_name' => $request->bank_name, 'branch_name' => $request->branch_name);

            $otherdetail = array('transporter_name'=> $request->transporter_name, 'driver_name' => $request->driver_name, 'contact_person_name' => $request->contact_person_name, 'contact_person_number' => $request->contact_person_number);
            // dd($vendor_no);
          
            $vendorsave['type']               = 'Vendor';
            $vendorsave['vendor_no']          = $vendor_no;
            $vendorsave['name']               = $request->name;
            $vendorsave['bank_details']       = json_encode($bankdetails);
            $vendorsave['pan']                = $request->pan;;
            $vendorsave['upload_pan']         = $panfile;
            $vendorsave['cancel_cheaque']     = $cheaquefile;
            $vendorsave['other_details']      = json_encode($otherdetail);
            $vendorsave['is_acc_verified']    = 0;
            $vendorsave['is_active']          = 1;

           
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
     
    public function paymentList()
    {
        $this->prefix = request()->route()->getPrefix();
        $drslist = TransactionSheet::with('ConsignmentDetail')
        ->orderBy('id', 'DESC')
        ->groupBy('drs_no')
        ->paginate(50);

        $vendors = Vendor::all();
        return view('vendors.drs-paymentlist',['prefix' => $this->prefix, 'drslist' => $drslist,'vendors' => $vendors]);

    }

    public function getdrsdetails(Request $request) 
        {

            $drs = TransactionSheet::where('drs_no',$request->drsno)->get();

            $response['get_data'] = $drs;
            $response['success'] = true;
            $response['error_message'] = "Can not find data";
            return response()->json($response);
        }

    public function vendorbankdetails(Request $request)
    {
        // dd($request->vendor_id);
        $vendors = Vendor::where('id',$request->vendor_id)->first();

        $response['vendor_details'] = $vendors;
        $response['success'] = true;
        $response['error_message'] = "Can not created Vendor please try again";
        return response()->json($response);
    }

    public function createPaymentRequest(Request $request)
    {
        dd($request->all());

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://127.0.0.1:8000/api/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('unique_code' => 'sahil',
            'name' => '32132114534',
            'acc_no' => 'SBIN13708',
            'beneficiary_name' => 'SBI',
            'ifsc' => 'Baldwara',
            'bank_name' => 'eternity',
            'payable_amount' => 'vipan',
            'claimed_amount' => 'vipan',
            'pfu' => '144242454',
            'ax_voucher_code' => '54534',
            'txn_route'=> new CURLFILE('8tDgKdrAK/bg-image-cake-and-coffee.png')),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;


    }
}
