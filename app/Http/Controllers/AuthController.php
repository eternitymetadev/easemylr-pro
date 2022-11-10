<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Driver;
use Validator;

class AuthController extends Controller

{

    /**

     * Create a new AuthController instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }



    /**

     * Get a JWT via given credentials.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function login(Request $request)

    {

        $credentials = [

            'login_id' => $request->get('login_id'),
            'password' => $request->get('password')

        ];
       
        $validator = Validator::make($credentials, [
            'login_id' => 'required',
            'password' => 'required',

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



    /**

     * Register a User.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function register(Request $request)

    {



        $validator = Validator::make($request->all(), [

        'name' => 'string',

        'email' => 'string',

        'phone' => 'string',

        'license_number' => 'string',

        'license_image' => 'string',

        'team_id' => 'string',

        'fleet_id' => 'string',

        'login_id' => 'string|between:2,100',

        'status' => 'integer',

        'created_at' => 'date_format:Y-m-d H:i:s',

        'updated_at' => 'date_format:Y-m-d H:i:s',

        ]);


        if ($validator->fails()) {

            return response()->json($validator->errors()->toJson(), 400);

        }


        $user = Drivers::create(array_merge(

            $validator->validated(),

            ['password' => bcrypt($request->password)]

        ));

        return response([

            'status' => 'success',

            'code' => 1,

            'message' => "Drivers successfully registered",

            'data' => $user

        ], 201);

    }





    /**

     * Log the user out (Invalidate the token).

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function logout()

    {

        auth()->logout();



        return response()->json(['message' => 'User successfully signed out']);

    }



    /**

     * Refresh a token.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function refresh()

    {

        return $this->createNewToken(auth()->refresh());

    }



    /**

     * Get the authenticated User.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function userProfile()

    {

        try {

            return response([

                'status' => 'success',

                'code' => 1,

                'message' => "Token Generated",

                'data' => auth()->user()

            ], 200);

        } catch (\Exception $exception) {

            return response([

                'status' => 'error',

                'code' => 0,

                'message' => "Failed to get user profile. {$exception->getMessage()}"

            ], 500);

        }

    }



    /**

     * Get the token array structure.

     *

     * @param string $token

     *

     * @return \Illuminate\Http\JsonResponse

     */

    protected function createNewToken($token)

    {

        return response([

            'status' => 'success',

            'code' => 1,

            'message' => "Token Generated",

            'data' => [

                'access_token' => $token,

                'token_type' => 'bearer',

                'expires_in' => auth()->factory()->getTTL() * 60 * 60 * 60,

                'user' => auth()->user()

            ]

        ], 200);

    }

}







