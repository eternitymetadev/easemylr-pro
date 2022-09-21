<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Driver;
use App\Models\Bank;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverExport;
use DB;
use URL;
use Crypt;
use Helper;
use Validator;
use Image;
use Storage;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->title =  "Drivers Listing";
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
            $data = Driver::orderBy('id','DESC')->get();
            
            return datatables()->of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="'.URL::to($this->prefix.'/'.$this->segment.'/'.Crypt::encrypt($row->id).'/edit').'" class="edit btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>';
                        $btn .= '&nbsp;&nbsp;';
                        $btn .= '<a href="'.URL::to($this->prefix.'/'.$this->segment.'/'.Crypt::encrypt($row->id)).'" class="view btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>';
                        $btn .= '&nbsp;&nbsp;';
                        $btn .= '<a class="delete btn btn-sm btn-danger delete_driver" data-id="'.$row->id.'" data-action="'.URL::to($this->prefix.'/'.$this->segment.'/delete-driver').'"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('licence', function ($data) {
                        if($data->license_image == null){
                            $licence = '-';
                        }else{
                            $licence = '<a href="'.URL::to('/storage/images/driverlicense_images/'.$data->license_image).' " target="_blank">view</a>';
                        }        
                        return $licence;
                    })
                    ->rawColumns(['action', 'licence'])
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
        return view('drivers.create-driver',['prefix'=>$this->prefix]);
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

        $driversave['name']            = $request->name;
        $driversave['phone']           = $request->phone;
        $driversave['license_number']  = $request->license_number;
        $driversave['team_id']         = $request->team_id;
        $driversave['fleet_id']        = $request->fleet_id;
        $driversave['login_id']        = $request->login_id;
        $driversave['driver_password']   = $request->password;
        $driversave['password']        = bcrypt($request->password);
        $driversave['status']          = '1';

        // upload license image
        if($request->license_image){
            $file = $request->file('license_image');
            $path = 'public/images/driverlicense_images';
            $name = Helper::uploadImage($file,$path);
            $driversave['license_image']  = $name;
        }

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
        return view('drivers.update-driver')->with(['prefix'=>$this->prefix,'getdriver'=>$getdriver]);
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

            $driversave['name']           = $request->name;
            $driversave['phone']          = $request->phone;
            $driversave['license_number'] = $request->license_number;
            $driversave['team_id']        = $request->team_id;
            $driversave['fleet_id']       = $request->fleet_id;
            $driversave['login_id']       = $request->login_id;
            $driversave['driver_password']       = $request->password;
            $driversave['password']       = bcrypt($request->password);

            // upload driver_license image 
            if($request->license_image){
                $file = $request->file('license_image');
                $path = 'public/images/driverlicense_images';
                $name = Helper::uploadImage($file,$path); 
                $driversave['license_image']  = $name;
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
