<?php
namespace App\Helpers;

use App\Models\Branch;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\Hrs;
use App\Models\Location;
use App\Models\PrsDrivertask;
use App\Models\RegionalClient;
use App\Models\State;
use App\Models\TransactionSheet;
use App\Models\Vehicle;
use App\Models\PaymentRequest;
use App\Models\HrsPaymentRequest;
use App\Models\PrsPaymentRequest;
use DB;
use Image;
use Storage;

class GlobalFunctions
{

    public static function PrsStatus($status)
    {
        if ($status == 1) {
            $status = 'Assigned';
        }
        else if ($status == 2) {
            $status = 'Pickup done';
        } else if ($status == 3) {
            $status = 'Received at HUB';
        }

        return $status;
    }

    public static function PrsDriverTaskStatus($status)
    {
        if ($status == 1) {
            $status = 'Assigned';
        } else if ($status == 2) {
            $status = 'Acknowledged';
        } else if ($status == 3) {
            $status = 'Picked up';
        } else if ($status == 4) {
            $status = 'Delivered';
        }

        return $status;
    }

    public static function VehicleReceiveGateStatus($status)
    {
        if ($status == 1) {
            $status = 'Incoming';
        } else if ($status == 2) {
            $status = 'Pickup done'; //Received
        } else if ($status == 3) {
            $status = 'Completed';
        }

        return $status;
    }

    // function for get branches //

    public static function getBranches()
    {
        $branches = Branch::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $branches;
    }

    public static function getLocations()
    {
        $locations = Location::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $locations;
    }

    public static function getRegionalClients()
    {
        $regclients = RegionalClient::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $regclients;
    }

    public static function getStates()
    {
        $states = State::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $states;
    }

    public static function getConsigners()
    {
        $consigners = Consigner::where('status', 1)->orderby('nick_name', 'ASC')->pluck('nick_name', 'id');
        return $consigners;
    }

    public static function getVehicles()
    {
        $vehicles = Vehicle::where('status', 1)->orderby('regn_no', 'ASC')->pluck('regn_no', 'id');
        return $vehicles;
    }

    public static function uploadImage($file, $path)
    {
        $name = time() . '.' . $file->getClientOriginalName();
        //save original
        $img = Image::make($file->getRealPath());
        $img->stream();
        Storage::disk('local')->put($path . '/' . $name, $img, 'public');
        //savethumb
        $img = Image::make($file->getRealPath());
        $img->resize(50, 50, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk('local')->put($path . '/thumb/' . $name, $img, 'public');
        return $name;
    }

    // function for show date in frontend //
    public static function ShowFormatDate($date)
    {
        if (!empty($date)) {
            $changeformat = date('d-M-Y', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10-07-2000
    public static function ShowDayMonthYear($date)
    {
        if (!empty($date)) {
            $changeformat = date('d-m-Y', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10/07/2000
    public static function ShowDayMonthYearslash($date)
    {

        if (!empty($date)) {
            $changeformat = date('d/m/Y', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 2022/07/01
    public static function yearmonthdate($date)
    {

        if (!empty($date)) {
            $changeformat = date('Y-m-d', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }

    // function for get random unique number //
    public static function random_number($length_of_number)
    {
        // Number of all number
        $str_result = '0123456789';
        // Shufle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result),
            0, $length_of_number);
    }

    // function for generate unique number //
    public static function generateSku()
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $skuId = substr(str_shuffle($str_result), 0, 6);
        $exist = ConsignmentNote::where('consignment_no', $skuId)->count();
        if ($exist > 0) {
            self::generateSku();
        }
        return 'C-' . $skuId;
    }

    public static function getCountDrs($drs_number)
    {
        $data = DB::table('transaction_sheets')->where('drs_no', $drs_number)
        ->whereNotIn('status', [2])->count();  //2,4
        
        return $data;
    }
    //////////
    public static function countdrslr($drs_number)
    {
        $data = TransactionSheet::
            // with('ConsignmentDetail')
            whereHas('ConsignmentDetail', function ($q) {
                $q->where('status', '!=', 0);
            })
            ->where('drs_no', $drs_number)
            ->where('status', '!=', 2)
            ->count();
        return $data;
    }

    public static function getdeleveryDate($drs_number)
    {
        $data = DB::table('transaction_sheets')->select('consignment_notes.delivery_date as deliverydate')
            ->join('consignment_notes', 'consignment_notes.id', '=', 'transaction_sheets.consignment_no')
            ->where('transaction_sheets.drs_no', $drs_number)
            ->where('consignment_notes.delivery_date', '!=', null)
            ->count();
        return $data;
    }

    public static function getdeleveryStatusbkp($drs_number)
    {
        // Get the consignment numbers related to the provided drs_number
        $get_lrs = TransactionSheet::where('drs_no', $drs_number)->where('status', '!=', 4)->pluck('consignment_no')->toArray();

        // Count the number of delivered and empty consignments
        $total_deldate = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->whereNotNull('delivery_date')->count();

        $countStatusZero = ConsignmentNote::whereIn('id', $get_lrs)->where('status', 0)->count();

        $countReattempt = TransactionSheet::where('drs_no', $drs_number)->whereIn('consignment_no', $get_lrs)->where('status', 4)->count();

        // Calculate the total number of consignments
        $totallrCount = count($get_lrs);

        $totalCount = $total_deldate + $countStatusZero + $countReattempt;

        // Determine the delivery status
        if ($total_deldate == $totallrCount) {
            $status = "Successful";
        } elseif ($total_deldate == 0) {
            $check_started = TransactionSheet::where('drs_no', $drs_number)->where('status', '!=', 4)->where('is_started', 1)->pluck('is_started')->first();
            if($check_started){
                $status = "Started";
            }else if($totalCount == $totallrCount){
                $status = "Successful";
            }else{
                $status = "Unassigned";
            }
        } else {
            if($totalCount == $totallrCount){
                $status = "Successful";
            }else{
                $status = "Partial";
            }
        }

        return $status;
    }
    public static function getdeleveryStatus1($drs_number)
    {
        // Get the consignment numbers related to the provided drs_number
        $get_lrs = TransactionSheet::where('drs_no', $drs_number)->where('status', '!=', 4)->pluck('consignment_no')->toArray();

        $allCancelled = TransactionSheet::whereIn('consignment_no', $get_lrs)->where('status', 0)->count() === count($get_lrs);

        // Count the number of delivered and empty consignments
        $total_deldate = ConsignmentNote::whereIn('id', $get_lrs)->where('status', '!=', 0)->whereNotNull('delivery_date')->count();

        $countStatusZero = ConsignmentNote::whereIn('id', $get_lrs)->where('status', 0)->count();

        $countReattempt = TransactionSheet::where('drs_no', $drs_number)->whereIn('consignment_no', $get_lrs)->where('status', 4)->count();

        // Calculate the total number of consignments
        $totallrCount = count($get_lrs);

        $totalCount = $total_deldate + $countStatusZero + $countReattempt;

        // Determine the delivery status
        if ($allCancelled) {
            return "Cancel";
        } else if ($total_deldate == $totallrCount) {
            $status = "Successful";
        }else if($countStatusZero == $totallrCount){
            $status = "Cancel";
        } else if ($total_deldate == 0) {
            $check_started = TransactionSheet::where('drs_no', $drs_number)->where('status', '!=', 4)->where('is_started', 1)->pluck('is_started')->first();
            if($check_started){
                $status = "Started";
            }else if($totalCount == $totallrCount){
                $status = "Successful";
            }else{
                $status = "Unassigned";
            }
        } else {
            if($totalCount == $totallrCount){
                $status = "Successful";
            }else{
                $status = "Partial";
            }
        }

        return $status;
    }

    public static function getdeleveryStatus($drs_number)
    {
        // Check if all transaction sheets related to the provided drs_number have status=0
        $allDrsCancelled = TransactionSheet::where('drs_no', $drs_number)
        ->where('status', 0)
        ->count() === TransactionSheet::where('drs_no', $drs_number)
        ->count();

        if ($allDrsCancelled) {
            return "Cancel";
        }
      
        // Get the consignment numbers related to the provided drs_number
        $get_lrs = TransactionSheet::where('drs_no', $drs_number)->where('status', '!=', 4)->pluck('consignment_no')->toArray();

        // Count the number of consignments
        $totallrCount = count($get_lrs);

        // Count the number of delivered and empty consignments
        $total_deldate = ConsignmentNote::whereIn('id', $get_lrs)
            ->where('status', '!=', 0)
            ->whereNotNull('delivery_date')
            ->count();

        // Count the number of consignments with status 0
        $countStatusZero = ConsignmentNote::whereIn('id', $get_lrs)
            ->where('status', 0)
            ->count();

        // Count the number of reattempts
        $countReattempt = TransactionSheet::where('drs_no', $drs_number)
            ->whereIn('consignment_no', $get_lrs)
            ->where('status', 4)
            ->count();

        // Check if all consignments are cancelled
        $allCancelled = TransactionSheet::whereIn('consignment_no', $get_lrs)
            ->where('status', 0)
            ->count() === $totallrCount;

        // Determine the delivery status
        if ($allCancelled) {
            return "Cancel";
        } elseif ($total_deldate == $totallrCount) {
            return "Successful";
        } elseif ($total_deldate == 0) {
            $check_started = TransactionSheet::where('drs_no', $drs_number)
                ->where('status', '!=', 4)
                ->where('is_started', 1)
                ->pluck('is_started')
                ->first();
            if ($check_started) {
                return "Started";
            } elseif ($countStatusZero == $totallrCount) {
                return "Cancel";
            } elseif (($total_deldate + $countStatusZero + $countReattempt) == $totallrCount) {
                return "Successful";
            } else {
                return "Unassigned";
            }
        } elseif (($total_deldate + $countStatusZero + $countReattempt) == $totallrCount) {
            return "Successful";
        } else {
            return "Partial";
        }
    }


    public static function drsPaymentStatus($paymentStatus)
    {
        if ($paymentStatus == 0) {
            $status = "Unpaid";
        } else if ($paymentStatus == 1) {
            $status = "Paid";
        } elseif ($paymentStatus == 2) {
            $status = "Processing";
        } elseif ($paymentStatus == 3) {
            $status = "Partial";
        } else {
            $status = "unknown";
        }
        return $status;
    }

    public static function oldnewLr($drs_number)
    {
        $transcationview = TransactionSheet::with('ConsignmentDetail')->where('drs_no', $drs_number)->first();
        $orderId = @$transcationview->ConsignmentDetail->order_id;
        return $orderId;
    }

    public static function regclientCoinsigner($regclient_id)
    {
        $totalconsigner = Consigner::where('id', $regclient_id)->count();
        return $totalconsigner;
    }

    public static function regclientCoinsignee($regclient_id)
    {
        $get_consigner = Consigner::where('regionalclient_id', $regclient_id)->first();
        $totalconsignee = Consignee::where('consigner_id', @$get_consigner->id)->count();
        return $totalconsignee;
    }

    public static function deliveryDate($drs_number)
    {
        $drs = TransactionSheet::select('consignment_no')->where('drs_no', $drs_number)->get();
        $drscount = TransactionSheet::where('drs_no', $drs_number)->count();

        $lrcount = ConsignmentNote::whereIn('id', $drs)->where('delivery_date', '!=', null)->count();

        if ($lrcount > 0) {
            $datecount = 1;
        } else {
            $datecount = 0;
        }
        return $datecount;
    }

    public static function getJobs($job_id)
    {
        $job = DB::table('consignment_notes')->select('jobs.status as job_status', 'jobs.response_data as trail')
            ->where('consignment_notes.job_id', $job_id)
            ->leftjoin('jobs', function ($data) {
                $data->on('jobs.job_id', '=', 'consignment_notes.job_id')
                    ->on('jobs.id', '=', DB::raw("(select max(id) from jobs WHERE jobs.job_id = consignment_notes.job_id)"));
            })->first();

        if (!empty($job)) {
            $job_data = json_decode($job->trail);
        } else {
            $job_data = '';
        }
        return $job_data;
    }

    public static function countDrsInTransaction($trans_id)
    {
        $data = DB::table('payment_requests')->where('transaction_id', $trans_id)->count();
        return $data;
    }

    public static function showDrsNo($trans_id)
    {
        $datas = DB::table('payment_requests')->where('transaction_id', $trans_id)->get();
        $drsarr = array();
        foreach($datas as $row){

            $drsarr[] = $row->drs_no;
        }
        $alldrs = implode(',',$drsarr);
        return $alldrs;
    }
    ///////////// Create Payment ////////

    public static function totalQuantity($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no', $drs_number)->get();

        $total_quantity = ConsignmentNote::select('total_quantity')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_quantity');

        return $total_quantity;
    }

    public static function totalGrossWeight($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no', $drs_number)->get();

        $total_gross = ConsignmentNote::select('total_gross_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_gross_weight');

        return $total_gross;
    }

    public static function totalWeight($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no', $drs_number)->get();

        $total_weight = ConsignmentNote::select('total_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_weight');

        return $total_weight;
    }

    public static function apiResponseSend($message,$data,$status = true,$errorCode){
        $errorCode = $status ? 200 : $errorCode;
        $result = [
            "status" => $status,
            "message" => $message,
            "data" => $data,
            'statuscode' => $errorCode
        ];
        return response()->json($result);
    }
    public static function DriverTaskStatusCheck($prs_id)
    {
        $countids = PrsDrivertask::where('prs_id', $prs_id)->count();
        $countstatus = PrsDrivertask::where('prs_id', $prs_id)->where('status', 3)->count();
        if ($countids == $countstatus) {
            $disable = '';
        } else {
            $disable = 'disable_n';
        }
        return $disable;
    }

    public static function PrsTotalQty($prs_id)
    {
        $driver_tasks = PrsDrivertask::whereIn('prs_id', [$prs_id])->with('ConsignerDetail:id,nick_name', 'PrsTaskItems')->get();
        // echo "<pre>"; print_r(json_decode($driver_tasks)); die;
        if (count($driver_tasks) > 0) {
            foreach ($driver_tasks as $value) {
                if (count($value->PrsTaskItems) > 0) {
                    foreach ($value->PrsTaskItems as $item_value) {
                        $total_qty = $item_value->sum('quantity');
                    }
                } else {
                    $total_qty = '0';
                }
            }
        } else {
            $total_qty = '0';
        }
        return $total_qty;
    }

    // public static function PrsStatusCheck($prs_id)
    // {
    //     $countids = PickupRunSheet::where('id',$prs_id)->count();
    //     $count_drivertaskids = PrsDrivertask::where('prs_id',$prs_id)->count();
    //     if($count_drivertaskids){

    //     }else{

    //     }

    //     return $prs_status;
    // }

    // ============================ HRS HELPER ========================== //

    public static function counthrslr($hrs_number)
    {
        $data = Hrs::
            with('ConsignmentDetai')
            ->whereHas('ConsignmentDetail', function ($q) {
                $q->where('status', '!=', 0);
            })
            ->where('hrs_no', $hrs_number)
            ->count();
        return $data;
    }

    public static function totalQuantityHrs($hrs_number)
    {
        $get_lrs = Hrs::select('consignment_id')->where('hrs_no', $hrs_number)->get();

        $total_quantity = ConsignmentNote::select('total_quantity')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_quantity');

        return $total_quantity;
    }

    public static function totalGrossWeightHrs($hrs_number)
    {
        $get_lrs = Hrs::select('consignment_id')->where('hrs_no', $hrs_number)->get();

        $total_gross = ConsignmentNote::select('total_gross_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_gross_weight');

        return $total_gross;
    }

    public static function totalWeightHrs($hrs_number)
    {
        $get_lrs = Hrs::select('consignment_id')->where('hrs_no', $hrs_number)->get();

        $total_weight = ConsignmentNote::select('total_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_weight');

        return $total_weight;
    }
    public static function countHrsInTransaction($trans_id)
    {
        $data = DB::table('hrs_payment_requests')->where('transaction_id', $trans_id)->count();
        return $data;
    }
    public static function getConsignerName($cnr_id)
    {
        $get_cnr = Consigner::select('id', 'nick_name')->whereIn('id', $cnr_id)->get();
        $cnr_name = array();
        foreach ($get_cnr as $key => $cnr) {
            $cnr_name[] = $cnr->nick_name;
        }
        $cnr_nickname = implode(',', $cnr_name);
        return $cnr_nickname;
    }
    public static function countPrsInTransaction($trans_id)
    {
        $data = DB::table('prs_payment_requests')->where('transaction_id', $trans_id)->count();
        return $data;
    }

    public static function InvoiceNumbers($lr_id)
    {
        $get_lr = ConsignmentNote::where('id',$lr_id)->first();

        if(empty(@$get_lr->invoice_no)){
            $get_invcs = ConsignmentItem::where('consignment_id',$lr_id)->get();
            $lr_invoices = array();
            foreach(@$get_invcs as $key => $invoice){
                $lr_invoices[] = @$invoice->invoice_no;
            }
            $invoice_nos = implode(',', $lr_invoices);
        }else{
            $invoice_nos = @$get_lr->invoice_no;
        }
        return $invoice_nos;
    }

    public static function DrsPaymentCheck($lr_id)
    {
        $checkdrs = TransactionSheet::where('consignment_no',$lr_id)->first();
        if($checkdrs){
            $checkPayment = PaymentRequest::where('drs_no',$checkdrs->drs_no)->first();
            if($checkPayment){
                $payment_id = $checkPayment->id;
             }else{
                $payment_id = '';
             }
        }else{
            $payment_id = '';
         }
         return $payment_id;

    }

    public static function LrCountMix($transaction_id)
    {
        $get_drs_nos = PaymentRequest::with('TransactionDetails')->where('transaction_id', $transaction_id)->get();
        $drs_no = array();
        foreach($get_drs_nos as $get_drs_no){
            $drs_no[] = $get_drs_no->drs_no;
        }
        $trans_sheet = TransactionSheet::whereIn('drs_no', $drs_no)->where('status', '!=', '0')->count();
        return $trans_sheet;
    }

    public static function totalQuantityMixReport($transaction_id)
    {
        $get_drs_nos = PaymentRequest::with('TransactionDetails')->where('transaction_id', $transaction_id)->get();
        $drs_no = array();
        foreach($get_drs_nos as $get_drs_no){
            $drs_no[] = $get_drs_no->drs_no;
        }

        $get_lrs = TransactionSheet::select('consignment_no')->whereIn('drs_no', $drs_no)->get();

        $total_quantity = ConsignmentNote::select('total_quantity')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_quantity');

        $total_gross = ConsignmentNote::select('total_gross_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_gross_weight');

        $total_weight = ConsignmentNote::select('total_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_weight');

        return (object)[
            'total_quantity' => $total_quantity,
            'total_gross' => $total_gross,
            'total_weight' => $total_weight,
        ];
    }

    public static function mixReportConsignee($transaction_id)
    {
        $get_drs_nos = PaymentRequest::with('TransactionDetails')->where('transaction_id', $transaction_id)->get();
        $drs_no = array();
        foreach($get_drs_nos as $get_drs_no){
            $drs_no[] = $get_drs_no->drs_no;
        }
        $trans_sheet = TransactionSheet::with('ConsignmentDetail.ConsigneeDetail','ConsignmentDetail.vehicletype','ConsignmentDetail.VehicleDetail')->whereIn('drs_no', $drs_no)->where('status', '!=', '0')->get();
        
        $cong_distt = array();
        $vehicle_type = array();
        $vehicle_no = array();
        foreach($trans_sheet as $consignee){
            $congDist = @$consignee->ConsignmentDetail->ConsigneeDetail->district;
            $vehicleType = @$consignee->ConsignmentDetail->vehicletype->name;
            $vehicle = @$consignee->ConsignmentDetail->VehicleDetail->regn_no;
        
            if ($congDist !== null && $congDist !== '') {
                $cong_distt[] = $congDist;
            }
        
            if ($vehicleType !== null && $vehicleType !== '') {
                $vehicle_type[] = $vehicleType;
            } 
            if ($vehicle !== null && $vehicle !== '') {
                $vehicle_no[] = $vehicle;
            }     
        }

        $district_consignee = implode(',',$cong_distt);
        $vehicle_type  = array_unique($vehicle_type);
        $vehicle_type_un  = implode(',',$vehicle_type);

        $vehicle_no_lr  = array_unique($vehicle_no);
        $vehicle_no_un  = implode(',',$vehicle_no_lr);
       
        return (object)[
            'district_consignee' => $district_consignee,
            'vehicle_type' => $vehicle_type_un,
            'vehicle_no' => $vehicle_no_un,
            
        ];
    }

    public static function LrCountMixHrs($transaction_id)
    {
        $get_hrs_nos = HrsPaymentRequest::with('HrsDetails')->where('transaction_id', $transaction_id)->get();
        $hrs_no = array();
        foreach($get_hrs_nos as $get_hrs_no){
            $hrs_no[] = $get_hrs_no->hrs_no;
        }
        $trans_sheet = Hrs::whereIn('hrs_no', $hrs_no)->where('status', '!=', '0')->count();
        return $trans_sheet;
    }

    public static function totalQuantityMixReportHrs($transaction_id)
    {
        $get_hrs_nos = HrsPaymentRequest::with('HrsDetails')->where('transaction_id', $transaction_id)->get();
        $hrs_no = array();
        foreach($get_hrs_nos as $get_hrs_no){
            $hrs_no[] = $get_hrs_no->hrs_no;
        }

        $get_lrs = Hrs::select('consignment_id')->whereIn('hrs_no', $hrs_no)->get();

        $total_quantity = ConsignmentNote::select('total_quantity')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_quantity');

        $total_gross = ConsignmentNote::select('total_gross_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_gross_weight');

        $total_weight = ConsignmentNote::select('total_weight')->where('status', '!=', 0)->whereIn('id', $get_lrs)->sum('total_weight');

        return (object)[
            'total_quantity' => $total_quantity,
            'total_gross' => $total_gross,
            'total_weight' => $total_weight,
        ];
    }

    public static function mixReportConsigneeHrs($transaction_id)
    {
        $get_hrs_nos = HrsPaymentRequest::with('HrsDetails')->where('transaction_id', $transaction_id)->get();
        $hrs_no = array();
        foreach($get_hrs_nos as $get_hrs_no){
            $hrs_no[] = $get_hrs_no->hrs_no;
        }
        $trans_sheet = Hrs::with('ConsignmentDetail.ConsigneeDetail','ConsignmentDetail.vehicletype','vehicletype','VehicleDetail')->whereIn('hrs_no', $hrs_no)->where('status', '!=', '0')->get();
        
        $cong_distt = array();
        $vehicle_type = array();
        $vehicle_arr = array();
        foreach($trans_sheet as $consignee){
            $congDist = @$consignee->ConsignmentDetail->ConsigneeDetail->district;
            $vehicleType = @$consignee->vehicletype->name;
            $vehicle = @$consignee->VehicleDetail->regn_no;
        
            if ($congDist !== null && $congDist !== '') {
                $cong_distt[] = $congDist;
            }
        
            if ($vehicleType !== null && $vehicleType !== '') {
                $vehicle_type[] = $vehicleType;
            }   
            if ($vehicle !== null && $vehicle !== '') {
                $vehicle_arr[] = $vehicle;
            }         
        }

        $district_consignee = implode(',',$cong_distt);
        $vehicle_type  = array_unique($vehicle_type);
        $vehicle_type_un  = implode(',',$vehicle_type);

        $vehicle_no  = array_unique($vehicle_arr);
        $vehicle_no_un  = implode(',',$vehicle_no);
        
       
        return (object)[
            'district_consignee' => $district_consignee,
            'vehicle_type' => $vehicle_type_un,
            'vehicle_no' => $vehicle_no_un,
            
        ];
    }

    public static function LrCountMixPrs($transaction_id)
    {
        $get_hrs_nos = PrsPaymentRequest::with('PickupRunSheet.Consignments')->where('transaction_id', $transaction_id)->get();
        $lr_count = 0;
        foreach($get_hrs_nos as $get_hrs_no){
            $lr_count += count($get_hrs_no->PickupRunSheet->Consignments);
        }
       
        return $lr_count;
    }

    public static function totalQuantityMixReportPrs($transaction_id)
    {
        $get_hrs_nos = PrsPaymentRequest::with('PickupRunSheet.Consignments')->where('transaction_id', $transaction_id)->get();
        $lrs_no = array();
        foreach($get_hrs_nos as $get_hrs_no){
            foreach($get_hrs_no->PickupRunSheet->Consignments as $get_lr){
                $lrs_no[] = $get_lr->id;
            }
        }


        // $get_lrs = Hrs::select('consignment_id')->whereIn('hrs_no', $hrs_no)->get();

        $total_quantity = ConsignmentNote::select('total_quantity')->where('status', '!=', 0)->whereIn('id', $lrs_no)->sum('total_quantity');

        $total_gross = ConsignmentNote::select('total_gross_weight')->where('status', '!=', 0)->whereIn('id', $lrs_no)->sum('total_gross_weight');

        $total_weight = ConsignmentNote::select('total_weight')->where('status', '!=', 0)->whereIn('id', $lrs_no)->sum('total_weight');

        return (object)[
            'total_quantity' => $total_quantity,
            'total_gross' => $total_gross,
            'total_weight' => $total_weight,
        ];
    }

    public static function mixReportConsigneePrs($transaction_id)
    {
        $get_hrs_nos = PrsPaymentRequest::with('PickupRunSheet.Consignments','PickupRunSheet.VehicleDetail','PickupRunSheet.VehicleType')->where('transaction_id', $transaction_id)->get();
        
        $lrs_no = array();
        $vehicle_no = array();
        $vehicleType = array();
        foreach($get_hrs_nos as $get_hrs_no){ 
            $vehicle_no[] = @$get_hrs_no->PickupRunSheet->VehicleDetail->regn_no;
            $vehicleType[] = @$get_hrs_no->PickupRunSheet->VehicleType->name;

            foreach($get_hrs_no->PickupRunSheet->Consignments as $get_lr){
                $lrs_no[] = $get_lr->id;
            }

        }
        

        $trans_sheet = ConsignmentNote::with('ConsigneeDetail','vehicletype')->whereIn('id', $lrs_no)->where('status', '!=', '0')->get();
        
        $cong_distt = array();
        foreach($trans_sheet as $consignee){
            $congDist = @$consignee->ConsigneeDetail->district;
        
            if ($congDist !== null && $congDist !== '') {
                $cong_distt[] = $congDist;
            } 
        }

        $district_consignee = implode(',',$cong_distt);
        $vehicle_type  = array_unique($vehicleType);
        $vehicle_type_un  = implode(',',$vehicle_type);

        $vehicle  = array_unique($vehicle_no);
        $vehicle_no_un  = implode(',',$vehicle);
        
       
        return (object)[
            'district_consignee' => $district_consignee,
            'vehicle_type' => $vehicle_type_un,
            'vehicle_no' => $vehicle_no_un,
            
        ];
    }

}
