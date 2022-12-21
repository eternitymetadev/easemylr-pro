@extends('layouts.main')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->

    <style>
        @media only screen and (max-width: 600px) {
            .checkbox-round {
                margin-left: 1px;
            }

        }

        h4 {
            font-size: 18px;

        }


        .checkbox-round {
            width: 2.3em;
            height: 2.3em;
            border-radius: 55%;
            border: 1px solid #ddd;
            margin-left: 103px;
        }

        p {
            font-size: 11px;
            font-weight: 500;
        }


        th, td {
            text-align: left;
            padding: 8px;
            color: black;
        }

        .cont {
            background: white;
            height: 240px;
            border-style: ridge;
            width: 390px;
            border-radius: 17px;
        }

        .mini_container {
            margin-top: 8px;
        }

        .wizard {
            background: #fff;
        }

        .wizard .nav-tabs {
            position: relative;
            margin: 40px auto;
            margin-bottom: 0;
        }

        .wizard > div.wizard-inner {
            position: relative;
        }

        .connecting-line {
            height: 2px;
            background: #e0e0e0;
            position: absolute;
            width: 80%;

            top: 42%;

        }

        .nav-tabs {
            border-bottom: none;
        }

        .wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
            color: #555555;
            cursor: default;
            border: none;
        }

        span.round-tab {
            width: 50px;
            height: 50px;
            line-height: 51px;
            display: inline-block;
            border-radius: 100px;
            background: #fff;
            border: 2px solid #e0e0e0;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 25px;
        }

        span.round-tab i {
            color: #555555;
        }

        .wizard li.active span.round-tab {
            background: #fff;
            border: 2px solid #5bc0de;

        }

        .wizard li.active span.round-tab i {
            color: #5bc0de;
        }

        span.round-tab:hover {
            color: #333;
            border: 2px solid #333;
        }

        .wizard .nav-tabs > li {
            width: 25%;
        }

        .wizard .nav-tabs > li a {
            width: 48px;
            height: 70px;
            border-radius: 100%;
            padding: 0;
        }

        @media ( max-width: 585px ) {

            .wizard {
                width: 90%;
                height: auto !important;
            }

            span.round-tab {
                font-size: 16px;
                width: 50px;
                height: 50px;
                line-height: 50px;
            }

            .wizard .nav-tabs > li a {
                width: 50px;
                height: 50px;
                line-height: 50px;
            }

            .wizard li.active:after {
                content: " ";
                position: absolute;
                left: 35%;
            }
        }

        /* / ////////////////////////////////////////////////////////////////////end wizard / */
        .select2-results__options {
            list-style: none;
            margin: 0;
            padding: 0;
            height: 160px;
            /* scroll-margin: 38px; */
            overflow: auto;
        }

        /*.form-group {*/
        /*    margin-bottom: 0;*/
        /*}*/

        .form-row > .col, .form-row > [class*=col-] {
            padding-inline: 10px !important;
        }

        span.select2.select2-container.mb-4 {
            margin-bottom: 0 !important;
        }

        .form-row {
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 0 3px #83838360;
            margin-bottom: 1rem;
        }

        .form-row h6 {
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .mainTr {
            outline: 1px solid #838383;
            background: #f4f4f4;
            border-radius: 12px;
            width: 100%;
        }

        .childTable {
            background: #F9B60030;
            border-radius: 12px;
        }

        .addRowButton {
            text-align: right;
            width: 100%;
            font-weight: 800;
            color: #f9b600;
            margin-right: 10px;
            cursor: pointer;
        }

        .addItem {
            float: right;
            font-weight: 800;
            color: #f9b600;
            margin-right: 10px;
            cursor: pointer;
        }

        .items_table_body tr {
            position: relative;
        }


        .main_table_body td {
            min-width: 150px;
        }

        .main_table_body td:has(div.removeIcon) {
            min-width: 50px;
            width: 50px;
        }

        .main_table_body td div.removeIcon:has(span) {
            cursor: pointer;
            height: 20px;
            width: 20px;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50vh;
            color: darkred;
            font-weight: 800;
            border: 1px solid darkred;
            transition: all 200ms ease-in-out;
        }

        .main_table_body td div.removeIcon:has(span):hover {
            background: darkred;
            color: white;
        }

        .appendedAddress:has(br) {
            padding: 1rem;
            border-radius: 12px;
            margin-top: 1rem;
            background: #f9b60024;
            color: #000;
        }

    </style>



    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Update Order</h2>
        </div>


        <form class="general_form" method="POST" action="{{url($prefix.'/orders/update-order')}}" id="updateorder"
              style="margin: auto; ">
            @csrf
            <input type="hidden" name="consignment_id" value="{{$getconsignments->id}}">
            <input type="hidden" name="booked_drs" value="{{$getconsignments->booked_drs}}">

            <div class="form-row">
                <h6 class="col-12">Bill To Information</h6>
                <div class=" col-sm-4 ">
                    <label for="exampleFormControlSelect1">
                        Select Bill to Client<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="select_regclient"
                            name="regclient_id" disabled>
                        <option value="">Select</option>
                        @if(count($regionalclient) > 0)
                            @foreach($regionalclient as $client)
                                <option
                                    value="{{ $client->id }}" {{ $client->id == $getconsignments->regclient_id ? 'selected' : ''}}>{{ucwords($client->name)}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" name="regclient_id" value="{{$getconsignments->regclient_id}}"/>
                </div>
                <div class=" col-sm-4">
                    <label for="exampleFormControlSelect1">
                        Payment Term<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" name="payment_type" disabled>
                        <option
                            value="To be Billed" {{$getconsignments->payment_type == 'To be Billed' ? 'selected' : ''}}>
                            To be Billed
                        </option>
                        <option
                            value="To Pay" {{$getconsignments->payment_type == 'To Pay' ? 'selected' : ''}}>
                            To Pay
                        </option>
                        <option value="Paid" {{$getconsignments->payment_type == 'Paid' ? 'selected' : ''}}>
                            Paid
                        </option>
                    </select>
                </div>
                <div class=" col-sm-4">
                    <label for="exampleFormControlSelect1">
                        Freight<span class="text-danger">*</span>
                    </label>
                    <Input type="number" class="form-control form-small" name="freight"
                           value="{{old('freight',isset($getconsignments->freight)?$getconsignments->freight:'')}}"
                           disabled/>
                </div>
            </div>

            <div class="form-row">
                <h6 class="col-12">Pickup and Drop Information</h6>
                <div class="col-sm-4 ">
                    <label>
                        Select Pickup Location (Consigner)<span class="text-danger">*</span>
                    </label>
                    <select id="select_consigner" class="my-select2 form-seteing" type="text"
                            name="consigner_id" disabled>
                        <option value="">Select Consigner</option>
                        @if(count($consigners) > 0)
                            @foreach($consigners as $k => $consigner)
                                <option
                                    value="{{ $k }}" {{ $k == $getconsignments->consigner_id ? 'selected' : ''}}>{{ucwords($consigner)}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" name="consigner_id" value="{{$getconsignments->consigner_id}}"/>
                    <div class="appendedAddress" id="consigner_address"></div>

                </div>
                <div class="col-sm-4 ">
                    <label>
                        Select Drop location (Bill To Consignee)<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" type="text"
                            name="consignee_id" id="select_consignee" disabled>
                        <option value="">Select Consignee</option>
                        @if(count($consignees) > 0)
                            @foreach($consignees as $k => $consignee)
                                <option
                                    value="{{ $k }}" {{ $k == $getconsignments->consignee_id ? 'selected' : ''}}>{{ucwords($consignee)}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" name="consignee_id" value="{{$getconsignments->consignee_id}}"/>
                    <div class="appendedAddress" id="consignee_address"></div>
                </div>
                <div class="col-sm-4 ">
                    <label>
                        Select Drop Location (Ship To Consignee)<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" type="text"
                            name="ship_to_id" id="select_ship_to" disabled>
                        <option value="">Select Ship To</option>
                        @if(count($consignees) > 0)
                            @foreach($consignees as $k => $consignee)
                                <option
                                    value="{{ $k }}" {{ $k == $getconsignments->ship_to_id ? 'selected' : ''}}>{{ucwords($consignee)}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" name="ship_to_id" value="{{$getconsignments->ship_to_id}}"/>
                    <div class="appendedAddress" id="ship_to_address"></div>
                </div>
            </div>

            {{--order info--}}
            <div class="form-row">
                <h6 class="col-12">Order Information</h6>

                <div style="width: 100%">
                    <div class="d-flex flex-wrap align-items-center form-group form-group-sm">
                        <div class="col-md-3">
                            <label>Item Description</label>
                            <input type="text" class="form-control" value="Pesticide"
                                   name="description" list="json-datalist" onkeyup="showResult(this.value)">
                            <datalist id="json-datalist"></datalist>
                        </div>
                        <div class="col-md-3">
                            <label>Mode of Packing</label>
                            <input type="text" class="form-control" value="Case/s"
                                   name="packing_type">
                        </div>
                        <div class="col-md-2">
                            <label>Total Quantity</label>
                            <span id="tot_qty">
                                <?php echo "0";?>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <label>Total Net Weight</label>
                            <span id="tot_nt_wt">
                                <?php echo "0";?>
                            </span> Kgs.
                        </div>
                        <div class="col-md-2">
                            <label>Total Gross Weight</label>
                            <span id="tot_gt_wt">
                                <?php echo "0";?>
                            </span> Kgs.
                        </div>
                    </div>
                </div>


                <div style="overflow-x:auto; padding: 1rem 8px 0; margin-top: 1rem; width: 100%;">
                    <table style="width: 100%; border-collapse: collapse;" id="items_table">
                        <tbody class="main_table_body">
                        <tr>
                            <td>
                                <table class="mainTr">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Order ID</label>
                                                <input type="text" class="form-control orderid" name="order_id">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Number</label>
                                                <input type="text" class="form-control invc_no" id="1"
                                                       name="invoice_no">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Date</label>
                                                <input type="date" class="form-control invc_date" name="invoice_date">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Amount</label>
                                                <input type="number" class="form-control invc_amt"
                                                       name="invoice_amount">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>E-way Bill Number</label>
                                                <input type="number" class="form-control ew_bill" name="e_way_bill">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>E-Way Bill Date</label>
                                                <input type="date" class="form-control ewb_date" name="e_way_bill_date">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <table id="childTable" class="childTable"
                                                   style="width: 85%; min-width: 500px; margin-inline: auto;">
                                                <tbody class="items_table_body">
                                                <tr>
                                                    <td width="200px">
                                                        <div class="form-group form-group-sm">
                                                            <label>Item</label>
                                                            <select class="form-control">
                                                                <option>Option 1</option>
                                                                <option>Option 2</option>
                                                                <option>Option 3</option>
                                                                <option>Option 4</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Quantity</label>
                                                            <input type="number" class="form-control" name="quantity">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Net Weight</label>
                                                            <input type="number" class="form-control" name="netWeight">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Gross Weight</label>
                                                            <input type="number" class="form-control"
                                                                   name="grossWeight">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Chargeable Weight</label>
                                                            <input type="number" class="form-control" name="cWeight">
                                                        </div>

                                                    </td>
                                                    <td><div class="removeIcon"></div></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <span style="margin-right: 8%" class="addItem">+ Add Item</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td><div class="removeIcon"></div></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <span class="addRowButton" onclick="insertMaintableRow()">+ Add Row</span>
            </div>

            <div class="form-row">
                <h6 class="col-12">Vehicle Information</h6>

                <div class=" col-sm-4 ">
                    <label>
                        Vehicle Number<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="vehicle_no" name="vehicle_id"
                            tabindex="-1">
                        <option value="">Select vehicle no</option>
                        @foreach($vehicles as $vehicle)
                            <option
                                value="{{$vehicle->id}}" {{ $vehicle->id == $getconsignments->vehicle_id ? 'selected' : ''}}>{{$vehicle->regn_no}}</option>
                        @endforeach
                    </select>
                </div>
                <div class=" col-sm-4 ">
                    <label>
                        Driver Name<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="driver_id" name="driver_id"
                            tabindex="-1">
                        <option value="">Select driver</option>
                        @foreach($drivers as $driver)
                            <option
                                value="{{$driver->id}}" {{ $driver->id == $getconsignments->driver_id ? 'selected' : ''}}>{{$driver->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class=" col-sm-4 ">
                    <label>
                        EDD<span class="text-danger">*</span>
                    </label>
                    <Input type="date" class="form-control form-small" name="edd"/>

                </div>

                <div class="form-group col-12 mt-2">
                    <input type="checkbox" id="chek" style="margin-left:19px;">
                    <label for="chek">Vehicle Purchase Information</label>
                </div>

                <div class="row flex-wrap align-items-center ml-0" id="veh"
                     style="width: 100%; display:none;">
                    <div class="form-group col-md-4">
                        <label>Vendor Name<span class="text-danger">*</span></label>
                        <Input type="text" class="form-control form-small" name="transporter_name"
                               value="{{$getconsignments->transporter_name}}"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>
                            Vehicle Type<span class="text-danger">*</span>
                        </label>

                        <select class="my-select2 sete" id="vehicle_type" name="vehicle_type"
                                tabindex="-1">
                            <option value="">Select vehicle type</option>
                            @foreach($vehicletypes as $vehicle)
                                <option
                                    value="{{$vehicle->id}}" {{ $vehicle->id == $getconsignments->vehicle_type ? 'selected' : ''}}>{{$vehicle->name}}</option>
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>
                            Purchase Price<span class="text-danger">*</span>
                        </label>
                        <Input type="number" class="form-control form-small"
                               name="purchase_price" value="{{$getconsignments->purchase_price}}"/>
                    </div>
                </div>

            </div>


            <input type="hidden" class="form-seteing date-picker" id="consignDate" name="consignment_date"
                   placeholder="" value="<?php echo date('d-m-Y'); ?>">


            <div class=" col-12 d-flex justify-content-end align-items-center" style="gap: 1rem; margin-top: 3rem;">
                <button type="submit" class="mt-2 btn btn-primary disableme" style="height: 40px; width: 200px">Submit</button>
            </div>

        </form>
        <!-- widget-content-area -->

        @endsection
        @section('js')

            <script>
                function insertMaintableRow() {
                    $("#items_table").each(function () {
                        var tds = `<tr>
                            <td>
                                <table class="mainTr">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Order ID</label>
                                                <input type="text" class="form-control orderid" name="order_id">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Number</label>
                                                <input type="text" class="form-control invc_no" id="1"
                                                       name="invoice_no">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Date</label>
                                                <input type="date" class="form-control invc_date" name="invoice_date">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Amount</label>
                                                <input type="number" class="form-control invc_amt"
                                                       name="invoice_amount">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>E-way Bill Number</label>
                                                <input type="number" class="form-control ew_bill" name="e_way_bill">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>E-Way Bill Date</label>
                                                <input type="date" class="form-control ewb_date" name="e_way_bill_date">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <table id="childTable" class="childTable"
                                                   style="width: 85%; min-width: 500px; margin-inline: auto;">
                                                <tbody class="items_table_body">
                                                <tr>
                                                    <td width="200px">
                                                        <div class="form-group form-group-sm">
                                                            <label>Item</label>
                                                            <select class="form-control">
                                                                <option>Option 1</option>
                                                                <option>Option 2</option>
                                                                <option>Option 3</option>
                                                                <option>Option 4</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Quantity</label>
                                                            <input type="number" class="form-control" name="quantity">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Net Weight</label>
                                                            <input type="number" class="form-control" name="netWeight">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Gross Weight</label>
                                                            <input type="number" class="form-control"
                                                                   name="grossWeight">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Chargeable Weight</label>
                                                            <input type="number" class="form-control" name="cWeight">
                                                        </div>

                                                    </td>
                                                    <td width="50px"><div class="removeIcon"></div></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <span style="margin-right: 8%" class="addItem"
                                                  onclick="insertItemTableRow()">+ Add Item</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="50px"><div class="removeIcon removeInvoice"><span>x</span></div></td>
                        </tr>`;

                        $(this).append(tds);
                    });
                };

                $(document).on("click", ".removeInvoice", function () {
                    $(this).closest("tr").remove();
                });


                $(document).on("click", ".addItem", function () {
                    var itemTds = `<tr>
                                                    <td width="200px">
                                                        <div class="form-group form-group-sm">
                                                            <label>Item</label>
                                                            <select class="form-control">
                                                                <option>Option 1</option>
                                                                <option>Option 2</option>
                                                                <option>Option 3</option>
                                                                <option>Option 4</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Quantity</label>
                                                            <input type="number" class="form-control" name="quantity">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Net Weight</label>
                                                            <input type="number" class="form-control" name="netWeight">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Gross Weight</label>
                                                            <input type="number" class="form-control"
                                                                   name="grossWeight">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Chargeable Weight</label>
                                                            <input type="number" class="form-control" name="cWeight">
                                                        </div>

                                                    </td>
                                                    <td width="50px"><div class="removeIcon removeItem"><span>x</span></div></td>
                                                </tr>`

                    $(this).siblings(".childTable").children('.items_table_body').append(itemTds);
                });

                $(document).on("click", ".removeItem", function () {
                    $(this).closest("tr").remove();
                });
            </script>


            <script>
                // $(function() {
                //     $('.basic').selectpicker();
                // });
                $(document).ready(function () {
                    $('.insert-more').attr('disabled', true);
                });

                jQuery(function () {
                    $('.my-select2').each(function () {
                        $(this).select2({
                            theme: "bootstrap-5",
                            dropdownParent: $(this).parent(), // fix select2 search input focus bug
                        })
                    })

                    // fix select2 bootstrap modal scroll bug
                    $(document).on('select2:close', '.my-select2', function (e) {
                        var evt = "scroll.select2"
                        $(e.target).parents().off(evt)
                        $(window).off(evt)
                    })
                })

                // add consignment date
                $('#consignDate, #date').val(new Date().toJSON().slice(0, 10));

                function showResult(str) {
                    if (str.length == 0) {
                        $(".search-suggestions").empty();
                        $(".search-suggestions").css('border', '0px');
                    } else if (str.length > 0) {
                        $(".search-suggestions").css('border', 'solid 1px #f6f6f6');
                        var options = '';
                        options = "<option value='Seeds'>";
                        options += "<option value='Chemicals'>";
                        options += "<option value='PGR'>";
                        options += "<option value='Fertilizer'>";
                        options += "<option value='Pesticides'>";
                        $('#json-datalist').html(options);
                    }
                }

                $('#chek').click(function () {
                    $('#veh').toggle();
                });

                function myMap() {
                    var mapProp = {
                        center: new google.maps.LatLng(51.508742, -0.120850),
                        zoom: 5,
                    };
                    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
                }

                $(document).ready(function () {
                    var regclient_id = $('#select_regclient').val();
                    var consigner_id = $('#select_consigner').val();
                    var consignee_id = $('#select_consignee').val();
                    var shipto_id = $('#select_ship_to').val();

                    $.ajax({
                        type: 'get',
                        url: APP_URL + '/get_consigners',
                        data: {consigner_id: consigner_id, regclient_id: regclient_id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (res) {
                            // $('#consigner_address').empty();
                            // $('#consignee_address').empty();
                            // $('#ship_to_address').empty();

                            $('#select_consignee').append('<option value="">Select Consignee</option>');
                            $('#select_ship_to').append('<option value="">Select Ship To</option>');
                            $.each(res.consignee, function (key, value) {
                                $('#select_consignee, #select_ship_to').append('<option value="' + value.id + '">' + value.nick_name + '</option>');
                            });
                            if (res.data) {
                                console.log(res.data);
                                if (res.data.address_line1 == null) {
                                    var address_line1 = '';
                                } else {
                                    var address_line1 = res.data.address_line1 + '<br>';
                                }
                                if (res.data.address_line2 == null) {
                                    var address_line2 = '';
                                } else {
                                    var address_line2 = res.data.address_line2 + '<br>';
                                }
                                if (res.data.address_line3 == null) {
                                    var address_line3 = '';
                                } else {
                                    var address_line3 = res.data.address_line3 + '<br>';
                                }
                                if (res.data.address_line4 == null) {
                                    var address_line4 = '';
                                } else {
                                    var address_line4 = res.data.address_line4 + '<br>';
                                }
                                if (res.data.gst_number == null) {
                                    var gst_number = '';
                                } else {
                                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                                }
                                if (res.data.phone == null) {
                                    var phone = '';
                                } else {
                                    var phone = 'Phone: ' + res.data.phone;
                                }

                                $('#consigner_address').append(address_line1 + ' ' + address_line2 + '' + address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');

                                $("#dispatch").val(res.data.city);

                                if (res.data.get_reg_client.name == null) {
                                    var regclient = '';
                                } else {
                                    var regclient = res.data.get_reg_client.name;
                                }
                                $("#regclient").val(regclient);

                                // multiple invoice chaeck on regclicent //
                                if (res.regclient == null) {
                                    var multiple_invoice = '';
                                } else {
                                    if (res.regclient.is_multiple_invoice == null || res.regclient.is_multiple_invoice == '') {
                                        var multiple_invoice = '';
                                    } else {
                                        var multiple_invoice = res.regclient.is_multiple_invoice;
                                    }
                                }

                                if (multiple_invoice == 1) {
                                    $('.insert-more').attr('disabled', false);
                                } else {
                                    $('.insert-more').attr('disabled', true);
                                }
                            }
                        }
                    });
                    ///////get consinee address//
                    $.ajax({
                        type: 'get',
                        url: APP_URL + '/get_consignees',
                        data: {consignee_id: consignee_id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (res) {
                            // $('#consignee_address').empty();
                            if (res.data) {
                                console.log(res.data);
                                if (res.data.address_line1 == null) {
                                    var address_line1 = '';
                                } else {
                                    var address_line1 = res.data.address_line1 + '<br>';
                                }
                                if (res.data.address_line2 == null) {
                                    var address_line2 = '';
                                } else {
                                    var address_line2 = res.data.address_line2 + '<br>';
                                }
                                if (res.data.address_line3 == null) {
                                    var address_line3 = '';
                                } else {
                                    var address_line3 = res.data.address_line3 + '<br>';
                                }
                                if (res.data.address_line4 == null) {
                                    var address_line4 = '';
                                } else {
                                    var address_line4 = res.data.address_line4 + '<br>';
                                }
                                if (res.data.gst_number == null) {
                                    var gst_number = '';
                                } else {
                                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                                }
                                if (res.data.phone == null) {
                                    var phone = '';
                                } else {
                                    var phone = 'Phone: ' + res.data.phone;
                                }

                                $('#consignee_address').append(address_line1 + ' ' + address_line2 + '' + address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
                            }
                        }
                    });
//////////get ship to address///
                    $.ajax({
                        type: 'get',
                        url: APP_URL + '/get_consignees',
                        data: {consignee_id: shipto_id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (res) {
                            // $('#ship_to_address').empty();
                            if (res.data) {
                                if (res.data.address_line1 == null) {
                                    var address_line1 = '';
                                } else {
                                    var address_line1 = res.data.address_line1 + '<br>';
                                }
                                if (res.data.address_line2 == null) {
                                    var address_line2 = '';
                                } else {
                                    var address_line2 = res.data.address_line2 + '<br>';
                                }
                                if (res.data.address_line3 == null) {
                                    var address_line3 = '';
                                } else {
                                    var address_line3 = res.data.address_line3 + '<br>';
                                }
                                if (res.data.address_line4 == null) {
                                    var address_line4 = '';
                                } else {
                                    var address_line4 = res.data.address_line4 + '<br>';
                                }
                                if (res.data.gst_number == null) {
                                    var gst_number = '';
                                } else {
                                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                                }
                                if (res.data.phone == null) {
                                    var phone = '';
                                } else {
                                    var phone = 'Phone: ' + res.data.phone;
                                }

                                $('#ship_to_address').append(address_line1 + ' ' + address_line2 + '' + address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
                            }
                        }
                    });


                });


            </script>
            <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ6x_bU2BIZPPsjS8Y8Zs-yM2g2Bs2mnM&callback=myMap"></script>
@endsection
