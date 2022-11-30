<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\TechnicalImport;
use App\Imports\ItemUpload;
use App\Models\TechnicalMaster;
use App\Models\ItemMaster;
use Excel;

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

    public function getItemDetails(Request $request)
    {
        echo "<pre>"; print_r($request); die;
    }
}
