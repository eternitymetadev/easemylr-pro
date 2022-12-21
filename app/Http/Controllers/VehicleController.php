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
use Validator;
use DataTables;
use Storage;
use Auth;

class VehicleController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Vehicles";
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

        return view('vehicles.vehicle-list', ['prefix' => $this->prefix, 'title' => $this->title, 'segment' => $this->segment]);
    }

    public function getData(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        if ($authuser->role_id == 1) {
            $this->prefix = 'admin';
        } elseif ($authuser->role_id == 2) {
            $this->prefix = 'branch-manager';
        } elseif ($authuser->role_id == 3) {
            $this->prefix = 'regional-manager';
        } elseif ($authuser->role_id == 4) {
            $this->prefix = 'branch-user';
        } elseif ($authuser->role_id == 5) {
            $this->prefix = 'account-manager';
        } else {
            $this->prefix = '';
        }

        $arrData = \DB::table('vehicles');
        $arrDatas = $arrData->get();

        return Datatables::of($arrData)->addIndexColumn()
            ->addColumn('regndate', function ($arrData) {
                if ($arrData->regndate == null) {
                    $regndate = '-';
                } else {
                    $regndate = Helper::ShowDayMonthYear($arrData->regndate);
                }
                return $regndate;
            })
            ->addColumn('mfg', function ($arrData) {
                if ($arrData->mfg == null) {
                    $mfg = '-';
                } else {
                    $mfg = '<div class="wrapText" style="max-width: 160px" title="'.$arrData->mfg.'">'.$arrData->mfg.'</div>';
                }
                return $mfg;
            })
            ->addColumn('make', function ($arrData) {
                if ($arrData->make == null) {
                    $make = '-';
                } else {
                    $make = '<div class="wrapText" style="max-width: 120px" title="'.$arrData->make.'">'.$arrData->make.'</div>';
                }
                return $make;
            })
            ->addColumn('rc_image', function ($arrData) {
                if ($arrData->rc_image == null) {
                    $rc_image = '-';
                } else {
                    $rc_image = '<a href="#" data-toggle="modal" data-target="#rcViewModal" style="text-align: center">view</a>';
//                    $rc_image = '<a href="' . URL::to('/storage/images/vehicle_rc_images/' . $arrData->rc_image) . ' " target="_blank">view</a>';
                }
                return $rc_image;
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<div class="d-flex align-content-center justify-content-center" style="gap: 6px"><a id="editVehicleModalIcon" href="#" data-toggle="modal" data-target="#editVehicleModal" class="edit editIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"> <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path> <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path> </svg></a>';
//                $actionBtn = '<a href="'.URL::to($this->prefix.'/vehicles/'.Crypt::encrypt($row->id).'/edit').'" class="edit btn btn-primary btn-sm"><span><i class="fa fa-edit"></i></span></a>';
                $actionBtn .= '<a href="#" data-toggle="modal" data-target="#vehicleDetailsModal" class="view viewIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
//                $actionBtn .= '<a href="'.URL::to($this->prefix.'/vehicles/'.Crypt::encrypt($row->id).'').'" class="view btn btn-info btn-sm"><span><i class="fa fa-eye"></i></span></a>';
                $actionBtn .= '<a class="delete deleteIcon delete_vehicle" data-id="' . $row->id . '" data-action="' . URL::to($this->prefix . '/vehicles/delete-vehicle') . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"                                                  viewBox="0 0 24 24"                                                  fill="none" stroke="currentColor" stroke-width="2"                                                  stroke-linecap="round"                                                  stroke-linejoin="round" class="feather feather-trash-2">                                                 <polyline points="3 6 5 6 21 6"></polyline>                                                 <path                                                     d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>                                                 <line x1="10" y1="11" x2="10" y2="17"></line>                                                 <line x1="14" y1="11" x2="14" y2="17"></line>                                             </svg></a></div>';
                return $actionBtn;
            })

            ->rawColumns(['action', 'rc_image', 'make', 'regndate', 'mfg'])
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
        return view('vehicles.create-vehicle', ['vehicles' => $vehicles, 'states' => $states, 'prefix' => $this->prefix]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'regn_no' => 'required|unique:vehicles',
            'mfg' => 'required',
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

        $vehiclesave['regn_no'] = $request->regn_no;
        $vehiclesave['mfg'] = $request->mfg;
        $vehiclesave['make'] = $request->make;
        $vehiclesave['engine_no'] = $request->engine_no;
        $vehiclesave['chassis_no'] = $request->chassis_no;
        $vehiclesave['gross_vehicle_weight'] = $request->gross_vehicle_weight;
        $vehiclesave['unladen_weight'] = $request->unladen_weight;
        $vehiclesave['tonnage_capacity'] = $request->tonnage_capacity;
        $vehiclesave['body_type'] = $request->body_type;
        $vehiclesave['state_id'] = $request->state_id;
        $vehiclesave['regndate'] = $request->regndate;
        $vehiclesave['hypothecation'] = $request->hypothecation;
        $vehiclesave['ownership'] = $request->ownership;
        $vehiclesave['owner_name'] = $request->owner_name;
        $vehiclesave['owner_phone'] = $request->owner_phone;
        $vehiclesave['status'] = '1';

        // upload rc image
        if ($request->rc_image) {
            $file = $request->file('rc_image');
            $path = 'public/images/vehicle_rc_images';
            $name = Helper::uploadImage($file, $path);
            $vehiclesave['rc_image'] = $name;
        }

        $savevehicle = Vehicle::create($vehiclesave);
        if ($savevehicle) {
            $response['success'] = true;
            $response['success_message'] = "Vehicle Added successfully";
            $response['error'] = false;
            // $response['resetform']       = true;
            $response['page'] = 'vehicle-create';
            $response['redirect_url'] = URL::to($this->prefix . '/vehicles');
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not created vehicle please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($vehicle)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($vehicle);
        $getvehicle = Vehicle::where('id', $id)->first();
        return view('vehicles.view-vehicle', ['prefix' => $this->prefix, 'getvehicle' => $getvehicle]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $states = Helper::getStates();
        $getvehicle = Vehicle::where('id', $id)->first();
        return view('vehicles.update-vehicle')->with(['prefix' => $this->prefix, 'getvehicle' => $getvehicle, 'states' => $states]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateVehicle(Request $request)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'regn_no' => 'required|unique:vehicles,regn_no,' . $request->vehicle_id,
                'mfg' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }

            $vehiclesave['regn_no'] = $request->regn_no;
            $vehiclesave['mfg'] = $request->mfg;
            $vehiclesave['make'] = $request->make;
            $vehiclesave['engine_no'] = $request->engine_no;
            $vehiclesave['chassis_no'] = $request->chassis_no;
            $vehiclesave['gross_vehicle_weight'] = $request->gross_vehicle_weight;
            $vehiclesave['unladen_weight'] = $request->unladen_weight;
            $vehiclesave['tonnage_capacity'] = $request->tonnage_capacity;
            $vehiclesave['body_type'] = $request->body_type;
            $vehiclesave['state_id'] = $request->state_id;
            $vehiclesave['regndate'] = $request->regndate;
            $vehiclesave['hypothecation'] = $request->hypothecation;
            $vehiclesave['ownership'] = $request->ownership;
            $vehiclesave['owner_name'] = $request->owner_name;
            $vehiclesave['owner_phone'] = $request->owner_phone;
            $vehiclesave['status'] = '1';

            // upload vehicle_rc image
            if ($request->rc_image) {
                $file = $request->file('rc_image');
                $path = 'public/images/vehicle_rc_images';
                $name = Helper::uploadImage($file, $path);
                $vehiclesave['rc_image'] = $name;
            }

            Vehicle::where('id', $request->vehicle_id)->update($vehiclesave);

            $response['success'] = true;
            $response['error'] = false;
            $response['page'] = 'vehicle-update';
            $response['success_message'] = "Vehicle Updated Successfully";
            $response['redirect_url'] = URL::to($this->prefix . '/vehicles');
        } catch (Exception $e) {
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
     * @param int $id
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
        $image_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $path;
        $getimagename = Vehicle::where('id', $request["rcimgid"])->first();

        $image_path = $image_path . '/' . $getimagename->rc_image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        $vehiclesave['rc_image'] = '';
        $savevehicle = Vehicle::where('id', $request["rcimgid"])->update($vehiclesave);

        if ($savevehicle) {
            $response['success'] = true;
            $response['success_message'] = 'Vehicle RC image deleted successfully';
            $response['error'] = false;
            $response['delvehicle_rc'] = "delvehicle_rc";
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not delete vehicle rc image please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    public function deleteVehicle(Request $request)
    {
        Vehicle::where('id', $request->vehicleid)->delete();

        $response['success'] = true;
        $response['success_message'] = 'Vehicle deleted successfully';
        $response['error'] = false;
        return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new VehicleExport, 'vehicles.csv');
    }

    // not use yet
    public function getDrivers(Request $request)
    {
        $getdrivers = Driver::select('name', 'phone', 'license_number')->where(['id' => $request->driver_id, 'status' => '1'])->first();
        if ($getdrivers) {
            $response['success'] = true;
            $response['success_message'] = "Driver list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getdrivers;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch driver list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }
}
