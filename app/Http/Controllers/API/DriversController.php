<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\ConsignmentNote;


class DriversController extends Controller
{
    public function index(Request $request)
    {
        try {
        
            $drivers = ConsignmentNote::with('ConsignerDetail', 'ConsigneeDetail', 'ShiptoDetail', 'VehicleDetail','DriverDetail')
            ->where('driver_id', 2)
            ->get();
            if ($drivers) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $drivers
                ], 200);
            } else {
                return response([
                    'status' => 'error',
                    'code' => 0,
                    'data' => "No record found"
                ], 404);
            }
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to get drivers, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
}
