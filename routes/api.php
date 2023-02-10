<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\Auth\TransactionSheetsController;

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

Route::group(['middleware' =>  ['jwt.verify'],'prefix' => 'auth'], function ($router) {

Route::get('transaction_sheets/{id}', [TransactionSheetsController::class, 'show']);
Route::post('/upload-img/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@uploadImage')->name('api.auth.upload-img.transaction_sheets');
Route::get('/transaction_sheets/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@show')->name('api.auth.show.transaction_sheets');
Route::put('/task-start/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@taskStart')->name('api.auth.taskStart.transaction_sheets');
Route::put('/task-ack/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@taskAcknowledge')->name('api.auth.taskAcknowledge.transaction_sheets');
Route::get('/single-task/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@singleTask')->name('api.auth.singleTask.transaction_sheets');
Route::post('/update-deliveryDetails/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@updateDeliveryDetails')->name('api.auth.updateDeliveryData.transaction_sheets');
Route::put('task-success/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@taskSuccessful')->name('api.auth.taskSuccessful.transaction_sheets');
Route::post('task-cancel/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@taskCancel')->name('api.auth.taskCancel.transaction_sheets');
Route::post('verified-lr/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@verifiedLr')->name('api.auth.verifiedLr.transaction_sheets');
Route::any('img-delete/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@imgDelete')->name('api.auth.imgDelete.transaction_sheets');
Route::post('store-coordinates/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@storeCoordinates')->name('api.auth.verifiedLr.transaction_sheets');

});


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth' 
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);   
});





