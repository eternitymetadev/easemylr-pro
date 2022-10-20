<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consignee;
use App\Models\Branch;
use App\Models\State;
use App\Models\Consigner;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsigneeExport;
use App\Models\Role;
use App\Models\Zone;
use DB;
use URL;
use Auth;
use Crypt;
use Helper;
use Validator;

class ConsigneeController extends Controller
{
    public function __construct()
    {
      $this->title =  "Consignees";
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
        if ($request->ajax()) {
            $query = Consignee::query();
            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $regclient = explode(',',$authuser->regionalclient_id);
            $cc = explode(',',$authuser->branch_id);
            $query = Consignee::with('Consigner.RegClient','Zone');

            if($authuser->role_id == 2 || $authuser->role_id == 3){
                if($authuser->role_id == $role_id->id){
                    $query = $query->whereHas('Consigner.RegClient', function ($regclientquery) use($cc) {
                    $regclientquery->whereIn('location_id', $cc);
                    });
                }else{
                    $query = $query;
                }
            }else if($authuser->role_id != 2 || $authuser->role_id != 3){
                if($authuser->role_id == $role_id->id){
                    if($authuser->role_id !=1){
                        $query = $query->whereHas('Consigner', function($query) use($regclient){
                            $query->whereIn('regionalclient_id', $regclient);
                        });
                    }else{
                        $query = $query;
                    }
                }else{
                    $query = $query;
                }
            }
            else{
                $query = $query;
            }
            $consignees = $query->get();
            return datatables()->of($consignees)
                ->addIndexColumn()
                ->addColumn('consigner', function($row){
                    if(isset($row->Consigner)){
                        $consigner = $row->Consigner->nick_name;
                    }else{
                        $consigner = '';
                    }
                    return $consigner;
                })
                ->addColumn('district', function($row){
                    if(isset($row->Zone)){
                        $district = $row->Zone->district;
                    }else{
                        $district = '';
                    }
                    return $district;
                })
                ->addColumn('state', function($row){
                    if(isset($row->Zone)){
                        $state = $row->Zone->state;
                    }else{
                        $state = '';
                    }
                    return $state;
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.URL::to($this->prefix.'/'.$this->segment.'/'.Crypt::encrypt($row->id).'/edit').'" class="edit btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>';
                    $btn .= '&nbsp;&nbsp;';
                    $btn .= '<a href="'.URL::to($this->prefix.'/'.$this->segment.'/'.Crypt::encrypt($row->id)).'" class="view btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>';
                    $btn .= '&nbsp;&nbsp;';
                    $btn .= '<a class="delete btn btn-sm btn-danger delete_consignee" data-id="'.$row->id.'" data-action="'.URL::to($this->prefix.'/'.$this->segment.'/delete-consignee').'"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action','consigner','district','state'])
                ->make(true);
        }
        return view('consignees.consignee-list',['prefix'=>$this->prefix,'segment'=>$this->segment]);
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
                $consigners = Consigner::whereIn('branch_id',$cc)->orderby('nick_name','ASC')->pluck('nick_name','id');
            }
        }else if($authuser->role_id != 2 || $authuser->role_id != 3){
            $consigners = Consigner::whereIn('regionalclient_id',$regclient)->orderby('nick_name','ASC')->pluck('nick_name','id');
        }else{
            $consigners = Consigner::where('status',1)->orderby('nick_name','ASC')->pluck('nick_name','id');
        }
        
        return view('consignees.create-consignee',['consigners'=>$consigners, 'prefix'=>$this->prefix, 'title'=>$this->title, 'pagetitle'=>'Create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $rules = array(
            'nick_name' => 'required|unique:consignees',
        );
        $validator = Validator::make($request->all(),$rules);
        
        if($validator->fails())
        {
            $errors                  = $validator->errors();
            $response['success']     = false;
            $response['validation']  = false;
            $response['formErrors']  = true;
            $response['errors']      = $errors;
            return response()->json($response);
        }
        $consigneesave['nick_name']           = $request->nick_name;
        $consigneesave['legal_name']          = $request->legal_name;
        $consigneesave['gst_number']          = $request->gst_number;
        $consigneesave['contact_name']        = $request->contact_name;
        $consigneesave['phone']               = $request->phone;
        $consigneesave['consigner_id']        = $request->consigner_id;
        $consigneesave['zone_id']             = $request->zone_id;
        $consigneesave['branch_id']           = $request->branch_id;
        $consigneesave['dealer_type']         = $request->dealer_type;
        $consigneesave['email']               = $request->email;
        $consigneesave['sales_officer_name']  = $request->sales_officer_name;
        $consigneesave['sales_officer_email'] = $request->sales_officer_email;
        $consigneesave['sales_officer_phone'] = $request->sales_officer_phone;
        $consigneesave['address_line1']       = $request->address_line1;
        $consigneesave['address_line2']       = $request->address_line2;
        $consigneesave['address_line3']       = $request->address_line3;
        $consigneesave['address_line4']       = $request->address_line4;
        $consigneesave['city']                = $request->city;
        $consigneesave['district']            = $request->district;
        $consigneesave['postal_code']         = $request->postal_code;
        $consigneesave['state_id']            = $request->state_id;
        $consigneesave['user_id']             = $authuser->id;

        $saveconsignee = Consignee::create($consigneesave); 
        if($saveconsignee)
        {
            $response['success'] = true;
            $response['success_message'] = "Consignee Added successfully";
            $response['error'] = false;
            $response['page'] = 'consignee-create'; 
            $response['redirect_url'] = URL::to($this->prefix.'/consignees');
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created consignee please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function show($consignee)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($consignee);
        $getconsignee = Consignee::where('id',$id)->with('GetConsigner','GetBranch','GetZone')->first();
        return view('consignees.view-consignee',['prefix'=>$this->prefix,'title'=>$this->title,'getconsignee'=>$getconsignee,'pagetitle'=>'View Details']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $branches = Helper::getLocations();
        $authuser = Auth::user();
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        
        if($authuser->role_id == 2 || $authuser->role_id == 3){
            if($authuser->role_id == $role_id->id){
                $consigners = Consigner::whereIn('branch_id',$cc)->orderby('nick_name','ASC')->pluck('nick_name','id');
            }
        }else if($authuser->role_id != 2 || $authuser->role_id != 3){
            $consigners = Consigner::whereIn('regionalclient_id',$regclient)->orderby('nick_name','ASC')->pluck('nick_name','id');
        }else{
            $consigners = Consigner::where('status',1)->orderby('nick_name','ASC')->pluck('nick_name','id');
        }      
        $getconsignee = Consignee::with('GetZone')->where('id',$id)->first();
        return view('consignees.update-consignee')->with(['prefix'=>$this->prefix, 'getconsignee'=>$getconsignee,'branches'=>$branches,'consigners'=>$consigners,'title'=>$this->title,'pagetitle'=>'Update']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function updateConsignee(Request $request)
    {
        try { 
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'nick_name' => 'required',
                'legal_name' => 'required'
            );

            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
                return response()->json($response);
            }

            $check_nickname_exist = Consignee::where(['nick_name'=>$request['nick_name']])->where('id','!=',$request->consignee_id)->get();

            if(!$check_nickname_exist->isEmpty()){
                $response['success'] = false;
                $response['error_message'] = "Nick name already exists.";
                $response['cnee_nickname_duplicate_error'] = true;
                return response()->json($response);
            }

            $consigneesave['nick_name']           = $request->nick_name;
            $consigneesave['legal_name']          = $request->legal_name;
            $consigneesave['gst_number']          = $request->gst_number;
            $consigneesave['contact_name']        = $request->contact_name;
            $consigneesave['phone']               = $request->phone;
            $consigneesave['consigner_id']        = $request->consigner_id;
            $consigneesave['zone_id']             = $request->zone_id;
            $consigneesave['branch_id']           = $request->branch_id;
            $consigneesave['dealer_type']         = $request->dealer_type;
            $consigneesave['email']               = $request->email;
            $consigneesave['sales_officer_name']  = $request->sales_officer_name;
            $consigneesave['sales_officer_email'] = $request->sales_officer_email;
            $consigneesave['sales_officer_phone'] = $request->sales_officer_phone;
            $consigneesave['address_line1']       = $request->address_line1;
            $consigneesave['address_line2']       = $request->address_line2;
            $consigneesave['address_line3']       = $request->address_line3;
            $consigneesave['address_line4']       = $request->address_line4;
            $consigneesave['city']                = $request->city;
            $consigneesave['district']            = $request->district;
            $consigneesave['postal_code']         = $request->postal_code;
            $consigneesave['state_id']            = $request->state_id;
            // $consigneesave['status']              = $request->status;
            
            Consignee::where('id',$request->consignee_id)->update($consigneesave);
            $url    =   URL::to($this->prefix.'/consignees');

            $response['page'] = 'consignee-update';
            $response['success'] = true;
            $response['success_message'] = "Consignee Updated Successfully";
            $response['error'] = false;
            $response['redirect_url'] = $url;
        }catch(Exception $e) {
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
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function deleteConsignee(Request $request)
    {
        Consignee::where('id',$request->consigneeid)->delete();

        $response['success']         = true;
        $response['success_message'] = 'Consignee deleted successfully';
        $response['error']           = false;
        return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new ConsigneeExport, 'consignees.csv');
    }

    // get address detail from postal code api
    public function getPostalAddress(Request $request){
        $postcode = $request->postcode;
        if(!empty($postcode)){
            $getZone = Zone::where('postal_code',$postcode)->first();
        }else{
            $getZone = '';
        }
            
        $pin = URL::to('get-address-by-postcode');
        $pin = file_get_contents('https://api.postalpincode.in/pincode/'.$postcode);
        $pins = json_decode($pin);
        foreach($pins as $key){
            if($key->PostOffice == null){
                $response['success'] = false;
                $response['error_message'] = "Can not fetch postal address please try again";
                $response['error'] = true;
                
            }else{
                $arr['city'] = $key->PostOffice[0]->Block;
                $arr['district'] = $key->PostOffice[0]->District;
                $arr['state'] = $key->PostOffice[0]->State;

                $response['success'] = true;
                $response['success_message'] = "Postal Address fetch successfully";
                $response['error'] = false;
                $response['data'] = $arr;
                $response['zone'] = $getZone;
            }
        }
        return response()->json($response);
    }

    
}
