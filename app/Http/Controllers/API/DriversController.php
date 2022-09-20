<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\ConsignmentNote;
use App\Models\TransactionSheet;



class DriversController extends Controller
{
    public function index(Request $request)
    {
        try {
        
            $drivers = TransactionSheet::with('ConsignmentNote')
            ->get();
            // echo "<pre>"; print_r($drivers->consignment_note); die;
            // $drivers = $drivers->ConsignmentNote::where('driver_id', 2)->get();
            foreach($drivers as $value) {
                   $data[] =[
                       'lr_no' => $value->id,
                       'lr_date'=> $value->consignment_date,
                       'drs_no' => $value->drs_no,

                   ];

            }
            // echo'<pre>'; print_r($data); die;
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
