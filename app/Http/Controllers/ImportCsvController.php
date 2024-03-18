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
use App\Imports\UpdateLatitudeLongitude;
use Maatwebsite\Excel\Facades\Excel;
use URL;
use Storage;
use ZipArchive;
use Aws\S3\S3Client;

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
        $failedLRs = '';
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
        if ($request->hasFile('deliverydatesfile')) {
            // $data = Excel::import(new DeliveryDateImport,request()->file('deliverydatesfile'));
            $import = new DeliveryDateImport;
            $data = Excel::import($import, request()->file('deliverydatesfile'));
            $url = URL::to($this->prefix.'/consignments');
            $failedLRs = $import->getFailedLRs();
            $message = 'Delivery dates Uploaded Successfully';
        }
        if($request->hasFile('manualdeliveryfile')){
            $data = Excel::import(new ManualDeliveryImport,request()->file('manualdeliveryfile'));
            $url  = URL::to($this->prefix.'/consignments');
            $message = 'Manual delivery status Uploaded Successfully';
        }
        if($request->hasFile('lat_lang')){
            $data = Excel::import(new UpdateLatitudeLongitude,request()->file('lat_lang'));
            $url  = URL::to($this->prefix.'/consignments');
            $message = 'Latitude longitude Uploaded Successfully';
        }

        // multiple pod images upload
        if ($request->hasFile('podsfile')) {
            $url  = URL::to($this->prefix.'/bulk-import');
            // Get the uploaded ZIP file
            $uploadedFile = $request->file('podsfile');

            // Create a temporary directory to extract the files
            $tempDir = storage_path('app/temp/');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Extract the ZIP file
            $zip = new ZipArchive;
            if ($zip->open($uploadedFile->path()) === true) {
                // Specify the directory where you want to extract the files
                $extractedDir = $tempDir ;
                if (!file_exists($extractedDir)) {
                    mkdir($extractedDir, 0755, true);
                }

                // Extract all files from the ZIP archive
                $zip->extractTo($extractedDir);
                $zip->close();
                $imgPath = $extractedDir.'pod_images';
                // Loop through the extracted files
                $successfulUploads = [];
                $failedUploads = [];
                foreach (scandir($imgPath) as $file) {
                    if ($file !== '.' && $file !== '..') {
                        // Construct the full path to the extracted file
                        $filePath = $extractedDir .'pod_images/'. $file;

                        // Check if the file is an image (you can add more image extensions if needed)
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                        $fileExtension = pathinfo(trim($file), PATHINFO_EXTENSION);
                        // dd('File Extension: ' . $fileExtension);

                        if (empty($fileExtension)) {
                            // You can change 'unknown' to a default extension of your choice
                            $fileExtension = 'unknown';
                        }
                        
                        if (in_array(strtolower($fileExtension), $imageExtensions)) {
                            
                            // Construct the full S3 URL using AWS_S3_URL
                            $s3Url = env('AWS_S3_URL') . 'pod_images/' . $file;

                            // Update the message with the S3 URL
                            // $message[] = 'Image Uploaded to S3: ' . $s3Url;
                            // Ensure the file exists before attempting to upload it
                            if (file_exists($filePath)) {
                                // Upload the file to AWS S3
                                // dd($file);
                                if (Storage::disk('s3')->putFileAs('pod_images', $filePath, $file)) {
                                    // Upload was successful
                                    $successfulUploads[] = $file;
                                } else {
                                    // Upload failed
                                    $failedUploads[] = $file;
                                    // You can log the error or take other appropriate actions here
                                }
                            } else {
                                $failedUploads[] = $file;
                            }
                        }
                    }
                }

                if($successfulUploads){
                    $data = true;
                    $message = 'Files successfully uploaded: ' . implode(', ', $successfulUploads);
                }else{
                    $message = 'Files not uploaded: ' . implode(', ', $failedUploads);
                }
                // $message = [
                //     'success' => 'Files successfully uploaded: ' . implode(', ', $successfulUploads),
                //     'failure' => 'Files not uploaded: ' . implode(', ', $failedUploads),
                // ];

                // Clean up the temporary directories
                Storage::disk('local')->deleteDirectory('temp');
                Storage::disk('local')->deleteDirectory('pod_images');

            }else{
                $message = 'Files not uploaded';
            } 
        }
        
        // if($request->hasFile('podsfile1')){
        //     $url  = URL::to($this->prefix.'/consignments');
        //     $fileName = $_FILES['podsfile']['name'];
        //     $fileNameArr = explode(".",$fileName);
        //     if($fileNameArr[count($fileNameArr)-1]=='zip'){
        //         $fileName = $fileNameArr[0];
        //         $zip = new ZipArchive();
        //         if($zip->open($_FILES['podsfile']['tmp_name'])===True){
        //             // $rand = rand(111111111,999999999);
        //             $data = $zip->extractTo("drs/");
        //             $zip->close();
        //             $message = 'Unzip done!';
        //         }else{
        //             $message = 'Something went wrong!';
        //         }
        //         $message = 'PODs Uploaded Successfully';
        //     }else{
        //         $message = 'Please select zip file';
        //     }
            
        // }
        $response = [];
        
        if($failedLRs){
            $response['failedLRs'] = $failedLRs;
        }else{
            $response['failedLRs'] = '';
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
            $response['error_message'] = "Can not import pods please try again";
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

    public function vendorRouteSampleDownload()
    {
        $path = public_path('sample/vendorroute_bulkimport.xlsx');
        return response()->download($path);
    }


}
