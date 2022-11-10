<?php



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

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





Route::group(['middleware' => 'api'], function($router) {

    Route::get('/', function() {

        return response()->json([

            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'

        ]);

    })->name('api.hello');

});



Route::group(['middleware' =>  ['jwt.auth'],'prefix' => 'auth'], function ($router) {

Route::post('/upload/file','App\Http\Controllers\Api\Auth\FileUploadController@upload')->name('api.auth.upload.file');

Route::post('/upload/image','App\Http\Controllers\Api\Auth\FileUploadController@uploadimage')->name('api.auth.upload.image');

    

Route::get('/transaction_sheets', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@index')->name('api.auth.index.transaction_sheets');

Route::get('/transaction_sheets/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@show')->name('api.auth.show.transaction_sheets');

Route::post('/transaction_sheets', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@store')->name('api.auth.store.transaction_sheets');

Route::put('/transaction_sheets/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@update')->name('api.auth.update.transaction_sheets');

Route::delete('/transaction_sheets/{id}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@destroy')->name('api.auth.delete.transaction_sheets');

Route::get('/transaction_sheets/search/{search}', 'App\Http\Controllers\Api\Auth\TransactionSheetsController@search')->name('api.auth.search.transaction_sheets');

Route::get('/drivers', 'App\Http\Controllers\Api\Auth\DriversController@index')->name('api.auth.index.drivers');

Route::get('/drivers/{id}', 'App\Http\Controllers\Api\Auth\DriversController@show')->name('api.auth.show.drivers');

Route::post('/drivers', 'App\Http\Controllers\Api\Auth\DriversController@store')->name('api.auth.store.drivers');

Route::put('/drivers/{id}', 'App\Http\Controllers\Api\Auth\DriversController@update')->name('api.auth.update.drivers');

Route::delete('/drivers/{id}', 'App\Http\Controllers\Api\Auth\DriversController@destroy')->name('api.auth.delete.drivers');

Route::get('/drivers/search/{search}', 'App\Http\Controllers\Api\Auth\DriversController@search')->name('api.auth.search.drivers');

Route::get('/consignment_notes', 'App\Http\Controllers\Api\Auth\ConsignmentNotesController@index')->name('api.auth.index.consignment_notes');

Route::get('/consignment_notes/{id}', 'App\Http\Controllers\Api\Auth\ConsignmentNotesController@show')->name('api.auth.show.consignment_notes');

Route::post('/consignment_notes', 'App\Http\Controllers\Api\Auth\ConsignmentNotesController@store')->name('api.auth.store.consignment_notes');

Route::put('/consignment_notes/{id}', 'App\Http\Controllers\Api\Auth\ConsignmentNotesController@update')->name('api.auth.update.consignment_notes');

Route::delete('/consignment_notes/{id}', 'App\Http\Controllers\Api\Auth\ConsignmentNotesController@destroy')->name('api.auth.delete.consignment_notes');

Route::get('/consignment_notes/search/{search}', 'App\Http\Controllers\Api\Auth\ConsignmentNotesController@search')->name('api.auth.search.consignment_notes');

Route::get('/consignment_items', 'App\Http\Controllers\Api\Auth\ConsignmentItemsController@index')->name('api.auth.index.consignment_items');

Route::get('/consignment_items/{id}', 'App\Http\Controllers\Api\Auth\ConsignmentItemsController@show')->name('api.auth.show.consignment_items');

Route::post('/consignment_items', 'App\Http\Controllers\Api\Auth\ConsignmentItemsController@store')->name('api.auth.store.consignment_items');

Route::put('/consignment_items/{id}', 'App\Http\Controllers\Api\Auth\ConsignmentItemsController@update')->name('api.auth.update.consignment_items');

Route::delete('/consignment_items/{id}', 'App\Http\Controllers\Api\Auth\ConsignmentItemsController@destroy')->name('api.auth.delete.consignment_items');

Route::get('/consignment_items/search/{search}', 'App\Http\Controllers\Api\Auth\ConsignmentItemsController@search')->name('api.auth.search.consignment_items');

Route::get('/consignees', 'App\Http\Controllers\Api\Auth\ConsigneesController@index')->name('api.auth.index.consignees');

Route::get('/consignees/{id}', 'App\Http\Controllers\Api\Auth\ConsigneesController@show')->name('api.auth.show.consignees');

Route::post('/consignees', 'App\Http\Controllers\Api\Auth\ConsigneesController@store')->name('api.auth.store.consignees');

Route::put('/consignees/{id}', 'App\Http\Controllers\Api\Auth\ConsigneesController@update')->name('api.auth.update.consignees');

Route::delete('/consignees/{id}', 'App\Http\Controllers\Api\Auth\ConsigneesController@destroy')->name('api.auth.delete.consignees');

Route::get('/consignees/search/{search}', 'App\Http\Controllers\Api\Auth\ConsigneesController@search')->name('api.auth.search.consignees');

   

});



Route::group([

    'middleware' => 'api',

    'prefix' => 'auth'



], function ($router) {

    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/user-profile', [AuthController::class, 'userProfile']);    

});





