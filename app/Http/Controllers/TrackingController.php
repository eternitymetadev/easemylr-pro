<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentNote;
use App\Models\Coordinate;
use Illuminate\Http\Request;
use Response;

class TrackingController extends Controller
{

    public function __construct()
    {
        $this->title = "Consignments Tracking";
    }

    public function trackOrder(Request $request)
    {
        $lr_no = $request->lr;

        $getconsi = ConsignmentNote::select('*')->with('ConsigneeDetail', 'ConsignerDetail', 'ShiptoDetail', 'VehicleDetail', 'DriverDetail', 'JobDetail')->where(['id' => $lr_no])
            ->get();
        $simplify = json_decode(json_encode($getconsi), true);

        $response['fetch'] = $simplify;
        $response['success'] = true;
        $response['messages'] = 'Succesfully loaded';
        return Response::json($response);

    }
    public function trackLr(Request $request, $id)
    {

        $get_lr_details = ConsignmentNote::with('ConsigneeDetail')->where('id', $id)->first();
        $get_consigne_pin = $get_lr_details->ConsigneeDetail->postal_code;

        $get_last_cordinate = Coordinate::where('consignment_id', $id)->orderBy('id', 'DESC')->first();

        return view('map-view', ['get_consigne_pin' => $get_consigne_pin, 'get_last_cordinate' => $get_last_cordinate]);

    }

}
