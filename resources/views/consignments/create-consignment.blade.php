@extends('layouts.main')
@section('content')
<style>
        @media only screen and (max-width: 600px) {
            .checkbox-round {
                margin-left: 1px;
            }

        }

        h4 {
            font-size: 18px;

        }

        .form-control {
            height: 33px;
            padding: 0px;

        }


        .checkbox-round {
            width: 2.3em;
            height: 2.3em;
            /* background-color: white; */
            border-radius: 55%;
            /* vertical-align: middle;  */
            border: 1px solid #ddd;
            /* appearance: none; */
            /* -webkit-appearance: none; */
            /* outline: none;  */
            /* cursor: pointer; */
            margin-left: 103px;
        }

        p {
            font-size: 11px;
            font-weight: 900;

        }


        th,
        td {
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
        .select2-results__options {
        list-style: none;
        margin: 0;
        padding: 0;
        height: 160px;
        /* scroll-margin: 38px; */
        overflow: auto;
    }
    </style>
<div class="layout-px-spacing">
                <div class="row">
                    <div class="col-lg-12 layout-spacing">

                        <div class="widget-header">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <h4 style="margin-left: 19px;"><b>Bill To Information</b></h4>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content widget-content-area">
                            <div class="row">
                                <div class=" col-sm-4 ">
                                    <p>Select Bill to Client</p>
                                    <select class="form-control form-small my-select2">
                                        <option selected="selected">orange</option>
                                        <option>white</option>
                                        <option>purple</option>
                                    </select>
                                </div>
                                <div class=" col-sm-2 ">
                                    <p>Payment Term</p>
                                    <select class="form-control form-small my-select2" style="width: 160px;">
                                        <option selected="selected">orange</option>
                                        <option>white</option>
                                        <option>purple</option>
                                    </select>
                                </div>
                                <div class=" col-sm-2 ">
                                    <p>Freight</p>
                                    <Input type="number" class="form-control form-small" style="width: 160px;">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-12 layout-spacing">

                        <div class="widget-header">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <h4 style="margin-left: 19px;"><b>Pickup and Drop Information</b></h4>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content widget-content-area">
                            <div class="row">
                                <div class="col-sm-4 ">
                                    <p>Select Pickup Location (Consigner)</p>
                                    <select class="form-control form-small my-select2" style="width: 328px;" id="select_consigner"  type="text"
                                                name="consigner_id">
                                    <option value="">Select Consignor</option>
                                                @foreach($consigners as $consigner)
                                                <option value="{{$consigner->id}}">{{$consigner->nick_name}}
                                                </option>
                                                @endforeach
                                    </select>
                                    <div id="consigner_address">
                                            </div>

                                </div>
                               
                                <div class="col-sm-4 ">
                                    <p>Select Drop location (Bill To Consignee)</p>
                                    <select class="form-control form-small my-select2" style="width: 328px;"  type="text" name="consignee_id"
                                                id="select_consignee">
                                                <option value="">Select Consignee</option>
                                    </select>
                                    <div id="consignee_address">

                                            </div>
                                </div>
                                <!-- <div class="col-sm-3 ">
										<p style="margin-left: 60px;">Different Ship To Location </p>
										<input type="checkbox" class="checkbox-round" />

										</div> -->
                                <div class="col-sm-4 ">
                                    <p>Select Drop Location (Ship To Consignee)</p>
                                    <select class="form-control form-small my-select2" style="width: 328px;"  type="text" name="ship_to_id"  id="select_ship_to">
                                    <option value="">Select Ship To</option>
                                    </select>
                                    <div id="ship_to_address">

                                            </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-12 layout-spacing">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <h4 style="margin-left:19px;"><b>Order Information</b></h4>
                                </div>
                            </div>
                        </div>
                        <table border="1" width="100%">
                            <div class="row">
                                <tr>
                                    <th>Number of invoice</th>
                                    <th>Item Description</th>
                                    <th>Mode of packing</th>
                                    <th>Total Quantity</th>
                                    <th>Total Net Weight</th>
                                    <th>Total Gross Weight</th>
                                </tr>
                                <tr>
                                    <td><input type="nmber" class="form-control form-small" id="no_of_inv"></td>
                                    <td><input type="nmber" class="form-control form-small"></td>
                                    <td><input type="nmber" class="form-control form-small"></td>
                                    <td><input type="nmber" class="form-control form-small"></td>
                                    <td><input type="nmber" class="form-control form-small"></td>
                                    <td><input type="nmber" class="form-control form-small"></td>
                                </tr>


                            </div>
                        </table>


                    </div>
                    <div class="col-lg-12 layout-spacing">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <div style="overflow-x:auto;">
                                        <table>
                                            <tr>
                                                <th style="width: 160px">Order ID</th>
                                                <th style="width: 180px">Invoice Number</th>
                                                <th style="width: 160px">Invoice Date</th>
                                                <th style="width: 180px">Invoice Amount</th>
                                                <th style="width: 210px">E-way Bill Number</th>
                                                <th style="width: 200px">E-Way Bill Date</th>
                                                <th style="width: 160px">Quantity</th>
                                                <th style="width: 160px">Net Weight</th>
                                                <th style="width: 160px">Gross Weight</th>

                                            </tr>
                                        </table>
                                        <table style=" border-collapse: collapse;" border='1' id="items_table" >
                                        <tbody>
                                            <tr>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>
                                                <td><input type="nmber" class="form-control form-small"></td>

                                            </tr>
                                       
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-12 layout-spacing">

                        <div class="widget-header">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <h4 style="margin-left:19px;"><b>vehicle Information </b></h4>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content widget-content-area">
                            <div class="row">
                                <div class=" col-sm-4 ">
                                    <p>vehicle Number</p>
                                    <select class="form-control form-small my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                                    <option value="">Select vehicle no</option>
                                                        @foreach($vehicles as $vehicle)
                                                        <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                                                        </option>
                                                        @endforeach
                                    </select>
                                </div>
                                <div class=" col-sm-3 ">
                                    <p>Driver Name</p>
                                    <select class="form-control form-small my-select2" id="driver_id" name="driver_id" tabindex="-1">
                                    <option value="">Select driver</option>
                                                    @foreach($drivers as $driver)
                                                    <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                                        '-'}}
                                                    </option>
                                                    @endforeach
                                    </select>
                                </div>
                                <div class=" col-sm-3 ">
                                    <p>EDD</p>
                                    <Input type="date" class="form-control form-small" style="width: 160px;">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-12 layout-spacing">

                        <div class="widget-header">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <input type="checkbox" id="chek" style="margin-left:19px;">
                                    <label for="vehicle1">Vehical Purchase Information</label><br>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content widget-content-area " style="display:none;" id="veh">
                            <div class="row">
                                <div class=" col-sm-4 ">
                                    <p>Vendor Name</p>
                                    <Input type="text" class="form-control form-small">
                                </div>
                                <div class=" col-sm-3 ">
                                    <p>Vehicle Type</p>
                                    <Input type="text" class="form-control form-small">
                                </div>
                                <div class=" col-sm-2 ">
                                    <p>Purchase Price</p>
                                    <Input type="number" class="form-control form-small" style="width: 160px;">
                                </div>
                                <div class=" col-sm-2 ">
                                    <p>Freight</p>
                                    <Input type="number" class="form-control form-small" style="width: 160px;">
                                </div>
                            </div>
                          
                        </div>
                        <div class=" col-sm-3">
                            <button type="submit" class="mt-2 btn btn-primary disableme">Submit</button>

                            <a class="mt-2 btn btn-primary" href="{{url($prefix.'/consignments') }}"> Back</a>
                        </div>
                    </div>
                </div>

@endsection
@section('js')
<script>
    // $(function() {
    //     $('.basic').selectpicker();
    // });
    $(document).ready(function() {
        $('.insert-more').attr('disabled',true);
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

////////////////////////////////////
        $('#no_of_inv').keyup(function () {
            var count = $(this).val();
            for (var i = 1; i < count; i++) {
                var tds = '<tr>';
            tds += ' <td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small" value=""></td><td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small"></td><td><input type="nmber" class="form-control form-small"></td>';
             tds += '</tr>';
             $('#items_table tbody').append(tds);

            }
        
        });
</script>
@endsection