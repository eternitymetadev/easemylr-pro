<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserPermission;
use App\Models\Permission;
use App\Models\Location;
use App\Models\RegionalClient;
use DB;
use URL;
use Helper;
use Hash;
use Crypt;
use Validator;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public $prefix;
    public $title;

    public function __construct()
    {
      $this->title =  "Users";
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
        $query = User::query();
        $data = $query->with('UserRole')->orderby('id','DESC')->get();
        return view('users.user-list',['data'=>$data,'prefix'=>$this->prefix,'title'=>$this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
        $this->prefix = request()->route()->getPrefix();
        $this->pagetitle =  "Create";
        $getpermissions = Permission::all();
        $getroles = Role::all();
        $branches = Helper::getLocations();
        $regionalclients = Helper::getRegionalClients();

        return view('users.create-user',['getroles'=>$getroles, 'getpermissions'=>$getpermissions, 'branches'=>$branches, 'regionalclients'=>$regionalclients, 'prefix'=>$this->prefix, 'title'=>$this->title, 'pagetitle'=>$this->pagetitle]);
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
            'login_id' => 'required|unique:users,login_id',
            'email'  => 'required',
            'password' => 'required',
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
        if(!empty($request->name)){
            $usersave['name']   = $request->name;
        }
        if(!empty($request->login_id)){
            $usersave['login_id']   = $request->login_id;
        }
        if(!empty($request->email)){
            $usersave['email']  = $request->email;
        }
        if(!empty($request->password)){
            $usersave['password'] = Hash::make($request->password);
        }

        if(!empty($request->role_id)){
            $usersave['role_id']   = $request->role_id;
        }
        $usersave['user_password'] = $request->password;
        // $usersave['branch_id']     = $request->branch_id;
        $usersave['phone']         = $request->phone;
        if(!empty($request->branch_id)){
            $branch = $request->branch_id;
            $usersave['branch_id']  = implode(',',$branch);
        }
        if(!empty($request->regionalclient_id)){
            $regclients = $request->regionalclient_id;
            $usersave['regionalclient_id'] = implode(',', $regclients);
        }
        if(!empty($request->permisssion_id)){
            $news = $request->permisssion_id;
            $usersave['assign_permission'] = implode(',', $news);
        }
        $usersave['status']  = "1";

        $saveuser = User::create($usersave); 
        if($saveuser)
        {
            $userid = $saveuser->id;
            if(!empty($request->permisssion_id)){         
              foreach ($request->permisssion_id as $key => $permissionvalue){
                  $savepermissions[] = [
                    'user_id'=>$userid,
                    'permisssion_id'=>$permissionvalue,
                  ];   
                }
                UserPermission::insert($savepermissions); 
            }
            $url    =   URL::to($this->prefix.'/users');
            $response['success'] = true;
            $response['success_message'] = "Users Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'user-create';
            $response['redirect_url'] = $url;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created user please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        $this->prefix = request()->route()->getPrefix();
        $this->pagetitle =  "View Details";
        $id = decrypt($user);
        $getuser = User::where('id',$id)->with('UserRole')->first();

        $branch = $getuser->branch_id;
        $branch_ids  = explode(',',$branch);
        $branches = Location::whereIn('id', $branch_ids)->pluck('name');

        return view('users.view-user',['prefix'=>$this->prefix,'title'=>$this->title,'getuser'=>$getuser,'branches'=>$branches,'pagetitle'=>$this->pagetitle]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
    {
        $this->prefix = request()->route()->getPrefix();
        $this->pagetitle =  "Update";
        $id = decrypt($user); 
        $getroles = Role::all();
        $getpermissions = Permission::all();
        
        $allpermissioncount = Permission::all()->count();
        $getuserpermissions = UserPermission::where('user_id',$id)->get();
        $branches = Helper::getLocations();
        
        $u = array();
        if(count($getuserpermissions) > 0)
        {
            foreach($getuserpermissions as $us)
            {
                $u[] = $us['permisssion_id'];
            }
        }
        $getuser = User::where('id',$id)->first();
        return view('users.update-user')->with(['prefix'=>$this->prefix,'title'=>$this->title, 'pagetitle'=>$this->pagetitle, 'getuser'=>$getuser,'getroles'=>$getroles,'getpermissions'=>$getpermissions,'getuserpermissions'=>$u,'allpermissioncount'=>$allpermissioncount,'branches'=>$branches]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        
    public function updateUser(Request $request)
    {
      try { 
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'name' => 'required',
            'login_id' => 'required',
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

        $getpass = User::where('id',$request->user_id)->get();

        $usersave['name']       = $request->name;
        $usersave['email']      = $request->email;
        $usersave['login_id']   = $request->login_id;
        $usersave['role_id']    = $request->role_id;
        $usersave['phone']      = $request->phone;
        // $usersave['branch_id']  = $request->branch_id;
        $branch = $request->branch_id;
        $usersave['branch_id']  = implode(',',$branch); 

        if(!empty($request->password)){
            $usersave['password'] = Hash::make($request->password);
            $usersave['user_password'] = $request->password;
        }else if(!empty($getpass->password)){
            $usersave['password'] = $getpass->password;
        }
            
        User::where('id',$request->user_id)->update($usersave);

            $userid = $request->user_id;
            UserPermission::where('user_id',$userid)->delete();
            if(!empty($request->permisssion_id)){                
                foreach ($request->permisssion_id as $key => $permissionvalue)  {
                    $savepermissions[] = [
                      'user_id'=>$userid,
                      'permisssion_id'=>$permissionvalue,
                    ];   
                }
                UserPermission::insert($savepermissions); 
            }

            $getsavedusers = User::where('id',$request->user_id)->first();
            $url    =   URL::to($this->prefix.'/users');

            $response['page'] = 'user-update';
            $response['success'] = true;
            $response['success_message'] = "User Updated Successfully";
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(Request $request)
    {
        UserPermission::where('user_id',$request->userid)->delete();
        User::where('id',$request->userid)->delete();

        $response['success']         = true;
        $response['success_message'] = 'User deleted successfully';
        $response['error']           = false;
        return response()->json($response);
    }

    // get regional clients on location change
    public function regClients(Request $request)
    {
        $getclients = RegionalClient::select('id', 'name', 'baseclient_id', 'location_id')->where(['location_id' => $request->branch_id, 'status' => '1'])->get();
        
        if ($getclients) {
            $response['success'] = true;
            $response['success_message'] = "Client list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getclients;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch client list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }
    
}
