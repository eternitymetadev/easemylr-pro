<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchAddress;
use App\Models\Zone;
use Validator;
use URL;
use Crypt;
use Helper;
use Config;
use Auth;
use App\Models\Role;
use App\Models\User;

class SettingController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Consignments";
        $this->segment = \Request::segment(2);
        $this->apikey = \Config::get('keys.api');
    }

    public function getbranchAddress(Request $request)
    {
        return view('setting.index');
    }

    // add branch address
    public function updateBranchadd(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $rules = array(
                'name' => 'required',
            );
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;                
                return response()->json($response);
            }
            if(!empty($request->name)){
                $settingsave['name']   = $request->name;
            }
            if(!empty($request->gst_number)){
                $settingsave['gst_number']   = $request->gst_number;
            }
            if(!empty($request->phone)){
                $settingsave['phone']   = $request->phone;
            }
            if(!empty($request->address)){
                $settingsave['address']   = $request->address;
            }
            if(!empty($request->state)){
                $settingsave['state']   = $request->state;
            }
            if(!empty($request->district)){
                $settingsave['district']   = $request->district;
            }
            if(!empty($request->city)){
                $settingsave['city']   = $request->city;
            }
            if(!empty($request->postal_code)){
                $settingsave['postal_code']   = $request->postal_code;
            }
            if(!empty($request->email)){
                $settingsave['email']   = $request->email;
            }
            $settingsave['status']      = "1";

            $savesetting = BranchAddress::updateOrCreate(['meta_key'=>'addressdata_key'],$settingsave);
            if($savesetting){

                $response['success'] = true;
                $response['success_message'] = "Branch address value updated successfully.";
                $response['error'] = false;
                $response['page'] = 'settings-branch-address';
                  
            }else{
                $response['success'] = false;
                $response['error_message'] = "Can not updated branch address value please try again";
                $response['error'] = true;
            }
            return response()->json($response);

        }
        else
        {
            $branchaddvalue = BranchAddress::where(['meta_key'=>'addressdata_key'])->first();
            return view('settings.branch-address',['branchaddvalue'=>$branchaddvalue,'prefix'=>$this->prefix]);
        }
    }
    // postal code edit
    public function postalCode(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = Zone::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $baseclient = explode(',', $authuser->baseclient_id);
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query->where('status',1);

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('drs_no', 'like', '%' . $search . '%')
                        ->orWhere('vehicle_no', 'like', '%' . $search . '%')
                        ->orWhere('driver_name', 'like', '%' . $search . '%')
                        ->orWhere('driver_no', 'like', '%' . $search . '%');
                });
            }

            if ($request->peritem) {
                Session::put('peritem', $request->peritem);
            }

            $peritem = Session::get('peritem');
            if (!empty($peritem)) {
                $peritem = $peritem;
            } else {
                $peritem = Config::get('variable.PER_PAGE');
            }
            $zones = $query->orderBy('id', 'DESC')->paginate($peritem);
            $zones = $zones->appends($request->query());

            $html = view('settings.postal-code-ediAajax', ['peritem' => $peritem, 'prefix' => $this->prefix, 'zones' => $zones])->render();

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $baseclient = explode(',', $authuser->baseclient_id);
        $regclient = explode(',', $authuser->regionalclient_id);

        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query
            ->where('status',1);

        $zones = $query->orderBy('id', 'DESC')->paginate($peritem);
        $zones = $zones->appends($request->query());

        return view('settings.postal-code-edit', ['peritem' => $peritem, 'prefix' => $this->prefix, 'zones' => $zones]);
    }

    public function editPostalCode(Request $request)
    {
        dd('like');
    }
}
