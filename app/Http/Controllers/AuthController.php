<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
            $user = Driver::where('login_id' ,$request->login_id)->first();
            if($user->login_id == $request->login_id && $user->driver_password == $request->password){
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            echo 'kk';
            // return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
