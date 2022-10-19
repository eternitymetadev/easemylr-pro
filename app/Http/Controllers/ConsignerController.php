<?php

namespace App\Http\Controllers;

use App\Models\Consigner;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Location;
use App\Models\Role;
use App\Models\RegionalClient;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsignerExport;
use DB;
use URL;
use Auth;
use Crypt;
use Helper; 
use Validator;

class ConsignerController extends Controller
{
    public function __construct()
    {
      $this->title =  "Consigners";
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
            $authuser = Auth::user();
            $role_id = Role::where('id','=',$authuser->role_id)->first();
            $regclient = explode(',',$authuser->regionalclient_id); 
            $cc = explode(',',$authuser->branch_id);
            
            $query = Consigner::query();
            $query = $query->with('RegClient','Zone');

            if($authuser->role_id == 1){
                $query = $query;
            } else if($authuser->role_id == 2 || $authuser->role_id == 3){
                // $query = $query->whereIn('branch_id', $cc);
                $query = $query->WhereHas('RegClient', function ($regclientquery) use($cc) {
                    $regclientquery->whereIn('location_id', $cc);
                });
            }
            else{
                $query = $query->whereIn('regionalclient_id', $regclient);
            }

            $consigners = $query->orderby('created_at','DESC')->get();

            return datatables()->of($consigners)
                ->addIndexColumn()
                ->addColumn('regclient', function($row){
                    if(isset($row->RegClient)){
                        $regional = $row->RegClient->name;
                    }else{
                        $regional = '';
                    }
                    return $regional;
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
                    $btn .= '<a class="delete btn btn-sm btn-danger delete_consigner" data-id="'.$row->id.'" data-action="'.URL::to($this->prefix.'/'.$this->segment.'/delete-consigner').'"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action','regclient','district','state'])
                ->make(true);
 
        }
        return view('consigners.consigner-list',['prefix'=>$this->prefix,'segment'=>$this->segment]);
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

        if($authuser->role_id !=1){
            if($authuser->role_id ==2 || $role_id->id ==3){
                $regclients = RegionalClient::whereIn('location_id',$cc)->orderby('name','ASC')->get();
            }else{
                $regclients = RegionalClient::whereIn('id',$regclient)->orderby('name','ASC')->get();
            }
        }else{
            $regclients = RegionalClient::where('status',1)->orderby('name','ASC')->get();
        }
        return view('consigners.create-consigner',['regclients'=>$regclients, 'prefix'=>$this->prefix, 'title'=>$this->title, 'pagetitle'=>'Create']);
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
        $rules = array(
            'nick_name' => 'required|unique:consigners',
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
        $consignersave['nick_name']    = $request->nick_name;
        $consignersave['legal_name']   = $request->legal_name;
        $consignersave['gst_number']   = $request->gst_number;
        $consignersave['contact_name'] = $request->contact_name;
        $consignersave['phone']        = $request->phone;
        $consignersave['regionalclient_id'] = $request->regionalclient_id;
        $consignersave['branch_id']     = $request->branch_id;
        $consignersave['email']         = $request->email;
        $consignersave['address_line1'] = $request->address_line1;
        $consignersave['address_line2'] = $request->address_line2;
        $consignersave['address_line3'] = $request->address_line3;
        $consignersave['address_line4'] = $request->address_line4;
        $consignersave['city']          = $request->city;
        $consignersave['district']      = $request->district;
        $consignersave['postal_code']   = $request->postal_code;
        $consignersave['state_id']      = $request->state_id;
        // $consignersave['status']       = $request->status;

        $saveconsigner = Consigner::create($consignersave);
        if($saveconsigner)
        {
            $response['success'] = true;
            $response['error']   = false;
            $response['page']    = 'consigner-create';
            $response['success_message'] = "Consigner Added successfully";
            $response['redirect_url'] = URL::to($this->prefix.'/consigners');
            // $response['resetform'] = true;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created consigner please try again";
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
    public function show($consigner)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($consigner);
        $getconsigner = Consigner::where('id',$id)->with('GetRegClient','GetZone')->first();
        return view('consigners.view-consigner',['prefix'=>$this->prefix,'title'=>$this->title, 'pagetitle'=>'View Details', 'getconsigner'=>$getconsigner]);
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
        $authuser = Auth::user();

        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id !=1){
            if($authuser->role_id ==2 || $role_id->id ==3){
                $regclients = RegionalClient::whereIn('location_id',$cc)->orderby('name','ASC')->get();
            }else{
                $regclients = RegionalClient::whereIn('id',$regclient)->orderby('name','ASC')->get();
            }
        }else{
            $regclients = RegionalClient::where('status',1)->orderby('name','ASC')->get();
        } 

        $getconsigner = Consigner::with('GetZone')->where('id',$id)->first();
        return view('consigners.update-consigner')->with(['prefix'=>$this->prefix,'getconsigner'=>$getconsigner,'regclients'=>$regclients, 'title'=>$this->title, 'pagetitle'=>'Update']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function updateConsigner(Request $request)
    {
        try { 
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'nick_name' => 'required'
            //   'nick_name' => 'required|unique:consigners,nick_name,'.$request->consigner_id,
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

            $check_nickname_exist = Consigner::where(['nick_name'=>$request['nick_name']])->where('id','!=',$request->consigner_id)->get();

            if(!$check_nickname_exist->isEmpty()){
                $response['success'] = false;
                $response['error_message'] = "Nick name already exists.";
                $response['cnr_nickname_duplicate_error'] = true;
                return response()->json($response);
            }

            $consignersave['nick_name']    = $request->nick_name;
            $consignersave['legal_name']   = $request->legal_name;
            $consignersave['gst_number']   = $request->gst_number;
            $consignersave['contact_name'] = $request->contact_name;
            $consignersave['phone']        = $request->phone;
            $consignersave['regionalclient_id'] = $request->regionalclient_id;
            $consignersave['branch_id']     = $request->branch_id;
            $consignersave['email']         = $request->email;
            $consignersave['address_line1'] = $request->address_line1;
            $consignersave['address_line2'] = $request->address_line2;
            $consignersave['address_line3'] = $request->address_line3;
            $consignersave['address_line4'] = $request->address_line4;
            $consignersave['city']         = $request->city;
            $consignersave['district']     = $request->district;
            $consignersave['postal_code']  = $request->postal_code;
            $consignersave['state_id']     = $request->state_id;
            // $consignersave['status']       = $request->status;
            
            Consigner::where('id',$request->consigner_id)->update($consignersave);
            $url    =   URL::to($this->prefix.'/consigners');

            $response['page'] = 'consigner-update';
            $response['success'] = true;
            $response['success_message'] = "Consigner Updated Successfully";
            $response['error'] = false;
            // $response['html'] = $html;
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
    public function deleteConsigner(Request $request)
    {
        Consigner::where('id',$request->consignerid)->delete();

        $response['success']         = true;
        $response['success_message'] = 'Consigner deleted successfully';
        $response['error']           = false;
        return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new ConsignerExport, 'consigners.csv');
    }

    // get locations on client change in create consigner
    public function regLocations(Request $request)
    {
        $getlocation = Location::select('id', 'name')->where(['id' => $request->location_id, 'status' => '1'])->first();
        if ($getlocation) {
            $response['success'] = true;
            $response['success_message'] = "Location list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getlocation;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch location list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

}
