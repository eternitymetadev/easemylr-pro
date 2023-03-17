<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Consignees;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;

class ConsigneesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
        
            $consignees = Consignees::paginate($request->paginator, ['*'], 'page', $request->page);
            if ($consignees) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignees
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
                'message' => "Failed to get consignees, please try again. {$exception->getMessage()}"
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
            $consignees = Consignees::create($request->all());
            $consignees->save();

            return response([
                'status' => 'success',
                'code' => 1,
                'data' => $consignees
            ], 200);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to store consignees, please try again. {$exception->getMessage()}"
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
            $requestData = ['id','nick_name','legal_name','user_id','branch_id','consigner_id','zone_id','dealer_type','gst_number','contact_name','phone','email','sales_officer_name','sales_officer_email','sales_officer_phone','address_line1','address_line2','address_line3','address_line4','city','district','postal_code','state_id','status','created_at','updated_at'];
            $consignees = Consignees::where(function ($q) use ($requestData, $searchQuery) {
                foreach ($requestData as $field)
                    $q->orWhere($field, 'like', "%{$searchQuery}%");
            })->paginate($request->paginator, ['*'], 'page', $request->page);
            if ($consignees) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignees
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
                'message' => "Failed to get consignees, please try again. {$exception->getMessage()}"
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
            $consignees = Consignees::where('id', '=', $id)->first();
            if ($consignees) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignees
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
                'message' => "Failed to get consignees data, please try again. {$exception->getMessage()}"
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

            $consignees = Consignees::find($id);

           $consignees->nick_name = $input['nick_name'];$consignees->legal_name = $input['legal_name'];$consignees->user_id = $input['user_id'];$consignees->branch_id = $input['branch_id'];$consignees->consigner_id = $input['consigner_id'];$consignees->zone_id = $input['zone_id'];$consignees->dealer_type = $input['dealer_type'];$consignees->gst_number = $input['gst_number'];$consignees->contact_name = $input['contact_name'];$consignees->phone = $input['phone'];$consignees->email = $input['email'];$consignees->sales_officer_name = $input['sales_officer_name'];$consignees->sales_officer_email = $input['sales_officer_email'];$consignees->sales_officer_phone = $input['sales_officer_phone'];$consignees->address_line1 = $input['address_line1'];$consignees->address_line2 = $input['address_line2'];$consignees->address_line3 = $input['address_line3'];$consignees->address_line4 = $input['address_line4'];$consignees->city = $input['city'];$consignees->district = $input['district'];$consignees->postal_code = $input['postal_code'];$consignees->state_id = $input['state_id'];$consignees->status = $input['status'];$consignees->created_at = $input['created_at'];$consignees->updated_at = $input['updated_at'];

            $res = $consignees->update();
            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $consignees
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update consignees"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update consignees, please try again. {$exception->getMessage()}"
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
            $res = Consignees::find($id)->delete();
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
                    'data' => "Failed to delete consignees"
                ], 500);
            }
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to delete consignees, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
}

