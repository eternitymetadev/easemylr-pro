<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;
use Auth;
use URL;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $rules = array(
            'login_id' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $errors                  = $validator->errors();
            $response['success']     = false;
            $response['formErrors']  = true;
            $response['errors']      = $errors;
        }

        // $remember = $request->has('remember') ? true : false;

        $credentials = $request->only('login_id', 'password');
        if (Auth::attempt(['login_id' => $request->input('login_id'), 'password' => $request->input('password')]))
        {
            // Authentication passed...
            $getauthuser = Auth::user();
            if($getauthuser->status == 0){
                $response['success'] = false;
                $response['error_message'] = "Please contact the system owner if you need access.";
                $response['error'] = true;
                $response['email_error'] = true;
                Auth::logout();
                return response()->json($response);
            }
            
            if($getauthuser->role_id == 1){
                $url = URL::to('/admin/dashboard');    
            }
            else if($getauthuser->role_id == 2) {
                $url = URL::to('/branch-manager/dashboard');  
            }
            else if($getauthuser->role_id == 3) {
                $url = URL::to('/regional-manager/dashboard');  
            }
            else if($getauthuser->role_id == 4) {
                $url = URL::to('/branch-user/dashboard');  
            } 
            else if($getauthuser->role_id == 5) {
                $url = URL::to('/account-manager/dashboard');  
            }
            else if($getauthuser->role_id == 6) {
                $url = URL::to('/client-account/consignments');  
            }
            else if($getauthuser->role_id == 7) {
                $url = URL::to('/client-user/consignments');
            }
            // Log::channel('customlog')->info('Activity: User Logged In, Name: '.Auth::user()->name);
            $response['success'] = true;
            $response['page'] = "login";
            $response['success_message'] = "Login Successfully";
            $response['error'] = false;
            $response['redirect_url'] = $url;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Incorrect login id and password";
            $response['error'] = true;
            $response['email_error'] = true;
        }
        return response()->json($response);
    }

    public function logout(Request $request){
        $user = Auth::user();
        $user_name = "";
        if(isset($user->name))
        {
            $user_name = $user->name;
        }
        Auth::logout();
        // Log::channel('customlog')->info('Activity: User Logged Out, Name: '.$user_name);
        return redirect('/login');
    }
    
}
