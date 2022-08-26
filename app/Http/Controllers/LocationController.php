<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use DB;
use URL;
use Helper;
use Validator;
use Image;
use Storage;
use Auth;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->title =  "Locations";
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
        $query = Location::query();
        $authuser = Auth::user();
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id == 2){
            $locations = $query->whereIn('id',$cc)->orderBy('id','DESC')->get();
        }
        else{
            $locations = $query->orderBy('id','DESC')->get();
        }
        return view('locations.location-list',['locations'=>$locations,'prefix'=>$this->prefix,'title'=>$this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name'    => 'required|unique:locations',
            'team_id' => 'required', 
            // 'consignment_no' => 'required|unique:locations',
            'email'  => 'required',
            'phone'  => 'required',
        );
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            // $a['name']  = "This name already exists";
            $errors                 = $validator->errors();
            $response['success']    = false;
            $response['validation'] = false;
            $response['formErrors'] = true;
            $response['errors']     = $errors;
            return response()->json($response);
        }
        if(!empty($request->name)){
            $addlocation['name'] = $request->name;
        }
        if(!empty($request->nick_name)){
            $addlocation['nick_name'] = $request->nick_name;
        }
        if(!empty($request->team_id)){
            $addlocation['team_id'] = $request->team_id;
        }
        // if(!empty($request->consignment_no)){
        //     $addlocation['consignment_no'] = $request->consignment_no;
        // }
        if(!empty($request->email)){
            $addlocation['email'] = $request->email;
        }
        if(!empty($request->phone)){
            $addlocation['phone'] = $request->phone;
        }
        $addlocation['with_vehicle_no'] = $request->with_vehicle_no;
        $addlocation['status'] = 1;

        $savelocation = Location::create($addlocation);
        if($savelocation){
            
            $response['success']    = true;
            $response['page']       = 'save-locations';
            $response['error']      = false;
            $response['success_message'] = "Location created successfully";
            $response['redirect_url'] = URL::to($this->prefix.'/locations');
        }else{
            $response['success']       = false;
            $response['error']         = true;
            $response['error_message'] = "Can not created location please try again";
        }
        return response()->json($response);
    }

    public function updateLocation(Request $request){
        $rules = array(
            'name'      => 'required|unique:locations,name,' . $request->id,
            // 'team_id' => 'unique:locations,team_id,' . $request->id,
            // 'consignment_no' => 'required|unique:locations,consignment_no,' . $request->id,
            'email'  => 'required',
            'phone'  => 'required',
            'team_id' => 'required',
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
        if(!empty($request->name)){
            $locationsave['name']       = $request->name;
        }
        if(!empty($request->nick_name)){
            $locationsave['nick_name']  = $request->nick_name;
        }
        if(!empty($request->team_id)){
            $locationsave['team_id']  = $request->team_id;
        }
        // if(!empty($request->consignment_no)){
        //     $locationsave['consignment_no']  = $request->consignment_no;
        // }
        if(!empty($request->email)){
            $locationsave['email']  = $request->email;
        }
        if(!empty($request->phone)){
            $locationsave['phone']  = $request->phone;
        }
        $locationsave['with_vehicle_no'] = $request->with_vehicle_no;

        $locationsave['status']     = 1;
        $location = Location::where('id',$request->id)->update($locationsave);
        if($location){
            $response['success']    = true;
            $response['page']       = 'update-locations';
            $response['error']      = false; 
            $response['location']   = $location;
            $response['success_message'] = "Location updated successfully";
        }else{
            $response['success']       = false;
            $response['error']         = true;
            $response['error_message'] = "Can not update location please try again";
        }
        return response()->json($response);
    }

    public function getLocation(Request $request)
    {
        $getloc = Location::where('id',$request->locationid)->first();
        $getlocation = json_decode(json_encode($getloc), true);

        return response()->json(['newcata' => $getlocation,'success' => true,'status' =>200]);
    }

    public function deleteLocation(Request $request)
    { 
      $deletelocation = Location::where('id',$request->location_id)->delete();
      $response['success']         = true;
      $response['success_message'] = 'Location deleted successfully';
      $response['error']           = false;

      return response()->json($response);
    }
   
}
