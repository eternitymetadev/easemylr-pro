<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\TransactionSheet;
use App\Models\ConsignmentNote;
use App\Models\Job;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use DB;

class TransactionSheetsController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        try {

            $transaction_sheets = TransactionSheet::with('ConsignmentNote.ConsigneeDetail')->whereHas('ConsignmentNote', function($q){
                $q->where('driver_id', 2);
            })
            ->get();

            if ($transaction_sheets) {

                return response([

                    'status' => 'success',

                    'code' => 1,

                    'data' => $transaction_sheets

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

                'message' => "Failed to get transaction_sheets, please try again. {$exception->getMessage()}"

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

            $transaction_sheets = TransactionSheets::create($request->all());

            $transaction_sheets->save();



            return response([

                'status' => 'success',

                'code' => 1,

                'data' => $transaction_sheets

            ], 200);

        } catch (\Exception $exception) {

            return response([

                'status' => 'error',

                'code' => 0,

                'message' => "Failed to store transaction_sheets, please try again. {$exception->getMessage()}"

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

            $requestData = ['id','drs_no','consignment_no','consignee_id','consignment_date','city','pincode','total_quantity','total_weight','order_no','vehicle_no','driver_name','driver_no','branch_id','delivery_status','delivery_date','job_id','status','created_at','updated_at'];

            $transaction_sheets = TransactionSheet::where(function ($q) use ($requestData, $searchQuery) {

                foreach ($requestData as $field)

                    $q->orWhere($field, 'like', "%{$searchQuery}%");

            })->paginate($request->paginator, ['*'], 'page', $request->page);

            if ($transaction_sheets) {

                return response([

                    'status' => 'success',

                    'code' => 1,

                    'data' => $transaction_sheets

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

                'message' => "Failed to get transaction_sheets, please try again. {$exception->getMessage()}"

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
            
          
             $consignments = ConsignmentNote::with('TransactionSheet','ConsigneeDetail','ConsignmentItems')->where('driver_id', $id)
            ->get();
            // echo'<pre>'; print_r($consignments); die;
         
            foreach($consignments as $value){
            //    echo'<pre>'; print_r($value->ConsignmentItems); die;
                    $order = array();
                    $invoices = array();
                   
                   foreach($value->ConsignmentItems as $orders){
                     
                           $order[] = $orders->order_id;
                        
                           $invoices[] = $orders->invoice_no;

                   }
                            $order_item['orders'] = implode(',', $order);
                            $order_item['invoices'] = implode(',', $invoices);

                    
                  $data[] =[
                       'lr_no'                     => $value->id,
                       'lr_date'                   => $value->consignment_date,
                       'edd'                       => $value->edd,
                       'total_gross_weight'        => $value->total_gross_weight,
                       'total_quantity'            => $value->total_quantity,
                       'drs_no'                    => $value->TransactionSheet->drs_no,
                       'consignee_name'            => $value->ConsigneeDetail->nick_name,
                       'consignee_mobile'          => $value->ConsigneeDetail->phone, 
                       'consignee_address'         => $value->ConsigneeDetail->address_line1.','.@$value->ConsigneeDetail->address_line2.','.        @$value->ConsigneeDetail->address_line3.','.@$value->ConsigneeDetail->address_line4,
                       'consignee_pincode'         => $value->ConsigneeDetail->postal_code,
                       'order_id'                  => $order_item['orders'],
                       'invoice_no'                => $order_item['invoices'],
                  ];
            }
            if ($consignments) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $data
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

                'message' => "Failed to get transaction_sheets data, please try again. {$exception->getMessage()}"

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
            $update_status = ConsignmentNote::find(1118713);
            $res = $update_status->update(['delivery_status' => 'Successful']);
            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $update_status
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update status"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update transaction_sheets, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
    //////
    public function updateMultipleTask(Request $request, $id)
    {
        try {
            $update_status = ConsignmentNote::find($id);
            $res = $update_status->update(['delivery_status' => 'Successful']);
            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'data' => $update_status
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update transaction_sheets"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update transaction_sheets, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }
          
    public function taskStart(Request $request, $id)
    {
        try {

            $update_status = ConsignmentNote::find($id);
            $res = $update_status->update(['delivery_status' => 'Started']);
            
            $currentdate = date("d-m-y h:i:sa");
              $respons2 = array('consignment_id' => $id, 'status' => 'Started','create_at' => $currentdate, 'type' => '2');
                        
                        $lastjob = DB::table('jobs')->select('response_data')->where('consignment_id', $id)->latest('consignment_id')->first();
                        $st = json_decode($lastjob->response_data);
                        array_push($st, $respons2);
                        $sts = json_encode($st); 

                        $start = Job::create(['consignment_id' => $id ,'response_data' => $sts, 'status' => 'Started','type'=> '2']);

            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'message' => 'Status Updated Successfully'
                    // 'data' => $update_status
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update status"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update transaction_sheets, please try again. {$exception->getMessage()}"
            ], 500);
        }
    }

    public function taskAcknowledge(Request $request, $id)
    {
        try {

            $update_status = ConsignmentNote::find($id);
            $res = $update_status->update(['delivery_status' => 'Acknowledge']);
            
            $currentdate = date("d-m-y h:i:sa");
            $respons = array(['consignment_id' => $id, 'status' => 'Acknowledge', 'create_at' => $currentdate,'type' => '2']);
            $respons_data = json_encode($respons);
            
            $create = Job::create(['consignment_id' => $id ,'response_data' => $respons_data,'status' => 'Acknowledge','type'=> '2']);
            

            if ($res) {
                return response([
                    'status' => 'success',
                    'code' => 1,
                    'message' => 'Status Updated Successfully',
                    //'data' => $update_status
                ], 200);
            }
            return response([
                'status' => 'error',
                'code' => 0,
                'data' => "Failed to update status"
            ], 500);
        } catch (\Exception $exception) {
            return response([
                'status' => 'error',
                'code' => 0,
                'message' => "Failed to update transaction_sheets, please try again. {$exception->getMessage()}"
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

            $res = TransactionSheets::find($id)->delete();
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
                  'data' => "Failed to delete transaction_sheets"
                ], 500);

            }

        } catch (\Exception $exception) {

            return response([

                'status' => 'error',

                'code' => 0,

                'message' => "Failed to delete transaction_sheets, please try again. {$exception->getMessage()}"

            ], 500);

        }

    }
    public function updateDeliveryData(Request $request,$id)
    {

        $get_data = $request->data;
        $img_path = array();
        foreach ($get_data as $key => $save_data) {
            // $lrno = $save_data['lr_no'];
            $images = @$save_data['pod_img'];

            $path = Storage::disk('s3')->put('images', $images);
            $img_path[] = Storage::disk('s3')->url($path);
            // $product_filename = $product_pic->getClientOriginalName();
            // $save = DB::table('app_media')->insert(['lr_no' => $lrno, 'pod_img' => $product_filename, 'type' => $type]);

        }
 
         /* Store $imageName name in DATABASE from HERE */
         return response([
            'success' =>'You have successfully upload image.',
            'image' => $img_path
        ], 200);

    }
 

}



