<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LastMilePartner;

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
}
