<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EfLastMilePartner;
use App\Models\EfCarrierPartner;
use App\Models\EfDriverPartner;
use App\Models\EfContactUs;
use App\Models\EfCareer;
use App\Models\EfShipnow;
use App\Models\EfDeliveryRating;
use DB;
use URL;
use Helper;
use Validator;
use Storage;
use Auth;

class TrackApiController extends Controller
{
    public function lastmilePartner(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();
            
            if(!empty($request->company)){
                $addmile['company_name'] = $request->company;
            }
            if(!empty($request->contactPerson)){
                $addmile['contact_name'] = $request->contactPerson;
            }
            if(!empty($request->email)){
                $addmile['email'] = $request->email;
            }
            if(!empty($request->phone)){
                $addmile['phone'] = $request->phone;
            }
            if(!empty($request->companyAddress)){
                $addmile['company_add'] = $request->companyAddress;
            }
            if(!empty($request->goodsType)){
                $addmile['goods_type'] = $request->goodsType;
            }
            if(!empty($request->workingState)){
                $addmile['state'] = $request->workingState;
            }
            if(!empty($request->volume)){
                $addmile['volume'] = $request->volume;
            }
            if(!empty($request->deliveryFrequency)){
                $addmile['delivery_frequency'] = $request->deliveryFrequency;
            }
            if(!empty($request->specialDeliveryConsideration)){
                $addmile['special_delivery'] = $request->specialDeliveryConsideration;
            }
            if(!empty($request->expectedTimeline)){
                $addmile['expected_timeline'] = $request->expectedTimeline;
            }
            if(!empty($request->deliveryType)){
                $addmile['delivery_type'] = $request->deliveryType;
            }
            if(!empty($request->reference)){
                $addmile['reference'] = $request->reference;
            }
            $addmile['status'] = 1;
        
            $saveLastmile = EfLastMilePartner::create($addmile);
            if($saveLastmile){
                $data = '';
                $message = "Last Mile created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addmile;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function carrierPartner(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();
            
            if (!empty($request->company)) {
                $addCarrier['company'] = $request->company;
            }
            if (!empty($request->contactPerson)) {
                $addCarrier['contactPerson'] = $request->contactPerson;
            }
            if (!empty($request->email)) {
                $addCarrier['email'] = $request->email;
            }
            if (!empty($request->phone)) {
                $addCarrier['phone'] = $request->phone;
            }
            if (!empty($request->companyAddress)) {
                $addCarrier['companyAddress'] = $request->companyAddress;
            }
            if (!empty($request->areaOfDelivery)) {
                $addCarrier['areaOfDelivery'] = $request->areaOfDelivery;
            }
            if (!empty($request->fleetSize)) {
                $addCarrier['fleetSize'] = $request->fleetSize;
            }
            if (!empty($request->isCompliant)) {
                $addCarrier['isCompliant'] = $request->isCompliant;
            }
            if (!empty($request->leaseVehicle)) {
                $addCarrier['leaseVehicle'] = $request->leaseVehicle;
            }
            if (!empty($request->reference)) {
                $addCarrier['reference'] = $request->reference;
            }
            if (!empty($request->specializedTransportation)) {
                $addCarrier['specializedTransportation'] = $request->specializedTransportation;
            }
            if (!empty($request->typeOfShipment)) {
                $addCarrier['typeOfShipment'] = $request->typeOfShipment;
            }
            if (!empty($request->valueAddedServices)) {
                $addCarrier['valueAddedServices'] = $request->valueAddedServices;
            }
            if (!empty($request->workingYears)) {
                $addCarrier['workingYears'] = $request->workingYears;
            }
            $addCarrier['status'] = 1;
            
            $saveCarrier = EfCarrierPartner::create($addCarrier);
            if($saveCarrier){
                $data = '';
                $message = "Carrier created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addCarrier;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function driverPartner(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }            
            if (!empty($request->name)) {
                $addDriver['name'] = $request->name;
            }
            if (!empty($request->email)) {
                $addDriver['email'] = $request->email;
            }
            if (!empty($request->phone)) {
                $addDriver['phone'] = $request->phone;
            }
            if (!empty($request->address)) {
                $addDriver['address'] = $request->address;
            }
            if (!empty($request->drivingRecord)) {
                $addDriver['driving_record'] = $request->drivingRecord;
            }
            if (!empty($request->expDetails)) {
                $addDriver['exp_details'] = $request->expDetails;
            }
            if (!empty($request->isAvailable)) {
                $addDriver['is_available'] = $request->isAvailable;
            }
            if (!empty($request->isCompliant)) {
                $addDriver['is_compliant'] = $request->isCompliant;
            }
            if (!empty($request->isFlexible)) {
                $addDriver['is_flexible'] = $request->isFlexible;
            }
            if (!empty($request->preferredState)) {
                $addDriver['preferred_state'] = $request->preferredState;
            }
            if (!empty($request->validLicense)) {
                $addDriver['valid_license'] = $request->validLicense;
            }
            if (!empty($request->workingYears)) {
                $addDriver['working_years'] = $request->workingYears;
            }
            if (!empty($request->reference)) {
                $addDriver['reference'] = $request->reference;
            }
            $addDriver['status'] = 1;
        
            $saveDriver = EfDriverPartner::create($addDriver);
            if($saveDriver){
                $data = '';
                $message = "Driver created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addDriver;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function contactUs(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();
            
            if (!empty($request->fullName)) {
                $addDriver['fullName'] = $request->fullName;
            }

            if (!empty($request->companyName)) {
                $addContact['companyName'] = $request->companyName;
            }
            if (!empty($request->companyWebsite)) {
                $addContact['companyWebsite'] = $request->companyWebsite;
            }
            if (!empty($request->connectionPreference)) {
                $addContact['connectionPreference'] = $request->connectionPreference;
            }
            if (!empty($request->consent)) {
                $addContact['consent'] = $request->consent;
            }
            if (!empty($request->email)) {
                $addContact['email'] = $request->email;
            }
            if (!empty($request->phone)) {
                $addContact['phone'] = $request->phone;
            }
            if (!empty($request->serviceType)) {
                $addContact['serviceType'] = $request->serviceType;
            }
            if (!empty($request->state)) {
                $addContact['state'] = $request->state;
            }

            $addContact['status'] = 1;
        
            $saveContact = EfContactUs::create($addContact);
            if($saveContact){
                $data = '';
                $message = "Contact created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addContact;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function career(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();
            
            if (!empty($request->fullName)) {
                $addCareer['fullName'] = $request->fullName;
            }
            if (!empty($request->email)) {
                $addCareer['email'] = $request->email;
            }
            if (!empty($request->phone)) {
                $addCareer['phone'] = $request->phone;
            }
            if (!empty($request->education)) {
                $addCareer['education'] = $request->education;
            }
            if (!empty($request->location)) {
                $addCareer['location'] = $request->location;
            }
            // if (!empty($request->cv)) {
            //     $addCareer['cv'] = $request->cv;
            // }
            $addCareer['status'] = 1;
        
            $saveCareer = EfCareer::create($addCareer);
            if($saveCareer){
                $data = '';
                $message = "Career created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addCareer;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }

        }catch(\Exception $e) {
            $data = '';
            $message = $e->getMessage();
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function shipnow(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();
            
            if (!empty($request->pickUp)) {
                $addShipnow['pickUp'] = $request->pickUp;
            }
            if (!empty($request->drop)) {
                $addShipnow['drop'] = $request->drop;
            }
            if (!empty($request->phone)) {
                $addShipnow['phone'] = $request->phone;
            }
            
            $addShipnow['status'] = 1;
        
            $saveShipnow = EfShipnow::create($addShipnow);
            if($saveShipnow){
                $data = '';
                $message = "Shipnow Us created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addShipnow;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function deliveryRating(Request $request){
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                //'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();
            
            if (!empty($request->rating)) {
                $addRating['rating'] = $request->rating;
            }
            if (!empty($request->feedback)) {
                $addRating['feedback'] = $request->feedback;
            }
            if (!empty($request->lr_id)) {
                $addRating['lr_id'] = $request->lr_id;
            }
            
            $addRating['status'] = 1;
        
            $saveRating = EfDeliveryRating::create($addRating);
            if($saveRating){
                $data = '';
                $message = "Rating Us created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addRating;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

}
