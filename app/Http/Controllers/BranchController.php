<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\State;
use App\Models\Consigner;
use App\Models\Consignee;
use App\Models\Broker;
use App\Models\User;
use App\Models\BranchImage;
use App\Models\Location;
use App\Models\BranchConnectivity;
use DB;
use URL;
use Helper;
use Validator;
use Storage;
use Auth;
use Config;

class BranchController extends Controller
{
    public function __construct()
    {
      $this->title =  "Branches Listing";
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
        $peritem = 20;
        $query = Branch::query();
        $authuser = Auth::user();
        if($authuser->role_id == 2){
            $branches = $query->where('id',$authuser->branch_id)->orderBy('id','DESC')->with('State')->paginate($peritem);
        }
        else{
            $branches = $query->orderBy('id','DESC')->with('State')->paginate($peritem);
        }
        return view('branch.branch-list',['branches'=>$branches,'prefix'=>$this->prefix])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $states = Helper::getStates();
        return view('branch.create-branch',['states'=>$states, 'prefix'=>$this->prefix]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();

        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'name' => 'required',
            // 'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'email' => 'required|unique:branches',
            'files.*' => 'mimes:jpg,jpeg,png|max:4096',
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

        $branchsave['name']             = $request->name;
        $branchsave['address']          = $request->address;
        $branchsave['gstin_number']     = $request->gstin_number;
        $branchsave['city']             = $request->city;
        $branchsave['district']         = $request->district;
        $branchsave['postal_code']      = $request->postal_code;
        $branchsave['state_id']         = $request->state_id;
        $branchsave['consignment_note'] = $request->consignment_note;
        $branchsave['email']            = $request->email;
        $branchsave['phone']            = $request->phone;
        $branchsave['status']           = $request->status;          

        $savebranch = Branch::create($branchsave); 
        if($savebranch)
        {
            // upload branch images
                if($request->hasfile('files')){
                    $files = $request->file('files');
                    foreach($files as $file){
                        $path = 'public/images/branch';
                        $name = Helper::uploadImage($file,$path);
                        $data[] = [
                            'name' =>  $name,
                            'branch_id' => $savebranch->id
                        ];
                    }
                    $savebranch->images()->insert($data);
                }

            $response['success'] = true;
            $response['success_message'] = "Branch Added successfully";
            $response['error'] = false;
            $response['resetform'] = true;
            $response['page'] = 'branch-create'; 
        }
        else{
            $response['success'] = false;
            $response['error_message'] = "Can not created branch please try again";
            $response['error'] = true;
        }
        DB::commit();
        }catch(Exception $e){
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
    public function show($branch)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($branch);
        $getbranch = Branch::where('id',$id)->with(['images','GetState'])->first();
        return view('branch.view-branch',['prefix'=>$this->prefix,'title'=>$this->title,'getbranch'=>$getbranch]);
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
        $getbranch = Branch::where('id',$id)->with(['images'])->first();
        // dd($getbranch);
        return view('branch.update-branch')->with(['prefix'=>$this->prefix,'getbranch'=>$getbranch,'states'=>$states]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBranch(Request $request)
    {
        try { 
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
              'name' => 'required',
              'email'  => 'required',
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

            $branchsave['name']             = $request->name;
            $branchsave['address']          = $request->address;
            $branchsave['gstin_number']     = $request->gstin_number;
            $branchsave['city']             = $request->city;
            $branchsave['district']         = $request->district;
            $branchsave['postal_code']      = $request->postal_code;
            $branchsave['state_id']         = $request->state_id;
            $branchsave['consignment_note'] = $request->consignment_note;
            $branchsave['email']            = $request->email;
            $branchsave['phone']            = $request->phone;
            $branchsave['status']           = $request->status;
            
            Branch::where('id',$request->branch_id)->update($branchsave);

            // upload branch images
            if($request->hasfile('files')){
                $files = $request->file('files');
                foreach($files as $file){
                    $path = 'public/images/branch';
                    $name = Helper::uploadImage($file,$path);
                    $data[] = [
                        'name' =>  $name,
                        'branch_id' => $request->branch_id
                    ];
                }
                BranchImage::insert($data);
            }

            $url    =   URL::to($this->prefix.'/branches');

            $response['page'] = 'branch-update';
            $response['success'] = true;
            $response['success_message'] = "Branch Updated Successfully";
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteBranch(Request $request)
    {
        $getconsignee = Consignee::where('branch_id',$request->branchid)->get();
        $getconsigner = Consigner::where('branch_id',$request->branchid)->get();
        $getbroker    = Broker::where('branch_id',$request->branchid)->get();
        $getuser      = User::where('branch_id',$request->branchid)->get();

        if(!empty($getconsignee) && count($getconsignee) > 0){
            $response['success'] = false;
        }else if(!empty($getconsigner)&& count($getconsigner) > 0){
            $response['success'] = false;
        }else if(!empty($getbroker)&& count($getbroker) > 0){
            $response['success'] = false;
        }else if(!empty($getuser)&& count($getuser) > 0){
            $response['success'] = false;
        }else{
            Branch::where('id',$request->branchid)->delete();

            $response['success']         = true;
            $response['success_message'] = 'Branch deleted successfully';
            $response['error']           = false;
        }
        return response()->json($response);
    }

    // Delete branch image from edit view
    public function deletebranchImage(Request $request)
    {
        $path = 'public/images/branch';
        $image_path=Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$path;
        $getimagename = BranchImage::where('id',$request->branchimgid)->first();
        $image_path=$image_path.'/'.$getimagename->name;
        if (\File::exists($image_path)) {

            unlink($image_path);
        }

      $deleteimage = BranchImage::where('id',$request->branchimgid)->delete();

      $response['success']         = true;
      $response['success_message'] = 'Branch Image deleted successfully';
      $response['error']           = false;

      return response()->json($response);
    }

    public function branchConnectivity(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = BranchConnectivity::query();
        $authuser = Auth::user();
        $cc = explode(',',$authuser->branch_id);
       
        $locations = $query->with('Location')->orderBy('id','ASC')->get();
        $branchs = Location::all();

        return view('branch.branch-connectivity',['locations'=>$locations,'branchs'=>$branchs,'prefix'=>$this->prefix,'title'=>$this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function addBranchConnectivity(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //  'efpl_hub' => 'required|unique:branch_connectivities',
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
            
            $check_branch = BranchConnectivity::where('efpl_hub',$request->hub)->first();
            if(!empty($check_branch)){

                $response['validation'] = true;
                $response['error'] = true;
                $response['error_message'] = 'Branch Already Exists';
                return response()->json($response);

            }

            $connective_hub = implode(',',$request->direct_connectivity);

            $savehub['efpl_hub'] = $request->hub;
            $savehub['direct_connectivity'] = $connective_hub;
            $savehub['status'] = 1;

            $saveconnectivity = BranchConnectivity::create($savehub);

            if ($saveconnectivity) {
                $url = $this->prefix . '/settings/branch-address';
                $response['success'] = true;
                $response['success_message'] = "Branch Added successfully";
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

    public function editBranchConnectivity(Request $request){
        $get_branch = BranchConnectivity::where('id', $request->branch_id)->first();
        if($get_branch){
            $response['success'] = true;
            $response['branch_data'] = $get_branch;
            $response['error'] = false;
        }else{
            $response['success']         = false;
            $response['success_message'] = 'data not found';
            $response['error']           = true;
        }
        return response()->json($response);
    }

    public function updateBranchConnectivity(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //  'efpl_hub' => 'required|unique:branch_connectivities',
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
            
            $check_branch = BranchConnectivity::where('id','!=',$request->branchid_hidden)->where('efpl_hub', $request->hub)->first();
            if(!empty($check_branch)){

                $response['validation'] = true;
                $response['error'] = true;
                $response['error_message'] = 'Branch Already Exists';
                return response()->json($response);

            }

            $connective_hub = implode(',',$request->direct_connectivity);

            $savehub['efpl_hub'] = $request->hub;
            $savehub['direct_connectivity'] = $connective_hub;
            $savehub['status'] = 1;

            $saveconnectivity = BranchConnectivity::where('id', $request->branchid_hidden)->update($savehub);

            if ($saveconnectivity) {
                $url = $this->prefix . '/settings/branch-address';
                $response['success'] = true;
                $response['success_message'] = "Branch Updated successfully";
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

    public function routeList(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $branches = Location::all();
        // $query = BranchConnectivity::query();

        $connected_hubs = BranchConnectivity::all();

        $graph = [];
        foreach ($connected_hubs as $hub) {
            $location = $hub->efpl_hub;
            $neighbors = explode(',', $hub->direct_connectivity);
            $graph[$location] = $neighbors;
        }

        $locations = [];
        foreach ($connected_hubs as $hub) {
            $location = $hub->efpl_hub;
            // $neighbors = explode(',', $hub->direct_connectivity);
            $locations[$location] = $location;
        }

        $no = 1;
        // Initialize visited and route arrays
    $routeData = "<table style='width: 100%; border:1px solid black; font-size: 16px'>
                    <thead>
                        <tr>
                            <th style='width: 70px; border:1px solid black;'>Sr.</th>
                            <th style='width: 120px; border:1px solid black;'>From</th>
                            <th style='width: 120px; border:1px solid black;'>To</th>
                            <th style='width: auto; border:1px solid black;'>Route</th>
                        </tr>
                    </thead>
                <tbody>";
    // $routeData .= "<table class=''><tr><td rowspan="2">d</td><td>ss</td></tr><tr><td>ss</td></tr>";
    foreach ($locations as $startkey => $startingLocation) {
        foreach ($locations as $endkey => $endingLocation) {
            if ($startingLocation !== $endingLocation) {
                // Initialize visited and route arrays
                $visited = array_fill_keys(array_keys($graph), false);

                $route = [];
                // Find routes using DFS
                $routes = $this->findRoutes($graph, $startingLocation, $endingLocation, $visited, $route);

                $start_branch = DB::table('locations')->where('id', $startingLocation)->first();
                $end_branch = DB::table('locations')->where('id', $endingLocation)->first();

                // Add the routes to the response string
                // $routeData .= "<h3>Routes from $start_branch->name to $end_branch->name:</h3><br/>";
    
                if (empty($routes)) {
                    // $routeData .= "<p>No route available.</p>";
                } else {
                    foreach ($routes as $route) {
                        $branch_name = [];
                       
                        foreach($route as $key => $r){
                        $getbranch = DB::table('locations')->where('id', $r)->first();
                        $branch_name[]= $getbranch->name;
                        }
                        $routeData .= "<tr>
                                            <td style='border:1px solid black;'>$no</td>
                                            <td style='border:1px solid black;'>$start_branch->name</td>
                                            <td style='border:1px solid black;'>$end_branch->name</td>
                                            <td style='border:1px solid black;'>".implode('  >  ', $branch_name)."</td>
                                        </tr>";
                        
                        // $routeData .= "<p>" . implode(' -> ', $route) . "</p>";
                        // $routeData .= "<p>" . implode(' -> ', $branch_name) . "</p>";
                        $no = $no + 1;
                    }
                }
                // Reverse the starting and ending locations to find vice versa routes
            }
        }
    }
    $routeData .= "</tbody></table>";

        return view('branch.route-list',['routeData'=>$routeData,'branches'=>$branches,'prefix'=>$this->prefix,'title'=>$this->title,'peritem' => $peritem]);
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
                foreach ($graph[$start] as $keyneighbor=>$neighbor) {
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

}
