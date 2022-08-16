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
use App\Models\Consigner;
use App\Models\ConsignmentNote;
use App\Models\RegionalClient;
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
    public static function ShowFormatDate($date){

        if(!empty($date)){
        $changeformat = date('d-M-Y',strtotime($date));
        }else{
        $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10-07-2000
    public static function ShowDayMonthYear($date){

        if(!empty($date)){
        $changeformat = date('d-m-Y',strtotime($date));
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
    public static function generateSku(){
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $skuId = substr(str_shuffle($str_result), 0, 6);
        $exist = ConsignmentNote::where('consignment_no',$skuId)->count();
        if($exist > 0){
           self::generateSku();
        }
        return 'C-'.$skuId;
     }

     public static function getCountDrs($drs_number){
        $data = DB::table('transaction_sheets')->where('drs_no',$drs_number)->where('status','!=', 2)->count();
        return $data;
     }

     public static function getdeleveryDate($drs_number){
        $data = DB::table('transaction_sheets')->select( 'consignment_notes.delivery_date as deliverydate')
        ->join('consignment_notes','consignment_notes.id','=','transaction_sheets.consignment_no')
        ->where('transaction_sheets.drs_no',$drs_number)
        ->where('consignment_notes.delivery_date','!=', null)
        ->count();
     
        return $data;
     }

}