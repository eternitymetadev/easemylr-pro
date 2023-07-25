<?php
header('Access-Control-Allow-Origin: https://eml-report.easemyorder.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
header('Access-Control-Allow-Headers: *');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSO\SSOController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    
});

Route::get('/drivers', 'App\Http\Controllers\Api\DriversController@index')->name('api.index.drivers');

Route::post('/connect_user', [SSOController::class, 'connectUserdirectly']);
Route::get('/login_user/{email}', [SSOController::class, 'login_user']);

Route::post('/assign_role', [SSOController::class, 'assign_role']);

Route::post('/remove_role', [SSOController::class, 'remove_role']);