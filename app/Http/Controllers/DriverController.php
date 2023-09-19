<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Driver;
use App\Models\Bank;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverExport;
use DB;
use URL;
use Crypt;
use Helper;
use Validator;
use Config;
use Image;
use Storage;
use Auth;
use Session;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->title =  "Drivers Listing";
        $this->segment = \Request::segment(2);
    }

    public function index(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = Driver::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }
            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('license_number', 'like', '%' . $search . '%');
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

            $drivers = $query->orderBy('id', 'DESC')->paginate($peritem);
            $drivers = $drivers->appends($request->query());
             // echo'<pre'; print_r($drivers); die;
             $html = view('drivers.driver-list-ajax', ['prefix' => $this->prefix, 'drivers' => $drivers, 'peritem' => $peritem])->render();

             return response()->json(['html' => $html]);
        }
        $drivers = $query->orderBy('id', 'DESC')->paginate($peritem);
        $drivers = $drivers->appends($request->query());
  
        return view('drivers.driver-list',['prefix'=>$this->prefix,'title'=>$this->title,'segment'=>$this->segment,'drivers'=>$drivers, 'peritem' => $peritem]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index1(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        if ($request->ajax()) {
            $data = Driver::orderBy('id','DESC')->get();
            
            return datatables()->of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="'.URL::to($this->prefix.'/'.$this->segment.'/'.Crypt::encrypt($row->id).'/edit').'" class="edit btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>';
                        $btn .= '&nbsp;&nbsp;';
                        $btn .= '<a href="'.URL::to($this->prefix.'/'.$this->segment.'/'.Crypt::encrypt($row->id)).'" class="view btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>';
                        $btn .= '&nbsp;&nbsp;';
                        // $btn .= '<a class="delete btn btn-sm btn-danger delete_driver" data-id="'.$row->id.'" data-action="'.URL::to($this->prefix.'/'.$this->segment.'/delete-driver').'"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('licence', function ($data) {
                        if($data->license_image == null){
                            $licence = '-';
                        }else{
                            $chk_url = "https://easemylr.s3.us-east-2.amazonaws.com/driverlicense_images";
                            $img_url = $data->license_image;
                            if($img_url != '' || $img_url != null){
                                $explode_url = explode("/",$img_url);
                                if(isset($explode_url[0]) && isset($explode_url[1]) && isset($explode_url[2]) && isset($explode_url[3])){
                                    $img_url = $explode_url[0].'/'.$explode_url[1].'/'.$explode_url[2].'/'.$explode_url[3];
                                }else{
                                    $img_url = '';
                                }
                                
                                if($chk_url == $img_url){
                                    $licence = '<a href="'.$data->license_image.' " target="_blank">view</a>';
                                }else{
                                    $licence = '<a href="'.$chk_url.'/'.$data->license_image.' " target="_blank">view</a>';
                                }
                            }else{
                                $licence = '';
                            }
                        }        
                        return $licence;
                    }) 
                    ->addColumn('access_status', function ($data) {
                        if($data->access_status == 0){
                            $access_status = 'Not Enabled';
                        }else{
                            $access_status = 'Enabled';
                        }        
                        return $access_status;
                    })
                    ->addColumn('branch_id', function ($data) {
                        if($data->branch_id){
                            $branch_id = explode(',',$data->branch_id);
                            $branch_ids = array();
                            foreach($branch_id as $branch){
                                $location = Location::where('id',$branch)->first();
                                $branch_ids[] = $location['name'];
                            }
                            $branch_name = implode('/', $branch_ids);
                        }else{
                            $branch_name = '';
                        }
                        return $branch_name;
                    }) 
                    ->rawColumns(['action', 'licence','branch_id'])
                    ->make(true);
        }
        return view('drivers.driver-list',['prefix'=>$this->prefix,'segment'=>$this->segment]);
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
        $cc = explode(',', $authuser->branch_id);
        if ($authuser->role_id != 1) {
            $branchs = Location::select('id', 'name')->whereIn('id',$cc)->get();
        }else{
            $branchs = Location::select('id', 'name')->get();
        }
        return view('drivers.create-driver',['prefix'=>$this->prefix, 'branchs' => $branchs]);
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
            'name' => 'required',
            'phone' => 'required|unique:drivers',
            'license_number' => 'required',
            'license_image' => 'mimes:jpg,jpeg,png|max:4096',
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
        // if($request->branch_id){
        //     if(($request->branch_id != null) || ($request->branch_id != '')){
        //         $branches = array_unique(array_merge($request->branch_id));
        //         $branch = implode(',', $branches);
        //     }
        //     else{
        //         $branch ='';
        //     }
        // }else{
        //         $branch ='';
        //     }

        $driversave['name']                 = $request->name;
        $driversave['phone']                = $request->phone;
        $driversave['license_number']       = $request->license_number;
        $driversave['team_id']              = $request->team_id;
        $driversave['fleet_id']             = $request->fleet_id;
        $driversave['login_id']             = $request->login_id;
        $driversave['driver_password']      = $request->password;
        $driversave['password']             = bcrypt($request->password);
        // $driversave['app_use']              =  $request->app_use;
        $driversave['branch_id']            =  '';
        $driversave['access_status']        =  $request->access_status;
        $driversave['status']               = '1';

        // upload license image
        if($request->file('license_image')){
            $originalFilename = uniqid() . '_' . $request->file('license_image')->getClientOriginalName();

            if (Storage::disk('s3')->putFileAs('driverlicense_images', $request->file('license_image'), $originalFilename)) {
                $imagePath = explode('/', $originalFilename);
                $driversave['license_image'] = end($imagePath);
            }
        }
        // if($request->license_image){
        //     $file = $request->file('license_image');
        //     $path = 'public/images/driverlicense_images';
        //     $name = Helper::uploadImage($file,$path);
        //     $driversave['license_image']  = $name;
        // }

        $savedriver = Driver::create($driversave); 
        if($savedriver)
        {
            $bankdetails['broker_id']          = $savedriver->id;
            $bankdetails['bank_name']          = $request->bank_name;
            $bankdetails['branch_name']        = $request->branch_name;
            $bankdetails['ifsc']               = $request->ifsc;
            $bankdetails['account_number']     = $request->account_number;
            $bankdetails['account_holdername'] = $request->account_holdername;
            $bankdetails['status']             = "1";
            
            $savebankdetails = Bank::create($bankdetails);

            $response['success']         = true;
            $response['success_message'] = "Driver Added successfully";
            $response['error']           = false;
            $response['page']            = 'driver-create';
            $response['redirect_url']    = URL::to($this->prefix.'/drivers');
            // $response['resetform']       = true;
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not created driver please try again";
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
    public function show($driver)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($driver);
        $getdriver = Driver::where('id',$id)->with(['BankDetail'=> function($query){
            $query->where('status',1);
        }])->first();
        return view('drivers.view-driver',['prefix'=>$this->prefix,'title'=>$this->title,'getdriver'=>$getdriver]);
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
        $getdriver = Driver::where('id',$id)->with(['BankDetail'=> function($query){
            $query->where('status',1);
        }])->first();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);
        if ($authuser->role_id != 1) {
            $branchs = Location::select('id', 'name')->whereIn('id',$cc)->get();
        }else{
            $branchs = Location::select('id', 'name')->get();
        }
        $branches = Location::select('id', 'name')->get();
        return view('drivers.update-driver')->with(['prefix'=>$this->prefix,'getdriver'=>$getdriver,'branchs' => $branchs, 'branches' => $branches]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDriver(Request $request)
    {
        // dd($request->branches_id);
        try { 
            $this->prefix = request()->route()->getPrefix();
             $rules = array(
                'name' => 'required',
                'license_number' => 'required|unique:drivers,license_number,'.$request->driver_id,
                'license_image'  => 'mimes:jpg,jpeg,png|max:4096',
            );

            $validator = Validator::make($request->all(),$rules);

            if($validator->fails())
            {
                $errors                 = $validator->errors();
                $response['success']    = false;
                $response['formErrors'] = true;
                $response['errors']     = $errors;
                return response()->json($response);
            }
            
            // if(($request->branch_id != null) || ($request->branches_id[0] != null) || (!empty($request->branch_id)) || (!empty($request->branches_id[0]))){
            // $branches = array_unique(array_merge($request->branch_id, $request->branches_id));
            // $branch = implode(',', $branches);
            // }else{
            //     $branch ='';
            // }

            $driversave['name']           = $request->name;
            $driversave['phone']          = $request->phone;
            $driversave['license_number'] = $request->license_number;
            $driversave['team_id']        = $request->team_id;
            $driversave['fleet_id']       = $request->fleet_id;
            $driversave['login_id']       = $request->login_id;
            $driversave['driver_password']= $request->password;
            $driversave['branch_id']      = '';
            $driversave['access_status']  = $request->access_status;
            $driversave['password']       = bcrypt($request->password);

            // upload driver_license image
            if($request->file('license_image')){
                $originalFilename = uniqid() . '_' . $request->file('license_image')->getClientOriginalName();
    
                if (Storage::disk('s3')->putFileAs('driverlicense_images', $request->file('license_image'), $originalFilename)) {
                    // Delete the old file (if exists)
                    if (!empty($driver->license_image)) {
                        Storage::disk('s3')->delete('driverlicense_images/' . $driver->license_image);
                    }
                    $imagePath = explode('/', $originalFilename);
                    $driversave['license_image'] = end($imagePath);
                }
            }
            
            $savedriver = Driver::where('id',$request->driver_id)->update($driversave);

            if($savedriver)
            {
                $bankdetails['bank_name']          = $request->bank_name;
                $bankdetails['branch_name']        = $request->branch_name;
                $bankdetails['ifsc']               = $request->ifsc;
                $bankdetails['account_number']     = $request->account_number;
                $bankdetails['account_holdername'] = $request->account_holdername;
                $bankdetails['status']             = "1";
            
                $savebankdetails = Bank::where('broker_id',$request->driver_id)->update($bankdetails);
                $url    =   URL::to($this->prefix.'/drivers');

                $response['redirect_url']    = $url;
                $response['success']         = true;
                $response['success_message'] = "Driver Updated Successfully";
                $response['error']           = false;
                $response['page']            = 'driver-update';
            }
        }catch(Exception $e) {
            $response['error']         = false;
            $response['error_message'] = $e;
            $response['success']       = false;
            $response['redirect_url']  = $url;   
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteDriver(Request $request)
    {
        Driver::where('id',$request->driverid)->delete();

        $response['success']         = true;
        $response['success_message'] = 'Driver deleted successfully';
        $response['error']           = false;
        return response()->json($response);
    }

    // Delete licence image from edit view
    public function deletelicenseImage(Request $request)
    {
            $path = 'public/images/driverlicense_images';
            $image_path=Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$path;   
            $getimagename = Driver::where('id',$request["licenseimgid"])->first(); 

            $image_path=$image_path.'/'.$getimagename->license_image;
            if(file_exists($image_path)){
                unlink($image_path);
            }
            $driversave['license_image']  = '';
            $savedriver = Driver::where('id',$request["licenseimgid"])->update($driversave);

            if($savedriver)
            {
                $response['success']         = true;
                $response['success_message'] = 'Driver license image deleted successfully';
                $response['error']           = false;
                $response['deldriver_license'] = "deldriver_license";
            }
            else{
                $response['success']         = false;
                $response['error_message']   = "Can not delete driver license image please try again";
                $response['error']           = true;
            }
            return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new DriverExport, 'drivers.csv');
    }

}
