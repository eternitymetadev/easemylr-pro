<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentNotes;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;

class ConsignmentNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
        
            $consignment_notes = ConsignmentNotes::paginate($request->paginator, ['*'], 'page', $request->page);
            if ($consignment_notes) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_notes
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
                'message' => "Failed to get consignment_notes, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $consignment_notes = ConsignmentNotes::create($request->all());
            $consignment_notes->save();

            return response([
                'status' => 'success',
                'code' => 1,
                'data' => $consignment_notes
            ], 200);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to store consignment_notes, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($search, Request $request)
    {
        try {
            $searchQuery = trim($search);
            $requestData = ['id','regclient_id','consigner_id','consignee_id','ship_to_id','consignment_no','consignment_date','payment_type','description','packing_type','dispatch','invoice_no','invoice_date','invoice_amount','vehicle_id','total_quantity','total_weight','total_gross_weight','total_freight','pdf_name','transporter_name','vehicle_type','purchase_price','freight','user_id','branch_id','driver_id','bar_code','reason_to_cancel','order_id','edd','status','job_id','tracking_link','delivery_status','delivery_date','signed_drs','e_way_bill','e_way_bill_date','created_at','updated_at'];
            $consignment_notes = ConsignmentNotes::where(function ($q) use ($requestData, $searchQuery) {
                foreach ($requestData as $field)
                    $q->orWhere($field, 'like', "%{$searchQuery}%");
            })->paginate($request->paginator, ['*'], 'page', $request->page);
            if ($consignment_notes) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_notes
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
                'message' => "Failed to get consignment_notes, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $consignment_notes = ConsignmentNotes::where('id', '=', $id)->first();
            if ($consignment_notes) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_notes
                ], 200);
            } else {

                return response([
                    'status' => 'error',
                    'code' => 0,
                    'message' => "No record found"
                ], 404);
            }
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to get consignment_notes data, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();

            $consignment_notes = ConsignmentNotes::find($id);

           $consignment_notes->regclient_id = $input['regclient_id'];$consignment_notes->consigner_id = $input['consigner_id'];$consignment_notes->consignee_id = $input['consignee_id'];$consignment_notes->ship_to_id = $input['ship_to_id'];$consignment_notes->consignment_no = $input['consignment_no'];$consignment_notes->consignment_date = $input['consignment_date'];$consignment_notes->payment_type = $input['payment_type'];$consignment_notes->description = $input['description'];$consignment_notes->packing_type = $input['packing_type'];$consignment_notes->dispatch = $input['dispatch'];$consignment_notes->invoice_no = $input['invoice_no'];$consignment_notes->invoice_date = $input['invoice_date'];$consignment_notes->invoice_amount = $input['invoice_amount'];$consignment_notes->vehicle_id = $input['vehicle_id'];$consignment_notes->total_quantity = $input['total_quantity'];$consignment_notes->total_weight = $input['total_weight'];$consignment_notes->total_gross_weight = $input['total_gross_weight'];$consignment_notes->total_freight = $input['total_freight'];$consignment_notes->pdf_name = $input['pdf_name'];$consignment_notes->transporter_name = $input['transporter_name'];$consignment_notes->vehicle_type = $input['vehicle_type'];$consignment_notes->purchase_price = $input['purchase_price'];$consignment_notes->freight = $input['freight'];$consignment_notes->user_id = $input['user_id'];$consignment_notes->branch_id = $input['branch_id'];$consignment_notes->driver_id = $input['driver_id'];$consignment_notes->bar_code = $input['bar_code'];$consignment_notes->reason_to_cancel = $input['reason_to_cancel'];$consignment_notes->order_id = $input['order_id'];$consignment_notes->edd = $input['edd'];$consignment_notes->status = $input['status'];$consignment_notes->job_id = $input['job_id'];$consignment_notes->tracking_link = $input['tracking_link'];$consignment_notes->delivery_status = $input['delivery_status'];$consignment_notes->delivery_date = $input['delivery_date'];$consignment_notes->signed_drs = $input['signed_drs'];$consignment_notes->e_way_bill = $input['e_way_bill'];$consignment_notes->e_way_bill_date = $input['e_way_bill_date'];$consignment_notes->created_at = $input['created_at'];$consignment_notes->updated_at = $input['updated_at'];

            $res = $consignment_notes->update();
            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_notes
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update consignment_notes"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update consignment_notes, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $res = ConsignmentNotes::find($id)->delete();
            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'message' => "Deleted successfully"
                ], 200);
            } else {
                return response([
                    'status' => 'error',
                    'code' => 0,
                    'data' => "Failed to delete consignment_notes"
                ], 500);
            }
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to delete consignment_notes, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
}

