<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\RegionalClient;
use App\Models\Driver;
use App\Models\Location;
use App\Models\TransactionSheet;
use App\Models\Vehicle;
use App\Models\Role;
use App\Models\VehicleType;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Auth;
use DB;
use QrCode;
use Storage;
use Validator;
use DataTables;
use Helper;
use Response;

class ConsignmentController extends Controller
{

    public function __construct()
    {
        $this->title = "Consignments";
        $this->segment = \Request::segment(2);
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
        $cc = explode(',', $authuser->branch_id);
        if ($authuser->role_id == 2) {
            $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
             // ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
             // ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                ->whereIn('consignment_notes.branch_id', $cc)
                ->get(['consignees.city']);

            // $consignments = $query->whereIn('branch_id',$cc)->orderby('id','DESC')->get();
        } else {
            $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            // ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
            // ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                ->get(['consignees.city']);

            // $consignments = $query->orderby('id','DESC')->get();
        }
        if ($request->ajax()) {
            if (isset($request->updatestatus)) {
                ConsignmentNote::where('id', $request->id)->update(['status' => $request->status, 'reason_to_cancel' => $request->reason_to_cancel]);
                ConsignmentItem::where('consignment_id', $request->id)->update(['status' => $request->status]);
            }

            $url = $this->prefix . '/consignments';
            $response['success'] = true;
            $response['success_message'] = "Consignment updated successfully";
            $response['error'] = false;
            $response['page'] = 'consignment-updateupdate';
            $response['redirect_url'] = $url;

            return response()->json($response);
        }
        return view('consignments.consignment-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function consignment_list(){
        
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id ==4){
                $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consigners.city as con_city', 'consigners.postal_code as con_pincode', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.address_line1 as con_add1', 'consignees.address_line2 as con_add2', 'consignees.address_line3 as con_add3')
                        ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                        ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                        ->where('consignment_notes.user_id', $authuser->id)
                        ->orderBy('id', 'DESC')
                        ->get(['consignees.city']);
            }else {
                $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consigners.city as con_city', 'consigners.postal_code as con_pincode', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.address_line1 as con_add1', 'consignees.address_line2 as con_add2', 'consignees.address_line3 as con_add3')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->orderBy('id', 'DESC')
                    ->get(['consignees.city']);
            }
        } else {
            $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consigners.city as con_city', 'consigners.postal_code as con_pincode', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode','consignees.address_line1 as con_add1', 'consignees.address_line2 as con_add2', 'consignees.address_line3 as con_add3' )
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                ->orderBy('id', 'DESC')
                ->get(['consignees.city']);
        }

        return Datatables::of($data)
        ->addColumn('lrdetails', function($data){
                     
            $trps = '<div class="">
                     <div class=""><span style="color:#4361ee;">LR No: </span>'.$data->id.'</div>
                     <div class=""><span style="color:#4361ee;">Order No: </span>'.$data->order_id.'</div>
                     <div class=""><span style="color:#4361ee;">Invoice No: </span>'.$data->invoice_no.'</div>
                     </div>'; 

            return $trps;
        })
        ->addColumn('route', function($data){
           // echo "<pre>";print_r($data);die;
            $troute = '<ul class="ant-timeline">
            <li class="ant-timeline-item  css-b03s4t">
                <div class="ant-timeline-item-tail"></div>
                <div class="ant-timeline-item-head ant-timeline-item-head-green"></div>
                <div class="ant-timeline-item-content">
                    <div class="css-16pld72">'.$data->con_pincode.', '.$data->con_city.'</div>
                </div>
            </li>
            <li class="ant-timeline-item ant-timeline-item-last css-phvyqn">
                <div class="ant-timeline-item-tail"></div>
                <div class="ant-timeline-item-head ant-timeline-item-head-red"></div>
                <div class="ant-timeline-item-content">
                <div class="css-16pld72">'.$data->pincode.', '.$data->city.'</div>
                <div class="css-16pld72" style="font-size: 12px; color: rgb(102, 102, 102);">     
                    <span>'.$data->con_add1.',</span>
                </div>
                </div>
            </li>
            </ul>';
                return $troute;
            })
            ->addColumn('impdates', function($data){
                     
                $dates = '<div class="">
                         <div class=""><span style="color:#4361ee;">LR Date: </span>'.$data->consignment_date.'</div>
                         <div class=""><span style="color:#4361ee;">EDD: </span>'.$data->edd.'</div>
                         </div>'; 
    
                return $dates;
            })
            ->addColumn('poptions', function($data){
                $po = '<a href="print-sticker/'.$data->id.'/" target="_blank" class="badge alert bg-info shadow-sm">Print Sticker</a> | <a href="consignments/'.$data->id.'/print-view/2/" target="_blank" class="badge alert bg-info shadow-sm">Print LR</a>';
                return $po;
            }) 
            ->addColumn('status', function($data){
                if($data->status == 0){
                 $st = '<span class="badge alert bg-secondary shadow-sm">Cancel</span>';
                } 
                elseif($data->status == 1){
                    $st = '<a class="activestatus btn btn-success" data-id = "'.$data->id.'" data-text="consignment" data-status = "0" ><span><i class="fa fa-check-circle-o"></i> Active</span';    
                }
                elseif($data->status == 2){
                    $st = '<span class="badge bg-success">Unverified</span>';    
                }
                elseif($data->status == 3){
                    $st = '<span class="badge bg-gradient-bloody text-white shadow-sm ">Unknown</span>';  
                }

                return $st;
            })   
            ->addColumn('delivery_status', function($data){
          
                if($data->delivery_status == "Unassigned"){

                    $dt = '<span class="badge alert bg-primary shadow-sm manual_updateLR" lr-no = "'.$data->id.'">'.$data->delivery_status.'</span>';

                 }
                 elseif($data->delivery_status == "Assigned"){

                    $dt = '<span class="badge alert bg-secondary shadow-sm manual_updateLR" lr-no = "'.$data->id.'">'.$data->delivery_status.'</span>';

                 }
                 elseif($data->delivery_status == "Started"){

                    $dt = '<span class="badge alert bg-warning shadow-sm manual_updateLR" lr-no = "'.$data->id.'">'.$data->delivery_status.'</span>';

                 }
                 elseif($data->delivery_status == "Successful"){

                    $dt = '<span class="badge alert bg-success shadow-sm" lr-no = "'.$data->id.'">'.$data->delivery_status.'</span>';

                 }else{
                     $dt = '<span class="badge alert bg-success shadow-sm" lr-no = "'.$data->id.'">need to update</span>';
                 }
                
                return $dt;
            })                      
        ->rawColumns(['lrdetails','route','impdates','poptions','status', 'delivery_status'])    
        ->make(true);

        
     
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
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);

        if($authuser->role_id == 2 || $authuser->role_id == 3){
            if($authuser->role_id == $role_id->id){
                $consigners = Consigner::select('id', 'nick_name')->whereIn('branch_id', $cc)->get();
            }else{
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        }else if($authuser->role_id != 2 || $authuser->role_id != 3){
            if($authuser->role_id !=1){
                $consigners = Consigner::select('id', 'nick_name')->whereIn('regionalclient_id',$regclient)->get();
            }else{
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        }else{
            $consigners = Consigner::select('id', 'nick_name')->get();
        }
        $consignees = Consignee::select('id', 'nick_name')->where('user_id', $authuser->id)->get();
        if (empty($consignees)) {
            $consignees = Consignee::select('id', 'nick_name')->get();
        }
            // $consignees = Consignee::select('id','nick_name')->whereIn('branch_id',$cc)->get();
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
        // $cc = explode('-',$cn->consignment_no);
        // $getconsignmentno = $cc[1] + 1;
        // $consignmentno = $cc[0].'-'.$getconsignmentno;
        if(empty($consignmentno)) {
            $consignmentno = "";
        }
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();

        return view('consignments.create-consignment', ['prefix' => $this->prefix, 'consigners' => $consigners, 'consignees' => $consignees, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'consignmentno' => $consignmentno, 'drivers' => $drivers]);
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
                'invoice_no' => 'required',
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
            $consignmentsave['consigner_id'] = $request->consigner_id;
            $consignmentsave['consignee_id'] = $request->consignee_id;
            $consignmentsave['ship_to_id'] = $request->ship_to_id;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['consignment_no'] = $consignmentno;
            $consignmentsave['dispatch'] = $request->dispatch;
            $consignmentsave['invoice_no'] = $request->invoice_no;
            $consignmentsave['invoice_date'] = $request->invoice_date;
            $consignmentsave['invoice_amount'] = $request->invoice_amount;
            $consignmentsave['total_quantity'] = $request->total_quantity;
            $consignmentsave['total_weight'] = $request->total_weight;
            $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            $consignmentsave['total_freight'] = $request->total_freight;
            $consignmentsave['transporter_name']  = $request->transporter_name;
            $consignmentsave['vehicle_type']      = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id']        = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;
            $consignmentsave['order_id'] = $request->order_id;
            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['e_way_bill'] = $request->e_way_bill;
            $consignmentsave['e_way_bill_date'] = $request->e_way_bill_date;
            $consignmentsave['status'] = $status;
            if (!empty($request->vehicle_id)) {    
                $consignmentsave['delivery_status'] = "Assigned";
            }else{
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            $saveconsignment = ConsignmentNote::create($consignmentsave);
            $consignment_id = $saveconsignment->id;

           //===================== Create DRS in LR ================================= //
           if(!empty($request->req_vehicle_id || $request->vehicle_id)){

           $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode','vehicles.regn_no as regn_no','drivers.name as driver_name', 'drivers.phone as driver_phone')
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


            $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $simplyfy['id'],'consignee_id' => $simplyfy['consignee_name'], 'consignment_date' => $simplyfy['consignment_date'], 'branch_id' => $authuser->branch_id , 'city' => $simplyfy['city'], 'pincode' => $simplyfy['pincode'], 'total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'],'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'order_no' => '1', 'delivery_status' => 'Assigned','status' => '1']);
           }
           //===========================End drs lr ================================= //
            if ($saveconsignment) {
                   /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
                $vn =  $consignmentsave['vehicle_id'];
                $lid = $saveconsignment->id;
                $lrdata = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name','consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $lid)
                    ->get();
                $simplyfy = json_decode(json_encode($lrdata), true);
                        
                //Send Data to API
                if (!empty($vn) && !empty($simplyfy[0]['team_id']) && !empty($simplyfy[0]['fleet_id'])) {
                    $createTask = $this->createTookanTasks($simplyfy);
                    $json = json_decode($createTask[0], true);
                    $job_id= $json['data']['job_id'];
                    $tracking_link= $json['data']['tracking_link'];
                    $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
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
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
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
        $getconsigners = Consigner::select('address_line1', 'address_line2', 'address_line3', 'address_line4', 'gst_number', 'phone', 'city', 'branch_id')->with('GetBranch')->where(['id' => $request->consigner_id, 'status' => '1'])->first();

        $getConsignees = Consignee::select('id', 'nick_name')->where(['consigner_id' => $request->consigner_id])->get();
        if ($getconsigners) {
            $response['success'] = true;
            $response['success_message'] = "Consigner list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getconsigners;
            $response['consignee'] = $getConsignees;
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

    public function consignPrintview(Request $request)
    {
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::first();
        $locations = Location::whereIn('id', $cc)->first();

        $cn_id = $request->id;
        $getdata = ConsignmentNote::where('id', $cn_id)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->first();
        $data = json_decode(json_encode($getdata), true);

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
        $regional = $data['consigner_detail']['regionalclient_id'];

        $getdataregional = RegionalClient::where('id', $regional)->with('BaseClient')->first();
        $sl = json_decode(json_encode($getdataregional), true);
        $baseclient = $sl['base_client']['client_name'];
        
        //$logo = url('assets/img/logo_se.jpg');
        $barcode = url('assets/img/barcode.png');

        //echo $barcode; die;
        return view('consignments.consignment-sticker', ['data' => $data, 'baseclient' => $baseclient]);

    }

    public function unverifiedList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id == $role_id->id){
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as consignee_district','zones.primary_zone as zone')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                // ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('zones', 'zones.id', '=', 'consignees.zone_id')
                    ->where('consignment_notes.status', '=', '2')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->get(['consignees.city']);
                // echo'<pre>';print_r($consignments);die;
            }
        } else {
            $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as consignee_district','zones.primary_zone as zone')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            // ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
               ->leftjoin('zones', 'zones.id', '=', 'consignees.zone_id')
                ->where('consignment_notes.status', '=', '2')
                ->get(['consignees.city']);
        }
        //echo'<pre>'; print_r($consignments); die;

        // if($authuser->role_id == 2){
        //  $consignments = ConsignmentNote::where('status', '=', '2')->whereIn('branch_id', $cc)->get();
        // }else{
        // $consignments = ConsignmentNote::where('status', '=', '2')->get();
        // }
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        return view('consignments.unverified-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    public function updateUnverifiedLr(Request $request)
    {

        $consignerId = $request->transaction_id;
        //echo'<pre>';print_r($consignerId);die;
        $cc = explode(',', $consignerId);
        $addvechileNo = $request->vehicle_id;
        $adddriverId = $request->driver_id;
        $vehicleType = $request->vehicle_type;
        $transporterName = $request->transporter_name;
        

        $consigner = DB::table('consignment_notes')->whereIn('id', $cc)->update(['vehicle_id' => $addvechileNo, 'driver_id' => $adddriverId, 'transporter_name' => $transporterName, 'vehicle_type' => $vehicleType, 'delivery_status' => 'Assigned']);
        //echo'hii';

        $consignees = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name','consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
            ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
            ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
            ->whereIn('consignment_notes.id', $cc)
            ->get(['consignees.city']);
        //echo'<pre>'; print_r($consignees); die;

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
        if(!empty($tooken_details[0]['fleet_id'])){
            $transaction = DB::table('transaction_sheets')->whereIn('consignment_no', $cc)->update(['vehicle_no' => $vehicle_no, 'driver_name' => $driverName, 'driver_no' => $driverPhone, 'delivery_status' => 'Assigned']);
            $createTask = $this->createTookanTasks($simplyfy);
            $json = json_decode($createTask[0], true);
         //echo "<pre>";print_r($json);die;
           
            $job_id= $json['data']['job_id'];
            $tracking_link= $json['data']['tracking_link'];
            $update = DB::table('consignment_notes')->whereIn('id', $cc)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
            $updatedrs = DB::table('transaction_sheets')->whereIn('consignment_no', $cc)->update(['job_id' => $job_id]);
        }
        else{
            $transaction = DB::table('transaction_sheets')->whereIn('consignment_no', $cc)->update(['vehicle_no' => $vehicle_no, 'driver_name' => $driverName, 'driver_no' => $driverPhone, 'delivery_status' => 'Assigned']);
        }

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        return response()->json($response);

    }    

    public function createTookanTasks($taskDetails) {
       
        foreach ($taskDetails as $task){

            $td = '{
                "api_key": "50666282f31751191c4f723c1410224319e5cdfb2fd5723e5a19",
                "order_id": "'.$task['order_id'].'",
                "job_description": "DRS-'.$task['id'].'",
                "customer_email": "'.$task['email'].'",
                "customer_username": "'.$task['consignee_name'].'",
                "customer_phone": "'.$task['phone'].'",
                "customer_address": "'.$task['pincode'].','.$task['city'].',India",
                "latitude": "",
                "longitude": "",
                "job_delivery_datetime": "'.$task['edd'].' 21:00:00",
                "custom_field_template": "Template_1",
                "meta_data": [
                    {
                        "label": "Invoice Amount",
                        "data": "'.$task['invoice_amount'].'"
                    },
                    {
                        "label": "Quantity",
                        "data": "'.$task['total_weight'].'"
                    }
                ],
                "team_id": "'.$task['team_id'].'",
                "auto_assignment": "1",
                "has_pickup": "0",
                "has_delivery": "1",
                "layout_type": "0",
                "tracking_link": 1,
                "timezone": "-330",
                "fleet_id": "'.$task['fleet_id'].'",
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
              CURLOPT_POSTFIELDS =>$td,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response[] = curl_exec($curl);
            
            curl_close($curl);

        }
        //echo "<pre>";print_r($response);echo "</pre>";die;
        return $response;
        
    }

     // Multiple Deliveries at once

     public function createTookanMultipleTasks($taskDetails) {

        $deliveries = array();
        foreach ($taskDetails as $task){

        $deliveries[] = '{
                "address": "'.$task['pincode'].','.$task['city'].',India",
                "name": "'.$task['consignee_name'].'",
                "latitude": " ",
                "longitude": " ",
                "time": "'.$task['edd'].' 21:00:00",
                "phone": "'.$task['phone'].'",
                "job_description": "DRS-'.$task['id'].'",
                "template_name": "Template_1",
                "template_data": [
                  {
                    "label": "Invoice Amount",
                    "data":  "'.$task['invoice_amount'].'"
                  },
                  {
                    "label": "Quantity",
                    "data": "'.$task['total_weight'].'"
                  }
                ],
                "email": null,
                 "order_id":  "'.$task['order_id'].'"
                }';  
            }
            $de_json = implode(",", $deliveries);
            //echo "<pre>"; print_r($de_json);die;

         $apidata = '{
                "api_key": "50666282f31751191c4f723c1410224319e5cdfb2fd5723e5a19",
                "fleet_id": "'.$taskDetails[0]['fleet_id'].'",
                "timezone": -330,
                "has_pickup": 0,
                "has_delivery": 1,
                "layout_type": 0,
                "geofence": 0,
                "team_id": "'.$taskDetails[0]['team_id'].'",
                "auto_assignment": 0,
                "tags": "",
                "deliveries": ['.$de_json.']
              }';

            echo "<pre>";print_r($apidata);echo "</pre>";die;

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
              CURLOPT_POSTFIELDS =>$apidata,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);

            echo "<pre>";print_r($response);echo "</pre>";die;

            return $response;
        
    }
    public function transactionSheet(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id == 4){ 
                $transaction = DB::table('transaction_sheets')->select('transaction_sheets.drs_no','transaction_sheets.driver_name','transaction_sheets.vehicle_no', 'transaction_sheets.status','transaction_sheets.delivery_status','transaction_sheets.created_at','transaction_sheets.driver_no','consignment_notes.user_id','consignment_notes.user_id')
                ->leftJoin('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')
                ->whereIn('transaction_sheets.status', ['1','0','3'])
                ->where('consignment_notes.user_id', $authuser->id)
                ->groupBy('transaction_sheets.drs_no')
                ->get();
            }else{
                $transaction = DB::table('transaction_sheets')->select('transaction_sheets.drs_no','transaction_sheets.driver_name','transaction_sheets.vehicle_no', 'transaction_sheets.status','transaction_sheets.delivery_status','transaction_sheets.created_at','transaction_sheets.driver_no','consignment_notes.user_id','consignment_notes.user_id')
                ->leftJoin('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')
                ->whereIn('transaction_sheets.status', ['1','0','3'])
                ->whereIn('transaction_sheets.branch_id', [$cc])
                ->groupBy('transaction_sheets.drs_no')
                ->get();
            }
        } else {
            $transaction = DB::table('transaction_sheets')->select('transaction_sheets.drs_no','transaction_sheets.driver_name','transaction_sheets.vehicle_no', 'transaction_sheets.status','transaction_sheets.delivery_status','transaction_sheets.created_at','transaction_sheets.driver_no','consignment_notes.user_id','consignment_notes.user_id')
                ->leftJoin('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')
                ->whereIn('transaction_sheets.status', ['1','0','3'])
                ->groupBy('transaction_sheets.drs_no')
                ->get();
            // $transaction = DB::table('transaction_sheets')->select('drs_no','driver_name','vehicle_no', 'status','delivery_status','created_at','driver_no', DB::raw('count("drs_no") as total'))
            // ->groupBy('drs_no','driver_name','vehicle_no', 'status','delivery_status','created_at','driver_no')->whereIn('status', ['1','0','3'])->get();  
        }

        if ($request->ajax()) {
            if (isset($request->updatestatus)) {
                if($request->drs_status == 'Started'){
                    TransactionSheet::where('drs_no', $request->drs_no)->update(['delivery_status' => $request->drs_status]);
                }elseif($request->drs_status == 'Successful'){
                    TransactionSheet::where('drs_no', $request->drs_no)->update(['delivery_status' => $request->drs_status]);
                }elseif($request->drs_status == 0){
                    TransactionSheet::where('drs_no', $request->drs_no)->update(['status' => $request->drs_status]);
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

        return view('consignments.transaction-sheet', ['prefix' => $this->prefix, 'title' => $this->title, 'transaction' => $transaction, 'vehicles' => $vehicles, 'drivers' => $drivers, 'vehicletypes' => $vehicletypes]);
    }

    public function getTransactionDetails(Request $request)
    {
        $id = $_GET['cat_id'];

       $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*', 'consignment_notes.consignment_no as c_no')
       ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')->where('drs_no', $id)->where('consignment_notes.status', '1')->orderby('order_no', 'asc')->get();
       $result = json_decode(json_encode($transcationview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }
    public function printTransactionsheet(Request $request)
    {
        //echo'<pre>'; print_r(); die;
        $id = $request->id;
        //echo'<pre>'; print_r($id); die;
        $transcationview = TransactionSheet::select('*')->with('ConsignmentDetail','consigneeDetail')->where('drs_no', $id)->whereIn('status', ['1','3'])->orderby('order_no', 'asc')->get();
        //dd($transcationview);
        $simplyfy = json_decode(json_encode($transcationview), true);
        $no_of_deliveries =  count($simplyfy);
        $details = $simplyfy[0];

        $pay = public_path('assets/img/LOGO_Frowarders.jpg');

        //<img src="" alt="logo" alt="" width="80" height="70">
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
                                <td>'.$no_of_deliveries.'</td>
                                <td>Driver No.</td>
                                <td>' . @$details['driver_no'] . '</td>
                            </tr>
                        </table>
                    </div>

                    </div>
                     <div class="column" style="margin-left: 56px;">
                        <img src="'.$pay.'" class="imga" style = "width: 170px; height: 80px; margin-top:30px;">
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
                    //echo'<pre>'; print_r($dataitem); die;


                    $i++;
            if ($i % 7 == 0) {
                $html .= '<div style="page-break-before: always; margin-top:160px;"></div>';
            }
           
            $total_Boxes += $dataitem['total_quantity'];
            $total_weight += $dataitem['total_weight'];
            //echo'<pre>'; print_r($dataitem['consignment_no']); die;
            $html .= '  
                <div class="row" style="border: 1px solid black;">
                    <div class="column" style="width:75px;">
                      <p style="margin-top:0px; overflow-wrap: break-word;">' . $dataitem['consignment_detail']['order_id'] . '</p>
                      <p></p>
                    </div>
                    <div class="column" style="width:75px;">
                        <p style="margin-top:0px;">' . $dataitem['consignment_no'] . '</p>
                        <p style="margin-top:-13px;">' . Helper::ShowDayMonthYear($dataitem['consignment_date']). '</p>
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
        //echo'<pre>'; print_r($consignmentId); die;
        $consigner = DB::table('consignment_notes')->where('id', $consignmentId)->update(['status' => '2']);
        $transac = DB::table('transaction_sheets')->where('consignment_no', $consignmentId)->update(['status' => '2']);

       
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);
    }

    public function CreateEdd(Request $request)
    {

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

            $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $unique_id, 'branch_id' => $cc, 'consignee_id' => $consignee_id, 'consignment_date' => $consignment_date, 'city' => $city, 'pincode' => $pincode, 'total_quantity' => $total_quantity, 'total_weight' => $total_weight, 'order_no' => $i, 'delivery_status' => 'Unassigned','status' => '1']);
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

    }
    public function view_saveDraft(Request $request)
    {
        //echo'hi';
        $id = $_GET['draft_id'];
        // $transcationview = TransactionSheet::select('*')->with('ConsignmentDetail')->where('drs_no', $id)->orderby('order_no', 'asc')->get();
        $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*','consignment_notes.status as lrstatus','consignment_notes.edd as edd' )
       ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')->where('drs_no', $id)->where('consignment_notes.status', '1')->orderby('order_no', 'asc')->get();
        $result = json_decode(json_encode($transcationview), true);

        $response['fetch'] = $result;
        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    public function updateDelivery(Request $request)
    {
        $id = $_GET['draft_id'];
        // $transcationview = TransactionSheet::select('*')->with('ConsignmentDetail')->where('drs_no', $id)->get();

       $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*','consignment_notes.status as lrstatus','consignment_notes.edd as edd','consignment_notes.delivery_date as dd'  )
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

    ///////////////////////////Reports/////////////////////////
    public function consignmentReports()
    {
        $this->prefix = request()->route()->getPrefix();

        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id == $role_id->id){
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->get(['consignees.city']);
                    // echo'<pre>'; print_r($consignments); die;
            }} else {
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->get(['consignees.city']);

            }
        //echo'<pre>'; print_r($consignments); die;

        return view('consignments.consignment-report', ['consignments' => $consignments, 'prefix' => $this->prefix]);

    }
      public function getFilterReport(Request $request)
      {
       // echo'<pre>'; print_r($_POST); die;
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id == $role_id->id){
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                    ->get(['consignees.city']);
            }} else {
                $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'consignees.nick_name as consignee_nickname', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.district as district', 'states.name as state', 'vehicles.regn_no as vechile_number', 'consigners.city as consigners_city')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('states', 'states.id', '=', 'consignees.state_id')
                    ->whereBetween('consignment_notes.consignment_date', [$_POST['first_date'], $_POST['last_date']])
                    ->get(['consignees.city']);

            }
            $response['fetch'] = $consignments;
            $response['success'] = true;
            $response['messages'] = 'Succesfully loaded';
            return Response::json($response);
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

/////////////////Web Hooks/////////////////////////////
    public function handle(Request $request)
    {
        header('Content-Type: application/json');
         $request = file_get_contents('php://input');
         $req_dump = print_r( $request, true );
         $fp = Storage::disk('local')->put('file.json', $req_dump);
         $data = Storage::disk('local')->get('file.json');
         //$update = DB::table('consignment_notes')->whereIn('id', $cc)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link]);
         //$json = json_decode($data, true);
         $json = json_decode($data, true);
         $job_id= $json['job_id'];
         //echo "<pre>";print_r($job_id);die;
         $update = DB::table('consignment_notes')->where('job_id', $job_id)->update(['delivery_status' => $json['job_state'], 'delivery_date' => $json['job_delivery_datetime_formatted']]);
         $updatedrs = DB::table('transaction_sheets')->where('job_id', $job_id)->update(['delivery_status' => $json['job_state'], 'delivery_date' => $json['job_delivery_datetime_formatted']]);
         //Do something with the event
         //logger($data);
    }

    ////////////////////////////////// Test Function /////////////////////////////////    


    public function testview(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        return view('consignments.test-list',['prefix' => $this->prefix]);
    }   

    public function test(){
        
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if ($authuser->role_id == $role_id->id) {
                $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consigners.city as con_city', 'consigners.postal_code as con_pincode', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'consignees.address_line1 as con_add1', 'consignees.address_line2 as con_add2', 'consignees.address_line3 as con_add3')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->whereIn('consignment_notes.branch_id', $cc)
                    ->orderBy('id', 'DESC')
                    ->get(['consignees.city']);
            }
        } else {
            $data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consigners.city as con_city', 'consigners.postal_code as con_pincode', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode','consignees.address_line1 as con_add1', 'consignees.address_line2 as con_add2', 'consignees.address_line3 as con_add3' )
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                ->orderBy('id', 'DESC')
                ->get(['consignees.city']);
        }

        return Datatables::of($data)
        ->addColumn('lrdetails', function($data){
                     
            $trps = '<ul class="ant-timeline">
                       <li class="ant-timeline-item"><span style="color:#4361ee;">LR No: </span>'.$data->id.'<li>
                       <li class="ant-timeline-item"><span style="color:#4361ee;">LR Date: </span>'.$data->consignment_date.'<li>
                     </ul>'; 

            return $trps;
        })
        ->addColumn('route', function($data){
           // echo "<pre>";print_r($data);die;
            $troute = '<ul class="ant-timeline">
            <li class="ant-timeline-item  css-b03s4t">
                <div class="ant-timeline-item-tail"></div>
                <div class="ant-timeline-item-head ant-timeline-item-head-green"></div>
                <div class="ant-timeline-item-content">
                    <div class="css-16pld72">'.$data->con_pincode.', '.$data->con_city.'</div>
                </div>
            </li>
            <li class="ant-timeline-item ant-timeline-item-last css-phvyqn">
                <div class="ant-timeline-item-tail"></div>
                <div class="ant-timeline-item-head ant-timeline-item-head-red"></div>
                <div class="ant-timeline-item-content">
                <div class="css-16pld72">'.$data->pincode.', '.$data->city.'</div>
                <div class="css-16pld72" style="font-size: 12px; color: rgb(102, 102, 102);">     
                    <span>'.$data->con_add1.',</span>
                    <span>'.$data->con_add2.', <span>'.$data->con_add3.'</span></span>
                </div>
                </div>
            </li>
            </ul>';
                return $troute;
            })
            ->addColumn('poptions', function($data){
                $po = '<a href="print-sticker/'.$data->id.'/" target="_blank" class="badge alert bg-warning shadow-sm">Print Sticker</a> || <a href="consignments/'.$data->id.'/print-view/1/" target="_blank" class="badge alert bg-warning shadow-sm">Print LR</a> || <a href="consignments/'.$data->id.'/print-view/2/" target="_blank" class="badge alert bg-warning shadow-sm">Print with Ship to</a>';

                return $po;
            }) 
            ->addColumn('status', function($data){
                if($data->status == 0){
                 $st = '<span class="badge alert bg-secondary shadow-sm">Cancel</span>';
                } 
                elseif($data->status == 1){
                    $st = '<span class="badge bg-info shadow-sm">Active</span>';    
                }
                elseif($data->status == 2){
                    $st = '<span class="badge bg-success">Unverified</span>';    
                }
                elseif($data->status == 3){
                    $st = '<span class="badge bg-gradient-bloody text-white shadow-sm ">Unknown</span>';  
                }

                return $st;
            })   
            ->addColumn('delivery_status', function($data){
          
                if($data->delivery_status == "Unassigned"){

                    $dt = '<span class="badge alert bg-primary shadow-sm">'.$data->delivery_status.'</span>';

                 }
                 elseif($data->delivery_status == "Assigned"){

                    $dt = '<span class="badge alert bg-secondary shadow-sm">'.$data->delivery_status.'</span>';

                 }
                 elseif($data->delivery_status == "Started"){

                    $dt = '<span class="badge alert bg-warning shadow-sm">'.$data->delivery_status.'</span>';

                 }
                 elseif($data->delivery_status == "Successful"){

                    $dt = '<span class="badge alert bg-success shadow-sm">'.$data->delivery_status.'</span>';

                 }else{
                     $dt = '<span class="badge alert bg-success shadow-sm">need to update</span>';
                 }
                

                return $dt;
                

            })                      
        ->rawColumns(['lrdetails','route','poptions','status', 'delivery_status'])    
        ->make(true);
     
    }
    //+++++++++++++get delevery data model+++++++++++++++++++++++++

    public function getdeliverydatamodel(Request $request)
    {
        $transcationview = DB::table('transaction_sheets')->select('transaction_sheets.*','consignment_notes.status as lrstatus','consignment_notes.edd as edd','consignment_notes.delivery_date as dd'  )
         ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')->where('drs_no', $request->drs_no)->where('consignment_notes.status', '1')->get();
        $result = json_decode(json_encode($transcationview), true);
        //echo'<pre>'; print_r($result); exit;
        $response['fetch'] = $result;

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    //========================Bulk Print LR ==============================//
    public function BulkLrView(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        // $peritem = 20;
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        if ($authuser->role_id == 2) {
            $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                ->whereIn('consignment_notes.branch_id', $cc)
                ->get(['consignees.city']);
                

            // $consignments = $query->whereIn('branch_id',$cc)->orderby('id','DESC')->get();
        } else {
            $consignments = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_id', 'consignees.city as city', 'consignees.postal_code as pincode')
                ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                ->get(['consignees.city']);

            // $consignments = $query->orderby('id','DESC')->get();
        }
      
        return view('consignments.bulkLr-view', ['prefix' => $this->prefix,'consignments' => $consignments, 'prefix' => $this->prefix, 'title' => $this->title]);
    }

    public function DownloadBulkLr(Request $request)
    {
        $lrno = $request->checked_lr;
        $pdftype = $request->type;
       //echo'<pre>'; print_r($pdftype); die;
        
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        $branch_add = BranchAddress::first();
        $locations = Location::whereIn('id', $cc)->first();
        foreach($lrno as $key => $value){
    
        $getdata = ConsignmentNote::where('id', $value)->with('ConsignmentItems', 'ConsignerDetail', 'ConsigneeDetail', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail')->first();
        $data = json_decode(json_encode($getdata), true);
        // echo'<pre>'; print_r($data); print_r('</pre>');
       
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
       foreach($pdftype as $i => $pdf){
             
            if($pdf == 1){
                $type = 'ORIGINAL';
            }elseif($pdf == 2){
                $type = 'DUPLICATE';
            }elseif ($pdf == 3){
               $type = 'TRIPLICATE';
            }elseif ($pdf == 4){
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
            $pdf->save(public_path() . '/bulk/congn-' . $i . '-'.$value.'.pdf')->stream('congn-' . $i . '-'.$value.'.pdf');
            $pdf_name[] = 'congn-' . $i . '-'.$value.'.pdf';
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
        $transcationview = DB::table('consignment_notes')->select('*')
        ->where('id', $request->lr_no)->get();
        $result = json_decode(json_encode($transcationview), true);
        $response['fetch'] = $result;

        $response['success'] = true;
        $response['success_message'] = "Data Imported successfully";
        echo json_encode($response);

    }

    public function updateLrStatus(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        if ($request->ajax()) {
            if (isset($request->updatestatus)) {

                if($request->lr_status == 'Unassigned'){
                   
                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                }elseif($request->lr_status == 'Assigned'){
                    
                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                }elseif($request->lr_status == 'Started'){
                   
                    ConsignmentNote::where('id', $request->lr_no)->update(['delivery_status' => $request->lr_status]);
                }elseif($request->lr_status == 'Successful'){
                   
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

}
