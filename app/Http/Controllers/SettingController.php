<?php

namespace App\Http\Controllers;

use App\Exports\ZoneExport;
use App\Models\BranchAddress;
use App\Models\BranchConnectivity;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use App\Models\Consigner;
use App\Models\Consignee;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
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

        } else {
            $branchaddvalue = BranchAddress::where(['meta_key' => 'addressdata_key'])->first();
            return view('settings.branch-address', ['branchaddvalue' => $branchaddvalue, 'prefix' => $this->prefix]);
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

            $query = $query->where('status', 1);

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('postal_code', 'like', '%' . $search . '%');
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

        $query = $query->with('Branch')
            ->where('status', 1);

        $zones = $query->orderBy('id', 'DESC')->paginate($peritem);
        $zones = $zones->appends($request->query());

        return view('settings.postal-code-edit', ['peritem' => $peritem, 'prefix' => $this->prefix, 'zones' => $zones, 'segment' => $this->segment, 'all_districts' => $all_districts, 'branchs' => $branchs, 'all_states' => $all_states]);
    }

    public function editPostalCode(Request $request)
    {
        $id = $request->postal_id;
        $postal_code = Zone::where('id', $id)->first();

        $response['zone_data'] = $postal_code;
        $response['success'] = true;
        $response['success_message'] = "Data Fetch";
        return response()->json($response);
    }

    public function updatePostalCode(Request $request)
    {
        try {
            DB::beginTransaction();
            Zone::where('id', $request->zone_id)->update(['district' => $request->district, 'state' => $request->state, 'primary_zone' => $request->primary_zone, 'hub_transfer' => $request->hub_transfer]);

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
    public function exportExcel()
    {
        return Excel::download(new ZoneExport, 'zones.csv');
    }

    public function updateDistrictHub(Request $request)
    {

        try {
            DB::beginTransaction();

            $get_location = Location::where('id', $request->branch_id)->first();

            Zone::where('state',$request->state_id)->whereIn('district', $request->district)->update(['hub_transfer' => $get_location->name, 'hub_nickname' => $get_location->id]);

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
}