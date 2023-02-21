<?php

namespace App\Http\Controllers;

use App\Models\BranchAddress;
use App\Models\Consignee;
use App\Models\Consigner;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentSubItem;
use App\Models\Driver;
use App\Models\ItemMaster;
use App\Models\Location;
use App\Models\RegionalClient;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Zone;
use Auth;
use Config;
use DB;
use Helper;
use Illuminate\Http\Request;
use Mail;
use QrCode;
use Response;
use Storage;
use Validator;

class FtlPtlController extends Controller
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

    public function createFtlLrForm()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        if ($authuser->role_id == 2 || $authuser->role_id == 3) {
            if ($authuser->role_id == $role_id->id) {
                $consigners = Consigner::select('id', 'nick_name')->whereIn('branch_id', $cc)->get();
            } else {
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else if ($authuser->role_id != 2 || $authuser->role_id != 3) {
            if ($authuser->role_id != 1) {
                $consigners = Consigner::select('id', 'nick_name')->whereIn('regionalclient_id', $regclient)->get();
            } else {
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else {
            $consigners = Consigner::select('id', 'nick_name')->get();
        }

        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $itemlists = ItemMaster::where('status', '1')->get();

        /////////////////////////////Bill to regional clients //////////////////////////

        if ($authuser->role_id == 2 || $authuser->role_id == 3) {
            $branch = $authuser->branch_id;
            $branch_loc = explode(',', $branch);
            $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name')->get();

        } elseif ($authuser->role_id == 4) {
            $reg = $authuser->regionalclient_id;
            $regional = explode(',', $reg);
            $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name')->get();
        } else {
            $regionalclient = RegionalClient::select('id', 'name')->get();
        }

        return view('Ftl.create-ftl', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists]);
    }

    public function storeFtlLr(Request $request)
    {

        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'consigner_id' => 'required',
                'consignee_id' => 'required',
                'ship_to_id' => 'required',
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

            $authuser = Auth::user();
            $cc = explode(',', $authuser->branch_id);
            $branch_add = BranchAddress::get();
            $locations = Location::whereIn('id', $cc)->first();

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
            }

            $consignmentsave['regclient_id'] = $request->regclient_id;
            $consignmentsave['consigner_id'] = $request->consigner_id;
            $consignmentsave['consignee_id'] = $request->consignee_id;
            $consignmentsave['ship_to_id'] = $request->ship_to_id;
            $consignmentsave['is_salereturn'] = $request->is_salereturn;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['payment_type'] = $request->payment_type;
            $consignmentsave['description'] = $request->description;
            $consignmentsave['packing_type'] = $request->packing_type;
            $consignmentsave['dispatch'] = $request->dispatch;
            // $consignmentsave['total_quantity'] = $request->total_quantity;
            // $consignmentsave['total_weight'] = $request->total_weight;
            // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            $consignmentsave['freight'] = $request->freight;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = $status;
            $consignmentsave['lr_type'] = $request->lr_type;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Assigned";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            $regional_id = RegionalClient::where('id', $request->regclient_id)->first();
            $regional_email = $regional_id->email;

            if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                $saveconsignment = ConsignmentNote::create($consignmentsave);
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {

                        $save_data['consignment_id'] = $saveconsignment->id;
                        $save_data['status'] = 1;
                        $saveconsignmentitems = ConsignmentItem::create($save_data);

                        if ($saveconsignmentitems) {
                            // dd($save_data['item_data']);
                            if (!empty($save_data['item_data'])) {
                                $qty_array = array();
                                $netwt_array = array();
                                $grosswt_array = array();
                                $chargewt_array = array();
                                foreach ($save_data['item_data'] as $key => $save_itemdata) {
                                    // echo "<pre>"; print_r($save_itemdata); die;
                                    $qty_array[] = $save_itemdata['quantity'];
                                    $netwt_array[] = $save_itemdata['net_weight'];
                                    $grosswt_array[] = $save_itemdata['gross_weight'];
                                    $chargewt_array[] = $save_itemdata['chargeable_weight'];

                                    $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                                    $save_itemdata['status'] = 1;

                                    $savesubitems = ConsignmentSubItem::create($save_itemdata);
                                }
                                $quantity_sum = array_sum($qty_array);
                                $netwt_sum = array_sum($netwt_array);
                                $grosswt_sum = array_sum($grosswt_array);
                                $chargewt_sum = array_sum($chargewt_array);

                                ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);

                                ConsignmentNote::where('id', $saveconsignment->id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                            }
                        }
                    }
                }
            } else {
                $consignmentsave['total_quantity'] = $request->total_quantity;
                $consignmentsave['total_weight'] = $request->total_weight;
                $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                $consignmentsave['total_freight'] = $request->total_freight;
                $saveconsignment = ConsignmentNote::create($consignmentsave);

                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
                        $save_data['consignment_id'] = $saveconsignment->id;
                        $save_data['status'] = 1;
                        $saveconsignmentitems = ConsignmentItem::create($save_data);
                    }
                }
            } ///////////////////////////////////////// drs api push/////////////////////////////////////////////
            $consignment_id = $saveconsignment->id;
            //  ======================== Send Email  ===================================//
            if (!empty($regional_email)) {
                $getdata = ConsignmentNote::where('id', $consignment_id)->with('ConsignmentItems', 'ConsignerDetail.GetZone', 'ConsigneeDetail.GetZone', 'ShiptoDetail.GetZone', 'VehicleDetail', 'DriverDetail')->first();
                $data = json_decode(json_encode($getdata), true);

                if (isset($data['consigner_detail']['legal_name'])) {
                    $legal_name = '<b>' . $data['consigner_detail']['legal_name'] . '</b><br>';
                } else {
                    $legal_name = '';
                }
                if (isset($data['consigner_detail']['address_line1'])) {
                    $address_line1 = '' . $data['consigner_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if (isset($data['consigner_detail']['address_line2'])) {
                    $address_line2 = '' . $data['consigner_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if (isset($data['consigner_detail']['address_line3'])) {
                    $address_line3 = '' . $data['consigner_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if (isset($data['consigner_detail']['address_line4'])) {
                    $address_line4 = '' . $data['consigner_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if (isset($data['consigner_detail']['city'])) {
                    $city = $data['consigner_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if (isset($data['consigner_detail']['get_zone']['state'])) {
                    $district = $data['consigner_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if (isset($data['consigner_detail']['postal_code'])) {
                    $postal_code = $data['consigner_detail']['postal_code'] . '<br>';
                } else {
                    $postal_code = '';
                }
                if (isset($data['consigner_detail']['gst_number'])) {
                    $gst_number = 'GST No: ' . $data['consigner_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if (isset($data['consigner_detail']['phone'])) {
                    $phone = 'Phone No: ' . $data['consigner_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }

                $conr_add = $legal_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

                if (isset($data['consignee_detail']['legal_name'])) {
                    $nick_name = '<b>' . $data['consignee_detail']['legal_name'] . '</b><br>';
                } else {
                    $nick_name = '';
                }
                if (isset($data['consignee_detail']['address_line1'])) {
                    $address_line1 = '' . $data['consignee_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if (isset($data['consignee_detail']['address_line2'])) {
                    $address_line2 = '' . $data['consignee_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if (isset($data['consignee_detail']['address_line3'])) {
                    $address_line3 = '' . $data['consignee_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if (isset($data['consignee_detail']['address_line4'])) {
                    $address_line4 = '' . $data['consignee_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if (isset($data['consignee_detail']['city'])) {
                    $city = $data['consignee_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if (isset($data['consignee_detail']['get_zone']['state'])) {
                    $district = $data['consignee_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if (isset($data['consignee_detail']['postal_code'])) {
                    $postal_code = $data['consignee_detail']['postal_code'] . '<br>';
                } else {
                    $postal_code = '';
                }

                if (isset($data['consignee_detail']['gst_number'])) {
                    $gst_number = 'GST No: ' . $data['consignee_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if (isset($data['consignee_detail']['phone'])) {
                    $phone = 'Phone No: ' . $data['consignee_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }

                $consnee_add = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

                if (isset($data['shipto_detail']['legal_name'])) {
                    $nick_name = '<b>' . $data['shipto_detail']['legal_name'] . '</b><br>';
                } else {
                    $nick_name = '';
                }
                if (isset($data['shipto_detail']['address_line1'])) {
                    $address_line1 = '' . $data['shipto_detail']['address_line1'] . '<br>';
                } else {
                    $address_line1 = '';
                }
                if (isset($data['shipto_detail']['address_line2'])) {
                    $address_line2 = '' . $data['shipto_detail']['address_line2'] . '<br>';
                } else {
                    $address_line2 = '';
                }
                if (isset($data['shipto_detail']['address_line3'])) {
                    $address_line3 = '' . $data['shipto_detail']['address_line3'] . '<br>';
                } else {
                    $address_line3 = '';
                }
                if (isset($data['shipto_detail']['address_line4'])) {
                    $address_line4 = '' . $data['shipto_detail']['address_line4'] . '<br><br>';
                } else {
                    $address_line4 = '<br>';
                }
                if (isset($data['shipto_detail']['city'])) {
                    $city = $data['shipto_detail']['city'] . ',';
                } else {
                    $city = '';
                }
                if (isset($data['shipto_detail']['get_zone']['state'])) {
                    $district = $data['shipto_detail']['get_zone']['state'] . ',';
                } else {
                    $district = '';
                }
                if (isset($data['shipto_detail']['postal_code'])) {
                    $postal_code = $data['shipto_detail']['postal_code'] . '<br>';
                } else {
                    $postal_code = '';
                }
                if (isset($data['shipto_detail']['gst_number'])) {
                    $gst_number = 'GST No: ' . $data['shipto_detail']['gst_number'] . '<br>';
                } else {
                    $gst_number = '';
                }
                if (isset($data['shipto_detail']['phone'])) {
                    $phone = 'Phone No: ' . $data['shipto_detail']['phone'] . '<br>';
                } else {
                    $phone = '';
                }

                $shiptoadd = $nick_name . ' ' . $address_line1 . ' ' . $address_line2 . ' ' . $address_line3 . ' ' . $address_line4 . '' . $city . ' ' . $district . ' ' . $postal_code . '' . $gst_number . ' ' . $phone;

                $generate_qrcode = QrCode::size(150)->generate('' . $consignment_id . '');
                $output_file = '/qr-code/img-' . time() . '.svg';
                Storage::disk('public')->put($output_file, $generate_qrcode);
                $fullpath = storage_path('app/public/' . $output_file);
                //  dd($generate_qrcode);
                $no_invoive = count($data['consignment_items']);

                if ($request->typeid == 1) {
                    $adresses = '<table width="100%">
                    <tr>
                        <td style="width:50%">' . $conr_add . '</td>
                        <td style="width:50%">' . $consnee_add . '</td>
                    </tr>
                </table>';
                } else if ($request->typeid == 2) {
                    $adresses = '<table width="100%">
                        <tr>
                            <td style="width:33%">' . $conr_add . '</td>
                            <td style="width:33%">' . $consnee_add . '</td>
                            <td style="width:33%">' . $shiptoadd . '</td>
                        </tr>
                    </table>';
                }

                // get branch address
                if ($locations->id == 2 || $locations->id == 6 || $locations->id == 26) {
                    $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[1]->name . ' </b></span><br />
        <b>' . $branch_add[1]->address . ',</b><br />
        <b>	' . $branch_add[1]->district . ' - ' . $branch_add[1]->postal_code . ',' . $branch_add[1]->state . '</b><br />
        <b>GST No. : ' . $branch_add[1]->gst_number . '</b><br />';
                } else {
                    $branch_address = '<span style="font-size: 14px;"><b>' . $branch_add[0]->name . ' </b></span><br />
        <b>	Plot no: ' . $branch_add[0]->address . ',</b><br />
        <b>	' . $branch_add[0]->district . ' - ' . $branch_add[0]->postal_code . ',' . $branch_add[0]->state . '</b><br />
        <b>GST No. : ' . $branch_add[0]->gst_number . '</b><br />';
                }

                // relocate cnr cnee address check for sale to return case
                if ($data['is_salereturn'] == '1') {
                    $cnradd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            ' . $consnee_add . '
            </p>
            </div>';
                    $cneadd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                ' . $conr_add . '
            </p>
            </div>';
                    $shipto_address = '';
                } else {
                    $cnradd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNOR NAME & ADDRESS</h5><br>
            </div>
            <div style="margin-top: -11px;">
            <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
            ' . $conr_add . '
            </p>
            </div>';
                    $cneadd_heading = '<div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">CONSIGNEE NAME & ADDRESS</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
                ' . $consnee_add . '
            </p>
            </div>';
                    $shipto_address = '<td width="30%" style="vertical-align:top;>
            <div class="container">
            <div>
            <h5  style="margin-left:6px; margin-top: 0px">SHIP TO NAME & ADDRESS</h5><br>
            </div>
                <div style="margin-top: -11px;">
                <p  style="margin-left:6px;margin-top: -13px; font-size: 12px;">
              ' . $shiptoadd . '
            </p>
                </div>
            </td>';
                }

                $pay = public_path('assets/img/LOGO_Frowarders.jpg');
                if (!empty($data['consigner_detail']['get_zone']['state'])) {
                    $cnr_state = $data['consigner_detail']['get_zone']['state'];
                } else {
                    $cnr_state = '';
                }

                $html = '<!DOCTYPE html>
            <html lang="en">
                <head>
                    <!-- Required meta tags -->
                    <meta charset="utf-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1" />

                    <!-- Bootstdap CSS -->

                    <style>
                        * {
                            box-sizing: border-box;
                        }
                        label {
                            padding: 12px 12px 12px 0;
                            display: inline-block;
                        }

                        /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
                        @media screen and (max-width: 600px) {
                        }
                        img {
                            width: 120px;
                            height: 60px;
                        }
                        .a {
                            width: 290px;
                            font-size: 11px;
                        }
                        td.b {
                            width: 238px;
                            margin: auto;
                        }
                        .width_set{
                            width:200px;
                        }
                        img.imgu {
                            margin-left: 58px;
                            height:100px;
                        }
                        .loc {
                                margin-bottom: -8px;
                                margin-top: 27px;
                            }
                            .table3 {
                border-collapse: collapse;
                width: 378px;
                height: 84px;
                margin-left: 71px;
            }
                  .footer {
               position: fixed;
               left: 0;
               bottom: 0;


            }
            .vl {
                border-left: solid;
                height: 18px;
                margin-left: 3px;
            }
            .ff{
              margin-top: 26px;
            }
            .relative {
              position: relative;
              left: 30px;
            }
            .mini-table1{

                border: 1px solid;
                border-radius: 13px;
                width: 429px;
                height: 72px;

            }
            .mini-th{
              width:90px;
              font-size: 12px;
            }
            .ee{
                margin:auto;
                margin-top:12px;
            }
            .nn{
              border-bottom:1px solid;
            }
            .mm{
            border-right:1px solid;
            padding:4px;
            }
            html { -webkit-print-color-adjust: exact; }
            .td_style{
                text-align: left;
                padding: 8px;
                color: #627429;
            }
                    </style>
                <!-- style="border-collapse: collapse; width: 369px; height: 72px; background:#d2c5c5;"class="table2" -->
                </head>
                <body style="font-family:Arial Helvetica,sans-serif;">
                    <div class="container-flex" style="margin-bottom: 5px; margin-top: -30px;">
                        <table style="height: 70px;">
                            <tr>
                            <td class="a" style="font-size: 10px;">
                            ' . $branch_address . '
                            </td>

                                <td class="a">
                                <b>	Email & Phone</b><br />
                                <b>	' . @$locations->email . '</b><br />
                                ' . @$locations->phone . '<br />

                                </td>
                            </tr>

                        </table>
                        <hr />
                        <table>
                            <tr>
                                <td class="b">
                        <div class="ff" >
                                      <img src="' . $fullpath . '" alt="" class="imgu" />
                        </div>
                                </td>
                                <td>
                                    <div style="margin-top: -15px; text-align: center">
                                        <h2 style="margin-bottom: -16px">CONSIGNMENT NOTE</h2>
                                        <P> Original </P>
                                    </div>
                       <div class="mini-table1" style="background:#C0C0C0;">
                                    <table style=" border-collapse: collapse;" class="ee">
                                        <tr>
                                            <th class="mini-th mm nn">LR Number</th>
                                            <th class="mini-th mm nn">LR Date</th>
                                            <th class="mini-th mm nn">Dispatch</th>
                                            <th class="mini-th nn">Delivery</th>
                                        </tr>
                                        <tr>
                                            <th class="mini-th mm" >' . $data['id'] . '</th>
                                            <th class="mini-th mm">' . date('d-m-Y', strtotime($data['consignment_date'])) . '</th>
                                            <th class="mini-th mm"> ' . @$data['consigner_detail']['city'] . '</th>
                                            <th class="mini-th">' . @$data['consignee_detail']['city'] . '</th>

                                        </tr>
                                    </table>
                        </div>
                                </td>
                            </tr>
                        </table>
                        <div class="loc">
                            <table>
                                <tr>
                                    <td class="width_set">
                                        <div style="margin-left: 20px">
                                    <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consigner_detail']['postal_code'] . ',' . @$data['consigner_detail']['city'] . ',' . @$cnr_state . '</b></i><div class="vl" ></div>

                                        <i class="fa-solid fa-location-dot" style="font-size: 10px; ">&nbsp;&nbsp;<b>' . @$data['consignee_detail']['postal_code'] . ',' . @$data['consignee_detail']['city'] . ',' . @$data['consignee_detail']['get_zone']['state'] . '</b></i><div style="font-size: 10px; margin-left: 3px;">&nbsp; &nbsp;</div>
                                        </div>
                                    </td>
                                    <td class="width_set">
                                        <table border="1px solid" class="table3">
                                            <tr>
                                                <td width="40%" ><b style="margin-left: 7px;">Vehicle No</b></td>
                                                <td>' . @$data['vehicle_detail']['regn_no'] . '</td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><b style="margin-left: 7px;"> Driver Name</b></td>
                                                <td>' . ucwords(@$data['driver_detail']['name']) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><b style="margin-left: 7px;">Driver Number</b></td>
                                                <td>' . ucwords(@$data['driver_detail']['phone']) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="container">
                                <div class="row">
                                    <div class="col-sm-12 ">
                                        <h4 style="margin-left:19px;"><b>Pickup and Drop Information</b></h4>
                                    </div>
                                </div>
                            <table border="1" style=" border-collapse:collapse; width: 690px; ">
                                <tr>
                                    <td width="30%" style="vertical-align:top; >
                                    ' . $cnradd_heading . '
                                    </td>
                                    <td width="30%" style="vertical-align:top;>
                                    ' . $cneadd_heading . '
                                    </td>
                                    ' . $shipto_address . '
                                </tr>
                            </table>
                      </div>
                                <div>
                                      <div class="row">
                                                           <div class="col-sm-12 ">
                                                <h4 style="margin-left:19px;"><b>Order Information</b></h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <table border="1" style=" border-collapse:collapse; width: 690px;height: 48px; font-size: 10px; background-color:#e0dddc40;">

                                                    <tr>
                                                        <th>Number of invoice</th>
                                                        <th>Item Description</th>
                                                        <th>Mode of packing</th>
                                                        <th>Total Quantity</th>
                                                        <th>Total Net Weight</th>
                                                        <th>Total Gross Weight</th>
                                                    </tr>
                                                    <tr>
                                                        <th>' . $no_invoive . '</th>
                                                        <th>' . $data['description'] . '</th>
                                                        <th>' . $data['packing_type'] . '</th>
                                                        <th>' . $data['total_quantity'] . '</th>
                                                        <th>' . $data['total_weight'] . ' Kgs.</th>
                                                        <th>' . $data['total_gross_weight'] . ' Kgs.</th>


                                                    </tr>
                                                </table>
                                </div>

                                <div class="inputfiled">
                                <table style="width: 690px;
                                font-size: 10px; background-color:#e0dddc40;">
                              <tr>
                                  <th style="width:70px ">Order ID</th>
                                  <th style="width: 70px">Inv No</th>
                                  <th style="width: 70px">Inv Date</th>
                                  <th style="width:70px " >Inv Amount</th>
                                  <th style="width:70px ">E-way No</th>
                                  <th style="width: 70px">E-Way Date</th>
                                  <th style="width: 60px">Quantity</th>
                                  <th style="width:70px ">Net Weight</th>
                                  <th style="width:70px ">Gross Weight</th>

                              </tr>
                            </table>
                            <table style=" border-collapse:collapse; width: 690px;height: 45px; font-size: 10px; background-color:#e0dddc40; text-align: center;" border="1" >';
                $counter = 0;
                foreach ($data['consignment_items'] as $k => $dataitem) {
                    $counter = $counter + 1;

                    $html .= ' <tr>
                                <td style="width:70px ">' . $dataitem['order_id'] . '</td>
                                <td style="width: 70px">' . $dataitem['invoice_no'] . '</td>
                                <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['invoice_date']) . '</td>
                                <td style="width:70px ">' . $dataitem['invoice_amount'] . '</td>
                                <td style="width: 70px">' . $dataitem['e_way_bill'] . '</td>
                                <td style="width:70px ">' . Helper::ShowDayMonthYear($dataitem['e_way_bill_date']) . '</td>
                                <td style="width:60px "> ' . $dataitem['quantity'] . '</td>
                                <td style="width:70px ">' . $dataitem['weight'] . ' Kgs. </td>
                                <td style="width:70px "> ' . $dataitem['gross_weight'] . ' Kgs.</td>

                                </tr>';
                }
                $html .= '      </table>
                                <div>
                                    <table style="margin-top:0px;">
                                    <tr>
                                    <td width="50%" style="font-size: 13px;"><p style="margin-top:60px;"><b>Received the goods mentioned above in good conditions.</b><br><br>Receivers Name & Number:<br><br>Receiving Date & Time	:<br><br>Receiver Signature:<br><br></p></td>
                                    <td  width="50%"><p style="margin-left: 99px; margin-bottom:150px;"><b>For Eternity Forwarders Pvt.Ltd</b></p></td>
                                </tr>
                                    </table>

                                </div>
                          </div>

                  <!-- <div class="footer">
                                  <p style="text-align:center; font-size: 10px;">Terms & Conditions</p>
                                <p style="font-size: 8px; margin-top: -5px">1. Eternity Solutons does not take any responsibility for damage,leakage,shortage,breakages,soliage by sun ran ,fire and any other damage caused.</p>
                                <p style="font-size: 8px; margin-top: -5px">2. The goods will be delivered to Consignee only against,payment of freight or on confirmation of payment by the consignor. </p>
                                <p style="font-size: 8px; margin-top: -5px">3. The delivery of the goods will have to be taken immediately on arrival at the destination failing which the  consignee will be liable to detention charges @Rs.200/hour or Rs.300/day whichever is lower.</p>
                                <p style="font-size: 8px; margin-top: -5px">4. Eternity Solutons takes absolutely no responsibility for delay or loss in transits due to accident strike or any other cause beyond its control and due to breakdown of vehicle and for the consequence thereof. </p>
                                <p style="font-size: 8px; margin-top: -5px">5. Any complaint pertaining the consignment note will be entertained only within 15 days of receipt of the meterial.</p>
                                <p style="font-size: 8px; margin-top: -5px">6. In case of mismatch in e-waybill & Invoice of the consignor, Eternity Solutons will impose a penalty of Rs.15000/Consignment  Note in addition to the detention charges stated above. </p>
                                <p style="font-size: 8px; margin-top: -5px">7. Any dispute pertaining to the consigment Note will be settled at chandigarh jurisdiction only.</p>
                  </div> -->
                    </div>
                    <!-- Optional JavaScript; choose one of the two! -->

                    <!-- Option 1: Bootstdap Bundle with Popper -->
                    <script
                        src="https://cdn.jsdelivr.net/npm/bootstdap@5.0.2/dist/js/bootstdap.bundle.min.js"
                        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                        crossorigin="anonymous"
                    ></script>

                    <!-- Option 2: Separate Popper and Bootstdap JS -->
                    <!--
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstdap@5.0.2/dist/js/bootstdap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKtdIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
                -->
                </body>
            </html>
            ';

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('legal', 'portrait');

                $data = ['Lr_No' => $consignment_id];
                $user['to'] = $regional_email;
                Mail::send('consignments.email-template', $data, function ($messges) use ($user, $pdf) {
                    $messges->to($user['to']);
                    $messges->subject('LR Created');
                    $messges->attachData($pdf->output(), "invoice.pdf");

                });
            }

            // ================================end Send Email ============================= //
            //===================== Create DRS in LR ================================= //
            if (!empty($request->vehicle_id)) {
                $consignmentdrs = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_name', 'consignees.nick_name as consignee_name', 'consignees.city as city', 'consignees.postal_code as pincode', 'vehicles.regn_no as regn_no', 'drivers.name as driver_name', 'drivers.phone as driver_phone')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->leftjoin('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->leftjoin('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $consignment_id)
                    ->first(['consignees.city']);
                $simplyfy = json_decode(json_encode($consignmentdrs), true);

                $no_of_digit = 5;
                $drs = DB::table('transaction_sheets')->select('drs_no')->latest('drs_no')->first();
                $drs_no = json_decode(json_encode($drs), true);
                if (empty($drs_no) || $drs_no == null) {
                    $drs_no['drs_no'] = 0;
                }
                $number = $drs_no['drs_no'] + 1;
                $drs_no = str_pad($number, $no_of_digit, "0", STR_PAD_LEFT);

                $transaction = DB::table('transaction_sheets')->insert(['drs_no' => $drs_no, 'consignment_no' => $simplyfy['id'], 'consignee_id' => $simplyfy['consignee_name'], 'consignment_date' => $simplyfy['consignment_date'], 'branch_id' => $authuser->branch_id, 'city' => $simplyfy['city'], 'pincode' => $simplyfy['pincode'], 'total_quantity' => $simplyfy['total_quantity'], 'total_weight' => $simplyfy['total_weight'], 'vehicle_no' => $simplyfy['regn_no'], 'driver_name' => $simplyfy['driver_name'], 'driver_no' => $simplyfy['driver_phone'], 'order_no' => '1', 'delivery_status' => 'Assigned', 'status' => '1']);
            }
            //===========================End drs lr ================================= //
            // if ($saveconsignment) {
            /******* PUSH LR to Shadow if vehicle available & Driver has team & fleet ID   ********/
            $get_branch_detail = Location::where('id', $authuser->branch_id)->first();
            // check app assign ========================================
            if($get_branch_detail->app_use == 'Eternity'){
                if(!empty($request->driver_id)){
                    $update = DB::table('consignment_notes')->where('id', $saveconsignment->id)->update(['lr_mode' => 2]);
                }
            }else{
                $vn = $consignmentsave['vehicle_id'];
                $lid = $saveconsignment->id;
                $lrdata = DB::table('consignment_notes')->select('consignment_notes.*', 'consigners.nick_name as consigner_id', 'consignees.nick_name as consignee_name', 'consignees.phone as phone', 'consignees.email as email', 'vehicles.regn_no as vehicle_id', 'consignees.city as city', 'consignees.postal_code as pincode', 'drivers.name as driver_id', 'drivers.phone as driver_phone', 'drivers.team_id as team_id', 'drivers.fleet_id as fleet_id')
                    ->join('consigners', 'consigners.id', '=', 'consignment_notes.consigner_id')
                    ->join('consignees', 'consignees.id', '=', 'consignment_notes.consignee_id')
                    ->join('vehicles', 'vehicles.id', '=', 'consignment_notes.vehicle_id')
                    ->join('drivers', 'drivers.id', '=', 'consignment_notes.driver_id')
                    ->where('consignment_notes.id', $lid)
                    ->get();
                $simplyfy = json_decode(json_encode($lrdata), true);
                //echo "<pre>";print_r($simplyfy);die;
                //Send Data to API
    
                if (($request->edd) >= $request->consignment_date) {
                    if (!empty($vn) && !empty($simplyfy[0]['team_id']) && !empty($simplyfy[0]['fleet_id'])) {
                        $createTask = $this->createTookanTasks($simplyfy);
                        $json = json_decode($createTask[0], true);
                        $job_id = $json['data']['job_id'];
                        $tracking_link = $json['data']['tracking_link'];
                        $update = DB::table('consignment_notes')->where('id', $lid)->update(['job_id' => $job_id, 'tracking_link' => $tracking_link, 'lr_mode' => 1]);
                    }
                }
            }

            $app_notify = $this->sendNotification($request->driver_id);

            $url = $this->prefix . '/consignments';
            $response['success'] = true;
            $response['success_message'] = "Consignment Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'create-consignment';
            $response['redirect_url'] = $url;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    // ============================== Create Ptl Form ============================= //
    public function createPtlLrForm()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $role_id = Role::where('id', '=', $authuser->role_id)->first();
        $regclient = explode(',', $authuser->regionalclient_id);
        $cc = explode(',', $authuser->branch_id);

        if ($authuser->role_id == 2 || $authuser->role_id == 3) {
            if ($authuser->role_id == $role_id->id) {
                $consigners = Consigner::select('id', 'nick_name')->whereIn('branch_id', $cc)->get();
            } else {
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else if ($authuser->role_id != 2 || $authuser->role_id != 3) {
            if ($authuser->role_id != 1) {
                $consigners = Consigner::select('id', 'nick_name')->whereIn('regionalclient_id', $regclient)->get();
            } else {
                $consigners = Consigner::select('id', 'nick_name')->get();
            }
        } else {
            $consigners = Consigner::select('id', 'nick_name')->get();
        }

        $vehicles = Vehicle::where('status', '1')->select('id', 'regn_no')->get();
        $drivers = Driver::where('status', '1')->select('id', 'name', 'phone')->get();
        $vehicletypes = VehicleType::where('status', '1')->select('id', 'name')->get();
        $itemlists = ItemMaster::where('status', '1')->get();

        /////////////////////////////Bill to regional clients //////////////////////////

        // if ($authuser->role_id == 2 || $authuser->role_id == 3) {
        //     $branch = $authuser->branch_id;
        //     $branch_loc = explode(',', $branch);
        //     $regionalclient = RegionalClient::whereIn('location_id', $branch_loc)->select('id', 'name')->get();

        // } elseif ($authuser->role_id == 4) {
        //     $reg = $authuser->regionalclient_id;
        //     $regional = explode(',', $reg);
        //     $regionalclient = RegionalClient::whereIn('id', $regional)->select('id', 'name')->get();
        // } else {
        $regionalclient = RegionalClient::select('id', 'name')->get();
        // }

        return view('Ftl.create-ptl', ['prefix' => $this->prefix, 'consigners' => $consigners, 'vehicles' => $vehicles, 'vehicletypes' => $vehicletypes, 'drivers' => $drivers, 'regionalclient' => $regionalclient, 'itemlists' => $itemlists]);
    }

    public function storePtlLr(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'consigner_id' => 'required',
                'consignee_id' => 'required',
                'ship_to_id' => 'required',
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

            $authuser = Auth::user();
            $cc = explode(',', $authuser->branch_id);

            if (empty($request->vehicle_id)) {
                $status = '2';
            } else {
                $status = '1';
            }

            $consignmentsave['regclient_id'] = $request->regclient_id;
            $consignmentsave['consigner_id'] = $request->consigner_id;
            $consignmentsave['consignee_id'] = $request->consignee_id;
            $consignmentsave['ship_to_id'] = $request->ship_to_id;
            $consignmentsave['is_salereturn'] = $request->is_salereturn;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['payment_type'] = $request->payment_type;
            $consignmentsave['description'] = $request->description;
            $consignmentsave['packing_type'] = $request->packing_type;
            $consignmentsave['dispatch'] = $request->dispatch;
            // $consignmentsave['total_quantity'] = $request->total_quantity;
            // $consignmentsave['total_weight'] = $request->total_weight;
            // $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
            $consignmentsave['freight'] = $request->freight;
            $consignmentsave['transporter_name'] = $request->transporter_name;
            $consignmentsave['vehicle_type'] = $request->vehicle_type;
            $consignmentsave['purchase_price'] = $request->purchase_price;
            $consignmentsave['user_id'] = $authuser->id;
            $consignmentsave['vehicle_id'] = $request->vehicle_id;
            $consignmentsave['driver_id'] = $request->driver_id;
            $consignmentsave['branch_id'] = $authuser->branch_id;

            $consignmentsave['edd'] = $request->edd;
            $consignmentsave['status'] = $status;
            $consignmentsave['lr_type'] = $request->lr_type;
            if (!empty($request->vehicle_id)) {
                $consignmentsave['delivery_status'] = "Started";
            } else {
                $consignmentsave['delivery_status'] = "Unassigned";
            }

            $consignee = Consignee::where('id', $request->consignee_id)->first();
            $consignee_pincode = $consignee->postal_code;
            if (empty($consignee_pincode)) {
                $response['success'] = false;
                $response['error_message'] = "Postal Code Not Found";
                $response['error'] = true;
                return response()->json($response);
            }

            $getpin_transfer = Zone::where('postal_code', $consignee_pincode)->first();
            $get_zonebranch = $getpin_transfer->hub_transfer;

            $get_location = Location::where('id', $authuser->branch_id)->first();
            $chk_h2h_branch = $get_location->with_h2h;
            $location_name = $get_location->name;

            if (!empty($get_zonebranch)) {
                $get_branch = Location::where('name', $get_zonebranch)->first();
                $get_branch_id = $get_branch->id;
            } else {
                $get_branch_id = $authuser->branch_id;
                $get_zonebranch = $location_name;
            }
            $consignmentsave['to_branch_id'] = $get_branch_id;

            ///h2h branch check
            if ($location_name == $get_zonebranch) {
                if (!empty($request->vehicle_id)) {
                    $consignmentsave['delivery_status'] = "Started";
                } else {
                    $consignmentsave['delivery_status'] = "Unassigned";
                }
                $consignmentsave['hrs_status'] = 2;
                $consignmentsave['h2h_check'] = 'lm';
                ///same location check
                if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {

                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            $saveconsignmentitems = ConsignmentItem::create($save_data);

                            if ($saveconsignmentitems) {
                                // dd($save_data['item_data']);
                                if (!empty($save_data['item_data'])) {
                                    $qty_array = array();
                                    $netwt_array = array();
                                    $grosswt_array = array();
                                    $chargewt_array = array();
                                    foreach ($save_data['item_data'] as $key => $save_itemdata) {
                                        // echo "<pre>"; print_r($save_itemdata); die;
                                        $qty_array[] = $save_itemdata['quantity'];
                                        $netwt_array[] = $save_itemdata['net_weight'];
                                        $grosswt_array[] = $save_itemdata['gross_weight'];
                                        $chargewt_array[] = $save_itemdata['chargeable_weight'];

                                        $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                                        $save_itemdata['status'] = 1;

                                        $savesubitems = ConsignmentSubItem::create($save_itemdata);
                                    }

                                    $quantity_sum = array_sum($qty_array);
                                    $netwt_sum = array_sum($netwt_array);
                                    $grosswt_sum = array_sum($grosswt_array);
                                    $chargewt_sum = array_sum($chargewt_array);

                                    ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);

                                    ConsignmentNote::where('id', $saveconsignment->id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                                }
                            }
                        }

                    }
                } else {
                    $consignmentsave['total_quantity'] = $request->total_quantity;
                    $consignmentsave['total_weight'] = $request->total_weight;
                    $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                    $consignmentsave['total_freight'] = $request->total_freight;
                    $saveconsignment = ConsignmentNote::create($consignmentsave);

                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            $saveconsignmentitems = ConsignmentItem::create($save_data);
                        }
                    }
                }
            } else {

                $consignmentsave['h2h_check'] = 'h2h';
                $consignmentsave['hrs_status'] = 2;

                if ($request->invoice_check == 1 || $request->invoice_check == 2) {
                    $saveconsignment = ConsignmentNote::create($consignmentsave);
                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            $saveconsignmentitems = ConsignmentItem::create($save_data);

                            if ($saveconsignmentitems) {
                                // dd($save_data['item_data']);
                                if (!empty($save_data['item_data'])) {
                                    $qty_array = array();
                                    $netwt_array = array();
                                    $grosswt_array = array();
                                    $chargewt_array = array();
                                    foreach ($save_data['item_data'] as $key => $save_itemdata) {
                                        // echo "<pre>"; print_r($save_itemdata); die;
                                        $qty_array[] = $save_itemdata['quantity'];
                                        $netwt_array[] = $save_itemdata['net_weight'];
                                        $grosswt_array[] = $save_itemdata['gross_weight'];
                                        $chargewt_array[] = $save_itemdata['chargeable_weight'];

                                        $save_itemdata['conitem_id'] = $saveconsignmentitems->id;
                                        $save_itemdata['status'] = 1;

                                        $savesubitems = ConsignmentSubItem::create($save_itemdata);
                                    }

                                    $quantity_sum = array_sum($qty_array);
                                    $netwt_sum = array_sum($netwt_array);
                                    $grosswt_sum = array_sum($grosswt_array);
                                    $chargewt_sum = array_sum($chargewt_array);

                                    ConsignmentItem::where('id', $savesubitems->conitem_id)->update(['quantity' => $quantity_sum, 'weight' => $netwt_sum, 'gross_weight' => $grosswt_sum, 'chargeable_weight' => $chargewt_sum]);

                                    ConsignmentNote::where('id', $saveconsignment->id)->update(['total_quantity' => $quantity_sum, 'total_weight' => $netwt_sum, 'total_gross_weight' => $grosswt_sum]);
                                }
                            }
                        }
                    }
                } else {
                    $consignmentsave['total_quantity'] = $request->total_quantity;
                    $consignmentsave['total_weight'] = $request->total_weight;
                    $consignmentsave['total_gross_weight'] = $request->total_gross_weight;
                    $consignmentsave['total_freight'] = $request->total_freight;
                    $saveconsignment = ConsignmentNote::create($consignmentsave);

                    if (!empty($request->data)) {
                        $get_data = $request->data;
                        foreach ($get_data as $key => $save_data) {
                            $save_data['consignment_id'] = $saveconsignment->id;
                            $save_data['status'] = 1;
                            $saveconsignmentitems = ConsignmentItem::create($save_data);
                        }
                    }
                }
            }

            $url = $this->prefix . '/consignments';
            $response['success'] = true;
            $response['success_message'] = "Consignment Added successfully";
            $response['error'] = false;
            // $response['resetform'] = true;
            $response['page'] = 'create-consignment';
            $response['redirect_url'] = $url;

            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    //++++++++++++++++++++++ Tookan API Push +++++++++++++++++++++++++++++++++++//

    public function createTookanTasks($taskDetails)
    {

        //echo "<pre>";print_r($taskDetails);die;

        foreach ($taskDetails as $task) {

            $td = '{
                "api_key": "' . $this->apikey . '",
                "order_id": "' . $task['consignment_no'] . '",
                "job_description": "DRS-' . $task['id'] . '",
                "customer_email": "' . $task['email'] . '",
                "customer_username": "' . $task['consignee_name'] . '",
                "customer_phone": "' . $task['phone'] . '",
                "customer_address": "' . $task['pincode'] . ',' . $task['city'] . ',India",
                "latitude": "",
                "longitude": "",
                "job_delivery_datetime": "' . $task['edd'] . ' 21:00:00",
                "custom_field_template": "Template_1",
                "meta_data": [
                    {
                        "label": "Invoice Amount",
                        "data": "' . $task['invoice_amount'] . '"
                    },
                    {
                        "label": "Quantity",
                        "data": "' . $task['total_weight'] . '"
                    }
                ],
                "team_id": "' . $task['team_id'] . '",
                "auto_assignment": "1",
                "has_pickup": "0",
                "has_delivery": "1",
                "layout_type": "0",
                "tracking_link": 1,
                "timezone": "-330",
                "fleet_id": "' . $task['fleet_id'] . '",
                "notify": 1,
                "tags": "",
                "geofence": 0
            }';

            //echo "<pre>";print_r($td);echo "</pre>";die;

            //die;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.tookanapp.com/v2/create_task',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $td,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            ));

            $response[] = curl_exec($curl);

            curl_close($curl);

        }
        //echo "<pre>";print_r($response);echo "</pre>";die;
        return $response;

    }

    // Multiple Deliveries at once

    public function createTookanMultipleTasks($taskDetails)
    {
        //echo "<pre>";print_r($taskDetails);die;
        $deliveries = array();
        foreach ($taskDetails as $task) {

            $deliveries[] = '{
                "address": "' . $task['pincode'] . ',' . $task['city'] . ',India",
                "name": "' . $task['consignee_name'] . '",
                "latitude": " ",
                "longitude": " ",
                "time": "' . $task['edd'] . '",
                "phone": "' . $task['phone'] . '",
                "job_description": "LR-ID:' . $task['id'] . '",
                "template_name": "Template_1",
                "template_data": [
                  {
                    "label": "Invoice Amount",
                    "data":  "' . $task['invoice_amount'] . '"
                  },
                  {
                    "label": "Quantity",
                    "data": "' . $task['total_weight'] . '"
                  }
                ],
                "email": null,
                 "order_id":  "' . $task['id'] . '"
                }';
        }
        $de_json = implode(",", $deliveries);
        //echo "<pre>"; print_r($de_json);die;

        $apidata = '{
                "api_key": "' . $this->apikey . '",
                "fleet_id": "' . $taskDetails[0]['fleet_id'] . '",
                "timezone": -330,
                "has_pickup": 0,
                "has_delivery": 1,
                "layout_type": 0,
                "geofence": 0,
                "team_id": "' . $taskDetails[0]['team_id'] . '",
                "auto_assignment": 1,
                "tags": "",
                "deliveries": [' . $de_json . ']
              }';

        //echo "<pre>";print_r($apidata);echo "</pre>";die;

        //die;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.tookanapp.com/v2/create_multiple_tasks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $apidata,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    //+++++++++++++++++++++++ webhook for status update +++++++++++++++++++++++++//
    public function sendNotification($request)
    {

         $firebaseToken = Driver::where('id', $request)->whereNotNull('device_token')->pluck('device_token')->all();

        $SERVER_API_KEY = "AAAAd3UAl0E:APA91bFmxnV3YOAWBLrjOVb8n2CRiybMsXsXqKwDtYdC337SE0IRr1BTFLXWflB5VKD-XUjwFkS4v7I2XlRo9xmEYcgPOqrW0fSq255PzfmEwXurbxzyUVhm_jS37-mtkHFgLL3yRoXh";
       

        $data_json = ['type' => 'Assigned', 'status' => 1];

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "LR Assigned",
                "body" => "New LR assigned to you, please check",
            ],
            "data" => $data_json,
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        return $response;
    }

}
