<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentItems;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;

class ConsignmentItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
        
            $consignment_items = ConsignmentItems::paginate($request->paginator, ['*'], 'page', $request->page);
            if ($consignment_items) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_items
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
                'message' => "Failed to get consignment_items, please try again. {$exception->getMessage()}"
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
            $consignment_items = ConsignmentItems::create($request->all());
            $consignment_items->save();

            return response([
                'status' => 'success',
                'code' => 1,
                'data' => $consignment_items
            ], 200);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to store consignment_items, please try again. {$exception->getMessage()}"
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
            $requestData = ['id','consignment_id','description','packing_type','quantity','weight','gross_weight','freight','payment_type','order_id','invoice_no','invoice_date','invoice_amount','e_way_bill','e_way_bill_date','status','created_at','updated_at'];
            $consignment_items = ConsignmentItems::where(function ($q) use ($requestData, $searchQuery) {
                foreach ($requestData as $field)
                    $q->orWhere($field, 'like', "%{$searchQuery}%");
            })->paginate($request->paginator, ['*'], 'page', $request->page);
            if ($consignment_items) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_items
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
                'message' => "Failed to get consignment_items, please try again. {$exception->getMessage()}"
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
            $consignment_items = ConsignmentItems::where('id', '=', $id)->first();
            if ($consignment_items) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_items
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
                'message' => "Failed to get consignment_items data, please try again. {$exception->getMessage()}"
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

            $consignment_items = ConsignmentItems::find($id);

           $consignment_items->consignment_id = $input['consignment_id'];$consignment_items->description = $input['description'];$consignment_items->packing_type = $input['packing_type'];$consignment_items->quantity = $input['quantity'];$consignment_items->weight = $input['weight'];$consignment_items->gross_weight = $input['gross_weight'];$consignment_items->freight = $input['freight'];$consignment_items->payment_type = $input['payment_type'];$consignment_items->order_id = $input['order_id'];$consignment_items->invoice_no = $input['invoice_no'];$consignment_items->invoice_date = $input['invoice_date'];$consignment_items->invoice_amount = $input['invoice_amount'];$consignment_items->e_way_bill = $input['e_way_bill'];$consignment_items->e_way_bill_date = $input['e_way_bill_date'];$consignment_items->status = $input['status'];$consignment_items->created_at = $input['created_at'];$consignment_items->updated_at = $input['updated_at'];

            $res = $consignment_items->update();
            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignment_items
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update consignment_items"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update consignment_items, please try again. {$exception->getMessage()}"
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
            $res = ConsignmentItems::find($id)->delete();
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
                    'data' => "Failed to delete consignment_items"
                ], 500);
            }
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to delete consignment_items, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
}

