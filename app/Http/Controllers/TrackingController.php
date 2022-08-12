<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\RegionalClient;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Vehicle;
use App\Models\VehicleType;
use DB;
use Storage;
use Helper;
use Response;


class TrackingController extends Controller
{

    public function __construct()
    {
        $this->title = "Consignments Tracking";
    }
    
    public function trackorder(Request $request)
    {

        echo "<pre>";print_r("hii");die;

    }

}    