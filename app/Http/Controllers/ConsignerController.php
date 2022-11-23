<?php

namespace App\Http\Controllers;

use App\Models\Consigner;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Location;
use App\Models\Role;
use App\Models\RegionalClient;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsignerExport;
use DB;
use URL;
use Auth;
use Crypt;
use Helper;
use Validator;

class ConsignerController extends Controller
{
    public function __construct()
    {
        $this->title = "Consigners";
        $this->segment = \Request::segment(2);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        if ($request->ajax()) {


            $query = Consigner::query();
            $query = $query->with('RegClient', 'Zone');

            if ($authuser->role_id == 1) {
                $query = $query;
            } else if ($authuser->role_id == 2 || $authuser->role_id == 3) {
                // $query = $query->whereIn('branch_id', $cc);
                $query = $query->WhereHas('RegClient', function ($regclientquery) use ($cc) {
                    $regclientquery->whereIn('location_id', $cc);
                });
            } else {
                $query = $query->whereIn('regionalclient_id', $regclient);
            }

            $consigners = $query->orderby('created_at', 'DESC')->get();

            return datatables()->of($consigners)
                ->addIndexColumn()
                ->addColumn('regclient', function ($row) {
                    if (isset($row->RegClient)) {
                        $regional = $row->RegClient->name;
                    } else {
                        $regional = '-';
                    }
                    $regnl_row = '<td valign="middle" style="max-width: 350px">
                            <p class="consigner">
                                <span class="legalName" title="consigner legal name">
                                    '.$regional.'
                                </span>
                                <span title="consigner name">
                                    '.$row->nick_name.'
                                </span>
                            </p>
                        </td>';
                    return $regnl_row;
                })
                ->addColumn('location', function ($row) {
                    if (isset($row->Zone)) {
                        $city = $row->Zone->city;
                    } else {
                        $city = '-';
                    }
                    $location = '<td valign="middle"><p><span class="legalName">'.@$row->Zone->district.'</span>
                                <span>'.@$row->Zone->state.' - '.@$row->Zone->postal_code.'</span></p></td>';
                    return $location;
                })
                ->addColumn('state', function ($row) {
                    if (isset($row->Zone)) {
                        $state = $row->Zone->state;
                    } else {
                        $state = '';
                    }
                    return $state;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-content-center justify-content-center" style="gap: 6px"><a id="editConsignerIcon" href="#" data-toggle="modal" data-target="#consignerDetailsEditModal" class="edit editIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"> <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path> <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path> </svg></a>';
//                    $btn = '<div class="d-flex align-content-center justify-content-center" style="gap: 6px"><a href="' . URL::to($this->prefix . '/' . $this->segment . '/' . Crypt::encrypt($row->id) . '/edit') . '" class="edit editIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"> <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path> <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path> </svg></a>';
                    $btn .= '<a href="#" data-toggle="modal" data-target="#consignerDetailsModal" class="view viewIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
//                    $btn .= '<a href="' . URL::to($this->prefix . '/' . $this->segment . '/' . Crypt::encrypt($row->id)) . '" class="view viewIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                    $btn .= '<a class="delete deleteIcon delete_consigner" data-id="' . $row->id . '" data-action="' . URL::to($this->prefix . '/' . $this->segment . '/delete-consigner') . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"                                                  viewBox="0 0 24 24"                                                  fill="none" stroke="currentColor" stroke-width="2"                                                  stroke-linecap="round"                                                  stroke-linejoin="round" class="feather feather-trash-2">                                                 <polyline points="3 6 5 6 21 6"></polyline>                                                 <path                                                     d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>                                                 <line x1="10" y1="11" x2="10" y2="17"></line>                                                 <line x1="14" y1="11" x2="14" y2="17"></line>                                             </svg></a></div>';
                    return $btn;
                })
                ->rawColumns(['action', 'regclient', 'location', 'state'])
                ->make(true);

        }

        if ($authuser->role_id != 1) {
            if ($authuser->role_id == 2 || $role_id->id == 3) {
                $regclients = RegionalClient::whereIn('location_id', $cc)->orderby('name', 'ASC')->get();
            } else {
                $regclients = RegionalClient::whereIn('id', $regclient)->orderby('name', 'ASC')->get();
            }
        } else {
            $regclients = RegionalClient::where('status', 1)->orderby('name', 'ASC')->get();
        }

        return view('consigners.consigner-list', ['prefix' => $this->prefix, 'segment' => $this->segment, 'regclients' => $regclients,]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        if ($authuser->role_id != 1) {
            if ($authuser->role_id == 2 || $role_id->id == 3) {
                $regclients = RegionalClient::whereIn('location_id', $cc)->orderby('name', 'ASC')->get();
            } else {
                $regclients = RegionalClient::whereIn('id', $regclient)->orderby('name', 'ASC')->get();
            }
        } else {
            $regclients = RegionalClient::where('status', 1)->orderby('name', 'ASC')->get();
        }
        return view('consigners.create-consigner', ['regclients' => $regclients, 'prefix' => $this->prefix, 'title' => $this->title, 'pagetitle' => 'Create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'nick_name' => 'required|unique:consigners',
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['success'] = false;
            $response['validation'] = false;
            $response['formErrors'] = true;
            $response['errors'] = $errors;
            return response()->json($response);
        }
        $consignersave['nick_name'] = $request->nick_name;
        $consignersave['legal_name'] = $request->legal_name;
        $consignersave['gst_number'] = $request->gst_number;
        $consignersave['contact_name'] = $request->contact_name;
        $consignersave['phone'] = $request->phone;
        $consignersave['regionalclient_id'] = $request->regionalclient_id;
        $consignersave['branch_id'] = $request->branch_id;
        $consignersave['email'] = $request->email;
        $consignersave['address_line1'] = $request->address_line1;
        $consignersave['address_line2'] = $request->address_line2;
        $consignersave['address_line3'] = $request->address_line3;
        $consignersave['address_line4'] = $request->address_line4;
        $consignersave['city'] = $request->city;
        $consignersave['district'] = $request->district;
        $consignersave['postal_code'] = $request->postal_code;
        $consignersave['state_id'] = $request->state_id;
        // $consignersave['status']       = $request->status;

        $saveconsigner = Consigner::create($consignersave);
        if ($saveconsigner) {
            $response['success'] = true;
            $response['error'] = false;
            $response['page'] = 'consigner-create';
            $response['success_message'] = "Consigner Added successfully";
            $response['redirect_url'] = URL::to($this->prefix . '/consigners');
            // $response['resetform'] = true;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not created consigner please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Consigner $consigner
     * @return \Illuminate\Http\Response
     */
    public function show($consigner)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($consigner);
        $getconsigner = Consigner::where('id', $id)->with('GetRegClient', 'GetZone')->first();
        return view('consigners.view-consigner', ['prefix' => $this->prefix, 'title' => $this->title, 'pagetitle' => 'View Details', 'getconsigner' => $getconsigner]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Consigner $consigner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);
        $authuser = Auth::user();

        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        if ($authuser->role_id != 1) {
            if ($authuser->role_id == 2 || $role_id->id == 3) {
                $regclients = RegionalClient::whereIn('location_id', $cc)->orderby('name', 'ASC')->get();
            } else {
                $regclients = RegionalClient::whereIn('id', $regclient)->orderby('name', 'ASC')->get();
            }
        } else {
            $regclients = RegionalClient::where('status', 1)->orderby('name', 'ASC')->get();
        }

        $getconsigner = Consigner::with('GetZone')->where('id', $id)->first();
        return view('consigners.update-consigner')->with(['prefix' => $this->prefix, 'getconsigner' => $getconsigner, 'regclients' => $regclients, 'title' => $this->title, 'pagetitle' => 'Update']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Consigner $consigner
     * @return \Illuminate\Http\Response
     */
    public function updateConsigner(Request $request)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'nick_name' => 'required'
                //   'nick_name' => 'required|unique:consigners,nick_name,'.$request->consigner_id,
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['success'] = false;
                $response['formErrors'] = true;
                $response['errors'] = $errors;
                return response()->json($response);
            }

            $check_nickname_exist = Consigner::where(['nick_name' => $request['nick_name']])->where('id', '!=', $request->consigner_id)->get();

            if (!$check_nickname_exist->isEmpty()) {
                $response['success'] = false;
                $response['error_message'] = "Nick name already exists.";
                $response['cnr_nickname_duplicate_error'] = true;
                return response()->json($response);
            }

            $consignersave['nick_name'] = $request->nick_name;
            $consignersave['legal_name'] = $request->legal_name;
            $consignersave['gst_number'] = $request->gst_number;
            $consignersave['contact_name'] = $request->contact_name;
            $consignersave['phone'] = $request->phone;
            $consignersave['regionalclient_id'] = $request->regionalclient_id;
            $consignersave['branch_id'] = $request->branch_id;
            $consignersave['email'] = $request->email;
            $consignersave['address_line1'] = $request->address_line1;
            $consignersave['address_line2'] = $request->address_line2;
            $consignersave['address_line3'] = $request->address_line3;
            $consignersave['address_line4'] = $request->address_line4;
            $consignersave['city'] = $request->city;
            $consignersave['district'] = $request->district;
            $consignersave['postal_code'] = $request->postal_code;
            $consignersave['state_id'] = $request->state_id;
            // $consignersave['status']       = $request->status;

            Consigner::where('id', $request->consigner_id)->update($consignersave);
            $url = URL::to($this->prefix . '/consigners');

            $response['page'] = 'consigner-update';
            $response['success'] = true;
            $response['success_message'] = "Consigner Updated Successfully";
            $response['error'] = false;
            // $response['html'] = $html;
            $response['redirect_url'] = $url;
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Consigner $consigner
     * @return \Illuminate\Http\Response
     */
    public function deleteConsigner(Request $request)
    {
        Consigner::where('id', $request->consignerid)->delete();

        $response['success'] = true;
        $response['success_message'] = 'Consigner deleted successfully';
        $response['error'] = false;
        return response()->json($response);
    }

    //download excel/csv
    public function exportExcel()
    {
        return Excel::download(new ConsignerExport, 'consigners.csv');
    }

    // get locations on client change in create consigner
    public function regLocations(Request $request)
    {
        $getlocation = Location::select('id', 'name')->where(['id' => $request->location_id, 'status' => '1'])->first();
        if ($getlocation) {
            $response['success'] = true;
            $response['success_message'] = "Location list fetch successfully";
            $response['error'] = false;
            $response['data'] = $getlocation;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch location list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

}
