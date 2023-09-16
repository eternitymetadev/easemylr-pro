<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Driver;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleExport;
use DB;
use URL;
use Helper;
use Hash;
use Crypt;
use Config;
use Validator;
use DataTables;
use Session;
use Storage;
use Auth;

class VehicleController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
      $this->title =  "Vehicles";
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
        $peritem = Config::get('variable.PER_PAGE');
        $query = Vehicle::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }
            $query = $query->with('GetState');

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('regn_no', 'like', '%' . $search . '%')
                    ->orWhere('body_type', 'like', '%' . $search . '%')
                    ->orWhere('make', 'like', '%' . $search . '%')
                    ->orWhere('tonnage_capacity', 'like', '%' . $search . '%')
                    ->orWhere('mfg', 'like', '%' . $search . '%');
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

            $vehicles = $query->orderBy('id', 'DESC')->paginate($peritem);
            $vehicles = $vehicles->appends($request->query());
            // echo'<pre'; print_r($vehicles); die;
            $html = view('vehicles.vehicle-list-ajax', ['prefix' => $this->prefix, 'vehicles' => $vehicles, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }
        $vehicles = $query->with('GetState')->orderBy('id', 'DESC')->paginate($peritem);
        $vehicles = $vehicles->appends($request->query());
  
        return view('vehicles.vehicle-list',['prefix'=>$this->prefix,'title'=>$this->title,'segment'=>$this->segment,'vehicles'=>$vehicles, 'peritem' => $peritem]);
    }

    public function getData(Request $request) {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        if($authuser->role_id == 1 ){
            $this->prefix = 'admin';
        }
        elseif($authuser->role_id == 2 ){
            $this->prefix = 'branch-manager';
        }elseif($authuser->role_id == 3 ){
            $this->prefix = 'regional-manager';
        }elseif($authuser->role_id == 4 ){
            $this->prefix = 'branch-user';
        }elseif($authuser->role_id == 5 ){
            $this->prefix = 'account-manager';
        }else{
            $this->prefix ='';
        }

        $arrData = \DB::table('vehicles');
        $arrDatas = $arrData->get();

        return Datatables::of($arrData)->addIndexColumn()
            // ->addColumn('rc_image', function ($arrData) {
            //     if($arrData->rc_image == null){
            //         $rc_image = '-';
            //     }else{
            //         $rc_image = '<a href="'.URL::to('/storage/images/vehicle_rc_images/'.$arrData->rc_image).' " target="_blank">view</a>';
            //     }
            //     return $rc_image;
            // })
            ->addColumn('rc_image', function ($data) {
                if($data->rc_image == null){
                    $rc_image = '-';
                }else{
                    $chk_url = "https://easemylr.s3.us-east-2.amazonaws.com/vehicle_rc_images";
                    $img_url = $data->rc_image;
                    if($img_url != '' || $img_url != null){
                        $explode_url = explode("/",$img_url);
                        if(isset($explode_url[0]) && isset($explode_url[1]) && isset($explode_url[2]) && isset($explode_url[3])){
                            $img_url = $explode_url[0].'/'.$explode_url[1].'/'.$explode_url[2].'/'.$explode_url[3];
                        }else{
                            $img_url = '';
                        }
                        
                        if($chk_url == $img_url){
                            $rc_image = '<a href="'.$data->rc_image.' " target="_blank">view</a>';
                        }else{
                            $rc_image = '<a href="'.$chk_url.'/'.$data->rc_image.' " target="_blank">view</a>';
                        }
                    }else{
                        $rc_image = '';
                    }
                }        
                return $rc_image;
            }) 
            // ->addColumn('second_rc_image', function ($arrData) {
            //     if($arrData->second_rc_image == null){
            //         $second_rc_image = '-';
            //     }else{
            //         $second_rc_image = '<a href="'.URL::to('/storage/images/vehicle_rc_images/'.$arrData->second_rc_image).' " target="_blank">view</a>';
            //     }
            //     return $second_rc_image;
            // })
            ->addColumn('second_rc_image', function ($data) {
                if($data->second_rc_image == null){
                    $second_rc_image = '-';
                }else{
                    $chk_url = "https://easemylr.s3.us-east-2.amazonaws.com/vehicle_rc_images";
                    $img_url = $data->second_rc_image;
                    if($img_url != '' || $img_url != null){
                        $explode_url = explode("/",$img_url);
                        if(isset($explode_url[0]) && isset($explode_url[1]) && isset($explode_url[2]) && isset($explode_url[3])){
                            $img_url = $explode_url[0].'/'.$explode_url[1].'/'.$explode_url[2].'/'.$explode_url[3];
                        }else{
                            $img_url = '';
                        }
                        
                        if($chk_url == $img_url){
                            $second_rc_image = '<a href="'.$data->second_rc_image.' " target="_blank">view</a>';
                        }else{
                            $second_rc_image = '<a href="'.$chk_url.'/'.$data->second_rc_image.' " target="_blank">view</a>';
                        }
                    }else{
                        $second_rc_image = '';
                    }
                }        
                return $second_rc_image;
            })
            ->addColumn('action', function($row){
                $actionBtn = '<a href="'.URL::to($this->prefix.'/vehicles/'.Crypt::encrypt($row->id).'/edit').'" class="edit btn btn-primary btn-sm"><span><i class="fa fa-edit"></i></span></a>';
                $actionBtn .= '&nbsp;&nbsp;';
                $actionBtn .= '<a href="'.URL::to($this->prefix.'/vehicles/'.Crypt::encrypt($row->id).'').'" class="view btn btn-info btn-sm"><span><i class="fa fa-eye"></i></span></a>';
                $actionBtn .= '&nbsp;&nbsp;';
                // $actionBtn .= '<button type="button" name="delete" data-id="'.$row->id.'" data-action="'.URL::to($this->prefix.'/vehicles/delete-vehicle').'" class="delete btn btn-danger btn-sm delete_vehicle"><span><i class="fa fa-trash"></i></span></button>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'rc_image','second_rc_image'])
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
        $vehicles = Vehicle::all();
        $states = Helper::getStates();
        return view('vehicles.create-vehicle',['vehicles'=>$vehicles,'states'=>$states,'prefix'=>$this->prefix]);
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
            'regn_no' => 'required|unique:vehicles',
            'mfg' => 'required',
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

        $vehiclesave['regn_no']        = $request->regn_no;
        $vehiclesave['mfg']            = $request->mfg;
        $vehiclesave['make']           = $request->make;
        $vehiclesave['engine_no']      = $request->engine_no;
        $vehiclesave['chassis_no']     = $request->chassis_no;
        $vehiclesave['gross_vehicle_weight'] = $request->gross_vehicle_weight;
        $vehiclesave['unladen_weight'] = $request->unladen_weight;
        $vehiclesave['tonnage_capacity'] = $request->tonnage_capacity;
        $vehiclesave['body_type']      = $request->body_type;
        $vehiclesave['state_id']       = $request->state_id;
        $vehiclesave['regndate']       = $request->regndate;
        $vehiclesave['hypothecation']  = $request->hypothecation;
        $vehiclesave['ownership']      = $request->ownership;
        $vehiclesave['owner_name']     = $request->owner_name;
        $vehiclesave['owner_phone']    = $request->owner_phone;
        $vehiclesave['status']         = '1';

        // upload rc image1
        if($request->rc_image){
            $rc_image = $request->file('rc_image');
            $path = Storage::disk('s3')->put('vehicle_rc_images', $rc_image);
            $rc_img_url = Storage::disk('s3')->url($path);
            $rc_img_url = str_replace('http:', 'https:', $rc_img_url);
            $vehiclesave['rc_image'] = $rc_img_url;

        }
        // upload rc image2
        if($request->second_rc_image){
            $second_rc_image = $request->file('second_rc_image');
            $path = Storage::disk('s3')->put('vehicle_rc_images', $second_rc_image);
            $rc_img2_url = Storage::disk('s3')->url($path);
            $rc_img2_url = str_replace('http:', 'https:', $rc_img2_url);
            $vehiclesave['second_rc_image'] = $rc_img2_url;
        }

        // upload rc image
        // if($request->rc_image){
        //     $file = $request->file('rc_image');
        //     $path = 'public/images/vehicle_rc_images';
        //     $name = Helper::uploadImage($file,$path);
        //     $vehiclesave['rc_image']  = $name;
        // }
        //  // upload rc image
        // if($request->second_rc_image){
        //     $file = $request->file('second_rc_image');
        //     $path = 'public/images/vehicle_rc_images';
        //     $second_name = Helper::uploadImage($file,$path);
        //     $vehiclesave['second_rc_image']  = $second_name;
        // }
        
        $savevehicle = Vehicle::create($vehiclesave); 
        if($savevehicle)
        {
            $response['success']         = true;
            $response['success_message'] = "Vehicle Added successfully";
            $response['error']           = false;
            // $response['resetform']       = true;
            $response['page']            = 'vehicle-create';
            $response['redirect_url']    = URL::to($this->prefix.'/vehicles');
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not created vehicle please try again";
            $response['error']           = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($vehicle)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($vehicle);
        $getvehicle = Vehicle::where('id',$id)->first();
        return view('vehicles.view-vehicle',['prefix'=>$this->prefix,'getvehicle'=>$getvehicle]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $states = Helper::getStates();
        $getvehicle = Vehicle::where('id',$id)->first();
        return view('vehicles.update-vehicle')->with(['prefix'=>$this->prefix,'getvehicle'=>$getvehicle,'states'=>$states]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateVehicle(Request $request)
    {
        try { 
            $this->prefix = request()->route()->getPrefix();
             $rules = array(
                'regn_no' => 'required|unique:vehicles,regn_no,'.$request->vehicle_id,
                'mfg' => 'required',
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

            $vehiclesave['regn_no']        = $request->regn_no;
            $vehiclesave['mfg']            = $request->mfg;
            $vehiclesave['make']           = $request->make;
            $vehiclesave['engine_no']      = $request->engine_no;
            $vehiclesave['chassis_no']     = $request->chassis_no;
            $vehiclesave['gross_vehicle_weight'] = $request->gross_vehicle_weight;
            $vehiclesave['unladen_weight'] = $request->unladen_weight;
            $vehiclesave['tonnage_capacity'] = $request->tonnage_capacity;
            $vehiclesave['body_type']      = $request->body_type;
            $vehiclesave['state_id']       = $request->state_id;
            $vehiclesave['regndate']       = $request->regndate;
            $vehiclesave['hypothecation']  = $request->hypothecation;
            $vehiclesave['ownership']      = $request->ownership;
            $vehiclesave['owner_name']     = $request->owner_name;
            $vehiclesave['owner_phone']    = $request->owner_phone;
            $vehiclesave['status']         = '1';

            // upload rc image1
            // if($request->rc_image){
            //     $rc_image = $request->file('rc_image');
            //     $path = Storage::disk('s3')->put('vehicle_rc_images', $rc_image);
            //     $vehiclesave['rc_image'] = Storage::disk('s3')->url($path);
            // }
            // upload rc image2
            // if($request->second_rc_image){
            //     $second_rc_image = $request->file('second_rc_image');
            //     $path = Storage::disk('s3')->put('vehicle_rc_images', $second_rc_image);
            //     $vehiclesave['second_rc_image'] = Storage::disk('s3')->url($path);
            // }

            // upload rc image1
            if($request->rc_image){
                $rc_image = $request->file('rc_image');
                $path = Storage::disk('s3')->put('vehicle_rc_images', $rc_image);
                $rc_img_url = Storage::disk('s3')->url($path);
                $rc_img_url = str_replace('http:', 'https:', $rc_img_url);
                $vehiclesave['rc_image'] = $rc_img_url;

            }
            // upload rc image2
            if($request->second_rc_image){
                $second_rc_image = $request->file('second_rc_image');
                $path = Storage::disk('s3')->put('vehicle_rc_images', $second_rc_image);
                $rc_img2_url = Storage::disk('s3')->url($path);
                $rc_img2_url = str_replace('http:', 'https:', $rc_img2_url);
                $vehiclesave['second_rc_image'] = $rc_img2_url;
            }

            // if($request->rc_image){
            //     $file = $request->file('rc_image');
            //     $path = 'public/images/vehicle_rc_images';
            //     $name = Helper::uploadImage($file,$path); 
            //     $vehiclesave['rc_image']  = $name;
            // }
            // if($request->second_rc_image){
            //     $file = $request->file('second_rc_image');
            //     $path = 'public/images/vehicle_rc_images';
            //     $second_name = Helper::uploadImage($file,$path); 
            //     $vehiclesave['second_rc_image']  = $second_name;
            // }
            
            Vehicle::where('id',$request->vehicle_id)->update($vehiclesave);
            
            $response['success'] = true;
            $response['error'] = false;
            $response['page'] = 'vehicle-update';
            $response['success_message'] = "Vehicle Updated Successfully";
            $response['redirect_url'] = URL::to($this->prefix.'/vehicles');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // Delete rc image from update view
    public function deletercImage(Request $request)
    {
            $path = 'public/images/vehicle_rc_images';
            $image_path=Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$path;   
            $getimagename = Vehicle::where('id',$request["rcimgid"])->first(); 

            $image_path=$image_path.'/'.$getimagename->rc_image;
            if(file_exists($image_path)){
                unlink($image_path);
            }
            $vehiclesave['rc_image']  = '';
            $savevehicle = Vehicle::where('id',$request["rcimgid"])->update($vehiclesave);

            if($savevehicle)
            {
                $response['success']         = true;
                $response['success_message'] = 'Vehicle RC image deleted successfully';
                $response['error']           = false;
                $response['delvehicle_rc'] = "delvehicle_rc";
            }
            else{
                $response['success']         = false;
                $response['error_message']   = "Can not delete vehicle rc image please try again";
                $response['error']           = true;
            }
            return response()->json($response);
    }

    public function deleteVehicle(Request $request)
    {
        Vehicle::where('id',$request->vehicleid)->delete();

        $response['success']         = true;
        $response['success_message'] = 'Vehicle deleted successfully';
        $response['error']           = false;
        return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new VehicleExport, 'vehicles.csv');
    }

    // not use yet
    public function getDrivers(Request $request){
        $getdrivers = Driver::select('name','phone','license_number')->where(['id'=>$request->driver_id,'status'=>'1'] )->first();
        if($getdrivers)
        {
            $response['success']         = true;
            $response['success_message'] = "Driver list fetch successfully";
            $response['error']           = false;
            $response['data']            = $getdrivers;
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not fetch driver list please try again";
            $response['error']           = true;
        }
    	return response()->json($response);
    }
}
