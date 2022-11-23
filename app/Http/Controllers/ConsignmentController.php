<?php

namespace App\Http\Controllers;

use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\Driver;
use App\Models\Job;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Role;
use App\Models\TransactionSheet;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\User;
use Auth;
use DataTables;
use DB;
use Helper;
use Illuminate\Http\Request;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use QrCode;
use Response;
use Storage;
use Validator;
use Config;
use Session;
Use URL;
use App\Events\RealtimeMessage;

class ConsignmentController extends Controller
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
    public function index(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = ConsignmentNote::query();
        
        if ($request->ajax()) {
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }
            if (isset($request->updatestatus)) {
                ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel, 'delivery_status' => 'Cancel']);
                ConsignmentItem::where('consignment_id', $request->id)->update(['status' => $request->status]);

                $url = $this->prefix . '/consignments';
                $response['success'] = true;
                $response['success_message'] = "Consignment updated successfully";
                $response['error'] = false;
                $response['page'] = 'consignment-updateupdate';
                $response['redirect_url'] = $url;

                return response()->json($response);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $baseclient = explode(',',$authuser->baseclient_id);
            $regclient = explode(',',$authuser->regionalclient_id);
            $cc = explode(',',$authuser->branch_id);

            $query = $query->where('status', '!=', 5)->with('ConsignmentItems','ConsignerDetail','ConsigneeDetail','VehicleDetail','DriverDetail','JobDetail');

            if($authuser->role_id ==1){
                $query;
            }
            elseif($authuser->role_id ==4){
                $query = $query->whereIn('regclient_id', $regclient);
            }
            elseif($authuser->role_id ==7){
                $query = $query->whereIn('regclient_id', $regclient);
            }
            else{
                $query = $query->whereIn('branch_id', $cc);
            }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                        $regclientquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ConsignerDetail',function( $query ) use($search,$searchT){
                            $query->where(function ($cnrquery)use($search,$searchT) {
                            $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('ConsigneeDetail',function( $query ) use($search,$searchT){
                        $query->where(function ($cneequery)use($search,$searchT) {
                            $cneequery->where('nick_name', 'like', '%' . $search . '%');
                        });
                    });

                });
            }

            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }


            $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            $consignments = $consignments->appends($request->query());

            $html =  view('consignments.consignment-list-ajax',['prefix'=>$this->prefix,'consignments' => $consignments,'peritem'=>$peritem])->render();
            

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $baseclient = explode(',',$authuser->baseclient_id);
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);

        $query = $query->where('status', '!=', 5)->with('ConsignmentItems','ConsignerDetail','ConsigneeDetail','VehicleDetail','DriverDetail','JobDetail');

        if($authuser->role_id ==1){
            $query;
        }
        elseif($authuser->role_id ==4){
            $query = $query->whereIn('regclient_id', $regclient);
        }
        elseif($authuser->role_id ==7){
            $query = $query->whereIn('regclient_id', $regclient);
        }
        else{
            $query = $query->whereIn('branch_id', $cc);
        }
        $consignments = $query->orderBy('id','DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());
        

        return view('consignments.consignment-list', ['consignments' => $consignments, 'peritem'=>$peritem, 'prefix' => $this->prefix, 'segment' => $this->segment]);
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
        }else if($authuser->role_id != 2 || $authuser->role_id != 3){
            if($authuser->role_id !=1){
                $consigners = Consigner::select('id', 'nick_name')->whereIn('regionalclient_id',$regclient)->get();
            }else{
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else {
            $consigners = Consigner::select('id', 'nick_name')->get();
        }
        
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

        /////////////////////////////Bill to regional clients //////////////////////////
       
        if($authuser->role_id == 2 || $authuser->role_id == 3){
            $branch = $authuser->branch_id;
            $branch_loc = explode(',', $branch);
            $regionalclient = RegionalClient::whereIn('location_id', $branch_loc )->select('id', 'name')->get();
        
        }elseif($authuser->role_id == 4){
            $reg = $authuser->regionalclient_id;
            $regional = explode(',', $reg);
            $regionalclient = RegionalClient::whereIn('id', $regional )->select('id', 'name')->get();
        }else{
            $regionalclient = RegionalClient::select('id', 'name')->get();
        }


        return view('consignments.create-consignment', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'consignmentno' => $consignmentno, 'drivers' => $drivers,'regionalclient' => $regionalclient]);
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
            $consignmentsave['is_salereturn'] = $request->is_salereturn;
            $consignmentsave['consignment_no'] = $consignmentno;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['payment_type'] = $request->payment_type;
            $consignmentsave['description'] = $request->description;
            $consignmentsave['packing_type'] = $request->packing_type;
            $consignmentsave['dispatch'] = $request->dispatch;
            $consignmentsave['total_quantity'] = $request->total_quantity;
            $consignmentsave['total_weight'] = $request->total_weight;
            $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            // $consignmentsave['total_freight'] = $request->total_freight;
            $consignmentsave['transporter_name']  = $request->transporter_name;
            $consignmentsave['vehicle_type']      = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;
            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = $status;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            $saveconsignment = ConsignmentNote::create($consignmentsave);
            $consignment_id = $saveconsignment->id;
           //===================== Create DRS in LR ================================= //
           
           if(!empty($request->vehicle_id)){
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
            //===========================End drs lr ================================= //
            if ($saveconsignment) {

                /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
                $vn = $consignmentsave['vehicle_id'];
                $lid = $saveconsignment->id;
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

                if(($request->edd) >= $request->consignment_date){
                    if (!empty($vn) && !empty($simplyfy[0]['team_id']) && !empty($simplyfy[0]['fleet_id'])) {
                        $createTask = $this->createTookanTasks($simplyfy);
                        $json = json_decode($createTask[0], true);
                        $job_id = $json['data']['job_id'];
                        $tracking_link = $json['data']['tracking_link'];
                        $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
                    }
                }
                // insert consignment items
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
                        $save_data['consignment_id'] = $saveconsignment->id;
                        $save_data['status'] = 1;
                        $saveconsignmentitems = ConsignmentItem::create($save_data);
                    }
                }
                $url = $this->prefix . '/consignments';
                $response['success'] = true;
                $response['success_message'] = "Consignment Added successfully";
                $response['error'] = false;
                // $response['resetform'] = true;
                $response['page'] = 'create-consignment';
                $response['redirect_url'] = $url;
            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not created consignment please try again";
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($consignment)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        if ($authuser->role_id != 1) {
            if ($authuser->role_id == $role_id->id) {
                $getconsignment = $query->whereIn('branch_id', $cc)->orderby('id', 'DESC')->get();
            }
        } else {
            $getconsignment = $query->orderby('id', 'DESC')->get();
        }
        $branch_add = BranchAddress::first();
        $locations = Location::whereIn('id', $cc)->first();
        return view('consignments.view-consignment', ['prefix' => $this->prefix, 'getconsignment' => $getconsignment, 'branch_add' => $branch_add, 'locations' => $locations]);
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

    // get consigner address on change
    public function getConsigners(Request $request)
    {
        $getconsigners = Consigner::select('address_line1', 'address_line2', 'address_line3', 'address_line4', 'gst_number', 'phone', 'city', 'branch_id','regionalclient_id')->with('GetRegClient','GetBranch')->where(['id' => $request->consigner_id, 'status' => '1'])->first();
        
        $getregclients = RegionalClient::select('id','is_multiple_invoice')->where('id', $request->regclient_id)->first();
        $getConsignees = Consignee::select('id', 'nick_name')->where(['consigner_id' => $request->consigner_id])->get();
        if ($getconsigners) {
            $response['success'] = true;
            $response['success_message'] = "Consigner list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getconsigners;
            $response['consignee'] = $getConsignees;
            $response['regclient'] = $getregclients;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consigner list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }
    //// get consioner from regional client ////
    public function getConsignersonRegional(Request $request)
    {
        $getconsigners = Consigner::select('id','nick_name')->where('regionalclient_id', $request->regclient_id)->get();

        $getregclients = RegionalClient::select('id','location_id','is_multiple_invoice')->where('id',$request->regclient_id)->first();
        
        if ($getconsigners) {
            $response['success'] = true;
            $response['success_message'] = "Consigner list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getconsigners;
            $response['data_regclient'] = $getregclients;

        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consigner list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }


    // get consigner address on change
    public function getConsignees(Request $request)
    {
        $getconsignees = Consignee::select('address_line1', 'address_line2', 'address_line3', 'address_line4', 'gst_number', 'phone')->where(['id' => $request->consignee_id, 'status' => '1'])->first();

        if ($getconsignees) {
            $response['success'] = true;
            $response['success_message'] = "Consignee list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getconsignees;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consignee list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    // getConsigndetails
    public function getConsigndetails(Request $request)
    {
        $cn_id = $request->id;
        $cn_details = ConsignmentNote::where('id', $cn_id)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->first();
        if ($cn_details) {
            $response['success'] = true;
            $response['success_message'] = "Consignment details fetch successfully";
            $response['error'] = false;
            $response['data'] = $cn_details;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch consignment details please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    // print LR for new view
    public function consignPrintview(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::get();
        $locations = Location::whereIn('id', $cc)->first();
        $cn_id = $request->id;
        
        $getdata = ConsignmentNote::where('id', $cn_id)->with('ConsignmentItems', 'ConsignerDetail.GetZone', 'ConsigneeDetail.GetZone', 'ShiptoDetail.GetZone', 'VehicleDetail', 'DriverDetail')->first();
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
        if (isset($data['consigner_detail']['address_line3'] )) {
            $address_line3 = '' . $data['consigner_detail']['address_line3'] . '<br>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['consigner_detail']['address_line4'] )) {
            $address_line4 = '' . $data['consigner_detail']['address_line4'] . '<br><br>';
        } else {
            $address_line4 = '<br>';
        }
        if (isset($data['consigner_detail']['city'] )) {
            $city = $data['consigner_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['consigner_detail']['get_zone']['state'] )) {
            $district = $data['consigner_detail']['get_zone']['state'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['consigner_detail']['postal_code'] )) {
            $postal_code = $data['consigner_detail']['postal_code'].'<br>';
        } else {
            $postal_code = '';
        }
        if (isset($data['consigner_detail']['gst_number'] )) {
            $gst_number = 'GST No: ' . $data['consigner_detail']['gst_number'] . '<br>';
        } else {
            $gst_number = '';
        }
        if (isset($data['consigner_detail']['phone'] )) {
            $phone = 'Phone No: ' . $data['consigner_detail']['phone'] . '<br>';
        } else {
            $phone = '';
        }

        $conr_add =  $legal_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

        if (isset($data['consignee_detail']['legal_name'] )) {
            $nick_name = '<b>' . $data['consignee_detail']['legal_name'] . '</b><br>';
        } else {
            $nick_name = '';
        }
        if (isset($data['consignee_detail']['address_line1'] )) {
            $address_line1 = '' . $data['consignee_detail']['address_line1'] . '<br>';
        } else {
            $address_line1 = '';
        }
        if (isset($data['consignee_detail']['address_line2'] )) {
            $address_line2 = '' . $data['consignee_detail']['address_line2'] . '<br>';
        } else {
            $address_line2 = '';
        }
        if (isset($data['consignee_detail']['address_line3'] )) {
            $address_line3 = '' . $data['consignee_detail']['address_line3'] . '<br>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['consignee_detail']['address_line4'] )) {
            $address_line4 = '' . $data['consignee_detail']['address_line4'] . '<br><br>';
        } else {
            $address_line4 = '<br>';
        }
        if (isset($data['consignee_detail']['city'] )) {
            $city = $data['consignee_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['consignee_detail']['get_zone']['state'] )) {
            $district = $data['consignee_detail']['get_zone']['state'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['consignee_detail']['postal_code'] )) {
            $postal_code = $data['consignee_detail']['postal_code'].'<br>';
        } else {
            $postal_code = '';
        }

        if (isset($data['consignee_detail']['gst_number'] )) {
            $gst_number = 'GST No: ' . $data['consignee_detail']['gst_number'] . '<br>';
        } else {
            $gst_number = '';
        }
        if (isset($data['consignee_detail']['phone'] )) {
            $phone = 'Phone No: ' . $data['consignee_detail']['phone'] . '<br>';
        } else {
            $phone = '';
        }

        $consnee_add = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

        if (isset($data['shipto_detail']['legal_name'] )) {
            $nick_name = '<b>' . $data['shipto_detail']['legal_name'] . '</b><br>';
        } else {
            $nick_name = '';
        }
        if (isset($data['shipto_detail']['address_line1'] )) {
            $address_line1 = '' . $data['shipto_detail']['address_line1'] . '<br>';
        } else {
            $address_line1 = '';
        }
        if (isset($data['shipto_detail']['address_line2'] )) {
            $address_line2 = '' . $data['shipto_detail']['address_line2'] . '<br>';
        } else {
            $address_line2 = '';
        }
        if (isset($data['shipto_detail']['address_line3'] )) {
            $address_line3 = '' . $data['shipto_detail']['address_line3'] . '<br>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['shipto_detail']['address_line4'] )) {
            $address_line4 = '' . $data['shipto_detail']['address_line4'] . '<br><br>';
        } else {
            $address_line4 = '<br>';
        }
        if (isset($data['shipto_detail']['city'] )) {
            $city = $data['shipto_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['shipto_detail']['get_zone']['state'] )) {
            $district = $data['shipto_detail']['get_zone']['state'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['shipto_detail']['postal_code'] )) {
            $postal_code = $data['shipto_detail']['postal_code'].'<br>';
        } else {
            $postal_code = '';
        }
        if (isset($data['shipto_detail']['gst_number'] )) {
            $gst_number = 'GST No: ' . $data['shipto_detail']['gst_number'] . '<br>';
        } else {
            $gst_number = '';
        }
        if (isset($data['shipto_detail']['phone'] )) {
            $phone = 'Phone No: ' . $data['shipto_detail']['phone'] . '<br>';
        } else {
            $phone = '';
        }

        $shiptoadd =  $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

        $generate_qrcode = QrCode::size(150)->generate('Eternity Forwarders Pvt. Ltd.');
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
        if($locations->id == 2 || $locations->id == 6 || $locations->id == 26){
            $branch_address = '<span style="font-size: 14px;"><b>'.$branch_add[1]->name.' </b></span><br />
        <b>' . $branch_add[1]->address . ',</b><br />
        <b>	' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . '</b><br />
        <b>GST No. : '. $branch_add[1]->gst_number .'</b><br />';
        }else{
            $branch_address = '<span style="font-size: 14px;"><b>'.$branch_add[0]->name.' </b></span><br />
        <b>	Plot no: ' . $branch_add[0]->address . ',</b><br />
        <b>	' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . '</b><br />
        <b>GST No. : '. $branch_add[0]->gst_number .'</b><br />';
        }
        
        // relocate cnr cnee address check for sale to return case
        if($data['is_salereturn'] == '1'){
            $cnradd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            '.$consnee_add.'
            </p>
            </div>';
            $cneadd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                '.$conr_add.'
            </p>
            </div>';
            $shipto_address = '';
        }else{
            $cnradd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            '.$conr_add.'
            </p>
            </div>';
            $cneadd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                '.$consnee_add.'
            </p>
            </div>';
            $shipto_address = '<td width="30%" style="vertical-align:top;>
            <div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">SHIP TO NAME & ADDRESS</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
              '.$shiptoadd.'
            </p>
                </div>
            </td>';
        }

        $pay = public_path('assets/img/LOGO_Frowarders.jpg');
        for ($i = 1; $i < 5; $i++) {
            if ($i == 1) {$type = 'ORIGINAL';} elseif ($i == 2) {$type = 'DUPLICATE';} elseif ($i == 3) {$type = 'TRIPLICATE';} elseif ($i == 4) {$type = 'QUADRUPLE';}
        if(!empty($data['consigner_detail']['get_zone']['state'])){
            $cnr_state = $data['consigner_detail']['get_zone']['state'];
        }
        else{
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
                            '.$branch_address.'
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
                                        <P>'.$type.'</P>
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
                                            <th class="mini-th">'.@$data['consignee_detail']['city'] . '</th>
                                            
                                        </tr>
                                    </table>
                        </div>  
                                </td>
                            </tr>
                        </table>
                        <div class="loc">
                            <table>
                                <tr>
                                    <td class="width_set">
                                        <div style="margin-left: 20px">
                                    <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i><div class="vl" ></div>

                                        <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>'.@$data['consignee_detail']['postal_code'].','.@$data['consignee_detail']['city'].','.@$data['consignee_detail']['get_zone']['state'].'</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>
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
                                    '.$cnradd_heading.'
                                    </td>
                                    <td width="30%" style="vertical-align:top;>
                                    '.$cneadd_heading.'
                                    </td>
                                    '.$shipto_address.'
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
                                                        <th>'.$no_invoive .'</th>
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
                               
                         $html .=' <tr>
                                <td style="width:70px ">' . $dataitem['order_id'] . '</td>
                                <td style="width: 70px">' . $dataitem['invoice_no'] . '</td>
                                <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['invoice_date']) . '</td>
                                <td style="width:70px ">' . $dataitem['invoice_amount'] . '</td>
                                <td style="width: 70px">' . $dataitem['e_way_bill'] . '</td>
                                <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['e_way_bill_date']) . '</td>
                                <td style="width:60px "> ' . $dataitem['quantity'] . '</td>
                                <td style="width:70px ">' . $dataitem['weight'] . ' Kgs. </td>
                                <td style="width:70px "> '. $dataitem['gross_weight'] . ' Kgs.</td>
                                
                                </tr>';
                              }
                      $html .='      </table>
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
            $pdf->save(public_path() . '/consignment-pdf/congn-' . $i . '.pdf')->stream('congn-' . $i . '.pdf');
            $pdf_name[] = 'congn-' . $i . '.pdf';
        }
        $pdfMerger = PDFMerger::init();
        foreach ($pdf_name as $pdf) {
            $pdfMerger->addPDF(public_path() . '/consignment-pdf/' . $pdf);
        }
        $pdfMerger->merge();
        $pdfMerger->save("all.pdf", "browser");
        $file = new Filesystem;
        $file->cleanDirectory('pdf');
    }

    // print LR for old view
    public function consignPrintviewold(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::get();
        $locations = Location::whereIn('id', $cc)->first();

        $cn_id = $request->id;
        $getdata = ConsignmentNote::where('id', $cn_id)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->first();
        $data = json_decode(json_encode($getdata), true);

        if (isset($data['consigner_detail']['legal_name'] )) {
            $legal_name = '<p><b>' . $data['consigner_detail']['legal_name'] . '</b></p>';
        } else {
            $legal_name = '';
        }
        if (isset($data['consigner_detail']['address_line1'] )) {
            $address_line1 = '<p>' . $data['consigner_detail']['address_line1'] . '</p>';
        } else {
            $address_line1 = '';
        }
        if (isset($data['consigner_detail']['address_line2'] )) {
            $address_line2 = '<p>' . $data['consigner_detail']['address_line2'] . '</p>';
        } else {
            $address_line2 = '';
        }
        if (isset($data['consigner_detail']['address_line3'] )) {
            $address_line3 = '<p>' . $data['consigner_detail']['address_line3'] . '</p>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['consigner_detail']['address_line4'] )) {
            $address_line4 = '<p>' . $data['consigner_detail']['address_line4'] . '</p>';
        } else {
            $address_line4 = '';
        }
        if (isset($data['consigner_detail']['city'] )) {
            $city = $data['consigner_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['consigner_detail']['get_zone']['district'] )) {
            $district = @$data['consigner_detail']['get_zone']['district'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['consigner_detail']['postal_code'] )) {
            $postal_code = $data['consigner_detail']['postal_code'];
        } else {
            $postal_code = '';
        }
        if (isset($data['consigner_detail']['gst_number'] )) {
            $gst_number = '<p>GST No: ' . $data['consigner_detail']['gst_number'] . '</p>';
        } else {
            $gst_number = '';
        }
        if (isset($data['consigner_detail']['phone'] )) {
            $phone = '<p>Phone No: ' . $data['consigner_detail']['phone'] . '</p>';
        } else {
            $phone = '';
        }

        $conr_add = '<p>' . 'CONSIGNOR NAME & ADDRESS' . '</p>
            ' . $legal_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '<p>' . $city . ' ' . $district . ' ' . $postal_code . '</p>' . $gst_number . ' ' . $phone;

        if (isset($data['consignee_detail']['nick_name'] )) {
            $nick_name = '<p><b>' . $data['consignee_detail']['nick_name'] . '</b></p>';
        } else {
            $nick_name = '';
        }
        if (isset($data['consignee_detail']['address_line1'] )) {
            $address_line1 = '<p>' . $data['consignee_detail']['address_line1'] . '</p>';
        } else {
            $address_line1 = '';
        }
        if (isset($data['consignee_detail']['address_line2'] )) {
            $address_line2 = '<p>' . $data['consignee_detail']['address_line2'] . '</p>';
        } else {
            $address_line2 = '';
        }
        if (isset($data['consignee_detail']['address_line3'] )) {
            $address_line3 = '<p>' . $data['consignee_detail']['address_line3'] . '</p>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['consignee_detail']['address_line4'] )) {
            $address_line4 = '<p>' . $data['consignee_detail']['address_line4'] . '</p>';
        } else {
            $address_line4 = '';
        }
        if (isset($data['consignee_detail']['city'] )) {
            $city = $data['consignee_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['consignee_detail']['get_zone']['district'] )) {
            $district = @$data['consignee_detail']['get_zone']['district'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['consignee_detail']['postal_code'] )) {
            $postal_code = $data['consignee_detail']['postal_code'];
        } else {
            $postal_code = '';
        }

        if (isset($data['consignee_detail']['gst_number'] )) {
            $gst_number = '<p>GST No: ' . $data['consignee_detail']['gst_number'] . '</p>';
        } else {
            $gst_number = '';
        }
        if (isset($data['consignee_detail']['phone'] )) {
            $phone = '<p>Phone No: ' . $data['consignee_detail']['phone'] . '</p>';
        } else {
            $phone = '';
        }

        $consnee_add = '<p>' . 'CONSIGNEE NAME & ADDRESS' . '</p>
        ' . $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '<p>' . $city . ' ' . $district . ' ' . $postal_code . '</p>' . $gst_number . ' ' . $phone;

        if (isset($data['shipto_detail']['nick_name'] )) {
            $nick_name = '<p><b>' . $data['shipto_detail']['nick_name'] . '</b></p>';
        } else {
            $nick_name = '';
        }
        if (isset($data['shipto_detail']['address_line1'] )) {
            $address_line1 = '<p>' . $data['shipto_detail']['address_line1'] . '</p>';
        } else {
            $address_line1 = '';
        }
        if (isset($data['shipto_detail']['address_line2'] )) {
            $address_line2 = '<p>' . $data['shipto_detail']['address_line2'] . '</p>';
        } else {
            $address_line2 = '';
        }
        if (isset($data['shipto_detail']['address_line3'] )) {
            $address_line3 = '<p>' . $data['shipto_detail']['address_line3'] . '</p>';
        } else {
            $address_line3 = '';
        }
        if (isset($data['shipto_detail']['address_line4'] )) {
            $address_line4 = '<p>' . $data['shipto_detail']['address_line4'] . '</p>';
        } else {
            $address_line4 = '';
        }
        if (isset($data['shipto_detail']['city'] )) {
            $city = $data['shipto_detail']['city'] . ',';
        } else {
            $city = '';
        }
        if (isset($data['shipto_detail']['get_zone']['district'] )) {
            $district = @$data['shipto_detail']['get_zone']['district'] . ',';
        } else {
            $district = '';
        }
        if (isset($data['shipto_detail']['postal_code'] )) {
            $postal_code = $data['shipto_detail']['postal_code'];
        } else {
            $postal_code = '';
        }
        if (isset($data['shipto_detail']['gst_number'] )) {
            $gst_number = '<p>GST No: ' . $data['shipto_detail']['gst_number'] . '</p>';
        } else {
            $gst_number = '';
        }
        if (isset($data['shipto_detail']['phone'] )) {
            $phone = '<p>Phone No: ' . $data['shipto_detail']['phone'] . '</p>';
        } else {
            $phone = '';
        }

        $shiptoadd = '<p>' . 'SHIP TO NAME & ADDRESS' . '</p>
        ' . $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '<p>' . $city . ' ' . $district . ' ' . $postal_code . '</p>' . $gst_number . ' ' . $phone;

        $generate_qrcode = QrCode::size(150)->generate('Eternity Forwarders Pvt. Ltd.');
        $output_file = '/qr-code/img-' . time() . '.svg';
        Storage::disk('public')->put($output_file, $generate_qrcode);
        $fullpath = storage_path('app/public/' . $output_file);
        //echo'<pre>'; print_r($fullpath);
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
        if($locations->id == 2 || $locations->id == 6 || $locations->id == 26){
        $branch_address = '<h2>' . $branch_add[1]->name . '</h2>
                                <table width="100%">
                                    <tr>
                                        <td width="50%">
                                            <p>' . $branch_add[1]->address . '</p>
                                            <p>' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . '</p>
                                            <p>GST No. : ' . $branch_add[1]->gst_number . '</p>
                                            <p>CIN No. : U63030PB2021PTC053388 </p>
                                            <p>Email : ' . @$locations->email . '</p>
                                            <p>Phone No. : ' . @$locations->phone . '' . '</p>
                                            <br>
                                            <span>
                                                <hr id="s" style="width:100%;">
                                                </hr>
                                            </span>
                                        </td>';
        }else{
            $branch_address = '<h2>' . $branch_add[0]->name . '</h2>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <p>Plot No. ' . $branch_add[0]->address . '</p>
                        <p>' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . '</p>
                        <p>GST No. : ' . $branch_add[0]->gst_number . '</p>
                        <p>CIN No. : U63030PB2021PTC053388 </p>
                        <p>Email : ' . @$locations->email . '</p>
                        <p>Phone No. : ' . @$locations->phone . '' . '</p>
                        <br>
                        <span>
                            <hr id="s" style="width:100%;">
                            </hr>
                        </span>
                    </td>';
        }

        for ($i = 1; $i < 5; $i++) {
            if ($i == 1) {$type = 'ORIGINAL';} elseif ($i == 2) {$type = 'DUPLICATE';} elseif ($i == 3) {$type = 'TRIPLICATE';} elseif ($i == 4) {$type = 'QUADRUPLE';}

            $html = '<!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <title>PDF</title>
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <style>
                                .aa{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                .bb{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                .cc{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                h2.l {
                                    margin-left: 90px;
                                    margin-top: 132px;
                                    margin-bottom: 2px;
                                }
                                p.l {
                                    margin-left: 90px;
                                }
                                img#set_img {
                                    margin-left: 25px;
                                    margin-bottom: 100px;
                                }
                                p {
                                    margin-top: 2px;
                                    margin-bottom: 2px;
                                }
                                h4 {
                                    margin-top: 2px;
                                    margin-bottom: 2px;
                                }
                                body {
                                    font-family: Arial, Helvetica, sans-serif;
                                    font-size: 14px;
                                }
                            </style>
                        </head>
                        <body>
                        <div class="container">
                            <div class="row">';

            $html .= $branch_address.'<td width="50%">
            <h2 class="l">CONSIGNMENT NOTE</h2>
            <p class="l">' . $type . '</p>
        </td>
    </tr>
</table></div></div>';
            $html .= '<div class="row"><div class="col-sm-6">
                                <table width="100%">
                                <tr>
                            <td width="30%">
                                <p><b>Consignment No.</b></p>
                                <p><b>Consignment Date</b></p>
                                <p><b>Dispatch From</b></p>
                                <p><b>Order Id</b></p>
                                <p><b>Invoice No.</b></p>
                                <p><b>Invoice Date</b></p>
                                <p><b>Value INR</b></p>
                                <p><b>Vehicle No.</b></p>
                                <p><b>Driver Name</b></p>
                                <p><b>Driver Number</b></p>
                            </td>
                            <td width="30%">';
            if (@$data['consignment_no'] != '') {
                $html .= '<p>' . $data['id'] . '</p>';
            } else {
                $html .= '<p>N/A</p>';
            }
            if (@$data['consignment_date'] != '') {
                $html .= '<p>' . date('d-m-Y', strtotime($data['consignment_date'])) . '</p>';
            } else {
                $html .= '<p> N/A </p>';
            }
            if (@$data['consigner_detail']['city'] != '') {
                $html .= '<p> ' . $data['consigner_detail']['city'] . '</p>';
            } else {
                $html .= '<p> N/A </p>';
            }
            if (@$data['order_id'] != '') {
                $html .= '<p>' . $data['order_id'] . '</p>';
            } else {
                $html .= '<p> - </p>';
            }
            if (@$data['invoice_no'] != '') {
                $html .= '<p>' . $data['invoice_no'] . '</p>';
            } else {
                $html .= '<p> N/A </p>';
            }
            if (@$data['invoice_date'] != '') {
                $html .= '<p>' . date('d-m-Y', strtotime($data['invoice_date'])) . '</p>';
            } else {
                $html .= '<p> N/A </p>';
            }

            if (@$data['invoice_amount'] != '') {
                $html .= '<p>' . $data['invoice_amount'] . '</p>';
            } else {
                $html .= '<p> N/A </p>';
            }
            if (@$data['vehicle_detail']['regn_no'] != '') {
                $html .= '<p>' . $data['vehicle_detail']['regn_no'] . '</p>';
            } else {
                $html .= '<p> - </p>';
            }
            if (@$data['driver_detail']['name'] != '') {
                $html .= '<p>' . ucwords($data['driver_detail']['name']) . '</p>';
            } else {
                $html .= '<p> - </p>';
            }
            if (@$data['driver_detail']['phone'] != '') {
                $html .= '<p>' . ucwords($data['driver_detail']['phone']) . '</p>';
            } else {
                $html .= '<p> - </p>';
            }

            $html .= '</td>
                            <td width="50%" colspan="3" style="text-align: center;">
                            <img src= "' . $fullpath . '" alt="barcode">
                            </td>
                        </tr>
                    </table>
                </div>
                <span><hr id="e"></hr></span>
            </div>
            <div class="main">' . $adresses . '</div>
            <span><hr id="e"></hr></span><br>';
            $html .= '<div class="bb">
                <table class="aa" width="100%">
                    <tr>
                        <th class="cc">Sr.No.</th>
                        <th class="cc">Description</th>
                        <th class="cc">Quantity</th>
                        <th class="cc">Net Weight</th>
                        <th class="cc">Gross Weight</th>
                        <th class="cc">Freight</th>
                        <th class="cc">Payment Terms</th>
                    </tr>';
            ///
            $counter = 0;
            foreach ($data['consignment_items'] as $k => $dataitem) {
                $counter = $counter + 1;
                $html .= '<tr>' .
                    '<td class="cc">' . $counter . '</td>' .
                    '<td class="cc">' . $dataitem['description'] . '</td>' .
                    '<td class="cc">' . $dataitem['packing_type'] . ' ' . $dataitem['quantity'] . '</td>' .
                    '<td class="cc">' . $dataitem['weight'] . ' Kgs.</td>' .
                    '<td class="cc">' . $dataitem['gross_weight'] . ' Kgs.</td>' .
                    '<td class="cc">INR ' . $dataitem['freight'] . '</td>' .
                    '<td class="cc">' . $dataitem['payment_type'] . '</td>' .
                    '</tr>';
            }
            $html .= '<tr><td colspan="2" class="cc"><b>TOTAL</b></td>
                            <td class="cc">' . $data['total_quantity'] . '</td>
                            <td class="cc">' . $data['total_weight'] . ' Kgs.</td>
                            <td class="cc">' . $data['total_gross_weight'] . ' Kgs.</td>
                            <td class="cc"></td>
                            <td class="cc"></td>
                        </tr></table></div><br><br>
                        <span><hr id="e"></hr></span>';

            $html .= '<div class="nn">
                                <table  width="100%">
                                    <tr>
                                        <td>
                                            <h4><b>Receivers Signature</b></h4>
                                            <p>Received the goods mentioned above in good condition.</p>
                                        </td>
                                        <td>
                                        <h4><b>For Eternity Forwarders Pvt. Ltd.</b></h4>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </body>
                    </html>';

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->save(public_path() . '/consignment-pdf/congn-' . $i . '.pdf')->stream('congn-' . $i . '.pdf');
            $pdf_name[] = 'congn-' . $i . '.pdf';
        }
        $pdfMerger = PDFMerger::init();
        foreach ($pdf_name as $pdf) {
            $pdfMerger->addPDF(public_path() . '/consignment-pdf/' . $pdf);
        }
        $pdfMerger->merge();
        $pdfMerger->save("all.pdf", "browser");
        $file = new Filesystem;
        $file->cleanDirectory('pdf');
    }

    public function printSticker(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::first();
        $locations = Location::whereIn('id', $cc)->first();

        $cn_id = $request->id;
        $getdata = ConsignmentNote::where('id', $cn_id)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->first();
        $data = json_decode(json_encode($getdata), true);
        //echo'<pre>'; print_r($data);die;
        $regional = $data['consigner_detail']['regionalclient_id'];

        $getdataregional = RegionalClient::where('id', $regional)->with('BaseClient')->first();
        $sl = json_decode(json_encode($getdataregional), true);
        if (!empty($sl)) {
            $baseclient = $sl['base_client']['client_name'];
        } else {
            $baseclient = '';
        }

        //$logo = url('assets/img/logo_se.jpg');
        $barcode = url('assets/img/barcode.png');

        // if ($authuser->branch_id == 28) {
        //     return view('consignments.consignment-sticker-ldh', ['data' => $data, 'baseclient' => $baseclient]);
        // } else {
            return view('consignments.consignment-sticker', ['data' => $data, 'baseclient' => $baseclient]);
        // }
        //echo $barcode; die;

    }

    public function unverifiedList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $baseclient = explode(',',$authuser->baseclient_id);
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $data = $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as consignee_district', 'zones.primary_zone as zone')
        ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
        ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
        ->leftjoin('zones', 'zones.id', '=', 'consignees.zone_id')
        ->whereIn('consignment_notes.status', ['2', '5'])
        ->where('consignment_notes.booked_drs', '!=', '1');

        if($authuser->role_id ==1){
            $data;
        }
        elseif($authuser->role_id ==4){
            $data = $data->whereIn('consignment_notes.regclient_id', $regclient);
        }
        elseif($authuser->role_id ==6){
            $data = $data->whereIn('base_clients.id', $baseclient);
        }
        elseif($authuser->role_id ==7){
             $data = $data->whereIn('regional_clients.id', $regclient);
        }
        else{
            $data = $data->whereIn('consignment_notes.branch_id', $cc);
        }
        $data = $data->orderBy('id', 'DESC');
        $consignments = $data->get();
        
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        return view('consignments.unverified-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function updateUnverifiedLr(Request $request)
    {

        $consignerId = $request->transaction_id;
        $cc = explode(',', $consignerId);
        $addvechileNo = $request->vehicle_id;
        $adddriverId = $request->driver_id;
        $vehicleType = $request->vehicle_type;
        $transporterName = $request->transporter_name;
        $purchasePrice = $request->purchase_price;

        $consigner = DB::table('consignment_notes')->whereIn('id', $cc)->update(['vehicle_id' => $addvechileNo, 'driver_id' => $adddriverId, 'transporter_name' => $transporterName, 'vehicle_type' => $vehicleType,'purchase_price' => $purchasePrice, 'delivery_status' => 'Started']);

        $consignees = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name', 'consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
            ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
            ->whereIn('consignment_notes.id', $cc)
            ->get(['consignees.city']);

        $simplyfy = json_decode(json_encode($consignees), true);
        foreach ($simplyfy as $value) {
            $consignment_no = $value['consignment_no'];
            $vehicle_no = $value['vehicle_id'];
            $consignee_name = $value['consignee_name'];
            $consignment_date = $value['consignment_date'];
            $city = $value['city'];
            $pincode = $value['pincode'];
            $total_quantity = $value['total_quantity'];
            $total_weight = $value['total_weight'];
            $driverName = $value['driver_id'];
            $driverPhone = $value['driver_phone'];

        }
        $chk_tooken = Driver::where('id', $adddriverId)->select('team_id', 'fleet_id')->get();
        $tooken_details = json_decode(json_encode($chk_tooken), true);
        // Push to tooken if Team Id & Fleet Id Available
        if (!empty($tooken_details[0]['fleet_id'])) {
            $transaction = DB::table('transaction_sheets')->whereIn('consignment_no', $cc)->update(['vehicle_no' => $vehicle_no, 'driver_name' => $driverName, 'driver_no' => $driverPhone, 'delivery_status' => 'Assigned']);
            $createTask = $this->createTookanMultipleTasks($simplyfy);
            $json = json_decode($createTask, true);
            $response = $json['data']['deliveries'];
            foreach ($response as $res) {
                $job_id = $res['job_id'];
                $orderId = $res['order_id'];
                $tracking_link = $res['result_tracking_link'];
                $update = DB::table('consignment_notes')->where('id', $orderId)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
                $updatedrs = DB::table('transaction_sheets')->where('consignment_no', $orderId)->update(['job_id' => $job_id]);
            }
        } else {

            $transaction = DB::table('transaction_sheets')->whereIn('consignment_no', $cc)->where('status', 1)->update(['vehicle_no' => $vehicle_no, 'driver_name' => $driverName, 'driver_no' => $driverPhone, 'delivery_status' => 'Assigned']);
        }

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function transactionSheet(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = TransactionSheet::query();

        $this->prefix = request()->route()->getPrefix();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        
        if ($request->ajax()) {
            if(isset($request->resetfilter)){
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment);
                return response()->json(['success' => true,'redirect_url'=>$url]);
            }

            if (isset($request->updatestatus)) {
                if ($request->drs_status == 'Started') {
                    TransactionSheet::where('drs_no', $request->drs_no)->update(['delivery_status' => $request->drs_status]);
                } elseif ($request->drs_status == 'Successful') {
                    TransactionSheet::where('drs_no', $request->drs_no)->update(['delivery_status' => $request->drs_status]);
                }
                $url = $this->prefix . '/transaction-sheet';
                $response['success'] = true;
                $response['success_message'] = "Dsr cancel status updated successfully";
                $response['error'] = false;
                $response['page'] = 'dsr-cancel-update';
                $response['redirect_url'] = $url;
    
                return response()->json($response);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $baseclient = explode(',',$authuser->baseclient_id);
            $regclient = explode(',',$authuser->regionalclient_id);
            $cc = explode(',',$authuser->branch_id);
            $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

            $query = $query->whereIn('status', ['1', '0', '3'])
                    ->groupBy('drs_no'); 

            if($authuser->role_id ==1){
                $query = $query->with('ConsignmentDetail');
            }
            elseif($authuser->role_id ==4){
                $query = $query
                ->whereHas('ConsignmentDetail', function($query) use($regclient){
                    $query->whereIn('regclient_id', $regclient);
                });
            }
            elseif($authuser->role_id ==6){
                $query = $query
                ->whereHas('ConsignmentDetail', function($query) use($baseclient){
                    $query->whereIn('base_clients.id', $baseclient);
                });
            }
            elseif($authuser->role_id ==7){
                $query = $query
                ->whereHas('ConsignmentDetail.ConsignerDetail.RegClient', function($query) use($baseclient){
                    $query->whereIn('id', $regclient);
                });
            }
            else{
                $query = $query->with('ConsignmentDetail')->whereIn('branch_id', $cc);
            }

            if(!empty($request->search)){
                $search = $request->search;
                $searchT = str_replace("'","",$search);
                $query->where(function ($query)use($search,$searchT) {
                    $query->where('drs_no', 'like', '%' . $search . '%')
                    ->orWhere('vehicle_no', 'like', '%' . $search . '%')
                    ->orWhere('driver_name', 'like', '%' . $search . '%')
                    ->orWhere('driver_no', 'like', '%' . $search . '%');
                });
            }


            if($request->peritem){
                Session::put('peritem',$request->peritem);
            }
      
            $peritem = Session::get('peritem');
            if(!empty($peritem)){
                $peritem = $peritem;
            }else{
                $peritem = Config::get('variable.PER_PAGE');
            }

            $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
            $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
            $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
            $transaction = $query->orderBy('id','DESC')->paginate($peritem);
            $transaction = $transaction->appends($request->query());

            $html =  view('consignments.download-drs-list-ajax',['peritem'=>$peritem, 'prefix' => $this->prefix, 'transaction' => $transaction, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])->render();
            
            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $baseclient = explode(',',$authuser->baseclient_id);
        $regclient = explode(',',$authuser->regionalclient_id);
      
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = $query->with('ConsignmentDetail')
                ->whereIn('status', ['1', '0', '3'])
                ->groupBy('drs_no');

        if($authuser->role_id ==1){
            $query = $query->with('ConsignmentDetail');
        }
        elseif($authuser->role_id ==4){
            $query = $query
            ->whereHas('ConsignmentDetail', function($query) use($regclient){
                $query->whereIn('regclient_id', $regclient);
            });
        }
        elseif($authuser->role_id ==6){
            $query = $query
            ->whereHas('ConsignmentDetail', function($query) use($baseclient){
                $query->whereIn('base_clients.id', $baseclient);
            });
        }
        elseif($authuser->role_id ==7){
            $query = $query->with('ConsignmentDetail')->whereIn('regional_clients.id', $regclient);
        }
        else{
            $query = $query->with('ConsignmentDetail')->whereIn('branch_id', $cc);
        }
        $transaction = $query->orderBy('id','DESC')->paginate($peritem);
        $transaction = $transaction->appends($request->query());
       
        return view('consignments.download-drs-list', ['peritem'=>$peritem, 'prefix' => $this->prefix, 'transaction' => $transaction, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes]);
    }

    public function getTransactionDetails(Request $request)
    {
        $id = $_GET['cat_id'];
        $query = TransactionSheet::query();
        $query = $query->where('drs_no', $id)
            ->with('ConsignmentDetail', function ($query) {
                $query->whereIn('status', [1,5]);
            });
        $query = $query
            ->orderby('order_no', 'asc')
            ->get();

        // $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*', 'consignment_notes.consignment_no as c_no')
        //     ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')->where('drs_no', $id)->where('consignment_notes.status', '1')->orderby('order_no', 'asc')->get();
        $result = json_decode(json_encode($query), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response); 

    }
    public function printTransactionsheetold(Request $request)
    {
        $id = $request->id;
        $transcationview = TransactionSheet::select('*')->with('ConsignmentDetail', 'consigneeDetail')->where('drs_no', $id)->whereIn('status', ['1', '3'])->orderby('order_no', 'asc')->get();

        $simplyfy = json_decode(json_encode($transcationview), true);
        $no_of_deliveries =  count($simplyfy);
        $details = $simplyfy[0]; 
        $pay = public_path('assets/img/LOGO_Frowarders.jpg');

        $drsDate = date('d-m-Y', strtotime($details['created_at']));
        $html = '<html>
        <head>
        <title>Document</title>
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
          <style>
          table,
          th,
          td {
              border: 1px solid black;
              border-collapse: collapse;
              text-align: left;
          }
            @page { margin: 100px 25px; }
            header { position: fixed; top: -60px; left: 0px; right: 0px; height: 200px; }
            footer { position: fixed; bottom: -105px; left: 0px; right: 0px;  height: 100px; }
           /* p { page-break-after: always; }
            p:last-child { page-break-after: never; } */
            * {
                box-sizing: border-box;
              }


              .column {
                float: left;
                width: 14.33%;
                padding: 5px;
                height: auto;
              }


              .row:after {
                content: "";
                display: table;
                clear: both;
              }
              .dd{
                margin-left: 0px;
              }
          </style>
        </head>
        <body style="font-size:13px; font-family:Arial Helvetica,sans-serif;">
                    <header><div class="row" style="display:flex;">
                    <div class="column"  style="width: 493px;">
                        <h1 class="dd">Delivery Run Sheet</h1>
                        <div  class="dd">
                        <table style="width:100%">
                            <tr>
                                <th>DRS No.</th>
                                <th>DRS-' . $details['drs_no'] . '</th>
                                <th>Vehicle No.</th>
                                <th>' . $details['vehicle_no'] . '</th>
                            </tr>
                            <tr>
                                <td>DRS Date</td>
                                <td>' . $drsDate . '</td>
                                <td>Driver Name</td>
                                <td>' . @$details['driver_name'] . '</td>
                            </tr>
                            <tr>
                                <td>No. of Deliveries</td>
                                <td>' . $no_of_deliveries . '</td>
                                <td>Driver No.</td>
                                <td>' . @$details['driver_no'] . '</td>
                            </tr>
                        </table>
                    </div>

                    </div>
                     <div class="column" style="margin-left: 56px;">
                        <img src="' . $pay . '" class="imga" style = "width: 170px; height: 80px; margin-top:30px;">
                    </div>
                </div>
                <br>
                <div id="content"><div class="row" style="border: 1px solid black;">
                <div class="column" style="width:75px;">
                    <h4 style="margin: 0px;">Order Id</h4>
                </div>
                <div class="column" style="width:75px;">
                    <h4 style="margin: 0px;">LR No. & Date</h4>
                </div>
                <div class="column" style="width:140px;">
                    <h4 style="margin: 0px;">Consignee Name & Mobile Number</h4>
                </div>
                <div class="column" style="width:110px;">
                    <h4 style="margin: 0px;">Delivery City & PIN</h4>
                    </div>
                    <div class="column">
                    <h4 style="margin: 0px;">Shipment Details</h4>
                    </div>
                    <div class="column" style="width:170px;">
                    <h4 style="margin: 0px; ">Stamp & Signature of Receiver</h4>
                    </div>
                </div>
                </div>
                </header>
                    <footer><div class="row">
                    <div class="col-sm-12" style="margin-left: 0px;">
                        <p>Head Office:Forwarders private Limited</p>
                        <p style="margin-top:-13px;">Add:Plot No.B-014/03712,prabhat,Zirakpur-140603</p>
                        <p style="margin-top:-13px;">Phone:07126645510 email:contact@eternityforwarders.com</p>
                    </div>
                </div></footer>
                    <main style="margin-top:150px;">';
        $i = 0;
        $total_Boxes = 0;
        $total_weight = 0;

        foreach ($simplyfy as $dataitem) {

            $i++;
            if ($i % 7 == 0) {
                $html .= '<div style="page-break-before: always; margin-top:160px;"></div>';
            }

            $total_Boxes += $dataitem['total_quantity'];
            $total_weight += $dataitem['total_weight'];
            
            $html .= '
                <div class="row" style="border: 1px solid black;">
                    <div class="column" style="width:75px;">
                      <p style="margin-top:0px; overflow-wrap: break-word;">' . $dataitem['consignment_detail']['order_id'] . '</p>
                      <p></p>
                    </div>
                    <div class="column" style="width:75px;">
                        <p style="margin-top:0px;">' . $dataitem['consignment_no'] . '</p>
                        <p style="margin-top:-13px;">' . Helper::ShowDayMonthYear($dataitem['consignment_date']) . '</p>
                    </div>
                    <div class="column" style="width:140px;">
                        <p style="margin-top:0px;">' . $dataitem['consignee_id'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['consignee_detail']['phone'] . '</p>

                    </div>
                    <div class="column" style="width:110px;">
                        <p style="margin-top:0px;">' . $dataitem['city'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['pincode'] . '</p>

                      </div>
                      <div class="column" >
                        <p style="margin-top:0px;">Boxes:' . $dataitem['total_quantity'] . '</p>
                        <p style="margin-top:-13px;">Wt:' . $dataitem['consignment_detail']['total_gross_weight'] . '</p>
                        <p style="margin-top:-13px;">Inv No. ' . $dataitem['consignment_detail']['invoice_no'] . '</p>

                      </div>
                      <div class="column" style="width:170px;">
                        <p></p>
                      </div>
                  </div>

                <br>';
        }

        $html .= '</main>
        </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('print.pdf');

    }

    public function printTransactionsheet(Request $request)
    {
        $id = $request->id;
        $transcationview = TransactionSheet::select('*')
        ->with('ConsignmentDetail.ConsignerDetail.GetRegClient', 'consigneeDetail','ConsignmentItem')
        ->whereHas('ConsignmentDetail', function($q){
            $q->where('status', '!=', 0);
        })
        ->where('drs_no', $id)
        ->whereIn('status', ['1', '3'])
        ->orderby('order_no', 'asc')->get();
        $simplyfy = json_decode(json_encode($transcationview), true);
        
        $no_of_deliveries =  count($simplyfy);
        $details = $simplyfy[0]; 
        $pay = public_path('assets/img/LOGO_Frowarders.jpg');

        $drsDate = date('d-m-Y', strtotime($details['created_at']));
        $html = '<html>
        <head>
        <title>Document</title>
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
          <style>
          table,
          th,
          td {
              border: 0px solid black;
              border-collapse: collapse;
              text-align: left;
          }
          .drs_t,
          .drs_r,
          .drs_d,
          .drs_h {
              border: 1px solid black;
              border-collapse: collapse;
              text-align: left;
          }
            @page { margin: 100px 25px; }
            header { position: fixed; top: -60px; left: 0px; right: 0px; height: 200px; }
            footer { position: fixed; bottom: -105px; left: 0px; right: 0px;  height: 100px; }
           /* p { page-break-after: always; }
            p:last-child { page-break-after: never; } */
            * {
                box-sizing: border-box;
              }


              .column {
                float: left;
                width: 14.33%;
                padding: 5px;
                height: auto;
              }


              .row:after {
                content: "";
                display: table;
                clear: both;
              }
              .dd{
                margin-left: 0px;
              }
            
          </style>
        </head>
        <body style="font-size:13px; font-family:Arial Helvetica,sans-serif;">
                    <header><div class="row" style="display:flex;">
                    <div class="column"  style="width: 493px;">
                        <h1 class="dd">Delivery Run Sheet</h1>
                        <div  class="dd">
                        <table class="drs_t" style="width:100%">
                            <tr class="drs_r">
                                <th class="drs_h">DRS No.</th>
                                <th class="drs_h">DRS-' . $details['drs_no'] . '</th>
                                <th class="drs_h">Vehicle No.</th>
                                <th class="drs_h">' . $details['vehicle_no'] . '</th>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">DRS Date</td>
                                <td class="drs_d">' . $drsDate . '</td>
                                <td class="drs_d">Driver Name</td>
                                <td class="drs_d">' . @$details['driver_name'] . '</td>
                            </tr>
                            <tr class="drs_r">
                                <td class="drs_d">No. of Deliveries</td>
                                <td class="drs_d">' . $no_of_deliveries . '</td>
                                <td class="drs_d">Driver No.</td>
                                <td class="drs_d">' . @$details['driver_no'] . '</td>
                            </tr>
                        </table>
                    </div>

                    </div>
                     <div class="column" style="margin-left: 56px;">
                        <img src="' . $pay . '" class="imga" style = "width: 170px; height: 80px; margin-top:30px;">
                    </div>
                </div>
                <br>
                <div id="content"><div class="row" style="border: 1px solid black;">
                <div class="column" style="width:125px;">
                    <h4 style="margin: 0px;"> Bill to Client</h4>
                    <h4 style="margin: 0px;">LR Details:</h4>
                </div>
                <div class="column" style="width:200px;">
                    <h4 style="margin: 0px;">Consignee Name & Mobile Number</h4>
                </div>
                <div class="column" style="width:125px;">
                    <h4 style="margin: 0px;">Delivery City, </h4>
                    <h4 style="margin: 0px;"> Dstt & PIN</h4>

                    </div>
                    <div class="column">
                    <h4 style="margin: 0px;">Shipment Details</h4>
                    </div>
                    <div class="column" style="width:170px;">
                    <h4 style="margin: 0px; ">Stamp & Signature of Receiver</h4>
                    </div>
                </div>
                </div>
                </header>
                    <footer><div class="row">
                    <div class="col-sm-12" style="margin-left: 0px;">
                        <p>Head Office:Forwarders private Limited</p>
                        <p style="margin-top:-13px;">Add:Plot No.B-014/03712,prabhat,Zirakpur-140603</p>
                        <p style="margin-top:-13px;">Phone:07126645510 email:contact@eternityforwarders.com</p>
                    </div>
                </div></footer>
                    <main style="margin-top:150px;">';
        $i = 0;
        $total_Boxes = 0;
        $total_weight = 0;

        foreach ($simplyfy as $dataitem) {
        //    echo'<pre>'; print_r($dataitem); die;

            $i++;
            if ($i % 5 == 0) {
                $html .= '<div style="page-break-before: always; margin-top:160px;"></div>';
            }

            $total_Boxes += $dataitem['total_quantity'];
            $total_weight += $dataitem['total_weight'];
            //echo'<pre>'; print_r($dataitem['consignment_no']); die;
            $html .= '
                <div class="row" style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; margin-bottom: -10px;">
                   
                    <div class="column" style="width:125px;">
                       <p style="margin-top:0px;">' . $dataitem['consignment_detail']['consigner_detail']['get_reg_client']['name'] . '</p>
                        <p style="margin-top:-8px;">' . $dataitem['consignment_no'] . '</p>
                        <p style="margin-top:-13px;">' . Helper::ShowDayMonthYear($dataitem['consignment_date']) . '</p>
                    </div>
                    <div class="column" style="width:200px;">
                        <p style="margin-top:0px;">' . $dataitem['consignee_id'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['consignee_detail']['phone'] . '</p>

                    </div>
                    <div class="column" style="width:125px;">
                        <p style="margin-top:0px;">' . $dataitem['city'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['consignee_detail']['district'] . '</p>
                        <p style="margin-top:-13px;">' . @$dataitem['pincode'] . '</p>

                      </div>
                      <div class="column" >
                        <p style="margin-top:0px;">Boxes:' . $dataitem['total_quantity'] . '</p>
                        <p style="margin-top:-13px;">Wt:' . $dataitem['consignment_detail']['total_gross_weight'] . '</p>
                        <p style="margin-top:-13px;">EDD:' . Helper::ShowDayMonthYear($dataitem['consignment_detail']['edd']) . '</p>
                      </div>
                      <div class="column" style="width:170px;">
                        <p></p>
                      </div>
                  </div>';
                  $html .='<div class="row" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; margin-top: 0px;">';
                  //echo'<pre>'; print_r($chunk); die;
                  $html .=' <div class="column" style="width:230px; margin-top: -10px;">';
                  $html .='<table class="neworder" style="margin-top: -10px;"><tr style="border:0px;"><td style="width: 190px; padding:6px;"><span style="font-weight: bold;">Order ID</span></td><td style="width: 190px;"><span style="font-weight: bold;">Invoice No</span></td></tr></table>';
                  $itm_no = 0;
                  foreach($dataitem['consignment_item'] as $cc){
                   $itm_no++;
              
                 $html .='  <table style="border:0; margin-top: -7px;"><tr><td style="width: 190px; padding:3px;">'.$itm_no.'.  '.$cc['order_id'].'</td><td style="width: 190px; padding:3px;">'.$itm_no.'.  '.$cc['invoice_no'].'</td></tr></table>';
                 
               }
               $html .= '</div> ';
        
               $html .='</div>

                <br>';
        
        }

        $html .= '</main>
        </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('print.pdf');

    }

    public function updateEDD(Request $request)
    {
        //echo'<pre>'; print_r($_POST); die;
        $edd = $_POST['drs_edd'];
        $consignmentId = $_POST['consignment_id'];

        $consigner = DB::table('consignment_notes')->where('id', $consignmentId)->update(['edd' => $edd]);
        if ($consigner) {
            //echo'ok';
            return response()->json(['success' => 'EDD Updated Successfully']);
        } else {
            return response()->json(['error' => 'Something went wrong']);
        }
    }
    //////////////////////////////////remove lr////////////////////
    public function removeLR(Request $request) 
    {
        //
        $consignmentId = $_GET['consignment_id'];
        $consigner = DB::table('consignment_notes')->where('id', $consignmentId)->update(['status' => '2']);
        $transac = DB::table('transaction_sheets')->where('consignment_no', $consignmentId)->delete();

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }

    public function CreateEdd(Request $request)
    {

        $consignmentId = $_POST['consignmentID'];
        $authuser = Auth::user();
        $cc = $authuser->branch_id;

        /////check order book drs
        $checklrstatus = ConsignmentNote::whereIn('id', $consignmentId)->get();
        foreach ($checklrstatus as $check) {
            if ($check->status == 5) {
                $consigner = DB::table('consignment_notes')->whereIn('id', $consignmentId)->update(['booked_drs' => '1']);
            } else {
                $consigner = DB::table('consignment_notes')->whereIn('id', $consignmentId)->update(['status' => '1']);
            }
        }

        $consignment = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            ->whereIn('consignment_notes.id', $consignmentId)
            ->get(['consignees.city']);

        $simplyfy = json_decode(json_encode($consignment), true);
        //echo'<pre>'; print_r($simplyfy); die;

        $no_of_digit = 5;
        $drs = DB::table('transaction_sheets')->select('drs_no')->latest('drs_no')->first();
        $drs_no = json_decode(json_encode($drs), true);
        if (empty($drs_no) || $drs_no == null) {
            $drs_no['drs_no'] = 0;
        }
        $number = $drs_no['drs_no'] + 1;
        $drs_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);

        $i = 0;
        foreach ($simplyfy as $value) {
            $i++;
            $unique_id = $value['id'];
            $consignment_no = $value['consignment_no'];
            $consignee_id = $value['consignee_id'];
            $consignment_date = $value['consignment_date'];
            $city = $value['city'];
            $pincode = $value['pincode'];
            $total_quantity = $value['total_quantity'];
            $total_weight = $value['total_weight'];
            //echo'<pre>'; print_r($data); die;

            $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $unique_id, 'branch_id' => $cc, 'consignee_id' => $consignee_id, 'consignment_date' => $consignment_date, 'city' => $city, 'pincode' => $pincode, 'total_quantity' => $total_quantity, 'total_weight' => $total_weight, 'order_no' => $i, 'delivery_status' => 'Unassigned', 'status' => '1']);
        }

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function updateSuffle(Request $request)
    {
        $page_id = $request->page_id_array;

        for ($count = 0; $count < count($page_id); $count++) {
            $drs = DB::table('transaction_sheets')->where('id', $page_id[$count])->update(['order_no' => $count + 1]);
        }

        $response['success'] = true;
        $response['success_message'] = " Data suffle updated successfully";
        return response()->json($response);
    }

    public function view_saveDraft(Request $request) 
    {
        //echo'hi';
        $id = $_GET['draft_id'];
         $transcationview = TransactionSheet::select('*')->with('ConsignmentDetail','ConsignmentItem')->where('drs_no', $id)
         ->whereHas('ConsignmentDetail', function ($query){
            $query->whereIn('status', ['1','5']);
        })
         ->orderby('order_no', 'asc')->get();
        // $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*','consignment_items.*','consignment_notes.status as lrstatus', 'consignment_notes.edd as edd')
        //     ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')
        //     ->join('consignment_items','consignment_items.consignment_id', '=', 'transaction_sheets.consignment_no')
        //     ->where('drs_no', $id)->where('consignment_notes.status', '1')->orderby('order_no', 'asc')->get();
        $result = json_decode(json_encode($transcationview), true);
        // echo'<pre>'; print_r($result); die;

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }

    public function updateDelivery(Request $request)
    {
        $id = $_GET['draft_id'];
        $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*', 'consignment_notes.status as lrstatus', 'consignment_notes.edd as edd', 'consignment_notes.delivery_date as dd')
            ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')->where('drs_no', $id)->where('consignment_notes.status', '1')->get();
        $result = json_decode(json_encode($transcationview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }

    public function updateDeliveryStatus(Request $request)
    {
        //echo'<pre>'; print_r($_POST); die;
        $consignmentId = $_POST['consignment_no'];
        $cc = explode(',', $consignmentId);

        $consigner = DB::table('consignment_notes')->whereIn('id', $cc)->update(['delivery_status' => 'Successful']);

        $drs = DB::table('transaction_sheets')->whereIn('consignment_no', $cc)->update(['status' => '3']);

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }

    public function updateDeliveryDateOneBy(Request $request)
    {
        $delivery_date = $_POST['delivery_date'];
        $consignmentId = $_POST['consignment_id'];
        $consigner = DB::table('consignment_notes')->where('id', $consignmentId)->update(['delivery_date' => $delivery_date]);
        if ($consigner) {
            //echo'ok';
            return response()->json(['success' => 'Delivery Date Updated Successfully']);
        } else {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    //+++++++++++++get delevery data model+++++++++++++++++++++++++

    public function getdeliverydatamodel(Request $request)
    {
        $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*', 'consignment_notes.status as lrstatus', 'consignment_notes.edd as edd', 'consignment_notes.delivery_date as dd', 'consignment_notes.signed_drs as signed_drs')
            ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')->where('drs_no', $request->drs_no)->whereIn('transaction_sheets.status', ['1', '0', '3'])->get();
        $result = json_decode(json_encode($transcationview), true);
        //echo'<pre>'; print_r($result); exit;
        $response['fetch'] = $result;

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    //======================== Bulk Print LR ==============================//
    public function BulkLrView(Request $request)
    {

        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $baseclient = explode(',',$authuser->baseclient_id);
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $query = ConsignmentNote::query();

        $query = $query->where('status', '!=', 5)
            ->with('ConsignerDetail','ConsigneeDetail');

        if($authuser->role_id ==1){
            $query;
        }
        elseif($authuser->role_id ==4){
            $query = $query->whereIn('regclient_id', $regclient);
        }
        elseif($authuser->role_id ==7){
            $query = $query->whereIn('regclient_id', $regclient);
        }
        else{
            $query = $query->whereIn('branch_id', $cc);
        }
        $query = $query->orderBy('id', 'DESC');
        $consignments = $query->get();
        
        return view('consignments.bulkLr-view', ['prefix' => $this->prefix, 'consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title]);
    }

    public function DownloadBulkLr(Request $request)
    {
        $lrno = $request->checked_lr;
        $pdftype = $request->type;
    
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::get();
        $locations = Location::whereIn('id', $cc)->first();

        foreach ($lrno as $key => $value) {

            $getdata = ConsignmentNote::where('id', $value)->with('ConsignmentItems', 'ConsignerDetail.GetZone', 'ConsigneeDetail.GetZone', 'ShiptoDetail.GetZone', 'VehicleDetail', 'DriverDetail')->first();
            $data = json_decode(json_encode($getdata), true);
            
    //  ==================================new lr view ==========================  //
            if(empty($data['invoice_no'])){
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
                    $postal_code = $data['consigner_detail']['postal_code'].'<br>';
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
        
                $conr_add =  $legal_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;
        
                if ($data['consignee_detail']['legal_name'] != null) {
                    $nick_name = '<b>' . $data['consignee_detail']['legal_name'] . '</b><br>';
                } else {
                    $nick_name = '';
                }
                if ($data['consignee_detail']['address_line1'] != null) {
                    $address_line1 = '' . $data['consignee_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if ($data['consignee_detail']['address_line2'] != null) {
                    $address_line2 = '' . $data['consignee_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if ($data['consignee_detail']['address_line3'] != null) {
                    $address_line3 = '' . $data['consignee_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if ($data['consignee_detail']['address_line4'] != null) {
                    $address_line4 = '' . $data['consignee_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if ($data['consignee_detail']['city'] != null) {
                    $city = $data['consignee_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if ($data['consignee_detail']['get_zone'] != null) {
                    $district = $data['consignee_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if ($data['consignee_detail']['postal_code'] != null) {
                    $postal_code = $data['consignee_detail']['postal_code'].'<br>';
                } else {
                    $postal_code = '';
                }
        
                if ($data['consignee_detail']['gst_number'] != null) {
                    $gst_number = 'GST No: ' . $data['consignee_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if ($data['consignee_detail']['phone'] != null) {
                    $phone = 'Phone No: ' . $data['consignee_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }
        
                $consnee_add = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;
        
                if ($data['shipto_detail']['legal_name'] != null) {
                    $nick_name = '<b>' . $data['shipto_detail']['legal_name'] . '</b><br>';
                } else {
                    $nick_name = '';
                }
                if ($data['shipto_detail']['address_line1'] != null) {
                    $address_line1 = '' . $data['shipto_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if ($data['shipto_detail']['address_line2'] != null) {
                    $address_line2 = '' . $data['shipto_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if ($data['shipto_detail']['address_line3'] != null) {
                    $address_line3 = '' . $data['shipto_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if ($data['shipto_detail']['address_line4'] != null) {
                    $address_line4 = '' . $data['shipto_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if ($data['shipto_detail']['city'] != null) {
                    $city = $data['shipto_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if ($data['shipto_detail']['get_zone'] != null) {
                    $district = $data['shipto_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if ($data['shipto_detail']['postal_code'] != null) {
                    $postal_code = $data['shipto_detail']['postal_code'].'<br>';
                } else {
                    $postal_code = '';
                }
                if ($data['shipto_detail']['gst_number'] != null) {
                    $gst_number = 'GST No: ' . $data['shipto_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if ($data['shipto_detail']['phone'] != null) {
                    $phone = 'Phone No: ' . $data['shipto_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }
                if($data['is_salereturn']!= 1){
                    $shiptoadd =  $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;
                }else{
                    $shiptoadd ='';
                }
        
                $generate_qrcode = QrCode::size(150)->generate('Eternity Forwarders Pvt. Ltd.');
                $output_file = '/qr-code/img-' . time() . '.svg';
                Storage::disk('public')->put($output_file, $generate_qrcode);
                $fullpath = storage_path('app/public/' . $output_file);
                //echo'<pre>'; print_r($fullpath);
                //  dd($generate_qrcode);
                $no_invoive = count($data['consignment_items']);
                if ($request->typeid == 1) {
                    if($data['is_salereturn']== "1"){
                        $adresses = '<table width="100%">
                            <tr>
                                <td style="width:50%">' . $consnee_add . '</td>
                                <td style="width:50%">' . $conr_add . '</td>
                            </tr>
                        </table>';
                    }else{
                        $adresses = '<table width="100%">
                            <tr>
                                <td style="width:50%">' . $conr_add . '</td>
                                <td style="width:50%">' . $consnee_add . '</td>
                            </tr>
                        </table>';
                    }
                    
                } else if ($request->typeid == 2) {
                    if($data['is_salereturn']== 1){
                        $adresses = '<table width="100%">
                                <tr>
                                    <td style="width:33%">' . $consnee_add . '</td>
                                    <td style="width:33%">' . $conr_add . '</td>
                                    
                                </tr>
                            </table>';
                    }else{
                        $adresses = '<table width="100%">
                                <tr>
                                    <td style="width:33%">' . $conr_add . '</td>
                                    <td style="width:33%">' . $consnee_add . '</td>
                                    <td style="width:33%">' . $shiptoadd . '</td>
                                </tr>
                            </table>';
                    }
                }
                if($locations->id == 2 || $locations->id == 6 || $locations->id == 26){
                    $branch_address = '<span style="font-size: 14px;"><b>'.$branch_add[1]->name.' </b></span><br />
                <b>' . $branch_add[1]->address . ',</b><br />
                <b>	' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . 'b</b><br />
                <b>GST No. : '. $branch_add[1]->gst_number .'</b><br />';
            }else{
                $branch_address = '<span style="font-size: 14px;"><b>'.$branch_add[0]->name.' </b></span><br />
                                    <b>	Plot no: ' . $branch_add[0]->address . ',</b><br />
                                    <b>	' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . 'b</b><br />
                                    <b>GST No. : '. $branch_add[0]->gst_number .'</b><br />';
            }
                $pay = public_path('assets/img/LOGO_Frowarders.jpg');
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
        if(!empty($data['consigner_detail']['get_zone'])){
            $cnr_state = $data['consigner_detail']['get_zone']['state'];
        }
        else{
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
                                    '. $branch_address .'
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
                                                <P>'.$type.'</P>
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
                                                    <th class="mini-th mm"> ' . $data['consigner_detail']['city'] . '</th>
                                                    <th class="mini-th">'.$data['consignee_detail']['city'] . '</th>
                                                    
                                                </tr>
                                            </table>
                                </div>  
                                        </td>
                                    </tr>
                                </table>
                                <div class="loc">
                                    <table>
                                        <tr>
                                            <td class="width_set">
                                                <div style="margin-left: 20px">
                                            <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . $data['consigner_detail']['postal_code'] . ',' . $data['consigner_detail']['city'] . ',' . $cnr_state . '</b></i><div class="vl" ></div>
        
                                                <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>'.$data['consignee_detail']['postal_code'].','.$data['consignee_detail']['city'].','.@$data['consignee_detail']['get_zone']['state'].'</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>
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
                                                <div class="container">
                                                <div>
                                                <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
                                                </div>
                                                <div style="margin-top: -11px;">';
                                            if($data['is_salereturn'] == "1"){
                                                $conr_address = $consnee_add;
                                            }else{
                                                $conr_address = $conr_add;
                                            }
                                                // '.$conr_add.'
                                            $html .=  '<p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">'.$conr_address.'</p>
                                                </div>
                                            </td>
                                            <td width="30%" style="vertical-align:top;>
                                            <div class="container">
                                            <div>
                                            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
                                            </div>
                                                <div style="margin-top: -11px;">';
                                                if($data['is_salereturn'] == "1"){
                                                    $consnee_address = $conr_add;
                                                }else{
                                                    $consnee_address = $consnee_add;
                                                }
                                            $html .=  '<p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                                                '.$consnee_address.'
                                            </p>
                                                </div>
                                            </td>
                                            <td width="30%" style="vertical-align:top;>
                                            <div class="container">
                                            <div>
                                            <h5  style="margin-left:6px; margin-top: 0px">SHIP TO NAME & ADDRESS</h5><br>
                                            </div>
                                                <div style="margin-top: -11px;">
                                                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                                              '.$shiptoadd.'
                                            </p>
                                                </div>
                                            </td>
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
                                                                <th>'.$no_invoive .'</th>
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
                                       
                                 $html .=' <tr>
                                        <td style="width:70px ">' . $dataitem['order_id'] . '</td>
                                        <td style="width: 70px">' . $dataitem['invoice_no'] . '</td>
                                        <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['invoice_date']) . '</td>
                                        <td style="width:70px ">' . $dataitem['invoice_amount'] . '</td>
                                        <td style="width: 70px">' . $dataitem['e_way_bill'] . '</td>
                                        <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['e_way_bill_date']) . '</td>
                                        <td style="width:60px "> ' . $dataitem['quantity'] . '</td>
                                        <td style="width:70px ">' . $dataitem['weight'] . ' Kgs. </td>
                                        <td style="width:70px "> '. $dataitem['gross_weight'] . ' Kgs.</td>
                                        
                                        </tr>';
                                      }
                              $html .='      </table>
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
                    $pdf->save(public_path() . '/bulk/congn-' . $i . '-' . $value . '.pdf')->stream('congn-' . $i . '-' . $value . '.pdf');
                $pdf_name[] = 'congn-' . $i . '-' . $value . '.pdf';
                }

            }else{

            

            if ($data['consigner_detail']['nick_name'] != null) {
                $nick_name = '<p><b>' . $data['consigner_detail']['nick_name'] . '</b></p>';
            } else {
                $nick_name = '';
            }
            if ($data['consigner_detail']['address_line1'] != null) {
                $address_line1 = '<p>' . $data['consigner_detail']['address_line1'] . '</p>';
            } else {
                $address_line1 = '';
            }
            if ($data['consigner_detail']['address_line2'] != null) {
                $address_line2 = '<p>' . $data['consigner_detail']['address_line2'] . '</p>';
            } else {
                $address_line2 = '';
            }
            if ($data['consigner_detail']['address_line3'] != null) {
                $address_line3 = '<p>' . $data['consigner_detail']['address_line3'] . '</p>';
            } else {
                $address_line3 = '';
            }
            if ($data['consigner_detail']['address_line4'] != null) {
                $address_line4 = '<p>' . $data['consigner_detail']['address_line4'] . '</p>';
            } else {
                $address_line4 = '';
            }
            if ($data['consigner_detail']['city'] != null) {
                $city = $data['consigner_detail']['city'] . ',';
            } else {
                $city = '';
            }
            if ($data['consigner_detail']['district'] != null) {
                $district = $data['consigner_detail']['district'] . ',';
            } else {
                $district = '';
            }
            if ($data['consigner_detail']['postal_code'] != null) {
                $postal_code = $data['consigner_detail']['postal_code'];
            } else {
                $postal_code = '';
            }
            if ($data['consigner_detail']['gst_number'] != null) {
                $gst_number = '<p>GST No: ' . $data['consigner_detail']['gst_number'] . '</p>';
            } else {
                $gst_number = '';
            }
            if ($data['consigner_detail']['phone'] != null) {
                $phone = '<p>Phone No: ' . $data['consigner_detail']['phone'] . '</p>';
            } else {
                $phone = '';
            }

            $conr_add = '<p>' . 'CONSIGNOR NAME & ADDRESS' . '</p>
            ' . $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '<p>' . $city . ' ' . $district . ' ' . $postal_code . '</p>' . $gst_number . ' ' . $phone;

            if ($data['consignee_detail']['nick_name'] != null) {
                $nick_name = '<p><b>' . $data['consignee_detail']['nick_name'] . '</b></p>';
            } else {
                $nick_name = '';
            }
            if ($data['consignee_detail']['address_line1'] != null) {
                $address_line1 = '<p>' . $data['consignee_detail']['address_line1'] . '</p>';
            } else {
                $address_line1 = '';
            }
            if ($data['consignee_detail']['address_line2'] != null) {
                $address_line2 = '<p>' . $data['consignee_detail']['address_line2'] . '</p>';
            } else {
                $address_line2 = '';
            }
            if ($data['consignee_detail']['address_line3'] != null) {
                $address_line3 = '<p>' . $data['consignee_detail']['address_line3'] . '</p>';
            } else {
                $address_line3 = '';
            }
            if ($data['consignee_detail']['address_line4'] != null) {
                $address_line4 = '<p>' . $data['consignee_detail']['address_line4'] . '</p>';
            } else {
                $address_line4 = '';
            }
            if ($data['consignee_detail']['city'] != null) {
                $city = $data['consignee_detail']['city'] . ',';
            } else {
                $city = '';
            }
            if ($data['consignee_detail']['district'] != null) {
                $district = $data['consignee_detail']['district'] . ',';
            } else {
                $district = '';
            }
            if ($data['consignee_detail']['postal_code'] != null) {
                $postal_code = $data['consignee_detail']['postal_code'];
            } else {
                $postal_code = '';
            }

            if ($data['consignee_detail']['gst_number'] != null) {
                $gst_number = '<p>GST No: ' . $data['consignee_detail']['gst_number'] . '</p>';
            } else {
                $gst_number = '';
            }
            if ($data['consignee_detail']['phone'] != null) {
                $phone = '<p>Phone No: ' . $data['consignee_detail']['phone'] . '</p>';
            } else {
                $phone = '';
            }

            $consnee_add = '<p>' . 'CONSIGNEE NAME & ADDRESS' . '</p>
        ' . $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '<p>' . $city . ' ' . $district . ' ' . $postal_code . '</p>' . $gst_number . ' ' . $phone;

            if ($data['shipto_detail']['nick_name'] != null) {
                $nick_name = '<p><b>' . $data['shipto_detail']['nick_name'] . '</b></p>';
            } else {
                $nick_name = '';
            }
            if ($data['shipto_detail']['address_line1'] != null) {
                $address_line1 = '<p>' . $data['shipto_detail']['address_line1'] . '</p>';
            } else {
                $address_line1 = '';
            }
            if ($data['shipto_detail']['address_line2'] != null) {
                $address_line2 = '<p>' . $data['shipto_detail']['address_line2'] . '</p>';
            } else {
                $address_line2 = '';
            }
            if ($data['shipto_detail']['address_line3'] != null) {
                $address_line3 = '<p>' . $data['shipto_detail']['address_line3'] . '</p>';
            } else {
                $address_line3 = '';
            }
            if ($data['shipto_detail']['address_line4'] != null) {
                $address_line4 = '<p>' . $data['shipto_detail']['address_line4'] . '</p>';
            } else {
                $address_line4 = '';
            }
            if ($data['shipto_detail']['city'] != null) {
                $city = $data['shipto_detail']['city'] . ',';
            } else {
                $city = '';
            }
            if ($data['shipto_detail']['district'] != null) {
                $district = $data['shipto_detail']['district'] . ',';
            } else {
                $district = '';
            }
            if ($data['shipto_detail']['postal_code'] != null) {
                $postal_code = $data['shipto_detail']['postal_code'];
            } else {
                $postal_code = '';
            }
            if ($data['shipto_detail']['gst_number'] != null) {
                $gst_number = '<p>GST No: ' . $data['shipto_detail']['gst_number'] . '</p>';
            } else {
                $gst_number = '';
            }
            if ($data['shipto_detail']['phone'] != null) {
                $phone = '<p>Phone No: ' . $data['shipto_detail']['phone'] . '</p>';
            } else {
                $phone = '';
            }

            $shiptoadd = '<p>' . 'SHIP TO NAME & ADDRESS' . '</p>
        ' . $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '<p>' . $city . ' ' . $district . ' ' . $postal_code . '</p>' . $gst_number . ' ' . $phone;

            $generate_qrcode = QrCode::size(150)->generate('Eternity Forwarders Pvt. Ltd.');
            $output_file = '/qr-code/img-' . time() . '.svg';
            Storage::disk('public')->put($output_file, $generate_qrcode);
            $fullpath = storage_path('app/public/' . $output_file);
            //echo'<pre>'; print_r($fullpath);
            //  dd($generate_qrcode);
            // if ($request->typeid == 1) {
            //     $adresses = '<table width="100%">
            //             <tr>
            //                 <td style="width:50%">' . $conr_add . '</td>
            //                 <td style="width:50%">' . $consnee_add . '</td>
            //             </tr>
            //         </table>';
            // } else if ($request->typeid == 2) {
            $adresses = '<table width="100%">
                        <tr>
                            <td style="width:33%">' . $conr_add . '</td>
                            <td style="width:33%">' . $consnee_add . '</td>
                            <td style="width:33%">' . $shiptoadd . '</td>
                        </tr>
                    </table>';
            // }

            // $type = count($pdftype);
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

                $html = '<!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <title>PDF</title>
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <style>
                                .aa{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                .bb{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                .cc{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                h2.l {
                                    margin-left: 90px;
                                    margin-top: 132px;
                                    margin-bottom: 2px;
                                }
                                p.l {
                                    margin-left: 90px;
                                }
                                img#set_img {
                                    margin-left: 25px;
                                    margin-bottom: 100px;
                                }

                                p {
                                    margin-top: 2px;
                                    margin-bottom: 2px;
                                }
                                h4 {
                                    margin-top: 2px;
                                    margin-bottom: 2px;
                                }
                                body {
                                    font-family: Arial, Helvetica, sans-serif;
                                    font-size: 14px;
                                }
                            </style>
                        </head>

                        <body>
                        <div class="container">
                            <div class="row">';

                $html .= '<h2>' . $branch_add->name . '</h2>
                                <table width="100%">
                                    <tr>
                                        <td width="50%">
                                            <p>Plot No. ' . $branch_add->address . '</p>
                                            <p>' . $branch_add->district . ' - ' . $branch_add->postal_code . ',' . $branch_add->state . '</p>
                                            <p>GST No. : ' . $branch_add['gst_number'] . '</p>
                                            <p>CIN No. : U63030PB2021PTC053388 </p>
                                            <p>Email : ' . @$locations->email . '</p>
                                            <p>Phone No. : ' . @$locations->phone . '' . '</p>
                                            <br>
                                            <span>
                                                <hr id="s" style="width:100%;">
                                                </hr>
                                            </span>
                                        </td>
                                        <td width="50%">
                                            <h2 class="l">CONSIGNMENT NOTE</h2>

                                            <p class="l">' . $type . '</p>
                                        </td>
                                    </tr>
                                </table></div></div>';
                $html .= '<div class="row"><div class="col-sm-6">
                                <table width="100%">
                                <tr>
                            <td width="30%">
                                <p><b>Consignment No.</b></p>
                                <p><b>Consignment Date</b></p>
                                <p><b>Dispatch From</b></p>
                                <p><b>Order Id</b></p>
                                <p><b>Invoice No.</b></p>
                                <p><b>Invoice Date</b></p>
                                <p><b>Value INR</b></p>
                                <p><b>Vehicle No.</b></p>
                                <p><b>Driver Name</b></p>
                            </td>
                            <td width="30%">';
                if (@$data['consignment_no'] != '') {
                    $html .= '<p>' . $data['id'] . '</p>';
                } else {
                    $html .= '<p>N/A</p>';
                }
                if (@$data['consignment_date'] != '') {
                    $html .= '<p>' . date('d-m-Y', strtotime($data['consignment_date'])) . '</p>';
                } else {
                    $html .= '<p> N/A </p>';
                }
                if (@$data['consigner_detail']['city'] != '') {
                    $html .= '<p> ' . $data['consigner_detail']['city'] . '</p>';
                } else {
                    $html .= '<p> N/A </p>';
                }
                if (@$data['order_id'] != '') {
                    $html .= '<p>' . $data['order_id'] . '</p>';
                } else {
                    $html .= '<p> - </p>';
                }
                if (@$data['invoice_no'] != '') {
                    $html .= '<p>' . $data['invoice_no'] . '</p>';
                } else {
                    $html .= '<p> N/A </p>';
                }
                if (@$data['invoice_date'] != '') {
                    $html .= '<p>' . date('d-m-Y', strtotime($data['invoice_date'])) . '</p>';
                } else {
                    $html .= '<p> N/A </p>';
                }

                if (@$data['invoice_amount'] != '') {
                    $html .= '<p>' . $data['invoice_amount'] . '</p>';
                } else {
                    $html .= '<p> N/A </p>';
                }
                if (@$data['vehicle_detail']['regn_no'] != '') {
                    $html .= '<p>' . $data['vehicle_detail']['regn_no'] . '</p>';
                } else {
                    $html .= '<p> - </p>';
                }
                if (@$data['driver_detail']['name'] != '') {
                    $html .= '<p>' . ucwords($data['driver_detail']['name']) . '</p>';
                } else {
                    $html .= '<p> - </p>';
                }

                $html .= '</td>
                            <td width="50%" colspan="3" style="text-align: center;">
                            <img src= "' . $fullpath . '" alt="barcode">
                            </td>
                        </tr>
                    </table>
                </div>
                <span><hr id="e"></hr></span>
            </div>
            <div class="main">' . @$adresses . '</div>
            <span><hr id="e"></hr></span><br>';
                $html .= '<div class="bb">
                <table class="aa" width="100%">
                    <tr>
                        <th class="cc">Sr.No.</th>
                        <th class="cc">Description</th>
                        <th class="cc">Quantity</th>
                        <th class="cc">Net Weight</th>
                        <th class="cc">Gross Weight</th>
                        <th class="cc">Freight</th>
                        <th class="cc">Payment Terms</th>
                    </tr>';
                ///
                $counter = 0;
                foreach ($data['consignment_items'] as $k => $dataitem) {
                    $counter = $counter + 1;
                    $html .= '<tr>' .
                        '<td class="cc">' . $counter . '</td>' .
                        '<td class="cc">' . $dataitem['description'] . '</td>' .
                        '<td class="cc">' . $dataitem['packing_type'] . ' ' . $dataitem['quantity'] . '</td>' .
                        '<td class="cc">' . $dataitem['weight'] . ' Kgs.</td>' .
                        '<td class="cc">' . $dataitem['gross_weight'] . ' Kgs.</td>' .
                        '<td class="cc">INR ' . $dataitem['freight'] . '</td>' .
                        '<td class="cc">' . $dataitem['payment_type'] . '</td>' .
                        '</tr>';
                }
                $html .= '<tr><td colspan="2" class="cc"><b>TOTAL</b></td>
                            <td class="cc">' . $data['total_quantity'] . '</td>
                            <td class="cc">' . $data['total_weight'] . ' Kgs.</td>
                            <td class="cc">' . $data['total_gross_weight'] . ' Kgs.</td>
                            <td class="cc"></td>
                            <td class="cc"></td>
                        </tr></table></div><br><br>
                        <span><hr id="e"></hr></span>';

                $html .= '<div class="nn">
                                <table  width="100%">
                                    <tr>
                                        <td>
                                            <h4><b>Receivers Signature</b></h4>
                                            <p>Received the goods mentioned above in good condition.</p>
                                        </td>
                                        <td>
                                        <h4><b>For Eternity Forwarders Pvt. Ltd.</b></h4>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </body>
                    </html>';

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                $pdf->save(public_path() . '/bulk/congn-' . $i . '-' . $value . '.pdf')->stream('congn-' . $i . '-' . $value . '.pdf');
                $pdf_name[] = 'congn-' . $i . '-' . $value . '.pdf';
            }
        }
    }
        $pdfMerger = PDFMerger::init();
        foreach ($pdf_name as $pdf) {
            $pdfMerger->addPDF(public_path() . '/bulk/' . $pdf);
        }
        $pdfMerger->merge();
        $pdfMerger->save("all.pdf", "browser");
        $file = new Filesystem;
        $file->cleanDirectory('pdf');

    }

    ////////////////get delevery date LR//////////////////////
    public function getDeleveryDateLr(Request $request)
    {
        $authuser = Auth::user();
        $role = $authuser->role_id;
        $transcationview = DB::table('consignment_notes')->select('consignment_notes.*', 'consignees.nick_name as consignee_nick', 'consignees.city as conee_city','jobs.status as job_status', 'jobs.response_data as trail')
        ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id') 
        ->where('consignment_notes.id', $request->lr_no)
        ->leftjoin('jobs', function($data){
            $data->on('jobs.job_id', '=', 'consignment_notes.job_id')
                ->on('jobs.id', '=', DB::raw("(select max(id) from jobs WHERE jobs.job_id = consignment_notes.job_id)"));
        })->get();
            
        $result = json_decode(json_encode($transcationview), true);
        //  echo '<pre>'; print_r($result); die;

        $response['fetch'] = $result;
        $response['role_id'] = $role;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    public function updateLrStatus(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        if ($request->ajax()) {
            if (isset($request->updatestatus)) {

                if ($request->lr_status == 'Unassigned') {

                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                } elseif ($request->lr_status == 'Assigned') {

                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                } elseif ($request->lr_status == 'Started') {

                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                } elseif ($request->lr_status == 'Successful') {

                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                }

            }

            $url = $this->prefix . '/consignments';
            $response['success'] = true;
            $response['success_message'] = "Dsr cancel status updated successfully";
            $response['error'] = false;
            $response['page'] = 'dsr-cancel-update';
            $response['redirect_url'] = $url;

            return response()->json($response);
        }

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

    // Multiple Deliveries at once

    public function createTookanMultipleTasks($taskDetails)
    {
        //echo "<pre>";print_r($taskDetails);die;
        $deliveries = array();
        foreach ($taskDetails as $task) {

            $deliveries[] = '{
                "address": "' . $task['pincode'] . ',' . $task['city'] . ',India",
                "name": "' . $task['consignee_name'] . '",
                "latitude": " ",
                "longitude": " ",
                "time": "' . $task['edd'] . '",
                "phone": "' . $task['phone'] . '",
                "job_description": "LR-ID:' . $task['id'] . '",
                "template_name": "Template_1",
                "template_data": [
                  {
                    "label": "Invoice Amount",
                    "data":  "' . $task['invoice_amount'] . '"
                  },
                  {
                    "label": "Quantity",
                    "data": "' . $task['total_weight'] . '"
                  }
                ],
                "email": null,
                 "order_id":  "' . $task['id'] . '"
                }';
        }
        $de_json = implode(",", $deliveries);
        //echo "<pre>"; print_r($de_json);die;

        $apidata = '{
                "api_key": "' . $this->apikey . '",
                "fleet_id": "' . $taskDetails[0]['fleet_id'] . '",
                "timezone": -330,
                "has_pickup": 0,
                "has_delivery": 1,
                "layout_type": 0,
                "geofence": 0,
                "team_id": "' . $taskDetails[0]['team_id'] . '",
                "auto_assignment": 1,
                "tags": "",
                "deliveries": [' . $de_json . ']
              }';

        //echo "<pre>";print_r($apidata);echo "</pre>";die;

        //die;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.tookanapp.com/v2/create_multiple_tasks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $apidata,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    //+++++++++++++++++++++++ webhook for status update +++++++++++++++++++++++++//

    public function handle(Request $request)
    {
        header('Content-Type: application/json');
        $request = file_get_contents('php://input');
        $req_dump = print_r($request, true);
        $fp = Storage::disk('local')->put('file.json', $req_dump);
        $data = Storage::disk('local')->get('file.json');
        $json = json_decode($data, true);
        $job_id = $json['job_id'];
        $time = strtotime($json['job_delivery_datetime']);
        $newformat = date('Y-m-d', $time);
        $delivery_status = $json['job_state'];

        //Update LR
        if($delivery_status == 'Successful'){
                $update = \DB::table('consignment_notes')->where('job_id', $job_id)->limit(1)->update(['delivery_status' => $json['job_state'], 'delivery_date' => $newformat]);
                //Update DRS
                $updatedrs = \DB::table('transaction_sheets')->where('job_id', $job_id)->limit(1)->update(['delivery_status' => $json['job_state'], 'delivery_date' => $newformat]);
        }else{
                $update = \DB::table('consignment_notes')->where('job_id', $job_id)->limit(1)->update(['delivery_status' => $json['job_state']]);
                //Update DRS
                $updatedrs = \DB::table('transaction_sheets')->where('job_id', $job_id)->limit(1)->update(['delivery_status' => $json['job_state']]);

        }

        //Save jobs response
        
        $jobData['job_id'] = $json['job_id'];
        $jobData['response_data'] = $data;
        $jobData['status'] = $json['job_state'];
        $jobData['type'] = 1;
        //echo "<pre>"; print_r($jobData);

        $saveJobresponse = Job::create($jobData);

    }

    //Notify on status update

    public function notifications()
    {
        $jobs = Job::select('job_id')->where('type', 1)->get();
        $simplyfy = json_decode(json_encode($jobs), true);
        $count = count($simplyfy);
        $jids = array();
        foreach ($simplyfy as $jid) {
            $lr = ConsignmentNote::select('id')->where('job_id', $jid['job_id'])->first();
            $smf = json_decode(json_encode($lr), true);
            //echo "<pre>"; print_r($smf);die;
            $jids[] = $smf['id'];
        }
        //return $count;
        $j = implode(',', $jids);
        return $j;
    }

    public function update_notifications()
    {

        $update = DB::table('jobs')->where('type', 1)->update(['type' => 0]);
        $response['success'] = true;

        return response()->json($response);

        event(new \App\Events\RealTimeMessage('Status updated as '. $json['job_state']. ' for consignment no -'.$job_id));


    }
// //////////   ACTIVE CANCEL STATUS DRS
    public function drsStatus(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        if ($request->ajax()) {
            if (isset($request->updatestatus)) {

                TransactionSheet::where('drs_no', $request->drs_id)->update(['status' => 0]);

                $drs = DB::table('transaction_sheets')->select('consignment_no')->where('drs_no', $request->drs_id)->get();
                $simplyfydrs = $data = json_decode(json_encode($drs), true);
                foreach ($simplyfydrs as $consgnment) {
                    $lrNo = $consgnment['consignment_no'];

                    ConsignmentNote::where('id', $lrNo)->update(['status' => 2]);
                }

            }

            $url = $this->prefix . '/transaction-sheet';
            $response['success'] = true;
            $response['success_message'] = "Dsr cancel status updated successfully";
            $response['error'] = false;
            $response['page'] = 'dsr-cancel-update';
            $response['redirect_url'] = $url;

            return response()->json($response);
        }

    }
    public function uploadDrsImg(Request $request)
    {
        try {

            $deliverydate = $request->delivery_date;
            $file = $request->file('file');
            if(!empty($file)){
            $filename = $file->getClientOriginalName();
            $file->move(public_path('drs/Image'), $filename);
            }else{
                $filename = NULL;
            }
            if (!empty($deliverydate)) {
                ConsignmentNote::where('id', $request->lr)->update(['signed_drs' => $filename,'delivery_date' => $deliverydate, 'delivery_status' => 'Successful']);
                TransactionSheet::where('consignment_no', $request->lr)->update(['delivery_status' => 'Successful']);

                $response['success'] = true;
                $response['messages'] = 'Image uploaded successfully';
                return Response::json($response);
            } else {
                $response['success'] = false;
                $response['messages'] = 'Img not Found';
                return Response::json($response);

            }

        } catch (\Exception $e) {
            $bug = $e->getMessage();
            $response['success'] = false;
            $response['messages'] = $bug;
            return Response::json($response);
        }

    }

    public function allSaveDRS(Request $request)
    {
          
          if (!empty($request->data)) {
            $get_data = $request->data;
            foreach ($get_data as $key => $save_data) {
                $lrno = $save_data['lrno'];
                $deliverydate = @$save_data['delivery_date'];
                $pic = @$save_data['img'];

                if(!empty($pic)){
                        $filename = $pic->getClientOriginalName();
                        $pic->move(public_path('drs/Image'), $filename);
                }else{
                    $filename = NULL ;
                }
                         
                if(!empty($deliverydate)){
                    $dateTimestamp1 = strtotime($save_data['lr_date']);
                    $dateTimestamp2 = strtotime($deliverydate);
                    // Compare the timestamp date 
                    if ($dateTimestamp1 > $dateTimestamp2){
                            $response['success'] = false;
                            $response['error'] = 'date_less';
                            $response['messages'] = 'Delivery date cannot be less than LR Date';
                            return Response::json($response);
                     } 
                        ConsignmentNote::where('id', $lrno)->update(['signed_drs' => $filename,'delivery_date' => $deliverydate, 'delivery_status' => 'Successful']);
                        TransactionSheet::where('consignment_no', $lrno)->update(['delivery_status' => 'Successful']);
                 
                }else{
                    if(!empty($filename)){
                    ConsignmentNote::where('id', $lrno)->update(['signed_drs' => $filename]);
                    // TransactionSheet::where('consignment_no', $lrno)->update(['delivery_status' => 'Successful']);
                }
            }
            }
            $response['success'] = true;
            $response['messages'] = 'img uploaded successfully';
            return Response::json($response);
        }
    }

    public function addmoreLr(Request $request){

        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $baseclient = explode(',',$authuser->baseclient_id);
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        $user = User::where('branch_id',$authuser->branch_id)->where('role_id',2)->first();

        $data = $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.city as consignee_city', 'consignees.district as consignee_district', 'zones.primary_zone as zone')
        ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
        ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
        ->leftjoin('zones', 'zones.id', '=', 'consignees.zone_id')
        ->where('consignment_notes.status', '=', '2')
        ->where('consignment_notes.status', '!=', 5);

        if($authuser->role_id ==1){
            $data;
        }
        elseif($authuser->role_id ==4){
            $data = $data->whereIn('consignment_notes.regclient_id', $regclient);
        }
        elseif($authuser->role_id ==6){
            $data = $data->whereIn('base_clients.id', $baseclient);
        }
        elseif($authuser->role_id ==7){
             $data = $data->whereIn('regional_clients.id', $regclient);
        }
        else{
            $data = $data->whereIn('consignment_notes.branch_id', $cc);
        }
        $data = $data->orderBy('id', 'DESC');
        $consignments = $data->get();
        
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        $response['lrlist'] = $consignments;
        $response['success'] = true;
        $response['messages'] = 'img uploaded successfully';
        return Response::json($response);
        
        
    }

    public function addunverifiedLr(Request $request)
    {
        $drs_no = $_POST['drs_no'];
        $consignmentId = $_POST['consignmentID'];
        $authuser = Auth::user();
        $cc = $authuser->branch_id;

        $consigner = DB::table('consignment_notes')->whereIn('id', $consignmentId)->update(['status' => '1']);
        $consignment = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            ->whereIn('consignment_notes.id', $consignmentId)
            ->get(['consignees.city']);
        $simplyfy = json_decode(json_encode($consignment), true);

        $drs_order_no = DB::table('transaction_sheets')->select('order_no')->where('drs_no', $drs_no)->latest('order_no')->first();
        $orderno = $drs_order_no->order_no; 


        $i = $orderno;
        foreach ($simplyfy as $value) {
            $i++;
            $unique_id = $value['id'];
            $consignment_no = $value['consignment_no'];
            $consignee_id = $value['consignee_id'];
            $consignment_date = $value['consignment_date'];
            $city = $value['city'];
            $pincode = $value['pincode'];
            $total_quantity = $value['total_quantity'];
            $total_weight = $value['total_weight'];

            $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $unique_id, 'branch_id' => $cc, 'consignee_id' => $consignee_id, 'consignment_date' => $consignment_date, 'city' => $city, 'pincode' => $pincode, 'total_quantity' => $total_quantity, 'total_weight' => $total_weight, 'order_no' => $i, 'delivery_status' => 'Unassigned', 'status' => '1']);
        }

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);
        
    }

    public function viewupdateInvoice(Request $request){
        $consignment = $_GET['consignment_id'];
        $consignmentitm = ConsignmentItem::where('consignment_id', $consignment)->get();

        $response['fetch'] = $consignmentitm;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);


    }
    public function allupdateInvoice(Request $request)
    {
        if (!empty($request->data)) {
            $get_data = $request->data;
            foreach ($get_data as $key => $save_data) {

                $itm_id   =         $save_data['id'];
                $billno   =         @$save_data['e_way_bill'];
                $billdate =         @$save_data['e_way_bill_date'];

                if(!empty($save_data['e_way_bill_date'])){
                    $billdate = $save_data['e_way_bill_date'];
                }else{
                    $billdate = NULL;
                }

                if(!empty($billno)){
                     ConsignmentItem::where('id', $itm_id)->update(['e_way_bill' => $billno]);
                if(!empty($billdate)){
                    ConsignmentItem::where('id', $itm_id)->update(['e_way_bill_date' => $billdate]);
                   }
                }else{
                    if(!empty($billdate)){
                        ConsignmentItem::where('id', $itm_id)->update(['e_way_bill_date' => $billdate]);
                    }
                }
            }
            $consignmentitm = ConsignmentItem::where('consignment_id', $request->cn_no)->get();

            $response['fetch'] = $consignmentitm;
            $response['success'] = true;
            $response['messages'] = 'img uploaded successfully';
            return Response::json($response);
        }
    }

    public function getJob(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $job = DB::table('consignment_notes')->select('consignment_notes.job_id as job_id','consignment_notes.tracking_link as tracking_link','consignment_notes.delivery_status as delivery_status','jobs.status as job_status', 'jobs.response_data as trail','consigners.postal_code as cnr_pincode', 'consignees.postal_code as cne_pincode')
            ->where('consignment_notes.job_id',$request->job_id )
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            ->leftjoin('jobs', function($data){
                $data->on('jobs.job_id', '=', 'consignment_notes.job_id')
                    ->on('jobs.id', '=', DB::raw("(select max(id) from jobs WHERE jobs.job_id = consignment_notes.job_id)"));
            })->first();
            
        if(!empty($job->trail)){
            $job_data= json_decode($job->trail);
            $tracking_history = array_reverse($job_data->task_history);
            // array_push($tracking_history,$job->job_id,$job->delivery_status);
            
            $url    =   URL::to($this->prefix.'/consignments');
            $response['success'] = true;
            $response['success_message'] = "Jobs fetch successfully";
            $response['error'] = false;
            $response['job_data'] = $tracking_history;
            $response['job_id'] = $job->job_id;
            $response['delivery_status'] = $job->delivery_status;
            $response['cnr_pincode'] = $job->cnr_pincode;
            $response['cne_pincode'] = $job->cne_pincode;
            $response['tracking_link'] = $job->tracking_link;
        }else{
            $url    =   URL::to($this->prefix.'/consignments');
            $response['success'] = true;
            $response['success_message'] = "Job data not found";
            $response['error'] = false;
            $response['job_data'] = '';
            $response['job_id'] = '';
            $response['delivery_status'] = $job->delivery_status;
            $response['cnr_pincode'] = $job->cnr_pincode;
            $response['cne_pincode'] = $job->cne_pincode;
            $response['tracking_link'] = $job->tracking_link;
        }
        
        return response()->json($response);
    }

    // public function invoiceCheck(Request $request)
    // {
    //     $invoice_check = Consignmentitem::where('invoice_no', $request->invc_no)->first();
        
    //     if(isset($request->invc_no)){
    //         if (isset($invoice_check)) {
    //             $response['success'] = true;
    //             $response['invc_check'] = true;
    //             $response['errors'] = "Invoice no already exist";
    //             return response()->json($response);
    //         }
    //         $response['success'] = false;
    //         $response['invc_check'] = false;
    //         return response()->json($response);
    //     }
    //     $response['success'] = false;
    //     $response['invc_check'] = false;
    //     return response()->json($response);
    // }

    public function exportDownloadDrs(Request $request)
    {
        return Excel::download(new exportDownloadDrs, 'PaymentReport.xlsx');
    }


}