<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LastMilePartner;
use App\Models\CarrierPartner;
use App\Models\DriverPartner;
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
        
            $saveLastmile = LastMilePartner::create($addmile);
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
        
            $saveCarrier = CarrierPartner::create($addCarrier);
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
            $authuser = Auth::user();
            
            if (!empty($request->name)) {
                $addDriver['contact_name'] = $request->name;
            }
            if (!empty($request->address)) {
                $addDriver['company_add'] = $request->address;
            }
            if (!empty($request->driving_record)) {
                $addDriver['exp_details'] = $request->driving_record;
            }
            if (!empty($request->exp_details)) {
                $addDriver['exp_details'] = $request->exp_details;
            }
            if (!empty($request->is_available)) {
                $addDriver['is_available'] = $request->is_available;
            }
            if (!empty($request->is_compliant)) {
                $addDriver['is_compliant'] = $request->is_compliant;
            }
            if (!empty($request->is_flexible)) {
                $addDriver['is_flexible'] = $request->is_flexible;
            }
            if (!empty($request->preferred_state)) {
                $addDriver['state'] = $request->preferred_state;
            }
            if (!empty($request->valid_license)) {
                $addDriver['valid_license'] = $request->valid_license;
            }
            if (!empty($request->working_years)) {
                $addDriver['working_years'] = $request->working_years;
            }
            $addDriver['status'] = 1;
        
            $saveDriver = DriverPartner::create($addDriver);
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
            
            if (!empty($request->name)) {
                $addDriver['contact_name'] = $request->name;
            }
            if (!empty($request->address)) {
                $addDriver['company_add'] = $request->address;
            }
            if (!empty($request->driving_record)) {
                $addDriver['exp_details'] = $request->driving_record;
            }
            if (!empty($request->exp_details)) {
                $addDriver['exp_details'] = $request->exp_details;
            }
            if (!empty($request->is_available)) {
                $addDriver['is_available'] = $request->is_available;
            }
            if (!empty($request->is_compliant)) {
                $addDriver['is_compliant'] = $request->is_compliant;
            }
            if (!empty($request->is_flexible)) {
                $addDriver['is_flexible'] = $request->is_flexible;
            }
            if (!empty($request->preferred_state)) {
                $addDriver['state'] = $request->preferred_state;
            }
            if (!empty($request->valid_license)) {
                $addDriver['valid_license'] = $request->valid_license;
            }
            if (!empty($request->working_years)) {
                $addDriver['working_years'] = $request->working_years;
            }
            $addDriver['status'] = 1;
        
            $saveDriver = DriverPartner::create($addDriver);
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

    public function carrier(Request $request){
        try {

        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function shipnow(Request $request){
        try {

        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

}
