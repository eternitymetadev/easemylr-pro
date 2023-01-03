<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\TechnicalImport;
use App\Imports\ItemUpload;
use App\Models\TechnicalMaster;
use App\Models\ItemMaster;
use Excel;
use Auth;

class TechnicalMasterController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Secondary Reports";
        $this->segment = \Request::segment(2);

    }
    public function techicalMaster()
    {
        $this->prefix = request()->route()->getPrefix();
        $query = TechnicalMaster::query();
        $technicals = $query->with('TechnicalFormula')->get();

        return view('technical-master.technical-masters', ['prefix' => $this->prefix , 'technicals' => $technicals]);
    }

    public function importTechnicalMaster(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $data = Excel::import(new TechnicalImport, request()->file('technical_file'));
        $message = "Data imported Successfully";

        if ($data) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import consignees please try again";
        }
        return response()->json($response);
    }

    public function itemUploadView()
    {
        $this->prefix = request()->route()->getPrefix();
        $query = ItemMaster::query();
        $items = $query->get();
        return view('item-upload.item-upload', ['prefix' => $this->prefix, 'items' => $items]);
    }

    public function importItems(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $data = Excel::import(new ItemUpload, request()->file('item_file'));
        $message = "Data imported Successfully";

        if ($data) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import item Master please try again";
        }
        return response()->json($response);
    }

    public function addTechnicalName(Request $request)
    { 
        $this->prefix = request()->route()->getPrefix();
         
        $check_technical_name = TechnicalMaster::where('technical_formula',$request->technical_name)->first();
        if(!empty($check_technical_name)){

            $response['success_message'] = false;
            $response['error'] = true;
            $response['error_message'] = "Name already exists";
            return response()->json($response);
        }

        $data =  TechnicalMaster::create(['technical_formula' => $request->technical_name]);
        $message = "Data imported Successfully";

        if ($data) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import item Master please try again";
        }
        return response()->json($response);
    }

    public function addItems(Request $request)
    { 
        $this->prefix = request()->route()->getPrefix();

            $itemsave['technical_formula'] = $request->technical_formula;
            $itemsave['erp_mat_code'] = $request->erp_mat_code;
            $itemsave['manufacturer'] = $request->manufacturer;
            $itemsave['brand_name']   = $request->brand_name;
            $itemsave['net_weight'] = $request->net_weight;
            $itemsave['gross_weight'] = $request->gross_weight;
            $itemsave['chargable_weight'] = $request->chargable_weight;

        $data =  ItemMaster::create($itemsave);
        $message = "Data imported Successfully";

        if ($data) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import item Master please try again";
        }
        return response()->json($response);
    }
    public function editItemName(Request $request)
    {
        
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $cc = explode(',', $authuser->branch_id);

        $getItem = ItemMaster::where('id', $request->item_id)->first();

        $response['getItem'] = $getItem;
        $response['success'] = true;
        $response['message'] = "verified account";
        return response()->json($response);
    }

    public function updateItemName(Request $request)
    {
        // echo'<pre>'; print_r($request->all()); die;
        $this->prefix = request()->route()->getPrefix();

            $itemsave['technical_formula'] = $request->technical_formula;
            $itemsave['erp_mat_code'] = $request->erp_mat_code;
            $itemsave['manufacturer'] = $request->manufacturer;
            $itemsave['brand_name']   = $request->brand_name;
            $itemsave['net_weight'] = $request->net_weight;
            $itemsave['gross_weight'] = $request->gross_weight;
            $itemsave['chargable_weight'] = $request->chargable_weight;

        $data =  ItemMaster::where('id', $request->item_id)->update($itemsave);
        $message = "Item Updated Successfully";

        if ($data) {
            $response['success'] = true;
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not update please try again";
        }
        return response()->json($response);
    }
}