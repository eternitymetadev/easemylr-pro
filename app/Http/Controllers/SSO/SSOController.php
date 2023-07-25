<?php

namespace App\Http\Controllers\SSO;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Illuminate\Support\Facades\Session;


class SSOController extends Controller
{

    //     Array
    // (
    //     [_token] => i9GEP9e7OLDDEDTWw86QmyicL59eZwYQqGLiyBoJ
    //     [state] => ruxfSG37cZG5bZBhFDJhRAydoLqNUEbWATz94OmP
    //     [client_id] => 994feaf2-aa7d-4359-b454-4497f2a14318
    //     [auth_token] => KzjWJP82QX0lP8eH
    // )
    // Client ID: 9965d546-83de-432f-a5e6-96182548829a
    // Client secret: 5OylSHvfuQxasok3dR0YweriWGy9lN06gEDgAqio
    public function getLogin(Request $request)
    {
        $request->session()->put("state", $state =  Str::random(40));
        $query = http_build_query([
            "client_id" => "9965d546-83de-432f-a5e6-96182548829a",
            "redirect_uri" => "http://localhost:8081/callback",
            "response_type" => "code",
            "scope" => config("auth.scopes"),
            "state" => $state,
            "prompt" => true
        ]);
        return redirect(config("auth.sso_host") .  "/oauth/authorize?" . $query);
    }
    public function getCallback(Request $request)
    {
        $state = $request->session()->pull("state");
        // return $state;

        throw_unless(strlen($state) > 0 && $state == $request->state, InvalidArgumentException::class);

        $response = Http::asForm()->post(
            config("auth.sso_host") .  "/oauth/token",
            [
                "grant_type" => "authorization_code",
                "client_id" => "9965d546-83de-432f-a5e6-96182548829a",
                "client_secret" => "5OylSHvfuQxasok3dR0YweriWGy9lN06gEDgAqio",
                "redirect_uri" => "http://localhost:8081/callback",
                "code" => $request->code
            ]
        );
        $request->session()->put($response->json());
        return redirect(route("sso.connect"));
    }
    public function connectUser(Request $request)
    {
        $access_token = $request->session()->get("access_token");
        // return $access_token;
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer " . $access_token
        ])->get(config("auth.sso_host") .  "/api/user");
        $userArray = $response->json();

        try {
            $email = $userArray['email'];
        } catch (\Throwable $th) {
            return redirect("login")->withError("Failed to get login information! Try again.");
        }
        $user = User::where("login_id", $email)->first();
        if (!$user) {
            $user = new User;
            // echo "<pre>";
            // print_r($userArray);
            // exit;
            $user->name = $userArray['name'];
            $user->password = "check_parent_table";
            $user->email = $userArray['email'];
            $user->email_verified_at = $userArray['email_verified_at'];
            $user->save();
        }
        Auth::login($user);
        return redirect('/admin/dashboard');
        // return redirect(route("home"));
    }

    // public function redirectUrl()
    // {
    //   $data=   Session::get('access_token');
    //   return $data;
    // }

    public function connectUserdirectly(Request $request)
    {
        $data = $request->all();

        $access_token = $data["access_token"];
        // Session::put('access_token', $access_token);

        // $access_token="Dasd";
        // return $access_token;
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer " . $access_token
        ])->get(config("auth.sso_host") .  "/api/user");


        // return $response;
        $userArray = $response->json();
        // return $userArray;
        try {
            $email = $userArray['email'];
            return $email;
        } catch (\Throwable $th) {
            return redirect("login")->withError("Failed to get login information! Try again.");
        }
    }

    public function login_user($email)
    {
        // Session::put('user_email', $email);
        // return $email;
        // Store data in the session
        session(['user_email' => $email]);
        return redirect()->route('login_to_portal', ['email' => $email]);
    }

    public function portal_login($email)
    {
        $user = User::where("login_id", $email)->first();
        Auth::login($user, true);
        if (Auth::check()) {
            $user = Auth::user();
            return redirect('/admin/dashboard');
        } else {
            return "User Doesn't Exists";
        }
        // return redirect(route("home"));
    }

    public function assign_role(Request $request)
    {
        $data = $request->all();
        // $access_token = $data["access_token"];
        $register_user = array();
        $register_user['email'] = $data['email'];
        $register_user['login_id'] = $data['email'];
        $register_user['password'] = $data['password'];
        // $register_user['role'] = $data['role'];
        $register_user['name'] = $data['name'];
        $register_user['status'] = '1';
        $assignRole = "";

        if ($data['role'] == "admin user") {
            $register_user['role_id'] = 1;
        } else {
            $register_user['role_id'] = "";
        }
        // return $assignRole;

        // $assignRole= "hr admin";

        // return $register_user;

        // $response = Http::withHeaders([
        //     "Accept" => "application/json",
        //     "Authorization" => "Bearer " . $access_token
        // ])->get(config("auth.sso_host") .  "/api/user");

        $user = User::where("email", $data['admin_email'])->first();


        // try {
        if (!empty($user)) {

            Auth::login($user, true);
            if (Auth::check()) {
                // return "33";
                $user = User::create($register_user);
                // if ($assignRole == "admin") {
                //     $user->assignRole($assignRole);
                // }
                return 1;
            }
            // return $email;

        } else {
            return 0;
        }
        // } catch (\Throwable $th) {
        //     return redirect("login")->withError("Failed to get login information! Try again.");
        // }
    }

    public function remove_role(Request $request)
    {
        $data = $request->all();
        // $access_token = $data["access_token"];
        $register_user = array();
        $register_user['email'] = $data['email'];


        // if ($data['role'] == "ter user") {
        //     $assignRole = "tr admin";
        // }

        // return $register_user;

        // $response = Http::withHeaders([
        //     "Accept" => "application/json",
        //     "Authorization" => "Bearer " . $access_token
        // ])->get(config("auth.sso_host") .  "/api/user");

        $user = User::where("email", $data['admin_email'])->first();


        // try {
        if (!empty($user)) {

            Auth::login($user, true);
            if (Auth::check()) {
                // return "33";
                $user = User::where('email', $register_user['email'])->delete();
                // $user->removeRole($assignRole);
                return 1;
            }
            // return $email;

        } else {
            return 0;
        }
        // } catch (\Throwable $th) {
        //     return redirect("login")->withError("Failed to get login information! Try again.");
        // }
    }
}
