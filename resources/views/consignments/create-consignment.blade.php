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
        }

        .childTable {
            background: #F9B60030;
            border-radius: 12px;
        }

        .addRowButton {
            float: right;
            font-weight: 800;
            color: #f9b600;
            margin-right: 10px;
            cursor: pointer;
        }

        .items_table_body tr {
            position: relative;
        }

        .main_table_body .removeIcon:first-child, .items_table_body .removeIcon:first-child {
            display: none;
        }


    </style>


    <div class="layout-px-spacing">
        {{--        page title--}}
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Create Consignment</h2>
        </div>


        <form class="general_form" method="POST" action="{{url($prefix.'/consignments')}}" id="createconsignment"
              style="margin: auto; ">

            {{--bill to info--}}
            <div class="form-row">
                <h6 class="col-12">Bill To Information</h6>

                <div class="form-group col-md-4">
                    <label for="exampleFormControlSelect1">
                        Select Bill to Client<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-control-sm my-select2" id="select_regclient"
                            name="regclient_id">
                        <option selected="selected" disabled>select client..</option>
                        @foreach($regionalclient as $client)
                            <option value="{{$client->id}}">{{$client->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="exampleFormControlSelect1">
                        Payment Term<span class="text-danger">*</span>
                    </label>
                    <select class="form-control my-select2" name="payment_type">
                        <option value="To be Billed" selected="selected">To be Billed</option>
                        <option value="To Pay">To Pay</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="exampleFormControlSelect1">
                        Freight<span class="text-danger">*</span>
                    </label>
                    <Input type="number" class="form-control" name="freight">
                </div>
                <div class="form-group d-flex col-md-3">

                    <div class="d-flex align-items-center px-2 pt-2">
                        <label class="mr-4">
                            Sale Return<span class="text-danger">*</span>
                        </label>
                        <div class="checkbox radio">
                            <label class="check-label">Yes
                                <input type="radio" name="is_salereturn" value="1" class="">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="checkbox radio ml-3">
                            <label class="check-label">No
                                <input type="radio" name="is_salereturn" value="0" checked="">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" class="form-seteing date-picker" id="consignDate" name="consignment_date"
                   placeholder="" value="<?php echo date('d-m-Y'); ?>">


            {{--pickup & drop info--}}
            <div class="form-row">
                <h6 class="col-12">Pickup and Drop Information</h6>

                <div class="form-group col-md-4">
                    <label>
                        Select Pickup Location (Consigner)<span class="text-danger">*</span>
                    </label>
                    <select class="form-control my-select2" id="select_consigner" name="consigner_id">
                        <option value="">Select Consignor</option>
                        {{--                        @foreach($consigners as $consigner)--}}
                        {{--                            <option value="{{$consigner->id}}">{{$consigner->nick_name}}--}}
                        {{--                            </option>--}}
                        {{--                        @endforeach--}}
                    </select>
                    <div id="consigner_address"></div>
                </div>
                <div class="form-group col-md-4">
                    <label>
                        Select Drop location (Bill To Consignee)<span class="text-danger">*</span>
                    </label>
                    <select class="form-control my-select2"
                            name="consignee_id" id="select_consignee">
                        <option value="">Select Consignee</option>
                    </select>
                    <div id="consignee_address"></div>
                </div>
                <div class="form-group col-md-4">
                    <label>
                        Select Drop Location (Ship To Consignee)<span class="text-danger">*</span>
                    </label>
                    <select class="form-control my-select2"
                            name="ship_to_id" id="select_ship_to">
                        <option value="">Select Ship To</option>
                    </select>
                    <div id="ship_to_address"></div>
                </div>
            </div>


            {{--order info--}}
            <div class="form-row">
                <h6 class="col-12">Order Information</h6>

                <div style="width: 100%">
                    <div class="d-flex align-items-center form-group form-group-sm">
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


                <div style="overflow-x:auto; padding: 1rem 8px; margin-top: 1rem;">
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
                                                    <td width="50px"><span class="removeIcon removeItem">xx</span></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <span style="margin-right: 8%" class="addRowButton "
                                            onclick="insertItemTableRow()">+ Add Item</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="50px"><span class="removeIcon removeInvoice">xx</span></td>
                        </tr>
                        </tbody>
                    </table>
                    <span class="addRowButton" onclick="insertMaintableRow()">+ Add Row</span>
                </div>
            </div>
        </form>
        <!-- widget-content-area -->

        @endsection
        @section('js')
            <script>
                function insertMaintableRow() {
                    $("#items_table").each(function () {
                        var item_no = $("tr", this).length;
                        // alert(item_no);
                        // if (item_no <= 6) {
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
                                                    <td width="50px"><span class="removeItem">xx</span></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <span style="margin-right: 8%" class="addRowButton"
                                                  onclick="insertItemTableRow()">+ Add Item</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="50px"><span class="removeInvoice">xx</span></td>
                        </tr>`;
                        // }
                        $(this).append(tds);
                    });
                };

                $(document).on("click", ".removeInvoice", function () {
                    var current_val = $(this).parent().siblings(":first").text();
                    $(this).closest("tr").remove();
                });

                $(document).on("click", ".removeItem", function () {
                    var current_val = $(this).parent().siblings(":first").text();
                    $(this).closest("tr").remove();
                });


                $(document).on("click", ".addItem", function () {
                    // $(this).closest("tr").remove();
                    // $("#childTable").each(function ()   {
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
                                                    <td width="50px"><span class="removeItem">xx</span></td>
                                                </tr>`

                    // var current_val = $(this).parent().siblings(":first").text();

                    $(this).siblings(".childTable").children('.items_table_body').append(itemTds);
                    // $(this).parentElement.appendChild(itemTds);
                    // });
                });

                function insertItemTableRow() {
                    $("#childTable").each(function () {
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
                                                    <td width="50px"><span class="removeItem">xx</span></td>
                                                </tr>`

                        // $(this).siblings(".childTable").children('.items_table_body').append(itemTds);

                        $(".items_table_body").append(itemTds);
                    });
                };
            </script>


            <script>
                // $(function() {
                //     $('.basic').selectpicker();
                // });
                // $(document).ready(function () {
                //     $('.insert-more').attr('disabled', true);
                // });

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

                // invoice no duplicate validate
                $('.invc_no').blur(function () {
                    var invc_no = $(this).val();
                    var row_id = jQuery(this).attr('id');
                    $.ajax({
                        url: "/invoice-check",
                        method: "get",
                        data: {
                            invc_no: invc_no
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                swal('error', response.errors, 'error');
                                $("#" + row_id).val('');
                            }
                        }
                    })
                });


            </script>
            <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ6x_bU2BIZPPsjS8Y8Zs-yM2g2Bs2mnM&callback=myMap"></script>
@endsection
