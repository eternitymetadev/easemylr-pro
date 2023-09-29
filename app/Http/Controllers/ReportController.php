<?php

namespace App\Http\Controllers;

use App\Exports\RegionalReport;
use App\Exports\Report1Export;
use App\Exports\Report2Export;
use App\Exports\Report3Export;
use App\Models\BaseClient;
use App\Models\Consigner;
use App\Models\ConsignmentNote;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Role;
use App\Models\User;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Response;
use Session;
use URL;

class ReportController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "MIS Reports";
        $this->segment = \Request::segment(2);
    }
    // MIS report2 get records
    public function consignmentReportsAll(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $sessionperitem = Session::get('peritem');
        if (!empty($sessionperitem)) {
            $peritem = $sessionperitem;
        } else {
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query
                ->where('status', '!=', 5)
                ->with(
                    'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
                );

            if ($authuser->role_id == 1) {
                $query = $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('ConsignerDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cnrquery) use ($search, $searchT) {
                                $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('ConsigneeDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cneequery) use ($search, $searchT) {
                                $cneequery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        });

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

            $startdate = $request->startdate;
            $enddate = $request->enddate;
            $baseclient_id = $request->baseclient_id;
            $regclient_id = $request->regclient_id;

            if (isset($startdate) && isset($enddate)) {
                $query = $query->whereBetween('consignment_date', [$startdate, $enddate]);
            }
            if ($baseclient_id) {
                $query = $query->whereHas('ConsignerDetail.GetRegClient.BaseClient', function ($q) use ($baseclient_id) {
                    $q->where('id', $baseclient_id);
                });
            }
            if ($regclient_id) {
                $query = $query->whereHas('ConsignerDetail.GetRegClient', function ($q) use ($regclient_id) {
                    $q->where('id', $regclient_id);
                });
            }

            $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);

            $html = view('consignments.consignment-reportAll-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem])->render();
            // $consignments = $consignments->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $regclients = RegionalClient::with('BaseClient:id,client_name')->select('id', 'name', 'location_id', 'baseclient_id')->whereIn('location_id', $cc)->get();

        $base_clients = [];
        foreach ($regclients as $val) {
            $base_clients[] = json_decode($val->BaseClient);
        }

        $uniqueData = array_unique($base_clients, SORT_REGULAR);
        $getbaseclients = array_values($uniqueData); // Resetting array keys

        $query = $query
            ->where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
            );

        if ($authuser->role_id == 1) {
            $query = $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('consignments.consignment-reportAll', ['consignments' => $consignments, 'prefix' => $this->prefix, 'peritem' => $peritem, 'getbaseclients' => $getbaseclients]);
    }

    // MIS report3 get records
    public function consignmentReportthree(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $sessionperitem = Session::get('peritem');
        if (!empty($sessionperitem)) {
            $peritem = $sessionperitem;
        } else {
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query
                ->where('status', '!=', 5)
                ->with(
                    'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
                );

            if ($authuser->role_id == 1) {
                $query = $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->whereIn('branch_id', $cc);
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

            $startdate = $request->startdate;
            $enddate = $request->enddate;
            $baseclient_id = $request->baseclient_id;
            $regclient_id = $request->regclient_id;

            if (isset($startdate) && isset($enddate)) {
                $query = $query->whereBetween('consignment_date', [$startdate, $enddate]);
            }
            if ($baseclient_id) {
                $query = $query->whereHas('ConsignerDetail.GetRegClient.BaseClient', function ($q) use ($baseclient_id) {
                    $q->where('id', $baseclient_id);
                });
            }
            if ($regclient_id) {
                $query = $query->whereHas('ConsignerDetail.GetRegClient', function ($q) use ($regclient_id) {
                    $q->where('id', $regclient_id);
                });
            }

            $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);

            $html = view('consignments.consignment-reportThree-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem])->render();
            // $consignments = $consignments->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $regclients = RegionalClient::with('BaseClient:id,client_name')->select('id', 'name', 'location_id', 'baseclient_id')->whereIn('location_id', $cc)->get();

        $base_clients = [];
        foreach ($regclients as $val) {
            $base_clients[] = json_decode($val->BaseClient);
        }

        $uniqueData = array_unique($base_clients, SORT_REGULAR);
        $getbaseclients = array_values($uniqueData); // Resetting array keys

        $query = $query
            ->where('status', '!=', 5)
            ->with(
                'ConsignmentItems:id,consignment_id,order_id,invoice_no,invoice_date,invoice_amount'
            );

        if ($authuser->role_id == 1) {
            $query = $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('consignments.consignment-reportThree', ['consignments' => $consignments, 'prefix' => $this->prefix, 'peritem' => $peritem, 'getbaseclients' => $getbaseclients]);
    }

    // MIS report1 get records
    public function consignmentReports(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $sessionperitem = Session::get('peritem');
        if (!empty($sessionperitem)) {
            $peritem = $sessionperitem;
        } else {
            $peritem = Config::get('variable.PER_PAGE');
        }

        $query = ConsignmentNote::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            $authuser = Auth::user();
            $role_id = Role::where('id', '=', $authuser->role_id)->first();
            $regclient = explode(',', $authuser->regionalclient_id);
            $cc = explode(',', $authuser->branch_id);
            $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

            $query = $query->where('status', '!=', 5)
                ->with('ConsignmentItems', 'ConsignerDetail.Zone', 'ConsigneeDetail.Zone', 'ShiptoDetail.Zone', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient.BaseClient', 'vehicletype');

            if ($authuser->role_id == 1) {
                $query;
            } elseif ($authuser->role_id == 4) {
                $query = $query->whereIn('regclient_id', $regclient);
            } elseif ($authuser->role_id == 7) {
                $query = $query->whereIn('regclient_id', $regclient);
            } else {
                $query = $query->whereIn('branch_id', $cc);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('ConsignerDetail.GetRegClient', function ($regclientquery) use ($search) {
                            $regclientquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('ConsignerDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cnrquery) use ($search, $searchT) {
                                $cnrquery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('ConsigneeDetail', function ($query) use ($search, $searchT) {
                            $query->where(function ($cneequery) use ($search, $searchT) {
                                $cneequery->where('nick_name', 'like', '%' . $search . '%');
                            });
                        });
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

            $startdate = $request->startdate;
            $enddate = $request->enddate;

            if (isset($startdate) && isset($enddate)) {
                $consignments = $query->whereBetween('consignment_date', [$startdate, $enddate])->orderby('created_at', 'DESC')->paginate($peritem);
            } else {
                $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
            }

            $html = view('consignments.mis-report-list-ajax', ['prefix' => $this->prefix, 'consignments' => $consignments, 'peritem' => $peritem])->render();
            // $consignments = $consignments->appends($request->query());

            return response()->json(['html' => $html]);
        }

        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);
        $user = User::where('branch_id', $authuser->branch_id)->where('role_id', 2)->first();

        $query = $query
            ->where('status', '!=', 5)
            ->with('ConsignmentItems', 'ConsignerDetail.Zone', 'ConsigneeDetail.Zone', 'ShiptoDetail.Zone', 'VehicleDetail', 'DriverDetail', 'ConsignerDetail.GetRegClient.BaseClient', 'vehicletype');

        if ($authuser->role_id == 1) {
            $query;
        } elseif ($authuser->role_id == 4) {
            $query = $query->whereIn('regclient_id', $regclient);
        } elseif ($authuser->role_id == 7) {
            $query = $query->whereIn('regclient_id', $regclient);
        } else {
            $query = $query->whereIn('branch_id', $cc);
        }

        $consignments = $query->orderBy('id', 'DESC')->paginate($peritem);
        $consignments = $consignments->appends($request->query());

        return view('consignments.mis-report-list', ['consignments' => $consignments, 'prefix' => $this->prefix, 'peritem' => $peritem]);
    }

    // =============================Admin Report ============================= //

    public function adminReport1(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $query = Consigner::query();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        $consigners = DB::table('consigners')->select('consigners.*', 'regional_clients.name as regional_clientname', 'base_clients.client_name as baseclient_name', 'zones.state as consigner_state', 'consignees.nick_name as consignee_nick_name', 'consignees.contact_name as consignee_contact_name', 'consignees.phone as consignee_phone', 'consignees.postal_code as consignee_postal_code', 'consignees.district as consignee_district', 'consigne_stat.state as consignee_state')
            ->join('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
            ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
            ->join('consignees', 'consignees.consigner_id', '=', 'consigners.id')
            ->leftjoin('zones', 'zones.postal_code', '=', 'consigners.postal_code')
            ->leftjoin('zones as consigne_stat', 'consigne_stat.postal_code', '=', 'consignees.postal_code')
            ->get();

        return view('consignments.admin-report1', ["prefix" => $this->prefix, 'adminrepo' => $consigners]);
    }

    public function adminReport2(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();

        $lr_data = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_nickname', 'regional_clients.name as regional_client_name', 'base_clients.client_name as base_client_name', 'locations.name as locations_name')
            ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
            ->leftjoin('regional_clients', 'regional_clients.id', '=', 'consigners.regionalclient_id')
            ->join('base_clients', 'base_clients.id', '=', 'regional_clients.baseclient_id')
            ->join('locations', 'locations.id', '=', 'regional_clients.location_id')
            ->get();
        return view('consignments.admin-report2', ["prefix" => $this->prefix, 'lr_data' => $lr_data]);
    }

    public function exportExcelReport1(Request $request)
    {
        return Excel::download(new Report1Export($request->startdate, $request->enddate), 'mis_report1.csv');
    }

    public function exportExcelReport2(Request $request)
    {
        return Excel::download(new Report2Export($request->startdate, $request->enddate, $request->baseclient_id, $request->regclient_id), 'mis_report2.csv');
    }

    public function exportExcelReport3(Request $request)
    {
        return Excel::download(new Report3Export($request->startdate, $request->enddate, $request->baseclient_id, $request->regclient_id), 'mis_report3.xlsx');
    }

    // get reg client on Baseclient change filter in MIS2
    public function getRegclients(Request $request)
    {
        $getRegclients = RegionalClient::select('id', 'name')->where(['baseclient_id' => $request->baseclient_id, 'status' => '1'])->get();
        if ($getRegclients) {
            $response['success'] = true;
            $response['success_message'] = "Regional Client list fetch successfully";
            $response['error'] = false;
            $response['data_regclient'] = $getRegclients;
        } else {
            $response['success'] = false;
            $response['error_message'] = "Can not fetch client list please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    public function regionalReport()
    {
        $regional_details = RegionalClient::all();

        foreach ($regional_details as $regional) {
            if($regional->is_misemail == 1){
                date_default_timezone_set('Asia/Kolkata');
                $current_time = date("h:i A");

                if (!empty($regional->email)) {

                    $consignment_details = ConsignmentNote::where('status', '!=', 5)
                        ->where('regclient_id', $regional->id)
                        ->whereDate('consignment_date', '>=', now()->subDays(45))
                        ->first();
                        // ->whereDate('consignment_date', '<=', now())
                        
                    // $consignment_details = ConsignmentNote::where('status', '!=', 5)->where('regclient_id', $regional->id)->whereMonth('consignment_date', date('m'))->whereYear('consignment_date', date('Y'))->first();
                    
                    if (!empty($consignment_details)) {
                        $path = 'regional/Shprider Auto MIS 910003.xlsx';

                        Excel::store(new RegionalReport($regional->id), $path, 'public');
                        $get_file = storage_path('app/public/regional/Shprider Auto MIS 910003.xlsx');

                        $data = ['client_name' => $regional->name, 'current_time' => $current_time];

                        $user['to'] = $regional->email;
                        $sec_emails = explode(',', $regional->secondary_email);
                        if(!empty($sec_emails)){
                            $user['cc'] = $sec_emails;
                        }

                        Mail::send('regional-report-email', $data, function ($messges) use ($user, $get_file) {
                            $messges->to($user['to']);
                            if(!empty($sec_emails)){
                            $messges->cc($user['cc']);
                            }
                            $messges->subject('ShipRider Auto MIS 910003-test');
                            $messges->attach($get_file);

                        });
                    }
                }
            }
        }
        return 'Email Sent';
    }


}
