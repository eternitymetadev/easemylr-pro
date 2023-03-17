<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ConsigneeImport;
use App\Imports\ConsigneePhoneImport;
use App\Imports\VehiclesImport;
use App\Imports\ConsignerImport;
use App\Imports\DriverImport;
use App\Imports\ZoneImport;
use App\Imports\DeliveryDateImport;
use App\Imports\ManualDeliveryImport;
use Maatwebsite\Excel\Facades\Excel;
use URL;
use ZipArchive;

class ImportCsvController extends Controller
{
    public $prefix;
    public function getBulkImport()
    {
        $this->prefix = request()->route()->getPrefix();
        return view('uploadcsv',['prefix'=>$this->prefix]);
    }

    public function uploadCsv(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        if($request->hasFile('consigneesfile')){
            $data = Excel::import(new ConsigneeImport,request()->file('consigneesfile'));
            $url  = URL::to($this->prefix.'/consignees');
            $message = 'Consignees Imported Successfully';
        }
        if($request->hasFile('consigneephonesfile')){
            $data = Excel::import(new ConsigneePhoneImport,request()->file('consigneephonesfile'));
            $url  = URL::to($this->prefix.'/consignees');
            $message = 'Consignee Phone Number Imported Successfully';
        }
        if($request->hasFile('vehiclesfile')){
            $data = Excel::import(new VehiclesImport,request()->file('vehiclesfile'));
            $url  = URL::to($this->prefix.'/vehicles');
            $message = "Vehicles Imported Successfully";
        }
        if($request->hasFile('consignersfile')){
            $data = Excel::import(new ConsignerImport,request()->file('consignersfile'));
            $url  = URL::to($this->prefix.'/consigners');
            $message = 'Consigners Uploaded Successfully';
        }
        if($request->hasFile('driversfile')){
            $data = Excel::import(new DriverImport,request()->file('driversfile'));
            $url  = URL::to($this->prefix.'/drivers');
            $message = 'Drivers Uploaded Successfully';
        }
        if($request->hasFile('zonesfile')){
            $data = Excel::import(new ZoneImport,request()->file('zonesfile'));
            $url  = URL::to($this->prefix.'/zones');
            $message = 'Zones Uploaded Successfully';
        }
        if($request->hasFile('deliverydatesfile')){
            $data = Excel::import(new DeliveryDateImport,request()->file('deliverydatesfile'));
            $url  = URL::to($this->prefix.'/consignments');
            $message = 'Delivery dates Uploaded Successfully';
        }
        if($request->hasFile('manualdeliveryfile')){
            $data = Excel::import(new ManualDeliveryImport,request()->file('manualdeliveryfile'));
            $url  = URL::to($this->prefix.'/consignments');
            $message = 'Manual delivery status Uploaded Successfully';
        }
        if($request->hasFile('podsfile')){
            $url  = URL::to($this->prefix.'/consignments');
            $fileName = $_FILES['podsfile']['name'];
            $fileNameArr = explode(".",$fileName);
            if($fileNameArr[count($fileNameArr)-1]=='zip'){
                $fileName = $fileNameArr[0];
                $zip = new ZipArchive();
                if($zip->open($_FILES['podsfile']['tmp_name'])===True){
                    // $rand = rand(111111111,999999999);
                    $data = $zip->extractTo("drs/");
                    $zip->close();
                    $message = 'Unzip done!';
                }else{
                    $message = 'Something went wrong!';
                }
                $message = 'PODs Uploaded Successfully';
            }else{
                $message = 'Please select zip file';
            }
            
        }
        if($data){            
            $response['success']    = true;
            $response['page']       = 'bulk-imports';
            $response['error']      = false;
            $response['success_message'] = $message;
            $response['redirect_url'] = $url;
        }else{
            $response['success']       = false;
            $response['error']         = true;
            $response['error_message'] = "Can not import consignees please try again";
        }
        return response()->json($response);
    }

    ////////////////////////sample download////////////////////////////////
    public function consigneesSampleDownload()
    {
        $path = public_path('sample/consignee_bulkimport.xlsx');
        return response()->download($path);     // use download() helper to download the file
    }

    public function consigneePhoneSampleDownload()
    {
        $path = public_path('sample/consigneephone_bulk_import.xlsx');
        return response()->download($path);     // use download() helper to download the file
    }

    public function consignerSampleDownload()
    {
        $path = public_path('sample/consigner_bulk_import.xlsx');
        return response()->download($path);   
    }

    public function vehicleSampleDownload()
    {
        $path = public_path('sample/vehicle_bulkimport.xlsx');
        return response()->download($path); 
    }

    public function driverSampleDownload()
    {
        $path = public_path('sample/driver_bulkimport.xlsx');
        return response()->download($path);   
    }

    public function zoneSampleDownload()
    {
        $path = public_path('sample/zone_bulkimport.xlsx');
        return response()->download($path);   
    }

    public function deliverydateSampleDownload()
    {
        $path = public_path('sample/deliverydate_bulkimport.xlsx');
        return response()->download($path);
    }

    public function manualdeliverySampleDownload()
    {
        $path = public_path('sample/manualdelivery_bulkimport.xlsx');
        return response()->download($path);
    }


}
