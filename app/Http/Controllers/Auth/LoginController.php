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
        $data=$request->all();
        $data['portal_id']=2;
        $httpHost = $_SERVER['HTTP_HOST'];

        // $httpHost ="Dw";

        if ($httpHost === 'localhost:8081' || $httpHost === 'localhost') {
            $url = 'http://localhost:8000/api/custom_portal_signin';
        } else {
            $url = 'https://api.etsbeta.com/api/custom_portal_signin';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('login' => $data['login_id'], 'password' => $data['password'], 'portal_id' => $data['portal_id']),
        ));

        $response_api = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response_api, true);


        if ($res['status'] == "fail") {
            $response['success'] = false;
            $response['error_message'] = $res['msg'];
            $response['error'] = true;
            $response['email_error'] = true;
            return response()->json($response);
        }

        if ($res['status'] == "fail-password") {
            $response['success'] = false;
            $response['error_message'] = $res['msg'];
            $response['error'] = true;
            $response['email_error'] = true;
            return response()->json($response);
        }

        if ($res['status'] == "success-login") {
       
        if (Auth::attempt(['login_id' => $res['email'], 'password' => $request->input('password')]))
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
          
    } else {
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
