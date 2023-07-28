<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchAddress;
use App\Models\Zone;
use App\Models\GstRegisteredAddress;
use App\Models\Location;
use App\Models\State;
use App\Exports\ZoneExport;
use App\Models\BranchConnectivity;
use App\Models\Role;
use App\Models\User;
use App\Models\Consigner;
use App\Models\Consignee;
use Auth;
use Config;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use URL;
use Validator;

class SettingController extends Controller
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

    public function getbranchAddress(Request $request)
    {
        
        return view('setting.index');
    }

    // add branch address
    public function updateBranchadd(Request $request)
    {

        $this->prefix = request()->route()->getPrefix();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rules = array(
                'name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }
            if (!empty($request->name)) {
                $settingsave['name'] = $request->name;
            }
            if (!empty($request->gst_number)) {
                $settingsave['gst_number'] = $request->gst_number;
            }
            if (!empty($request->phone)) {
                $settingsave['phone'] = $request->phone;
            }
            if (!empty($request->address)) {
                $settingsave['address'] = $request->address;
            }
            if (!empty($request->state)) {
                $settingsave['state'] = $request->state;
            }
            if (!empty($request->district)) {
                $settingsave['district'] = $request->district;
            }
            if (!empty($request->city)) {
                $settingsave['city'] = $request->city;
            }
            if (!empty($request->postal_code)) {
                $settingsave['postal_code'] = $request->postal_code;
            }
            if (!empty($request->email)) {
                $settingsave['email'] = $request->email;
            }
            $settingsave['status'] = "1";

            $savesetting = BranchAddress::updateOrCreate(['meta_key' => 'addressdata_key'], $settingsave);
            if ($savesetting) {

                $response['success'] = true;
                $response['success_message'] = "Branch address value updated successfully.";
                $response['error'] = false;
                $response['page'] = 'settings-branch-address';

            } else {
                $response['success'] = false;
                $response['error_message'] = "Can not updated branch address value please try again";
                $response['error'] = true;
            }
            return response()->json($response);

        }
        else
        {
            $branchaddvalue = BranchAddress::where(['meta_key'=>'addressdata_key'])->first();
            $branchs = Location::all();
            $states = State::all();
            $gstaddresses = GstRegisteredAddress::all();
            return view('settings.branch-address',['branchaddvalue'=>$branchaddvalue,'prefix'=>$this->prefix, 'branchs' => $branchs, 'states' => $states,'gstaddresses' => $gstaddresses]);
        }
    }
    // postal code list
    public function postalCode(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = Zone::query();
        $all_districts = Zone::select('district')->groupBy('district')->get();
        $all_states = Zone::select('state')->groupBy('state')->get();
        $branchs = Location::all();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query->with('Branch','GetLocation')->where('status', 1);

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('postal_code', 'like', '%' . $search . '%')
                    ->orWhere('district', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%');
                    
                });
            }
            
            if ($request->state_name) {
                $query = $query->whereIn('state', $request->state_name);
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
            $zones = $query->orderBy('id', 'DESC')->paginate($peritem);
            $zones = $zones->appends($request->query());

            $html = view('settings.postal-code-editAjax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'zones' => $zones])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query->with('Branch','GetLocation')
            ->where('status', 1);

        $zones = $query->orderBy('id', 'DESC')->paginate($peritem);
        $zones = $zones->appends($request->query());

        return view('settings.postal-code-edit', ['peritem' => $peritem, 'prefix' => $this->prefix, 'zones' => $zones, 'segment' => $this->segment, 'all_districts' => $all_districts, 'branchs' => $branchs, 'all_states' => $all_states]);
    }

    // postal code list
    public function storePostalCode(Request $request)
    {
        // dd($request->all());
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'postal_code' => 'required|unique:zones',
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

        $get_location = Location::where('id', $request->branch_id)->first();

        if(!empty($request->postal_code)){
            $addpostal['postal_code'] = $request->postal_code;
        }
        if(!empty($request->city)){
            $addpostal['city'] = $request->city;
        }
        if(!empty($request->state)){
            $addpostal['state'] = $request->state;
        }
        if(!empty($request->district)){
            $addpostal['district'] = $request->district;
        }
        if(!empty($request->pickup_hub)){
            $addpostal['pickup_hub'] = $request->pickup_hub;
        }
        // if(!empty($request->hub_transfer)){
        //     $addpostal['hub_transfer'] = $request->hub_transfer;
        // }
        $addpostal['hub_transfer'] = $get_location->name;
        $addpostal['hub_nickname'] = $request->branch_id;
        $addpostal['status'] = 1;
        
        $savepostal = Zone::create($addpostal);
        
        if($savepostal){
            $response['success']    = true;
            $response['page']       = 'postalcode-create';
            $response['error']      = false;
            $response['success_message'] = "Postal code created successfully";
            $response['redirect_url'] = URL::to($this->prefix.'/postal-code');
        }else{
            $response['success']       = false;
            $response['error']         = true;
            $response['error_message'] = "Can not created postal code please try again";
        }
        return response()->json($response);
    }

    public function editPostalCode(Request $request)
    {
        $id = $request->postal_id;
        $postal_code = Zone::where('id', $id)->first();
        $branchs = Location::all();

        $response['zone_data'] = $postal_code;
        $response['branch_data'] = $branchs;
        $response['success'] = true;
        $response['success_message'] = "Data Fetch";
        return response()->json($response);
    }

    public function updatePostalCode(Request $request)
    {
        try {
            DB::beginTransaction();
            $get_location = Location::where('id', $request->branch_id)->first();

            // $zoneupdate['state'] = $request->state;
            $zoneupdate['city'] = $request->city;
            $zoneupdate['pickup_hub'] = $request->pickup_hub;
            $zoneupdate['hub_transfer'] = @$get_location->name;
            $zoneupdate['hub_nickname'] = @$request->branch_id;
           
            Zone::where('id', $request->zone_id)->update($zoneupdate);

            $response['success'] = true;
            $response['success_message'] = "Zone Data successfully";
            $response['error'] = false;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    //zone download excel/csv
    public function exportExcel(Request $request)
    {
        return Excel::download(new ZoneExport($request->state_name), 'zones.csv');
    }
    public function updateDistrictHub(Request $request)
    {
        try {
            DB::beginTransaction();
            $get_location = Location::where('id', $request->branch_id)->first();
            // $pickup_location = Location::where('id', $request->pickup_hub)->first();

            Zone::where('state',$request->state_id)->whereIn('district', $request->district)->update(['pickup_hub' => $request->pickup_hub,'hub_transfer' => $get_location->name, 'hub_nickname' => $request->branch_id]);

            $response['success'] = true;
            $response['success_message'] = "Hub Updated successfully";
            $response['error'] = false;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);

    }

    public function routeFinder()
    {
        $this->prefix = request()->route()->getPrefix();
        return view('settings.route-finder', ['prefix' => $this->prefix]);
    }

    public function findRoute(Request $request)
    {
        $startingPincode = $request->startingPincode;
        $endingPincode = $request->endingPincode;

        $connected_hubs = BranchConnectivity::all();

        $graph = [];
        foreach ($connected_hubs as $hub) {
            $location = $hub->efpl_hub;
            $neighbors = explode(',', $hub->direct_connectivity);
            $graph[$location] = $neighbors;
        }

        $pincode_locations = Zone::all();

        $pincodeLocations = [];
        foreach ($pincode_locations as $pincode_location) {
            $pincodeLocations[$pincode_location->postal_code] = $pincode_location->hub_nickname;
        }

        $response = '';

        if (isset($pincodeLocations[$startingPincode]) && isset($pincodeLocations[$endingPincode])) {
            $startingLocation = $pincodeLocations[$startingPincode];
            $endingLocation = $pincodeLocations[$endingPincode];

            $response .= "<h3>Routes between $startingPincode and $endingPincode :</h3>";

            // Initialize visited and route arrays
            $visited = array_fill_keys(array_keys($graph), false);
            $route = [];

            // Find routes using DFS
            $routes = $this->findRoutes($graph, $startingLocation, $endingLocation, $visited, $route);
            
            // If no routes were found, display a message
            if (empty($routes)) {
                $response .= "<p>No route available between $startingPincode ($startingLocation) and $endingPincode ($endingLocation).</p>";
            } else {

                // Display all routes found
                foreach ($routes as $key => $route) {
                    $new = array();
                   // $i = 0;
                   
                    foreach ($route as $key => $r) {
                        $getbranch = DB::table('locations')->where('id', $r)->first();
                        $new[]= $getbranch->name;
                    }
                    $response .= "<p>" . implode(' -> ', $new) . "</p>";
   
                }
              
            }
        } else {
            $response .= "<p>Invalid pincode entered.</p>";
        }

        echo $response;

    }

    public function findRoutes($graph, $start, $end, $visited, $route)
    {
        // Mark the current location as visited
        $visited[$start] = true;

        // Add the current location to the route
        $route[] = $start;

        // If the destination location is reached, return the route
        if ($start === $end) {
            return [$route];
        } else {
            $allRoutes = [];

            // Check if the current location exists in the graph
            if (isset($graph[$start])) {
                // Iterate over the neighbors of the current location
                foreach ($graph[$start] as $neighbor) {
                    // Visit unvisited neighbors recursively
                    if (!$visited[$neighbor]) {
                        $newRoutes = $this->findRoutes($graph, $neighbor, $end, $visited, $route);
                        $allRoutes = array_merge($allRoutes, $newRoutes);
                    }
                }
            }

            return $allRoutes;
        }
    }

    public function getDistrict(Request $request)
    {
         $all_district = Zone::select('district')->where('state', $request->state_name)->get();

         $district_array = array();
         foreach($all_district as $district){
            $district_array[] = $district->district;

         }
         $state_district = array_unique($district_array);

        $response['all_district'] = $state_district;
        $response['success'] = true;
        $response['message'] = "District Fetched";

        return response()->json($response);
    }

     public function getRoute(Request $request)
     {
        $consioner_pin = Consigner::where('id', $request->consigner_id)->first();
        $consignee_pin = Consignee::where('id', $request->consignee_id)->first();

        $startingPincode = $consioner_pin->postal_code;
        $endingPincode = $consignee_pin->postal_code;

        $connected_hubs = BranchConnectivity::all();

        $graph = [];
        foreach ($connected_hubs as $hub) {
            $location = $hub->efpl_hub;
            $neighbors = explode(',', $hub->direct_connectivity);
            $graph[$location] = $neighbors;
        }

        $pincode_locations = Zone::all();

        $pincodeLocations = [];
        foreach ($pincode_locations as $pincode_location) {
            $pincodeLocations[$pincode_location->postal_code] = $pincode_location->hub_nickname;
        }

        $response = '';

        if (isset($pincodeLocations[$startingPincode]) && isset($pincodeLocations[$endingPincode])) {
            $startingLocation = $pincodeLocations[$startingPincode];
            $endingLocation = $pincodeLocations[$endingPincode];

            $response .= "<h3>Routes between $startingPincode and $endingPincode :</h3>";

            // Initialize visited and route arrays
            $visited = array_fill_keys(array_keys($graph), false);
            $route = [];

            // Find routes using DFS
            $routes = $this->findRoutes($graph, $startingLocation, $endingLocation, $visited, $route);
            
            // If no routes were found, display a message
            if (empty($routes)) {
                $response .= "<p>No route available between $startingPincode ($startingLocation) and $endingPincode ($endingLocation).</p>";
            } else {
                $i = 0;

                // Display all routes found
                foreach ($routes as $key => $route) {
                    $new = array();
                   
                    foreach ($route as $key => $r) {
                        $getbranch = DB::table('locations')->where('id', $r)->first();
                        $new[]= $getbranch->name;
                    }
                    // $response .= "<p>" . implode(' -> ', $new) . "</p>";
                    $response .= "<div><input type='radio' onchange='enableSUbmitButton()' id='route-$i' name='lr_routes' value=".implode(',', $route)."><label for='route-$i'>".implode(' -> ', $new)."</label></div>" ;
                    $i++;
                }
              
            }
        } else {
            $response .= "<p>Invalid pincode entered.</p>";
        }

        return response()->json($response);


     }

    // ===========
    public function addGstAddress(Request $request)
    {
        
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'gst_no' => 'required|unique:gst_registered_addresses',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['error_message'] = $errors;
                return response()->json($response);
            }

            $gst = $request->file('upload_gst');

            if (!empty($gst)) {
                $gstfile = $gst->getClientOriginalName();
                $gst->move(public_path('drs/company_gst'), $gstfile);
            } else {
                $gstfile = null;
            }

            $gstsave['gst_no'] = $request->gst_no;
            // $gstsave['branch_id'] = $branch;
            $gstsave['state'] = $request->state;
            $gstsave['address_line_1'] = $request->address_line_1;
            $gstsave['address_line_2'] = $request->address_line_2;
            $gstsave['upload_gst'] = $gstfile;

            $gstsave = GstRegisteredAddress::create($gstsave);

            if(!empty($request->branch_id)){
                foreach($request->branch_id as $branch){
                    Location::where('id', $branch)->update(['gst_registered_id'=> $gstsave->id]);
                }
            }

            if ($gstsave) {
                $url = $this->prefix . '/settings/branch-address';
                $response['success'] = true;
                $response['success_message'] = "Address Added successfully";
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

    public function editGstAddress(Request $request)
    {
        $id = $request->gst_id;
        $gst_num = GstRegisteredAddress::with('Branch')->where('id', $id)->first(); 

        $response['gst_num'] = $gst_num;
        $response['success'] = true;
        $response['success_message'] = "Data Fetch";
        return response()->json($response);
    }

    public function updateGstAddress(Request $request)
    {
        
        try {
            DB::beginTransaction();
            $old_branch = explode(',', $request->old_branch);
            $result = array_diff($old_branch,$request->branch_id);
            if(!empty($result)){
                foreach($result as $val){
                    Location::where('id', $val)->update(['gst_registered_id' => NULL]);
                }
            }

            $gst = $request->file('upload_gst');
            if (!empty($gst)) {
                $gstfile = $gst->getClientOriginalName();
                $gst->move(public_path('drs/company_gst'), $gstfile);
            } else {
                $getgstimg = GstRegisteredAddress::where('id',$request->gst_id)->first();
                $gstfile = $getgstimg->upload_gst;
            }

            GstRegisteredAddress::where('id', $request->gst_id)->update(['gst_no' => $request->gst_no, 'state' => $request->state, 'address_line_1' => $request->address_line_1,'address_line_2' => $request->address_line_2, 'upload_gst' => $gstfile]);

            foreach($request->branch_id as $branch){
                Location::where('id', $branch)->update(['gst_registered_id'=> $request->gst_id]);
            }

            $response['success'] = true;
            $response['success_message'] = "Address Data successfully";
            $response['error'] = false;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }
    public function viewGstAddress(Request $request)
    {
        $id = $request->gst_id;
        $gst_num = GstRegisteredAddress::with('Branch')->where('id', $id)->first(); 

        $response['gst_num'] = $gst_num;
        $response['success'] = true;
        $response['success_message'] = "Data Fetch";
        return response()->json($response);
    }
}