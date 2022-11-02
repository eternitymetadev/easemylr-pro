<?php
namespace App\Helpers;
use DOMDocument;
use DB;
use Mail;
use Session;
use Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Location;
use App\Models\State;
use App\Models\Job;
use App\Models\Consigner;
use App\Models\Consignee;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\TransactionSheet;
use App\Models\RegionalClient;
use App\Models\Vehicle;
use URL;
use Crypt;
use Storage;
use Image;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class GlobalFunctions {

  // function for get branches //

    public static function getBranches(){
        $branches = Branch::where('status',1)->orderby('name','ASC')->pluck('name','id');
        return $branches;
    }

    public static function getLocations(){
        $locations = Location::where('status',1)->orderby('name','ASC')->pluck('name','id');
        return $locations;
    }

    public static function getRegionalClients(){
        $regclients = RegionalClient::where('status',1)->orderby('name','ASC')->pluck('name','id');
        return $regclients;
    }

    public static function getStates(){
        $states = State::where('status',1)->orderby('name','ASC')->pluck('name','id');
        return $states;
    }

    public static function getConsigners(){
        $consigners = Consigner::where('status',1)->orderby('nick_name','ASC')->pluck('nick_name','id');
        return $consigners;
    }

    public static function getVehicles(){
        $vehicles = Vehicle::where('status',1)->orderby('regn_no','ASC')->pluck('regn_no','id');
        return $vehicles;
    }

    public static function uploadImage($file,$path)
    {
        $name = time() . '.' . $file->getClientOriginalName();
        //save original
        $img = Image::make($file->getRealPath());
        $img->stream();
        Storage::disk('local')->put($path.'/'.$name, $img, 'public');
        //savethumb
        $img = Image::make($file->getRealPath());
        $img->resize(50, 50, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk('local')->put($path.'/thumb/'.$name, $img, 'public');
        return $name;
    }

    // function for show date in frontend //
    public static function ShowFormatDate($date)
    {
        if(!empty($date)){
        $changeformat = date('d-M-Y',strtotime($date));
        }else{
        $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10-07-2000
    public static function ShowDayMonthYear($date)
    {
        if(!empty($date)){
        $changeformat = date('d-m-Y',strtotime($date));
        }else{
        $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10/07/2000
    public static function ShowDayMonthYearslash($date){

        if(!empty($date)){
        $changeformat = date('d/m/Y',strtotime($date));
        }else{
        $changeformat = '-';
        }
        return $changeformat;
    }
      //////format 2022/07/01
      public static function yearmonthdate($date){

        if(!empty($date)){
        $changeformat = date('Y-m-d',strtotime($date));
        }else{
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
        $exist = ConsignmentNote::where('consignment_no',$skuId)->count();
        if($exist > 0){
           self::generateSku();
        }
        return 'C-'.$skuId;
    }

    public static function getCountDrs($drs_number)
    {
        $data = DB::table('transaction_sheets')->where('drs_no',$drs_number)->where('status','!=', 2)->count();
        return $data;
    }
    //////////
    public static function countdrslr($drs_number)
    {
        $data = TransactionSheet::
        with('ConsignmentDetai')
        ->whereHas('ConsignmentDetail', function($q){
            $q->where('status', '!=', 0);
        })
        ->where('drs_no', $drs_number)
        ->where('status','!=', 2)
        ->count();
        return $data;
    }

    public static function getdeleveryDate($drs_number)
    {
        $data = DB::table('transaction_sheets')->select( 'consignment_notes.delivery_date as deliverydate')
        ->join('consignment_notes','consignment_notes.id','=','transaction_sheets.consignment_no')
        ->where('transaction_sheets.drs_no',$drs_number)
        ->where('consignment_notes.delivery_date','!=', null)
        ->count();
        return $data;
    }

    public static function getdeleveryStatus($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no',$drs_number)->get();

        $getlr_deldate = ConsignmentNote::select('delivery_date')->where('status','!=',0)->whereIn('id',$get_lrs)->get();
        $total_deldate = ConsignmentNote::whereIn('id',$get_lrs)->where('status','!=',0)->where('delivery_date', '!=', NULL)->count();
        $total_empty = ConsignmentNote::whereIn('id',$get_lrs)->where('status','!=',0)->where('delivery_date', '=', NULL)->count();

        $total_lr = ConsignmentNote::whereIn('id',$get_lrs)->where('status','!=',0)->count();

        if($total_deldate == $total_lr){
            $status = "Successful";
        }elseif($total_lr == $total_empty){
            $status = "Started";
        }else{
            $status = "Partial Delivered";
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

        $lr = ConsignmentNote::select('delivery_date')->whereIn('id', $drs)->get();
        $lrcount = ConsignmentNote::whereIn('id', $drs)->where('delivery_date', '!=', NULL)->count();

        if($lrcount > 0){
            $datecount = 1;
        }else{
            $datecount = 0;
        }
        return $datecount;
    }

    public static function getJobs($job_id)
    {
        $job = DB::table('consignment_notes')->select('jobs.status as job_status', 'jobs.response_data as trail')
        ->where('consignment_notes.job_id',$job_id )
        ->leftjoin('jobs', function($data){
            $data->on('jobs.job_id', '=', 'consignment_notes.job_id')
                 ->on('jobs.id', '=', DB::raw("(select max(id) from jobs WHERE jobs.job_id = consignment_notes.job_id)"));
        })->first();

        if(!empty($job)){
            $job_data= json_decode($job->trail);
        }else{
            $job_data= '';
        }
        return $job_data;
    }

    public static function countDrsInTransaction($trans_id)
    {
        $data = DB::table('payment_requests')->where('transaction_id',$trans_id)->count();
        return $data;
    }
    ///////////// Create Payment ////////
    
    public static function totalQuantity($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no',$drs_number)->get();

        $total_quantity = ConsignmentNote::select('total_quantity')->where('status','!=',0)->whereIn('id',$get_lrs)->sum('total_quantity');
      
        return $total_quantity;
    }

    public static function totalGrossWeight($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no',$drs_number)->get();

        $total_gross = ConsignmentNote::select('total_gross_weight')->where('status','!=',0)->whereIn('id',$get_lrs)->sum('total_gross_weight');
      
        return $total_gross;
    }

    public static function totalWeight($drs_number)
    {
        $get_lrs = TransactionSheet::select('consignment_no')->where('drs_no',$drs_number)->get();

        $total_weight = ConsignmentNote::select('total_weight')->where('status','!=',0)->whereIn('id',$get_lrs)->sum('total_weight');
      
        return $total_weight;
    }
}