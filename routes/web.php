<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ConsignerController;
use App\Http\Controllers\ConsigneeController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ConsignmentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ImportCsvController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\API\ReceiveAddressController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\TechnicalMasterController;
use App\Http\Controllers\PickupRunSheetController;
use App\Http\Controllers\HubtoHubController;
use App\Http\Controllers\FtlPtlController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/landing', function () {
    return view('landing');
});

Route::get('/', function () {
    if(Auth::check())
    {
       $userrole = Auth::user()->role_id;
        if($userrole == 1){
            return redirect('/admin/dashboard');
        }
        else if($userrole == 2) {
            return redirect('/branch-manager/dashboard');
        }
        else if($userrole == 3) {
            return redirect('/regional-manager/dashboard');
        }
        else if($userrole == 4) {
            return redirect('/branch-user/dashboard');
        }
        else if($userrole == 5) {
            return redirect('/account-manager/dashboard');
        }
        else if($userrole == 6) {
            return redirect('/client-account/dashboard');
        }
        else if($userrole == 7) {
            return redirect('/client-user/consignments');
        }
        else if($userrole == 8) {
            return redirect('/lr-cancel/consignments');
        }
    }
   else
    {
      return view('auth.login');
    }
});

Route::get('qrcode', function () {
    return QrCode::size(300)->generate('A basic example of QR code!');
});

// Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('logout', [LoginController::class, 'logout']);

Route::group(['prefix'=>'admin', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('dashboard', DashboardController::class);
    Route::resource('/', DashboardController::class);
    Route::resource('users', UserController::class);
    Route::post('/users/update-user', [UserController::class, 'updateUser']);
    Route::post('/users/delete-user', [UserController::class, 'deleteUser']);

    Route::resource('branches', BranchController::class);
    Route::post('branches/update-branch', [BranchController::class, 'updateBranch']);
    Route::post('branches/delete-branch', [BranchController::class, 'deleteBranch']);
    Route::post('branches/delete-branchimage', [BranchController::class, 'deletebranchImage']);

    Route::resource('consigners', ConsignerController::class);
    Route::post('consigners/update-consigner', [ConsignerController::class, 'updateConsigner']);
    Route::post('consigners/delete-consigner', [ConsignerController::class, 'deleteConsigner']);
    Route::get('consigners/export/excel', [ConsignerController::class, 'exportExcel']);

    Route::resource('consignees', ConsigneeController::class);
    Route::post('consignees/update-consignee', [ConsigneeController::class, 'updateConsignee']);
    Route::post('consignees/delete-consignee', [ConsigneeController::class, 'deleteConsignee']);
    Route::get('consignees/export/excel', [ConsigneeController::class, 'exportExcel']);

    // Route::resource('brokers', BrokerController::class);
    // Route::post('brokers/update-broker', [BrokerController::class, 'updateBroker']);
    // Route::post('brokers/delete-broker', [BrokerController::class, 'deleteBroker']);
    // Route::post('/brokers/delete-brokerimage', [BrokerController::class, 'deletebrokerImage']);

    Route::resource('drivers', DriverController::class);
    Route::post('drivers/update-driver', [DriverController::class, 'updateDriver']);
    Route::post('drivers/delete-driver', [DriverController::class, 'deleteDriver']);
    Route::post('/drivers/delete-licenseimage', [DriverController::class, 'deletelicenseImage']);
    Route::get('drivers/export/excel', [DriverController::class, 'exportExcel']);

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/update-vehicle', [VehicleController::class, 'updateVehicle']);
    Route::post('vehicles/delete-vehicle', [VehicleController::class, 'deleteVehicle']);
    Route::post('/vehicles/delete-rcimage', [VehicleController::class, 'deletercImage']);
    Route::get('vehicles/export/excel', [VehicleController::class, 'exportExcel']);

    Route::resource('orders', OrderController::class);
    Route::post('orders/update-order', [OrderController::class, 'updateOrder']);
    Route::get('order-book-ftl', [OrderController::class, 'orderBookFtl']);
    Route::get('order-book-ptl', [OrderController::class, 'orderBookptl']);
    Route::post('store-Ftl-order', [OrderController::class, 'storeFtlOrder']);
    Route::post('store-Ptl-order', [OrderController::class, 'storePtlOrder']);
    Route::post('prs-receive-material', [OrderController::class, 'prsReceiveMaterial']);

    Route::resource('consignments', ConsignmentController::class);
    //Test Routes
    Route::any('testview', [ConsignmentController::class, 'testview']);
    Route::any('test', [ConsignmentController::class, 'test']);
    // Test Routes
    Route::get('unverified-list', [ConsignmentController::class, 'unverifiedList']);
    Route::any('update_unverifiedLR', [ConsignmentController::class, 'updateUnverifiedLr']);
    Route::post('consignments/update-consignment', [ConsignmentController::class, 'updateConsignment']);
    Route::post('consignments/delete-consignment', [ConsignmentController::class, 'deleteConsignment']);
    Route::post('consignments/get-consign-details', [ConsignmentController::class, 'getConsigndetails']);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::get('consignments/{id}/print-viewold/{typeid}', [ConsignmentController::class, 'consignPrintviewold']);
    Route::get('transaction-sheet', [ConsignmentController::class, 'transactionSheet']);
    Route::any('view-transactionSheet/{id}', [ConsignmentController::class, 'getTransactionDetails']);
    Route::any('print-transaction/{id}', [ConsignmentController::class, 'printTransactionsheet']);
    Route::any('print-transactionold/{id}', [ConsignmentController::class, 'printTransactionsheetold']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::any('update-edd', [ConsignmentController::class, 'updateEDD']);
    Route::any('create-drs', [ConsignmentController::class, 'CreateEdd']);
    Route::any('update-suffle', [ConsignmentController::class, 'updateSuffle']);
    Route::any('view-draftSheet/{id}', [ConsignmentController::class, 'view_saveDraft']);
    Route::any('update-delivery/{id}', [ConsignmentController::class, 'updateDelivery']);
    Route::any('update-delivery-status', [ConsignmentController::class, 'updateDeliveryStatus']);
    Route::any('update-delivery-date', [ConsignmentController::class, 'updateDeliveryDateOneBy']);
    Route::any('remove-lr', [ConsignmentController::class, 'removeLR']);
    Route::any('get-delivery-datamodel', [ConsignmentController::class, 'getdeliverydatamodel']);
    Route::any('get-delivery-dateLR', [ConsignmentController::class, 'getDeleveryDateLr']);
    Route::any('update-lrstatus', [ConsignmentController::class, 'updateLrStatus']);
    Route::post('all-save-deliverydate', [ConsignmentController::class, 'allSaveDRS']);
    Route::post('get-add-lr', [ConsignmentController::class, 'addmoreLr']);
    Route::post('add-unverified-lr', [ConsignmentController::class, 'addunverifiedLr']);
    Route::any('upload-delivery-img', [ConsignmentController::class, 'uploadDrsImg']);
    Route::any('create-lr-form', [ConsignmentController::class, 'createNewLrForm']);
    Route::post('consignments/new-lr-create', [ConsignmentController::class, 'newStoreLr']);

    Route::resource('locations', LocationController::class);
    Route::post('/locations/update', [LocationController::class, 'updateLocation']);
    Route::any('locations/get-location', [LocationController::class, 'getLocation']);
    Route::post('locations/delete-location', [LocationController::class, 'deleteLocation']);

    Route::get('bulk-import', [ImportCsvController::class, 'getBulkImport']);
    Route::post('consignees/upload_csv', [ImportCsvController::class, 'uploadCsv']);

    // Route::get('settings/branch-address', [SettingController::class, 'getbranchAddress']);
    Route::any('settings/branch-address', [SettingController::class, 'updateBranchadd']);
    Route::any('settings/add-gst-address', [SettingController::class, 'addGstAddress']);

    Route::get('/sample-consignees',[ImportCsvController::class, 'consigneesSampleDownload']);
    Route::get('/sample-consignee-phone',[ImportCsvController::class, 'consigneePhoneSampleDownload']);
    Route::get('/sample-consigner',[ImportCsvController::class, 'consignerSampleDownload']);
    Route::get('/sample-vehicle',[ImportCsvController::class, 'vehicleSampleDownload']);
    Route::get('/sample-driver',[ImportCsvController::class, 'driverSampleDownload']);
    Route::get('/sample-zone',[ImportCsvController::class, 'zoneSampleDownload']); 
    Route::get('/sample-deliverydate',[ImportCsvController::class, 'deliverydateSampleDownload']);
    Route::get('/sample-manualdelivery',[ImportCsvController::class, 'manualdeliverySampleDownload']);

    Route::resource('clients', ClientController::class);
    Route::get('clients-list', [ClientController::class, 'clientList']);
    Route::post('/clients/update-client', [ClientController::class, 'UpdateClient']);
    Route::get('reginal-clients', [ClientController::class, 'regionalClients']);
    Route::post('/clients/delete-client', [ClientController::class, 'deleteClient']);
    Route::get('/reginal-clients/add-regclient-detail/{id}', [ClientController::class, 'createRegclientdetail']);
    Route::post('/regclient-detail/update-detail', [ClientController::class, 'updateRegclientdetail']);
    Route::get('/reginal-clients/view-regclient-detail/{id}', [ClientController::class, 'viewRegclientdetail']);
    Route::get('/regclient-detail/{id}/edit', [ClientController::class, 'editRegClientDetail']);
    Route::post('/save-regclient-detail', [ClientController::class, 'storeRegclientdetail']);
    Route::get('create-regional-client', [ClientController::class, 'createRegionalClient']);
    Route::any('generate-regional', [ClientController::class, 'generateRegionalName']);
    Route::any('regclient-detail/update-generate-regional', [ClientController::class, 'updateGenerateRegionalName']);
    Route::any('create-regional', [ClientController::class, 'storeRegionalClient']);
    Route::any('edit-baseclient/{id}', [ClientController::class, 'editBaseClient']);
    Route::any('update-base-client', [ClientController::class, 'updateBaseClient']);

    Route::any('consignment-report2', [ReportController::class, 'consignmentReportsAll']);
    Route::any('consignment-report3', [ReportController::class, 'consignmentReportthree']);
    Route::any('consignment-misreport', [ReportController::class, 'consignmentReports']);
    Route::any('get-filter-report', [ConsignmentController::class, 'getFilterReport']);


    Route::any('admin-report1', [ReportController::class, 'adminReport1']);
    Route::any('admin-report2', [ReportController::class, 'adminReport2']);
    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('reports/export2', [ReportController::class, 'exportExcelReport2']);
    Route::any('reports/export3', [ReportController::class, 'exportExcelReport3']);

    Route::any('view_invoices/{id}', [ConsignmentController::class, 'viewupdateInvoice']);
    Route::any('all-invoice-save', [ConsignmentController::class, 'allupdateInvoice']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);
    Route::any('update-poddetails', [ConsignmentController::class, 'updatePod']);
    Route::any('change-pod-mode', [ConsignmentController::class, 'changePodMode']);
    Route::any('delete-pod-status', [ConsignmentController::class, 'deletePodStatus']);
    Route::any('pod-export', [ConsignmentController::class, 'exportPodFile']);


    Route::get('technical-master', [TechnicalMasterController::class, 'techicalMaster']);
    Route::any('import-technical-master', [TechnicalMasterController::class, 'importTechnicalMaster']);
    Route::get('item-view', [TechnicalMasterController::class, 'itemUploadView']);
    Route::any('import-item-master', [TechnicalMasterController::class, 'importItems']);
    Route::any('add-technical-name', [TechnicalMasterController::class, 'addTechnicalName']);
    Route::any('add-items-name', [TechnicalMasterController::class, 'addItems']);
    Route::any('edit-items-name', [TechnicalMasterController::class, 'editItemName']);
    Route::any('update-items-name', [TechnicalMasterController::class, 'updateItemName']);
    
    Route::resource('prs', PickupRunSheetController::class);
    Route::post('/prs/update-prs', [PickupRunSheetController::class, 'UpdatePrs']);
    Route::get('prs/export/excel', [PickupRunSheetController::class, 'exportExcel']);
    Route::any('driver-tasks', [PickupRunSheetController::class,'driverTasks']);
    Route::any('driver-tasks/create-taskitem', [PickupRunSheetController::class,'createTaskItem']);
    Route::any('vehicle-receivegate', [PickupRunSheetController::class,'vehicleReceivegate']);
    Route::any('add-receive-vehicle', [PickupRunSheetController::class,'createReceiveVehicle']);
    Route::any('getlr-item', [PickupRunSheetController::class, 'getlrItems']);
    Route::get('pickup-loads', [PickupRunSheetController::class, 'pickupLoads']);
    Route::get('pickup-loads/export', [PickupRunSheetController::class, 'exportPickupLoad']);

    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);
    Route::any('postal-code', [SettingController::class,'postalCode']);
    Route::any('edit-postal-code/{id}', [SettingController::class, 'editPostalCode']);
    Route::any('update-postal-code', [SettingController::class, 'updatePostalCode']);
    Route::get('postal-code/export/excel', [SettingController::class, 'exportExcel']);
    Route::any('settings/edit-gst-address/{id}', [SettingController::class, 'editGstAddress']);
    Route::any('settings/update-gst-address', [SettingController::class, 'updateGstAddress']);
    Route::any('settings/view-gst-address/{id}', [SettingController::class, 'viewGstAddress']);

    Route::any('import-ordre-booking', [OrderController::class, 'importOrderBooking']);

    Route::any('hub-transportation', [HubtoHubController::class,'hubtransportation']);
    Route::any('hrs-list', [HubtoHubController::class,'hrsList']);
    Route::any('create-hrs', [HubtoHubController::class, 'createHrs']);
    Route::any('hrs-sheet', [HubtoHubController::class, 'hrsSheet']);
    Route::get('hrs-sheet/export/excel', [HubtoHubController::class, 'exportHrsSheet']);
    Route::any('view-hrsdetails/{id}', [HubtoHubController::class, 'view_saveHrsDetails']);
    Route::any('update_vehicle_hrs', [HubtoHubController::class, 'updateVehicleHrs']);
    Route::any('view-hrsSheetDetails/{id}', [HubtoHubController::class, 'getHrsSheetDetails']);
    Route::post('get-add-lr-hrs', [HubtoHubController::class, 'addmoreLrHrs']);
    Route::post('created-lr-hrs', [HubtoHubController::class, 'createdLrHrs']);
    Route::any('incoming-hrs', [HubtoHubController::class, 'incomingHrs']);
    Route::any('view-lr-hrs/{id}', [HubtoHubController::class, 'viewLrHrs']);
    Route::any('receving-hrs-details/{id}', [HubtoHubController::class, 'recevingHrsDetails']);
    Route::any('update_hrs_receving_details', [HubtoHubController::class, 'updateRecevingDetails']);
    Route::any('print-hrs/{id}', [HubtoHubController::class, 'printHrs']);
    Route::any('hrs-payment-list', [HubtoHubController::class, 'hrsPaymentList']);
    Route::any('view-hrslr/{id}', [HubtoHubController::class, 'viewhrsLr']);
    Route::any('get-hrs-details', [HubtoHubController::class, 'getHrsdetails']);
    Route::any('outgoing-hrs', [HubtoHubController::class, 'outgoingHrs']);
    Route::any('hrs-payment-request', [HubtoHubController::class, 'createHrsPayment']);
    Route::any('hrs-request-list', [HubtoHubController::class, 'hrsRequestList']);
    Route::any('update-purchas-price-hrs', [HubtoHubController::class, 'updatePurchasePriceHrs']);
    Route::any('get-second-pymt-details', [HubtoHubController::class, 'getSecondPaymentDetails']);
    Route::any('show-hrs', [HubtoHubController::class, 'showHrs']); 
    Route::any('second-payment-hrs', [HubtoHubController::class, 'createSecondPaymentRequest']); 
    Route::get('edit-purchase-price-hrs', [HubtoHubController::class, 'editPurchasePriceHrs']);
    Route::any('update-purchas-price-vehicle-type-hrs', [HubtoHubController::class, 'updatePurchasePriceVehicleTypeHrs']);

    Route::any('create-ftl', [FtlPtlController::class, 'createFtlLrForm']);
    Route::post('new-Ftl-create', [FtlPtlController::class, 'storeFtlLr']);
    Route::any('create-ptl', [FtlPtlController::class, 'createPtlLrForm']);
    Route::post('new-Ptl-create', [FtlPtlController::class, 'storePtlLr']);

});

Route::group(['prefix'=>'branch-manager', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('dashboard', DashboardController::class);
    Route::resource('/', DashboardController::class);

    Route::resource('users', UserController::class);
    Route::post('/users/update-user', [UserController::class, 'updateUser']);
    Route::post('/users/delete-user', [UserController::class, 'deleteUser']);

    Route::resource('branches', BranchController::class);
    Route::post('branches/update-branch', [BranchController::class, 'updateBranch']);
    Route::post('branches/delete-branch', [BranchController::class, 'deleteBranch']);
    Route::post('branches/delete-branchimage', [BranchController::class, 'deletebranchImage']);

    Route::resource('consigners', ConsignerController::class);
    Route::post('consigners/update-consigner', [ConsignerController::class, 'updateConsigner']);
    Route::post('consigners/delete-consigner', [ConsignerController::class, 'deleteConsigner']);
    Route::get('consigners/export/excel', [ConsignerController::class, 'exportExcel']);

    Route::resource('consignees', ConsigneeController::class);
    Route::post('consignees/update-consignee', [ConsigneeController::class, 'updateConsignee']);
    Route::post('consignees/delete-consignee', [ConsigneeController::class, 'deleteConsignee']);
    Route::get('consignees/export/excel', [ConsigneeController::class, 'exportExcel']);

    Route::resource('drivers', DriverController::class);
    Route::post('drivers/update-driver', [DriverController::class, 'updateDriver']);
    Route::post('drivers/delete-driver', [DriverController::class, 'deleteDriver']);
    Route::post('/drivers/delete-licenseimage', [DriverController::class, 'deletelicenseImage']);
    Route::get('drivers/export/excel', [DriverController::class, 'exportExcel']);

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/update-vehicle', [VehicleController::class, 'updateVehicle']);
    Route::post('vehicles/delete-vehicle', [VehicleController::class, 'deleteVehicle']);
    Route::get('vehicles/export/excel', [VehicleController::class, 'exportExcel']);
    Route::get('vehicles/list',[VehicleController::class, 'getData']);

    Route::resource('orders', OrderController::class);
    Route::post('orders/update-order', [OrderController::class, 'updateOrder']);
    Route::get('order-book-ftl', [OrderController::class, 'orderBookFtl']);
    Route::get('order-book-ptl', [OrderController::class, 'orderBookptl']);
    Route::post('store-Ftl-order', [OrderController::class, 'storeFtlOrder']);
    Route::post('store-Ptl-order', [OrderController::class, 'storePtlOrder']);
    Route::post('prs-receive-material', [OrderController::class, 'prsReceiveMaterial']);
    Route::any('reserve-lr', [OrderController::class, 'reserveLr']);
    Route::get('reserve-lr/{id}/edit', [OrderController::class, 'editReserveLr']);
    Route::any('reserve-lr/update-reserve-lr', [OrderController::class, 'updateReserveLr']);

    Route::resource('consignments', ConsignmentController::class);
    Route::post('consignments/createlritem', [ConsignmentController::class, 'storeLRItem']);
    Route::get('unverified-list', [ConsignmentController::class, 'unverifiedList']);
    Route::any('update_unverifiedLR', [ConsignmentController::class, 'updateUnverifiedLr']);
    Route::post('consignments/update-consignment', [ConsignmentController::class, 'updateConsignment']);
    Route::post('consignments/delete-consignment', [ConsignmentController::class, 'deleteConsignment']);
    Route::post('consignments/get-consign-details', [ConsignmentController::class, 'getConsigndetails']);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::get('consignments/{id}/print-viewold/{typeid}', [ConsignmentController::class, 'consignPrintviewold']);
    Route::get('transaction-sheet', [ConsignmentController::class, 'transactionSheet']);
    Route::any('view-transactionSheet/{id}', [ConsignmentController::class, 'getTransactionDetails']);
    Route::any('print-transaction/{id}', [ConsignmentController::class, 'printTransactionsheet']);
    Route::any('print-transactionold/{id}', [ConsignmentController::class, 'printTransactionsheetold']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::any('update-edd', [ConsignmentController::class, 'updateEDD']);
    Route::any('create-drs', [ConsignmentController::class, 'CreateEdd']);
    Route::any('update-suffle', [ConsignmentController::class, 'updateSuffle']);
    Route::any('view-draftSheet/{id}', [ConsignmentController::class, 'view_saveDraft']);
    Route::any('update-delivery/{id}', [ConsignmentController::class, 'updateDelivery']);
    Route::any('update-delivery-status', [ConsignmentController::class, 'updateDeliveryStatus']);
    Route::any('update-delivery-date', [ConsignmentController::class, 'updateDeliveryDateOneBy']);
    Route::any('remove-lr', [ConsignmentController::class, 'removeLR']);
    Route::any('get-delivery-datamodel', [ConsignmentController::class, 'getdeliverydatamodel']);
    Route::any('bulklr-view', [ConsignmentController::class, 'BulkLrView']);
    Route::any('download-bulklr', [ConsignmentController::class, 'DownloadBulkLr']);
    Route::any('get-delivery-dateLR', [ConsignmentController::class, 'getDeleveryDateLr']);
    Route::any('update-lrstatus', [ConsignmentController::class, 'updateLrStatus']);
    Route::any('get-filter-report', [ConsignmentController::class, 'getFilterReport']);
    Route::any('drs-status', [ConsignmentController::class, 'drsStatus']);
    Route::any('upload-delivery-img', [ConsignmentController::class, 'uploadDrsImg']);
    Route::post('all-save-deliverydate', [ConsignmentController::class, 'allSaveDRS']);
    Route::post('get-add-lr', [ConsignmentController::class, 'addmoreLr']);
    Route::post('add-unverified-lr', [ConsignmentController::class, 'addunverifiedLr']);
    Route::any('create-lr-form', [ConsignmentController::class, 'createNewLrForm']);
    Route::post('consignments/new-lr-create', [ConsignmentController::class, 'newStoreLr']);


    Route::get('/get-consigner-regional', [ConsignmentController::class, 'uploadDrsImgss']);
    Route::get('export-drs-table', [ConsignmentController::class, 'exportDownloadDrs']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);
    Route::any('update-poddetails', [ConsignmentController::class, 'updatePod']);
    Route::any('change-pod-mode', [ConsignmentController::class, 'changePodMode']);
    Route::any('delete-pod-status', [ConsignmentController::class, 'deletePodStatus']);
    Route::any('pod-export', [ConsignmentController::class, 'exportPodFile']);
    

    Route::resource('locations', LocationController::class);
    Route::post('/locations/update', [LocationController::class, 'updateLocation']);
    Route::any('locations/get-location', [LocationController::class, 'getLocation']);

    Route::resource('clients', ClientController::class);
    Route::get('clients-list', [ClientController::class, 'clientList']);

    Route::any('consignment-report2', [ReportController::class, 'consignmentReportsAll']);
    Route::any('consignment-report3', [ReportController::class, 'consignmentReportthree']);
    Route::any('consignment-misreport', [ReportController::class, 'consignmentReports']);

    Route::any('view_invoices/{id}', [ConsignmentController::class, 'viewupdateInvoice']);
    Route::any('all-invoice-save', [ConsignmentController::class, 'allupdateInvoice']);
    Route::any('mis-report2', [ReportController::class, 'misreport']);
    Route::post('export-mis', [ReportController::class,'exportExcelmisreport2']);
    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('reports/export2', [ReportController::class, 'exportExcelReport2']);
    Route::any('reports/export3', [ReportController::class, 'exportExcelReport3']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);

    Route::any('vendor-list', [VendorController::class, 'index']);
    Route::any('vendor/create', [VendorController::class, 'create']);
    Route::post('vendor/add-vendor', [VendorController::class, 'store']);
    Route::any('vendor/check-account-no', [VendorController::class, 'checkAccValid']);
    Route::any('drs-paymentlist', [VendorController::class, 'paymentList']);
    Route::any('drs-payment/export', [VendorController::class, 'drsPaymentExport']);
    Route::any('get-drs-details', [VendorController::class, 'getdrsdetails']);
    Route::any('vendor-details', [VendorController::class, 'vendorbankdetails']);
    Route::any('create-payment', [VendorController::class, 'createPaymentRequest']);
    Route::any('view-vendor-details', [VendorController::class, 'view_vendor_details']);
    Route::any('update-purchas-price', [VendorController::class, 'update_purchase_price']);
    Route::any('edit-vendor/{id}', [VendorController::class, 'editViewVendor']);
    Route::post('vendor/update-vendor', [VendorController::class, 'updateVendor']);
    Route::any('import-vendor', [VendorController::class, 'importVendor']);
    Route::any('export-vendor', [VendorController::class, 'exportVendor']);
    Route::any('view-drslr/{id}', [VendorController::class, 'viewdrsLr']);
    
    Route::any('create-payment_request', [VendorController::class, 'createPaymentRequestVendor']);
    Route::any('repay-request', [VendorController::class, 'repayRequest']);
    Route::any('request-list', [VendorController::class, 'requestList']);
    Route::any('get-vender-req-details', [VendorController::class, 'getVendorReqDetails']);
    Route::any('show-drs', [VendorController::class, 'showDrs']);
    Route::get('edit-purchase-price', [VendorController::class, 'editPurchasePrice']);
    Route::any('update-purchas-price-vehicle-type', [VendorController::class, 'updatePurchasePriceVehicleType']); //
    Route::get('get-balance-amount', [VendorController::class, 'getBalanceAmount']);
    Route::get('payment-report-view', [VendorController::class, 'paymentReportView']);
    Route::get('payment-reportExport', [VendorController::class, 'exportPaymentReport']);

    Route::get('prs-payment-report', [VendorController::class, 'prsPaymentReport']);
    Route::get('prs-payment-reportExport', [VendorController::class, 'exportPrsPaymentReport']);

    Route::get('drswise-report', [VendorController::class, 'drsWiseReport']);
    Route::get('export-drswise-report', [VendorController::class, 'exportdrsWiseReport']);
    Route::any('get-repay-details', [VendorController::class, 'getRepayDetails']);

    Route::resource('prs', PickupRunSheetController::class);
    Route::post('/prs/update-prs', [PickupRunSheetController::class, 'UpdatePrs']);
    Route::get('prs/export/excel', [PickupRunSheetController::class, 'exportExcel']);
    Route::any('driver-tasks', [PickupRunSheetController::class,'driverTasks']);
    Route::any('driver-tasks/create-taskitem', [PickupRunSheetController::class,'createTaskItem']);
    Route::any('vehicle-receivegate', [PickupRunSheetController::class,'vehicleReceivegate']);
    Route::any('add-receive-vehicle', [PickupRunSheetController::class,'createReceiveVehicle']);
    Route::any('getlr-item', [PickupRunSheetController::class, 'getlrItems']);
    Route::get('pickup-loads', [PickupRunSheetController::class, 'pickupLoads']);
    Route::get('pickup-loads/export', [PickupRunSheetController::class, 'exportPickupLoad']);
    Route::any('prs-paymentlist', [PickupRunSheetController::class, 'paymentList']);
    Route::any('update-purchas-price-prs', [PickupRunSheetController::class, 'updatePurchasePricePrs']);
    Route::any('get-prs-details', [PickupRunSheetController::class, 'getPrsdetails']);
    Route::any('prs-payment-request', [PickupRunSheetController::class, 'createPrsPayment']);
    Route::any('prs-request-list', [PickupRunSheetController::class, 'prsRequestList']);
    Route::any('get-second-pymt-details-prs', [PickupRunSheetController::class, 'getSecondPaymentDetailsPrs']);
    Route::any('second-payment-prs', [PickupRunSheetController::class, 'createSecondPaymentRequestPrs']); 
    Route::any('show-prs', [PickupRunSheetController::class, 'showPrs']);

    Route::any('import-ordre-booking', [OrderController::class, 'importOrderBooking']);
    
    Route::any('hub-transportation', [HubtoHubController::class,'hubtransportation']);
    Route::any('hrs-list', [HubtoHubController::class,'hrsList']);
    Route::any('create-hrs', [HubtoHubController::class, 'createHrs']);
    Route::any('hrs-sheet', [HubtoHubController::class, 'hrsSheet']);
    Route::get('hrs-sheet/export/excel', [HubtoHubController::class, 'exportHrsSheet']);
    Route::any('view-hrsdetails/{id}', [HubtoHubController::class, 'view_saveHrsDetails']);
    Route::any('update_vehicle_hrs', [HubtoHubController::class, 'updateVehicleHrs']);
    Route::any('view-hrsSheetDetails/{id}', [HubtoHubController::class, 'getHrsSheetDetails']);
    Route::post('get-add-lr-hrs', [HubtoHubController::class, 'addmoreLrHrs']);
    Route::post('created-lr-hrs', [HubtoHubController::class, 'createdLrHrs']);
    Route::any('incoming-hrs', [HubtoHubController::class, 'incomingHrs']);
    Route::any('view-lr-hrs/{id}', [HubtoHubController::class, 'viewLrHrs']);
    Route::any('receving-hrs-details/{id}', [HubtoHubController::class, 'recevingHrsDetails']);
    Route::any('update_hrs_receving_details', [HubtoHubController::class, 'updateRecevingDetails']);
    Route::any('print-hrs/{id}', [HubtoHubController::class, 'printHrs']);
    Route::any('hrs-payment-list', [HubtoHubController::class, 'hrsPaymentList']);
    Route::any('view-hrslr/{id}', [HubtoHubController::class, 'viewhrsLr']);
    Route::any('get-hrs-details', [HubtoHubController::class, 'getHrsdetails']);
    Route::any('outgoing-hrs', [HubtoHubController::class, 'outgoingHrs']);
    Route::any('hrs-payment-request', [HubtoHubController::class, 'createHrsPayment']);
    Route::any('hrs-request-list', [HubtoHubController::class, 'hrsRequestList']);
    Route::any('update-purchas-price-hrs', [HubtoHubController::class, 'updatePurchasePriceHrs']);
    Route::any('get-second-pymt-details', [HubtoHubController::class, 'getSecondPaymentDetails']);
    Route::any('show-hrs', [HubtoHubController::class, 'showHrs']); 
    Route::any('second-payment-hrs', [HubtoHubController::class, 'createSecondPaymentRequest']); 
    Route::get('edit-purchase-price-hrs', [HubtoHubController::class, 'editPurchasePriceHrs']);
    Route::any('update-purchas-price-vehicle-type-hrs', [HubtoHubController::class, 'updatePurchasePriceVehicleTypeHrs']);
    Route::any('remove-hrs', [HubtoHubController::class, 'removeHrs']);



    Route::any('create-ftl', [FtlPtlController::class, 'createFtlLrForm']);
    Route::post('new-Ftl-create', [FtlPtlController::class, 'storeFtlLr']);
    Route::any('create-ptl', [FtlPtlController::class, 'createPtlLrForm']);
    Route::post('new-Ptl-create', [FtlPtlController::class, 'storePtlLr']);
    Route::any('postal-code', [SettingController::class,'postalCode']);
    Route::any('edit-postal-code/{id}', [SettingController::class, 'editPostalCode']);
    Route::any('update-postal-code', [SettingController::class, 'updatePostalCode']);

});
Route::group(['prefix'=>'regional-manager', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('dashboard', DashboardController::class);
    Route::resource('/', DashboardController::class);

    Route::resource('users', UserController::class);
    Route::post('/users/update-user', [UserController::class, 'updateUser']);
    Route::post('/users/delete-user', [UserController::class, 'deleteUser']);

    Route::resource('branches', BranchController::class);
    Route::post('branches/update-branch', [BranchController::class, 'updateBranch']);
    Route::post('branches/delete-branch', [BranchController::class, 'deleteBranch']);
    Route::post('branches/delete-branchimage', [BranchController::class, 'deletebranchImage']);

    Route::resource('consigners', ConsignerController::class);
    Route::post('consigners/update-consigner', [ConsignerController::class, 'updateConsigner']);
    Route::post('consigners/delete-consigner', [ConsignerController::class, 'deleteConsigner']);
    Route::get('consigners/export/excel', [ConsignerController::class, 'exportExcel']);

    Route::resource('consignees', ConsigneeController::class);
    Route::post('consignees/update-consignee', [ConsigneeController::class, 'updateConsignee']);
    Route::post('consignees/delete-consignee', [ConsigneeController::class, 'deleteConsignee']);
    Route::get('consignees/export/excel', [ConsigneeController::class, 'exportExcel']);

    Route::resource('drivers', DriverController::class);
    Route::post('drivers/update-driver', [DriverController::class, 'updateDriver']);
    Route::post('drivers/delete-driver', [DriverController::class, 'deleteDriver']);
    Route::post('/drivers/delete-licenseimage', [DriverController::class, 'deletelicenseImage']);
    Route::get('drivers/export/excel', [DriverController::class, 'exportExcel']);

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/update-vehicle', [VehicleController::class, 'updateVehicle']);
    Route::post('vehicles/delete-vehicle', [VehicleController::class, 'deleteVehicle']);
    Route::post('/vehicles/delete-rcimage', [VehicleController::class, 'deletercImage']);
    Route::get('vehicles/export/excel', [VehicleController::class, 'exportExcel']);

    Route::resource('orders', OrderController::class);
    Route::post('orders/update-order', [OrderController::class, 'updateOrder']);
    Route::get('order-book-ftl', [OrderController::class, 'orderBookFtl']);
    Route::get('order-book-ptl', [OrderController::class, 'orderBookptl']);
    Route::post('store-Ftl-order', [OrderController::class, 'storeFtlOrder']);
    Route::post('store-Ptl-order', [OrderController::class, 'storePtlOrder']);
    Route::post('prs-receive-material', [OrderController::class, 'prsReceiveMaterial']);

    Route::resource('consignments', ConsignmentController::class);
    Route::get('unverified-list', [ConsignmentController::class, 'unverifiedList']);
    Route::any('update_unverifiedLR', [ConsignmentController::class, 'updateUnverifiedLr']);
    Route::post('consignments/update-consignment', [ConsignmentController::class, 'updateConsignment']);
    Route::post('consignments/delete-consignment', [ConsignmentController::class, 'deleteConsignment']);
    Route::post('consignments/get-consign-details', [ConsignmentController::class, 'getConsigndetails']);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::get('consignments/{id}/print-viewold/{typeid}', [ConsignmentController::class, 'consignPrintviewold']);
    Route::get('transaction-sheet', [ConsignmentController::class, 'transactionSheet']);
    Route::any('view-transactionSheet/{id}', [ConsignmentController::class, 'getTransactionDetails']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::any('print-transaction/{id}', [ConsignmentController::class, 'printTransactionsheet']);
    Route::any('print-transactionold/{id}', [ConsignmentController::class, 'printTransactionsheetold']);
    Route::any('update-edd', [ConsignmentController::class, 'updateEDD']);
    Route::any('create-drs', [ConsignmentController::class, 'CreateEdd']);
    Route::any('update-suffle', [ConsignmentController::class, 'updateSuffle']);
    Route::any('view-draftSheet/{id}', [ConsignmentController::class, 'view_saveDraft']);
    Route::any('update-delivery/{id}', [ConsignmentController::class, 'updateDelivery']);
    Route::any('update-delivery-status', [ConsignmentController::class, 'updateDeliveryStatus']);
    Route::any('update-delivery-date', [ConsignmentController::class, 'updateDeliveryDateOneBy']);
    Route::any('remove-lr', [ConsignmentController::class, 'removeLR']);
    Route::any('get-delivery-datamodel', [ConsignmentController::class, 'getdeliverydatamodel']);
    Route::any('bulklr-view', [ConsignmentController::class, 'BulkLrView']);
    Route::any('download-bulklr', [ConsignmentController::class, 'DownloadBulkLr']);
    Route::any('get-delivery-dateLR', [ConsignmentController::class, 'getDeleveryDateLr']);
    Route::any('update-lrstatus', [ConsignmentController::class, 'updateLrStatus']);
    Route::any('get-filter-report', [ConsignmentController::class, 'getFilterReport']);
    Route::any('drs-status', [ConsignmentController::class, 'drsStatus']);
    Route::any('upload-delivery-img', [ConsignmentController::class, 'uploadDrsImg']);
    Route::post('all-save-deliverydate', [ConsignmentController::class, 'allSaveDRS']);
    Route::post('get-add-lr', [ConsignmentController::class, 'addmoreLr']);
    Route::post('add-unverified-lr', [ConsignmentController::class, 'addunverifiedLr']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);
    Route::any('update-poddetails', [ConsignmentController::class, 'updatePod']);
    Route::any('change-pod-mode', [ConsignmentController::class, 'changePodMode']);
    Route::any('delete-pod-status', [ConsignmentController::class, 'deletePodStatus']);
    Route::any('pod-export', [ConsignmentController::class, 'exportPodFile']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);


    Route::resource('locations', LocationController::class);
    Route::post('/locations/update', [LocationController::class, 'updateLocation']); 
    Route::any('locations/get-location', [LocationController::class, 'getLocation']);
    // Route::any('locations/delete-location', [LocationController::class, 'deleteLocation']);

    Route::get('bulk-import', [ImportCsvController::class, 'getBulkImport']);
    Route::post('consignees/upload_csv', [ImportCsvController::class, 'uploadCsv']);

    // Route::get('settings/branch-address', [SettingController::class, 'getbranchAddress']);
    Route::any('settings/branch-address', [SettingController::class, 'updateBranchadd']);

    Route::get('/sample-consignees',[ImportCsvController::class, 'consigneesSampleDownload']);
    Route::get('/sample-consignee-phone',[ImportCsvController::class, 'consigneePhoneSampleDownload']);
    Route::get('/sample-consigner',[ImportCsvController::class, 'consignerSampleDownload']);
    Route::get('/sample-vehicle',[ImportCsvController::class, 'vehicleSampleDownload']);
    Route::get('/sample-driver',[ImportCsvController::class, 'driverSampleDownload']);

    Route::resource('clients', ClientController::class);

    Route::any('consignment-report2', [ReportController::class, 'consignmentReportsAll']);
    Route::any('consignment-report3', [ReportController::class, 'consignmentReportthree']);
    Route::any('consignment-misreport', [ReportController::class, 'consignmentReports']);
    Route::any('view_invoices/{id}', [ConsignmentController::class, 'viewupdateInvoice']);
    Route::any('all-invoice-save', [ConsignmentController::class, 'allupdateInvoice']);
    Route::any('mis-report2', [ReportController::class, 'misreport']);
    Route::post('export-mis', [ReportController::class,'exportExcelmisreport2']);
    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('reports/export2', [ReportController::class, 'exportExcelReport2']);
    Route::any('reports/export3', [ReportController::class, 'exportExcelReport3']);

    Route::any('vendor-list', [VendorController::class, 'index']);
    Route::any('vendor/create', [VendorController::class, 'create']);
    Route::post('vendor/add-vendor', [VendorController::class, 'store']);
    Route::any('vendor/check-account-no', [VendorController::class, 'checkAccValid']);
    Route::any('drs-paymentlist', [VendorController::class, 'paymentList']);
    Route::any('drs-payment/export', [VendorController::class, 'drsPaymentExport']);
    Route::any('get-drs-details', [VendorController::class, 'getdrsdetails']);
    Route::any('vendor-details', [VendorController::class, 'vendorbankdetails']);
    Route::any('create-payment', [VendorController::class, 'createPaymentRequest']);
    Route::any('view-vendor-details', [VendorController::class, 'view_vendor_details']);
    Route::any('update-purchas-price', [VendorController::class, 'update_purchase_price']);
    Route::any('edit-vendor/{id}', [VendorController::class, 'editViewVendor']);
    Route::post('vendor/update-vendor', [VendorController::class, 'updateVendor']);
    Route::any('import-vendor', [VendorController::class, 'importVendor']);
    Route::any('export-vendor', [VendorController::class, 'exportVendor']);
    Route::any('view-drslr/{id}', [VendorController::class, 'viewdrsLr']);
    Route::any('create-payment_request', [VendorController::class, 'createPaymentRequestVendor']);
    Route::any('request-list', [VendorController::class, 'requestList']);
    Route::any('get-vender-req-details', [VendorController::class, 'getVendorReqDetails']);
    Route::any('show-drs', [VendorController::class, 'showDrs']);
    Route::get('edit-purchase-price', [VendorController::class, 'editPurchasePrice']);
    Route::any('update-purchas-price-vehicle-type', [VendorController::class, 'updatePurchasePriceVehicleType']); 
    Route::get('get-balance-amount', [VendorController::class, 'getBalanceAmount']);
    Route::get('payment-report-view', [VendorController::class, 'paymentReportView']);
    Route::get('payment-reportExport', [VendorController::class, 'exportPaymentReport']);
    
    Route::get('prs-payment-report', [VendorController::class, 'prsPaymentReport']);
    Route::get('prs-payment-reportExport', [VendorController::class, 'exportPrsPaymentReport']);
    Route::get('drswise-report', [VendorController::class, 'drsWiseReport']);
    Route::get('handshake-report', [VendorController::class, 'handshakeReport']);
    Route::get('export-drswise-report', [VendorController::class, 'exportdrsWiseReport']);

    Route::resource('prs', PickupRunSheetController::class);
    Route::post('/prs/update-prs', [PickupRunSheetController::class, 'UpdatePrs']);
    Route::get('prs/export/excel', [PickupRunSheetController::class, 'exportExcel']);
    Route::any('driver-tasks', [PickupRunSheetController::class,'driverTasks']);
    Route::any('driver-tasks/create-taskitem', [PickupRunSheetController::class,'createTaskItem']);
    Route::any('vehicle-receivegate', [PickupRunSheetController::class,'vehicleReceivegate']);
    Route::any('add-receive-vehicle', [PickupRunSheetController::class,'createReceiveVehicle']);
    Route::any('getlr-item', [PickupRunSheetController::class, 'getlrItems']);
    Route::get('pickup-loads', [PickupRunSheetController::class, 'pickupLoads']);
    Route::get('pickup-loads/export', [PickupRunSheetController::class, 'exportPickupLoad']);
    Route::any('prs-payment-request', [PickupRunSheetController::class, 'createPrsPayment']);
    Route::any('prs-request-list', [PickupRunSheetController::class, 'prsRequestList']);
    Route::any('get-vender-req-details-prs', [PickupRunSheetController::class, 'getVendorReqDetailsPrs']);
    Route::any('prs-rm-approver', [PickupRunSheetController::class, 'rmApproverRequest']);
    Route::any('show-prs', [PickupRunSheetController::class, 'showPrs']);
    Route::any('prs-paymentlist', [PickupRunSheetController::class, 'paymentList']);

    Route::any('hub-transportation', [HubtoHubController::class,'hubtransportation']);
    Route::any('hrs-list', [HubtoHubController::class,'hrsList']);
    Route::any('create-hrs', [HubtoHubController::class, 'createHrs']);
    Route::any('hrs-sheet', [HubtoHubController::class, 'hrsSheet']);
    Route::any('view-hrsdetails/{id}', [HubtoHubController::class, 'view_saveHrsDetails']);
    Route::any('update_vehicle_hrs', [HubtoHubController::class, 'updateVehicleHrs']);
    Route::any('view-hrsSheetDetails/{id}', [HubtoHubController::class, 'getHrsSheetDetails']);
    Route::post('get-add-lr-hrs', [HubtoHubController::class, 'addmoreLrHrs']);
    Route::post('created-lr-hrs', [HubtoHubController::class, 'createdLrHrs']);
    Route::any('incoming-hrs', [HubtoHubController::class, 'incomingHrs']);
    Route::any('view-lr-hrs/{id}', [HubtoHubController::class, 'viewLrHrs']);
    Route::any('receving-hrs-details/{id}', [HubtoHubController::class, 'recevingHrsDetails']);
    Route::any('update_hrs_receving_details', [HubtoHubController::class, 'updateRecevingDetails']);
    Route::any('print-hrs/{id}', [HubtoHubController::class, 'printHrs']);
    Route::any('hrs-payment-list', [HubtoHubController::class, 'hrsPaymentList']);
    Route::any('view-hrslr/{id}', [HubtoHubController::class, 'viewhrsLr']);
    Route::any('get-hrs-details', [HubtoHubController::class, 'getHrsdetails']);
    Route::any('outgoing-hrs', [HubtoHubController::class, 'outgoingHrs']);
    Route::any('hrs-payment-request', [HubtoHubController::class, 'createHrsPayment']);
    Route::any('hrs-request-list', [HubtoHubController::class, 'hrsRequestList']);
    Route::any('get-vender-req-details-hrs', [HubtoHubController::class, 'getVendorReqDetailsHrs']);
    Route::any('rm-approver', [HubtoHubController::class, 'rmApproverRequest']);
    Route::any('update-purchas-price-hrs', [HubtoHubController::class, 'updatePurchasePriceHrs']);
    Route::any('show-hrs', [HubtoHubController::class, 'showHrs']);
    Route::any('remove-hrs', [HubtoHubController::class, 'removeHrs']);

    Route::any('postal-code', [SettingController::class,'postalCode']);
    Route::any('edit-postal-code/{id}', [SettingController::class, 'editPostalCode']);
    Route::any('update-postal-code', [SettingController::class, 'updatePostalCode']);

});
Route::group(['prefix'=>'branch-user', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('dashboard', DashboardController::class);
    Route::resource('/', DashboardController::class);

    Route::resource('users', UserController::class);
    Route::post('/users/update-user', [UserController::class, 'updateUser']);
    Route::post('/users/delete-user', [UserController::class, 'deleteUser']);

    Route::resource('branches', BranchController::class);
    Route::post('branches/update-branch', [BranchController::class, 'updateBranch']);
    Route::post('branches/delete-branch', [BranchController::class, 'deleteBranch']);
    Route::post('branches/delete-branchimage', [BranchController::class, 'deletebranchImage']);

    Route::resource('consigners', ConsignerController::class);
    Route::post('consigners/update-consigner', [ConsignerController::class, 'updateConsigner']);
    Route::post('consigners/delete-consigner', [ConsignerController::class, 'deleteConsigner']);
    Route::get('consigners/export/excel', [ConsignerController::class, 'exportExcel']);

    Route::resource('consignees', ConsigneeController::class);
    Route::post('consignees/update-consignee', [ConsigneeController::class, 'updateConsignee']);
    Route::post('consignees/delete-consignee', [ConsigneeController::class, 'deleteConsignee']);
    Route::get('consignees/export/excel', [ConsigneeController::class, 'exportExcel']);

    Route::resource('drivers', DriverController::class);
    Route::post('drivers/update-driver', [DriverController::class, 'updateDriver']);
    Route::post('drivers/delete-driver', [DriverController::class, 'deleteDriver']);
    Route::post('/drivers/delete-licenseimage', [DriverController::class, 'deletelicenseImage']);
    Route::get('drivers/export/excel', [DriverController::class, 'exportExcel']);

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/update-vehicle', [VehicleController::class, 'updateVehicle']);
    Route::post('vehicles/delete-vehicle', [VehicleController::class, 'deleteVehicle']);
    Route::post('/vehicles/delete-rcimage', [VehicleController::class, 'deletercImage']);
    Route::get('vehicles/export/excel', [VehicleController::class, 'exportExcel']);

    Route::resource('orders', OrderController::class);
    Route::post('orders/update-order', [OrderController::class, 'updateOrder']);
    Route::get('order-book-ftl', [OrderController::class, 'orderBookFtl']);
    Route::get('order-book-ptl', [OrderController::class, 'orderBookptl']);
    Route::post('store-Ftl-order', [OrderController::class, 'storeFtlOrder']);
    Route::post('store-Ptl-order', [OrderController::class, 'storePtlOrder']);
    Route::post('prs-receive-material', [OrderController::class, 'prsReceiveMaterial']);
    Route::any('reserve-lr', [OrderController::class, 'reserveLr']);
    Route::get('reserve-lr/{id}/edit', [OrderController::class, 'editReserveLr']);
    Route::any('reserve-lr/update-reserve-lr', [OrderController::class, 'updateReserveLr']);

    Route::resource('consignments', ConsignmentController::class);
    Route::post('consignments/createlritem', [ConsignmentController::class, 'storeLRItem']);
    Route::get('unverified-list', [ConsignmentController::class, 'unverifiedList']);
    Route::any('update_unverifiedLR', [ConsignmentController::class, 'updateUnverifiedLr']);
    Route::post('consignments/update-consignment', [ConsignmentController::class, 'updateConsignment']);
    Route::post('consignments/delete-consignment', [ConsignmentController::class, 'deleteConsignment']);
    Route::post('consignments/get-consign-details', [ConsignmentController::class, 'getConsigndetails']);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::get('consignments/{id}/print-viewold/{typeid}', [ConsignmentController::class, 'consignPrintviewold']);
    Route::get('transaction-sheet', [ConsignmentController::class, 'transactionSheet']);
    Route::any('view-transactionSheet/{id}', [ConsignmentController::class, 'getTransactionDetails']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::any('print-transaction/{id}', [ConsignmentController::class, 'printTransactionsheet']);
    Route::any('print-transactionold/{id}', [ConsignmentController::class, 'printTransactionsheetold']);
    Route::any('update-edd', [ConsignmentController::class, 'updateEDD']);
    Route::any('create-drs', [ConsignmentController::class, 'CreateEdd']);
    Route::any('update-suffle', [ConsignmentController::class, 'updateSuffle']);
    Route::any('view-draftSheet/{id}', [ConsignmentController::class, 'view_saveDraft']);
    Route::any('update-delivery/{id}', [ConsignmentController::class, 'updateDelivery']);
    Route::any('update-delivery-status', [ConsignmentController::class, 'updateDeliveryStatus']);
    Route::any('update-delivery-date', [ConsignmentController::class, 'updateDeliveryDateOneBy']);
    Route::any('remove-lr', [ConsignmentController::class, 'removeLR']);
    Route::any('get-delivery-datamodel', [ConsignmentController::class, 'getdeliverydatamodel']);
    Route::any('bulklr-view', [ConsignmentController::class, 'BulkLrView']);
    Route::any('download-bulklr', [ConsignmentController::class, 'DownloadBulkLr']);
    Route::any('get-delivery-dateLR', [ConsignmentController::class, 'getDeleveryDateLr']);
    Route::any('update-lrstatus', [ConsignmentController::class, 'updateLrStatus']);
    Route::any('get-filter-report', [ConsignmentController::class, 'getFilterReport']);
    Route::any('drs-status', [ConsignmentController::class, 'drsStatus']);
    Route::any('upload-delivery-img', [ConsignmentController::class, 'uploadDrsImg']);
    Route::post('all-save-deliverydate', [ConsignmentController::class, 'allSaveDRS']);
    Route::post('get-add-lr', [ConsignmentController::class, 'addmoreLr']);
    Route::post('add-unverified-lr', [ConsignmentController::class, 'addunverifiedLr']);
    Route::any('create-lr-form', [ConsignmentController::class, 'createNewLrForm']);
    Route::post('consignments/new-lr-create', [ConsignmentController::class, 'newStoreLr']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);
    Route::any('update-poddetails', [ConsignmentController::class, 'updatePod']);
    Route::any('change-pod-mode', [ConsignmentController::class, 'changePodMode']);
    Route::any('delete-pod-status', [ConsignmentController::class, 'deletePodStatus']);
    Route::any('pod-export', [ConsignmentController::class, 'exportPodFile']);

    Route::resource('locations', LocationController::class);
    Route::post('/locations/update', [LocationController::class, 'updateLocation']);
    Route::any('locations/get-location', [LocationController::class, 'getLocation']);
    //Route::any('locations/delete-location', [LocationController::class, 'deleteLocation']);

    Route::get('bulk-import', [ImportCsvController::class, 'getBulkImport']);
    Route::post('consignees/upload_csv', [ImportCsvController::class, 'uploadCsv']);

    // Route::get('settings/branch-address', [SettingController::class, 'getbranchAddress']);
    Route::any('settings/branch-address', [SettingController::class, 'updateBranchadd']);

    Route::get('/sample-consignees',[ImportCsvController::class, 'consigneesSampleDownload']);
    Route::get('/sample-consigner',[ImportCsvController::class, 'consignerSampleDownload']);
    Route::get('/sample-vehicle',[ImportCsvController::class, 'vehicleSampleDownload']);
    Route::get('/sample-driver',[ImportCsvController::class, 'driverSampleDownload']);

    Route::resource('clients', ClientController::class);

    Route::any('consignment-report2', [ReportController::class, 'consignmentReportsAll']);
    Route::any('consignment-report3', [ReportController::class, 'consignmentReportthree']);
    Route::any('consignment-misreport', [ReportController::class, 'consignmentReports']);
    Route::any('view_invoices/{id}', [ConsignmentController::class, 'viewupdateInvoice']);
    Route::any('all-invoice-save', [ConsignmentController::class, 'allupdateInvoice']);
    Route::any('mis-report2', [ReportController::class, 'misreport']);
    Route::post('export-mis', [ReportController::class,'exportExcelmisreport2']);
    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('reports/export2', [ReportController::class, 'exportExcelReport2']);
    Route::any('reports/export3', [ReportController::class, 'exportExcelReport3']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);

    Route::any('postal-code', [SettingController::class,'postalCode']);
    Route::any('edit-postal-code/{id}', [SettingController::class, 'editPostalCode']);
    Route::any('update-postal-code', [SettingController::class, 'updatePostalCode']);

    Route::any('hub-transportation', [HubtoHubController::class,'hubtransportation']);
    Route::any('hrs-list', [HubtoHubController::class,'hrsList']);
    Route::any('create-hrs', [HubtoHubController::class, 'createHrs']);
    Route::any('hrs-sheet', [HubtoHubController::class, 'hrsSheet']);
    Route::any('view-hrsdetails/{id}', [HubtoHubController::class, 'view_saveHrsDetails']);
    Route::any('update_vehicle_hrs', [HubtoHubController::class, 'updateVehicleHrs']);
    Route::any('view-hrsSheetDetails/{id}', [HubtoHubController::class, 'getHrsSheetDetails']);
    Route::post('get-add-lr-hrs', [HubtoHubController::class, 'addmoreLrHrs']);
    Route::post('created-lr-hrs', [HubtoHubController::class, 'createdLrHrs']);
    Route::any('incoming-hrs', [HubtoHubController::class, 'incomingHrs']);
    Route::any('view-lr-hrs/{id}', [HubtoHubController::class, 'viewLrHrs']);
    Route::any('receving-hrs-details/{id}', [HubtoHubController::class, 'recevingHrsDetails']);
    Route::any('update_hrs_receving_details', [HubtoHubController::class, 'updateRecevingDetails']);
    Route::any('print-hrs/{id}', [HubtoHubController::class, 'printHrs']);
    Route::any('hrs-payment-list', [HubtoHubController::class, 'hrsPaymentList']);
    Route::any('view-hrslr/{id}', [HubtoHubController::class, 'viewhrsLr']);
    Route::any('get-hrs-details', [HubtoHubController::class, 'getHrsdetails']);
    Route::any('outgoing-hrs', [HubtoHubController::class, 'outgoingHrs']);
    Route::any('hrs-payment-request', [HubtoHubController::class, 'createHrsPayment']);
    Route::any('hrs-request-list', [HubtoHubController::class, 'hrsRequestList']);
    Route::any('update-purchas-price-hrs', [HubtoHubController::class, 'updatePurchasePriceHrs']);
    Route::any('get-second-pymt-details', [HubtoHubController::class, 'getSecondPaymentDetails']);
    Route::any('show-hrs', [HubtoHubController::class, 'showHrs']); 
    Route::any('second-payment-hrs', [HubtoHubController::class, 'createSecondPaymentRequest']); 
    Route::get('edit-purchase-price-hrs', [HubtoHubController::class, 'editPurchasePriceHrs']);
    Route::any('update-purchas-price-vehicle-type-hrs', [HubtoHubController::class, 'updatePurchasePriceVehicleTypeHrs']);

    Route::any('create-ftl', [FtlPtlController::class, 'createFtlLrForm']);
    Route::post('new-Ftl-create', [FtlPtlController::class, 'storeFtlLr']);
    Route::any('create-ptl', [FtlPtlController::class, 'createPtlLrForm']);
    Route::post('new-Ptl-create', [FtlPtlController::class, 'storePtlLr']);

    Route::resource('prs', PickupRunSheetController::class); 
    Route::post('/prs/update-prs', [PickupRunSheetController::class, 'UpdatePrs']);
    Route::get('prs/export/excel', [PickupRunSheetController::class, 'exportExcel']);
    Route::any('driver-tasks', [PickupRunSheetController::class,'driverTasks']);
    Route::any('driver-tasks/create-taskitem', [PickupRunSheetController::class,'createTaskItem']);
    Route::any('vehicle-receivegate', [PickupRunSheetController::class,'vehicleReceivegate']);
    Route::any('add-receive-vehicle', [PickupRunSheetController::class,'createReceiveVehicle']);
    Route::any('getlr-item', [PickupRunSheetController::class, 'getlrItems']);
    Route::get('pickup-loads', [PickupRunSheetController::class, 'pickupLoads']);
    Route::get('pickup-loads/export', [PickupRunSheetController::class, 'exportPickupLoad']);
    Route::any('prs-payment-request', [PickupRunSheetController::class, 'createPrsPayment']);
    Route::any('prs-request-list', [PickupRunSheetController::class, 'prsRequestList']);
    Route::any('get-vender-req-details-prs', [PickupRunSheetController::class, 'getVendorReqDetailsPrs']);
    Route::any('prs-rm-approver', [PickupRunSheetController::class, 'rmApproverRequest']);
    Route::any('show-prs', [PickupRunSheetController::class, 'showPrs']);
    Route::any('prs-paymentlist', [PickupRunSheetController::class, 'paymentList']);

});

Route::group(['prefix'=>'account-manager', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('dashboard', DashboardController::class);
    Route::resource('/', DashboardController::class);

    Route::resource('users', UserController::class);
    Route::post('/users/update-user', [UserController::class, 'updateUser']);
    Route::post('/users/delete-user', [UserController::class, 'deleteUser']);

    Route::resource('branches', BranchController::class);
    Route::post('branches/update-branch', [BranchController::class, 'updateBranch']);
    Route::post('branches/delete-branch', [BranchController::class, 'deleteBranch']);
    Route::post('branches/delete-branchimage', [BranchController::class, 'deletebranchImage']);

    Route::resource('consigners', ConsignerController::class);
    Route::post('consigners/update-consigner', [ConsignerController::class, 'updateConsigner']);
    Route::post('consigners/delete-consigner', [ConsignerController::class, 'deleteConsigner']);
    Route::get('consigners/export/excel', [ConsignerController::class, 'exportExcel']);

    Route::resource('consignees', ConsigneeController::class);
    Route::post('consignees/update-consignee', [ConsigneeController::class, 'updateConsignee']);
    Route::post('consignees/delete-consignee', [ConsigneeController::class, 'deleteConsignee']);
    Route::get('consignees/export/excel', [ConsigneeController::class, 'exportExcel']);

    Route::resource('drivers', DriverController::class);
    Route::post('drivers/update-driver', [DriverController::class, 'updateDriver']);
    Route::post('drivers/delete-driver', [DriverController::class, 'deleteDriver']);
    Route::post('/drivers/delete-licenseimage', [DriverController::class, 'deletelicenseImage']);
    Route::get('drivers/export/excel', [DriverController::class, 'exportExcel']);

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/update-vehicle', [VehicleController::class, 'updateVehicle']);
    Route::post('vehicles/delete-vehicle', [VehicleController::class, 'deleteVehicle']);
    Route::get('vehicles/export/excel', [VehicleController::class, 'exportExcel']);

    Route::resource('orders', OrderController::class);
    Route::post('orders/update-order', [OrderController::class, 'updateOrder']);
    Route::get('order-book-ftl', [OrderController::class, 'orderBookFtl']);
    Route::get('order-book-ptl', [OrderController::class, 'orderBookptl']);
    Route::post('store-Ftl-order', [OrderController::class, 'storeFtlOrder']);
    Route::post('store-Ptl-order', [OrderController::class, 'storePtlOrder']);
    Route::post('prs-receive-material', [OrderController::class, 'prsReceiveMaterial']);

    Route::resource('consignments', ConsignmentController::class);
    Route::post('consignments/update-consignment', [ConsignmentController::class, 'updateConsignment']);
    Route::post('consignments/delete-consignment', [ConsignmentController::class, 'deleteConsignment']);
    Route::post('consignments/get-consign-details', [ConsignmentController::class, 'getConsigndetails']);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::get('consignments/{id}/print-viewold/{typeid}', [ConsignmentController::class, 'consignPrintviewold']);
    Route::any('get-delivery-datamodel', [ConsignmentController::class, 'getdeliverydatamodel']);
    Route::any('bulklr-view', [ConsignmentController::class, 'BulkLrView']);
    Route::any('download-bulklr', [ConsignmentController::class, 'DownloadBulkLr']);
    Route::any('drs-status', [ConsignmentController::class, 'drsStatus']);
    Route::any('upload-delivery-img', [ConsignmentController::class, 'uploadDrsImg']);
    Route::post('all-save-deliverydate', [ConsignmentController::class, 'allSaveDRS']);
    Route::post('add-unverified-lr', [ConsignmentController::class, 'addunverifiedLr']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);

    Route::resource('locations', LocationController::class);
    Route::post('/locations/update', [LocationController::class, 'updateLocation']);
    Route::any('locations/get-location', [LocationController::class, 'getLocation']);

    Route::any('consignment-report2', [ReportController::class, 'consignmentReportsAll']);
    Route::any('consignment-report3', [ReportController::class, 'consignmentReportthree']);
    Route::any('view_invoices/{id}', [ConsignmentController::class, 'viewupdateInvoice']);
    Route::any('all-invoice-save', [ConsignmentController::class, 'allupdateInvoice']);

    Route::any('client-report', [ClientController::class, 'clientReport']);
    Route::get('/consignment-regclient', [ClientController::class, 'getConsignmentClient']);
    Route::get('clients/export', [ClientController::class, 'clientReportExport']);

    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('reports/export2', [ReportController::class, 'exportExcelReport2']);
    Route::any('reports/export3', [ReportController::class, 'exportExcelReport3']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);

    // vendor paymant
    Route::any('vendor-list', [VendorController::class, 'index']);
    Route::any('vendor/create', [VendorController::class, 'create']);
    Route::post('vendor/add-vendor', [VendorController::class, 'store']);
    Route::any('vendor/check-account-no', [VendorController::class, 'checkAccValid']);
    Route::any('drs-paymentlist', [VendorController::class, 'paymentList']);
    Route::any('get-drs-details', [VendorController::class, 'getdrsdetails']);
    Route::any('vendor-details', [VendorController::class, 'vendorbankdetails']);
    Route::any('create-payment', [VendorController::class, 'createPaymentRequest']);
    Route::any('view-vendor-details', [VendorController::class, 'view_vendor_details']);
    Route::any('update-purchas-price', [VendorController::class, 'update_purchase_price']);
    Route::any('edit-vendor/{id}', [VendorController::class, 'editViewVendor']);
    Route::post('vendor/update-vendor', [VendorController::class, 'updateVendor']);
    Route::any('import-vendor', [VendorController::class, 'importVendor']);
    Route::any('export-vendor', [VendorController::class, 'exportVendor']);
    Route::any('view-drslr/{id}', [VendorController::class, 'viewdrsLr']);
    Route::any('create-payment_request', [VendorController::class, 'createPaymentRequestVendor']);
    Route::any('request-list', [VendorController::class, 'requestList']);
    Route::any('get-vender-req-details', [VendorController::class, 'getVendorReqDetails']);
    Route::any('show-drs', [VendorController::class, 'showDrs']);
    Route::get('edit-purchase-price', [VendorController::class, 'editPurchasePrice']);
    Route::any('update-purchas-price-vehicle-type', [VendorController::class, 'updatePurchasePriceVehicleType']); //
    Route::get('get-balance-amount', [VendorController::class, 'getBalanceAmount']);
    Route::get('payment-report-view', [VendorController::class, 'paymentReportView']);
    Route::get('payment-reportExport', [VendorController::class, 'exportPaymentReport']);
    
    Route::get('prs-payment-report', [VendorController::class, 'prsPaymentReport']);
    Route::get('prs-payment-reportExport', [VendorController::class, 'exportPrsPaymentReport']);
    Route::get('drswise-report', [VendorController::class, 'drsWiseReport']);
    Route::get('export-drswise-report', [VendorController::class, 'exportdrsWiseReport']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);
    Route::any('pod-export', [ConsignmentController::class, 'exportPodFile']);

    Route::resource('clients', ClientController::class);
    Route::get('clients-list', [ClientController::class, 'clientList']);
    Route::post('/clients/update-client', [ClientController::class, 'UpdateClient']);
    Route::get('reginal-clients', [ClientController::class, 'regionalClients']);
    Route::post('/clients/delete-client', [ClientController::class, 'deleteClient']);
    Route::get('/reginal-clients/add-regclient-detail/{id}', [ClientController::class, 'createRegclientdetail']);
    Route::post('/regclient-detail/update-detail', [ClientController::class, 'updateRegclientdetail']);
    Route::get('/reginal-clients/view-regclient-detail/{id}', [ClientController::class, 'viewRegclientdetail']);
    Route::get('/regclient-detail/{id}/edit', [ClientController::class, 'editRegClientDetail']);
    Route::post('/save-regclient-detail', [ClientController::class, 'storeRegclientdetail']);   

    Route::get('create-regional-client', [ClientController::class, 'createRegionalClient']);
    Route::any('generate-regional', [ClientController::class, 'generateRegionalName']);
    Route::any('regclient-detail/update-generate-regional', [ClientController::class, 'updateGenerateRegionalName']);
    Route::any('create-regional', [ClientController::class, 'storeRegionalClient']);
    Route::any('edit-baseclient/{id}', [ClientController::class, 'editBaseClient']);
    Route::any('update-base-client', [ClientController::class, 'updateBaseClient']);

});
Route::group(['prefix'=>'client-account', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('consignments', ConsignmentController::class);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);
});
Route::group(['prefix'=>'client-user', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('consignments', ConsignmentController::class);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::any('consignment-misreport', [ReportController::class, 'consignmentReports']);
    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('get-filter-report', [ConsignmentController::class, 'getFilterReport']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);

    Route::any('get-delivery-dateLR', [ConsignmentController::class, 'getDeleveryDateLr']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);

});
Route::group(['prefix'=>'lr-cancel', 'middleware'=>['auth','PermissionCheck']], function()
{
    Route::resource('consignments', ConsignmentController::class);
    Route::get('consignments/{id}/print-view/{typeid}', [ConsignmentController::class, 'consignPrintview']);
    Route::any('print-sticker/{id}', [ConsignmentController::class, 'printSticker']);
    Route::any('consignment-misreport', [ReportController::class, 'consignmentReports']);
    Route::any('update-lrstatus', [ConsignmentController::class, 'updateLrStatus']);
    Route::any('reports/export1', [ReportController::class, 'exportExcelReport1']);
    Route::any('get-filter-report', [ConsignmentController::class, 'getFilterReport']);
    Route::get('get-jobs', [ConsignmentController::class, 'getJob']);

    Route::any('get-delivery-dateLR', [ConsignmentController::class, 'getDeleveryDateLr']);
    Route::get('pod-view', [ConsignmentController::class, 'podView']);
    Route::get('pod-list', [ConsignmentController::class, 'podList']);

});

Route::middleware(['auth'])->group(function () {
    Route::get('/get_drivers', [VehicleController::class, 'getDrivers']);
    Route::get('/get_consigners', [ConsignmentController::class, 'getConsigners']);
    Route::get('/get_consignees', [ConsignmentController::class, 'getConsignees']);

    Route::get('/get_regclients', [UserController::class, 'regClients']);
    Route::get('/get_locations', [ConsignerController::class, 'regLocations']);
    Route::any('/get-address-by-postcode', [ConsignerController::class, 'getPostalAddress']);
    Route::get('/get-consigner-regional', [ConsignmentController::class, 'getConsignersonRegional']);
    Route::get('/get-consignerprs', [PickupRunSheetController::class, 'getConsigner']);
    Route::get('/get-bill-client', [OrderController::class, 'getBillClient']);
 
    Route::get('vehicles/list',[VehicleController::class, "getData"]);
    Route::any('add-vendor', [VendorController::class, 'store']);
    Route::get('invoice-check', [ConsignmentController::class, 'invoiceCheck']);
    Route::get('vehicle/get-item', [PickupRunSheetController::class, 'getVehicleItem']);
    Route::get('get-items', [ConsignmentController::class, 'getItems']);

    Route::get('/get-regclients', [ReportController::class, 'getRegclients']);

});

Route::get('/forgot-session', [DashboardController::class, 'ForgotSession']);

Route::get('/forbidden-error', [DashboardController::class, 'ForbiddenPage']);
Route::post('webhook', [ConsignmentController::class, 'handle']);
Route::any('track-order', [TrackingController::class, 'trackOrder']);
Route::any('track-vehicle/{id}', [TrackingController::class, 'trackLr']);

///check paid status
Route::any('check-paid-status', [VendorController::class, 'check_paid_status']);
Route::any('check-paid-status-fully', [VendorController::class, 'check_paid_status_fully']);
Route::any('check-paid-status-advance', [VendorController::class, 'check_paid_status_advance']);
Route::any('regional-report', [ReportController::class, 'regionalReport']);


