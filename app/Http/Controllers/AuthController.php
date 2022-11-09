<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drivers;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    
    public function login(Request $request)
    {
        $credentials = [
            'login_id' => $request->get('login_id'),
            'password' => $request->get('password')
        ];
       
        $validator = Validator::make($credentials, [
            'login_id' => 'required|string|min:6',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => $validator->errors()
            ], 422);

        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Unauthorized"
            ], 401);
        }
        return $this->createNewToken($token);

    }
}
