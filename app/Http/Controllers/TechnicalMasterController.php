<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\TechnicalImport;
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

        return view('technical-master.technical-masters', ['prefix' => $this->prefix]);
    }

    public function importTechnicalMaster(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $data = Excel::import(new TechnicalImport, request()->file('technical_file'));
        die;

        if ($data) {
            $response['success'] = true;
            $response['page'] = 'bulk-imports';
            $response['error'] = false;
            $response['success_message'] = $message;
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['error_message'] = "Can not import consignees please try again";
        }
        return response()->json($response);
    }
}
