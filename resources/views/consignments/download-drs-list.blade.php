@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_html5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css') }}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <style>
        --btnColor: var(--primaryColor);

        .select2-results__options {
            list-style: none;
            margin: 0;
            padding: 0;
            height: 160px;
            /* scroll-margin: 38px; */
            overflow: auto;
        }

        .move {
            cursor: move;
        }

        .table>tbody>tr>td {
            vertical-align: middle;
            color: #515365;
            padding: 3px 21px;
            font-size: 13px;
            letter-spacing: normal;
            font-weight: 600;
        }

        .btn {
            font-size: 10px;
        }

        .reAttemptBtn {
            white-space: nowrap;
            padding: 2px 12px;
            border-radius: 12px;
            background: var(--btnColor) !important;
            border-color: var(--btnColor) !important;
        }


        /* for tabs */
        .tasks {

            width: 96vw;
            max-width: 400px;
            background: #ffffff;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: stretch;
            border-radius: 50vh;
            padding: 2px;
            outline: 2px solid #f9b808;
            margin: auto;
            gap: 4px;
            position: relative;
        }



        .taskOption {
            cursor: pointer;
            background: transparent;
            flex: 1;
            background: #f9b80820;
            border-radius: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem !important;
            font-weight: 600;
            color: #000 !important;
            z-index: 1;
            transition: all 260ms ease-in-out;
        }

        .taskOption.activeTab {
            background: #f9b808;
            transition: all 260ms ease-in-out;
        }

        /* for appended div */
        #taskAppendDiv {
            padding: 1rem;
            background: #f9b8081a;
            border-radius: 18px;
        }

        .submitButton {
            min-width: auto;
            font-size: 14px !important;
            padding-inline: 16px;
        }

        .form-control {
            height: auto !important;
            border-radius: 12px
        }

        .tableContainer {
            border-radius: 12px;
            box-shadow: 0 0 12px -3px #83838380 inset;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered{
            border-radius:12px !important;
            height: auto !important;
            padding: 0.5rem 1.25rem !important;
            margin-bottom: 0 !important;
        }
        .select2-container--open .select2-dropdown--below {
            margin-left: 1.5rem;
            margin-top: 1.5rem;
        }
    </style>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="page-header">
                    <nav class="breadcrumb-one" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Download
                                    DRS
                                    List</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="widget-content widget-content-area br-6">
                    <div class="mb-4 mt-4">
                        <!-- <a class="btn btn-success ml-2 mt-3" href="{{ url($prefix . '/export-drs-table') }}">Export
                                                                                                                        data</a> -->

                        <div class="container-fluid">
                            <div class="row winery_row_n spaceing_2n mb-3">
                                <!-- <div class="col-xl-5 col-lg-3 col-md-4">
                                                                                                                                    <h4 class="win-h4">List</h4>
                                                                                                                                </div> -->
                                <div class="col d-flex pr-0">
                                    <div class="search-inp w-100">
                                        <form class="navbar-form" role="search">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Search"
                                                    id="search" data-action="<?php echo url()->current(); ?>">
                                                <!-- <div class="input-group-btn">
                                                                                                                                                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                                                                                                                                </div> -->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg lead_bladebtop1_n pl-0">
                                    <div class="winery_btn_n btn-section px-0 text-right">
                                        <!-- <a class="btn-primary btn-cstm btn ml-2"
                                                                                                                                            style="font-size: 15px; padding: 9px; width: 130px"
                                                                                                                                            href="{{ 'consignments/create' }}"><span><i class="fa fa-plus"></i> Add
                                                                                                                                                New</span></a> -->
                                        <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                                            style="font-size: 15px; padding: 9px;" data-action="<?php echo url()->current(); ?>"><span>
                                                <i class="fa fa-refresh"></i> Reset Filters</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @csrf
                        <div class="main-table table-responsive">
                            @include('consignments.download-drs-list-ajax')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('models.transaction-sheet')
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
        integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>
    <script>
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
        
        $(document).ready(function() {
            $(document).on('click', '.reAttemptBtn', function() {
                var get_lrid = $(this).attr("data-lrid");
                $("#reattempt_lrid").val(get_lrid);
            });


            $("#reason").change(function() {
                $("#otherText").val('');
                if ($(this).val() === "other") {
                    $("#otherInput").show();
                    $("#otherText").attr('required', true);
                } else {
                    $("#otherInput").hide();
                    $("#otherText").removeAttr('required');
                }
            });

            

            $('#sheet').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'print'
                ]
            });
        });


        function fetchLrDetails(drsId) {
            $.ajax({
                type: "GET",
                url: "view-transactionSheet/" + drsId,
                data: {
                    drsId: drsId
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        $('#sheet').dataTable().fnClearTable();
                        $('#sheet').dataTable().fnDestroy();
                        $("#sss").empty();
                        $("#ppp").empty();
                        $("#nnn").empty();
                        $("#drsdate").empty();
                    },
                success: function(data) {
                    console.log(data.fetch[0].consignment_detail.driver_id);
                    // var re = jQuery.parseJSON(data)
                    var re = data;
                    var drs_no = re.fetch[0]['drs_no'];
                    $('#current_drs').val(drs_no);

                    var consignmentID = [];
                    var totalBox = 0;
                    var totalweight = 0;
                    $.each(re.fetch, function(index, value) {
                        var alldata = value;
                        consignmentID.push(value.consignment_no);
                        totalBox += parseInt(value.total_quantity);
                        totalweight += parseInt(value.total_weight);

                        $('#sheet tbody')
                            .append(`<tr id="${value.id}" class='move'>
                                        <td><a href='#' data-toggle='modal' data-target='modal-2' class='btn btn-danger ewayupdate' data-id="${value.consignment_no}">Edit</a></td>
                                        <td><input type='date' name='edd[]' data-id="${value.consignment_no}" class='new_edd eddInput' value="${value.consignment_detail.edd ?? ''}" /></td>
                                        <td>${value.consignment_no}</td>
                                        <td>${value.consignment_date}</td>
                                        <td>${value.consignee_id}</td>
                                        <td>${value.city}</td>
                                        <td>${value.pincode}</td>
                                        <td>${value.consignment_detail.total_quantity}</td>
                                        <td>${value.consignment_detail.total_weight}</td>
                                        <td><button type='button' data-id="${value.consignment_no}" ${re.fetch.length == 1 ? ' disabled ' : ' '} class='btn btn-primary remover_lr'>remove</button></td>
                                    </tr>`);

                    });
                    var rowCount = $("#sheet tbody tr").length;
                    $("#total_box").html("No Of Boxes: " + totalBox);
                    $("#totalweight").html("Net Weight: " + totalweight);
                    $("#total").html(rowCount);
                    $(".draft-sheet").attr("data-drsid", drsId);

                    $("#transaction_id").val(consignmentID);
                    var rowCount = $("#save-DraftSheet tbody tr").length;
                    $("#total_boxes").append("No Of Boxes: " + totalBox);
                    $("#totalweights").append("Net Weight: " + totalweight);
                    $("#totallr").append(rowCount);

                    showLibrary();

                    // $("#vehicle_no").val(re.fetch_lrs.vehicle_id).trigger('change');
                    // $("#driver_id").val(re.fetch_lrs.driver_id).trigger('change');
                    // $("#vehicle_type").val(re.fetch_lrs.vehicle_type).trigger('change');
                    $("#Transporter").val(re.fetch_lrs.transporter_name);
                    $("#draft_purchase").val(re.fetch_lrs.purchase_price);
                    
                    // Create a new option element
                    if (re.fetchVehicle) {
                        var newVehicleOption = `<option value="${re.fetchVehicle.id}" selected>${re.fetchVehicle.regn_no}</option>`;
                        $('#vehicle_no').append(newVehicleOption);

                        if (re.fetchDriver) {
                        var newDriverOption =
                        `<option value="${re.fetchDriver.id}" selected>${re.fetchDriver.name}</option>`;
                        $('#driver_id').append(newDriverOption);
                        }
                        
                        if (re.fetchVehicleType) {
                            var newVehicleTypeOption =
                            `<option value="${re.fetchVehicleType.id}" selected>${re.fetchVehicleType.name}</option>`;
                            $('#vehicle_type').append(newVehicleTypeOption);
                        }
                    }
                    
                   
                }
            });
        }
        $(document).on('click', '.view-sheet', function() {
            var drsId = $(this).attr('value');

            $("#addlr").attr('data-drsId', drsId);

            $('#opm').modal('show');
            $('#opm').find('input, textarea, select').val('');
            
            fetchLrDetails(drsId)


            $("#mainLoader").show();
            $(".loader").show();
            var drsId = $(this).attr('value');
            $(this).addClass('activeTab');

            $('#addlr').removeClass('activeTab');
            $.ajax({
                type: "GET",
                url: "view-draftSheet/" + drsId,
                data: {
                    drsId: drsId,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        // $('#unverifiedlrlist').dataTable().fnClearTable();             
                        // $('#unverifiedlrlist').dataTable().fnDestroy();
                    },
                success: function(data) {
                    
                    var re = data;
                    console.log(data.fetch);
                    var consignmentID = [];
                    var totalBoxes = 0;
                    var totalweights = 0;
                    var i = 0;
                    $.each(re.fetch, function(index, value) {
                        $("#mainLoader").hide();
                        $(".loader").hide();
                        i++;
                        var alldata = value;
                        consignmentID.push(alldata.consignment_no);
                        totalBoxes += parseInt(value.consignment_detail.total_quantity);
                        totalweights += parseInt(value.consignment_detail.total_weight);

                        $('#save-DraftSheet tbody').append("<tr class='outer-tr' id=" + value
                            .id +
                            "><td><a href='#' data-toggle='modal' data-target='modal-2' class='btn btn-danger ewayupdate' data-id=" +
                            value.consignment_no +
                            ">Edit</a></td><td><input type='date' name='edd[]' data-id=" +
                            value
                            .consignment_no + " class='new_edd' value='" + value
                            .consignment_detail.edd + "'></td><td>" + value.consignment_no +
                            "</td><td>" + value.consignment_date + "</td><td>" + value
                            .consignee_id + "</td><td>" + value.city + "</td><td>" + value
                            .pincode + "</td><td>" + value.total_quantity + "</td><td>" +
                            value
                            .total_weight + "</td></tr>");

                            
                            
                    });
                   

                    $('#vehicle_no').select2();
                    $('#driver_id').select2();
                    $('#vehicle_type').select2();
                    // jQuery(function () {
                    //     $('.my-select2').each(function () {
                    //         $(this).select2({
                    //             theme: "bootstrap-5",
                    //             dropdownParent: $(this).parent(), // fix select2 search input focus bug
                    //         })
                    //     })
                    //     // fix select2 bootstrap modal scroll bug
                    //     $(document).on('select2:close', '.my-select2', function (e) {
                    //         var evt = "scroll.select2"
                    //         $(e.target).parents().off(evt)
                    //         $(window).off(evt)
                    //     })
                    // })
                    

                    $("#mainLoader").hide();
                    $(".loader").hide();
                }
            });
        });

        /////////////Start btn drs list///////////////////
    
        $(document).on('click', '.ewayupdate', function() {

            var consignment_id = $(this).attr('data-id');
            $('#modal-2').modal('show');
            $.ajax({
                type: "GET",
                url: "view_invoices/" + consignment_id,
                data: {
                    consignment_id: consignment_id
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        $('#view_invoices').dataTable().fnClearTable();
                        $('#view_invoices').dataTable().fnDestroy();
                    },
                success: function(data) {
                    var i = 1;
                    // console.log(data.fetch[0].consignment_id );
                    $('#cn_no').val(data.fetch[0].consignment_id)
                    $.each(data.fetch, function(index, value) {

                        if (value.e_way_bill == null || value.e_way_bill == '') {
                            var billno = "<input type='text' name='data[" + i +
                                "][e_way_bill]' >";
                        } else {
                            var billno = value.e_way_bill;
                        }

                        if (value.e_way_bill_date == null || value.e_way_bill_date == '') {
                            var billdate = "<input type='date' name='data[" + i +
                                "][e_way_bill_date]' >";
                        } else {
                            var billdate = value.e_way_bill_date;
                        }

                        $('#view_invoices tbody').append(
                            "<tr><input type='hidden' name='data[" +
                            i + "][id]' value=" + value.id + " ><td>" + value
                            .consignment_id +
                            "</td><td>" + value.invoice_no + "</td><td>" + billno +
                            "</td><td>" + billdate + "</td></tr>");
                        i++;
                    });

                }
            });


        });

        ////////////////
        $('#suffle').sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                var page_id_array = new Array();
                $('#suffle tr').each(function() {
                    page_id_array.push($(this).attr('id'));
                });
                //alert(page_id_array);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "update-suffle",
                    method: "POST",
                    data: {
                        page_id_array: page_id_array,
                        action: 'update'
                    },
                    success: function() {
                        load_data();
                    }
                })
            }
        });
        ///////////////
        function printData() {
            var divToPrint = document.getElementById("www");
            newWin = window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            newWin.close();
        }

        $('#print').on('click', function() {
            printData();

        })

        $('.discardButton').click(function() {
            $('.indicator-progress').removeAttr('disabled');
            $('.indicator-label').removeAttr('disabled');
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            $('#save-DraftSheet tbody').html('');
        })

        //save add driver data on draft modal //
        function drsSubmit(is_started) {
            // $("#mainLoader").show();
            // $(".loader").show();
            this.event.preventDefault();

            var consignmentID = [];
            // Get all elements with class 'edd'
            var elements = document.getElementsByClassName('eddInput');

            // Loop through the elements
            for (var i = 0; i < elements.length; i++) {
                // console.log(`qwe - ${i}`, elements[i].value)
                if(is_started == 1){
                    // Check if the element has a value
                    if (!elements[i].value) {
                        $("#mainLoader").hide();
                        $(".loader").hide();
                    // console.log(`qwe - ${i}`, elements[i].value);

                        swal('error', 'Please enter EDD', 'error');
                        return;
                    }
                }
                consignmentID.push(this.value);
            }

            // $('input[name="edd[]"]').each(function() {
            //     if (is_started == 1) {
            //         if (!($(this).val())) {
            //             console.log($(this).val(), '1');
            //             $("#mainLoader").hide();
            //             $(".loader").hide();
            //             swal('error', 'Please enter EDD', 'error');
            //             return 1;
            //         }
            //     }
            //     consignmentID.push(this.value);
            // });



            var ct = consignmentID.length;
            var rowCount = $("#save-DraftSheet tbody tr").length;

            var vehicle = $('#vehicle_no').val();
            var driver = $('#driver_id').val();
            if (is_started == 1) {
                if (!vehicle) {
                    $("#mainLoader").hide();
                    $(".loader").hide();

                    swal('error', 'Please select vehicle', 'error');
                    return false;
                }
                if (!driver) {
                    $("#mainLoader").hide();
                    $(".loader").hide();

                    swal('error', 'Please select driver', 'error');
                    return false;
                }
            }

            const drsForm = new FormData();
            drsForm.append('vehicle_id', $('#vehicle_no').val());
            drsForm.append('driver_id', $('#driver_id').val());
            drsForm.append('vehicle_type', $('#vehicle_type').val());
            drsForm.append('transporter_name', $('#Transporter').val());
            drsForm.append('purchase_price', $('#draft_purchase').val());
            drsForm.append('transaction_id', $('#transaction_id').val());
            drsForm.append('is_started', is_started);

            function draftsaveAjax() {
                $.ajax({
                    url: "update_unverifiedLR",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: drsForm,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.indicator-progress').prop('disabled', true);
                        $('.indicator-label').prop('disabled', true);

                        $(".indicator-progress").show();
                        $(".indicator-label").hide();
                    },
                    complete: function(response) {
                        $('.indicator-progress').prop('disabled', true);
                        $('.indicator-label').prop('disabled', true);
                        $("#mainLoader").hide();
                        $(".loader").hide();
                    },
                    success: (data) => {
                        $(".indicator-progress").hide();
                        $(".indicator-label").show();
                        if (data.success == true) {
                            swal('success', 'Data Updated Successfully', 'success');
                            location.reload();
                        } else if (data.success == false) {
                            swal('error', data.error_message, 'error');
                        } else {
                            swal('error', 'something wrong', 'error');
                        }
                        $("#mainLoader").show();
                        $(".loader").show();
                        $("#opm").hide();
                        $("#start-commonconfirm").hide();
                    }
                });
            }
            if (is_started == 1) {
                jQuery('#start-commonconfirm').modal('show');
                jQuery(".confirmStartClick").one("click", function() {
                    draftsaveAjax();
                });
            } else {
                draftsaveAjax()
            }

        }
        $('#updt_vehicle1').submit(function(e) {
            e.preventDefault();

            var consignmentID = [];
            $('input[name="edd[]"]').each(function() {
                // if (this.value == '') {
                //     swal('error', 'Please enter EDD', 'error');
                //     exit;
                // }
                consignmentID.push(this.value);
            });

            var ct = consignmentID.length;
            var rowCount = $("#save-DraftSheet tbody tr").length;

            var vehicle = $('#vehicle_no').val();
            var driver = $('#driver_id').val();

            $.ajax({
                url: "update_unverifiedLR",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('.indicator-progress').prop('disabled', true);
                    $('.indicator-label').prop('disabled', true);

                    $(".indicator-progress").show();
                    $(".indicator-label").hide();
                },
                complete: function(response) {
                    $('.indicator-progress').prop('disabled', true);
                    $('.indicator-label').prop('disabled', true);
                },
                success: (data) => {
                    $(".indicator-progress").hide();
                    $(".indicator-label").show();
                    if (data.success == true) {
                        alert('Data Updated Successfully');
                        location.reload();
                    } else if (data.success == false) {
                        alert(data.error_message);
                    } else {
                        alert('something wrong');
                    }
                }
            });
        });

        //start update start status on drs list start modal //
        $('#startupdt_vehicle').submit(function(e) {
            e.preventDefault();

            var consignmentID = [];
            $('input[name="edd[]"]').each(function() {
                if (this.value == '') {
                    swal('error', 'Please enter EDD', 'error');
                    exit;
                }
                consignmentID.push(this.value);
            });

            var ct = consignmentID.length;
            var rowCount = $("#start-DraftSheet tbody tr").length;

            var vehicle = $('#start-vehicle').val();
            var driver = $('#start-driver').val();
            if (vehicle == '') {
                swal('error', 'Please select vehicle', 'error');
                return false;
            }
            if (driver == '') {
                swal('error', 'Please select driver', 'error');
                return false;
            }
            var data = new FormData(this);
            jQuery('#start-commonconfirm').modal('show');
            jQuery(".confirmStartClick").one("click", function() {
                // if (confirm("Are you sure you want to submit the form?")) {
                $.ajax({
                    url: "start-unverifiedLR",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.indicator-progress').prop('disabled', true);
                        $('.indicator-label').prop('disabled', true);

                        $(".indicator-progress").show();
                        $(".indicator-label").hide();
                    },
                    complete: function(response) {
                        $('.indicator-progress').prop('disabled', true);
                        $('.indicator-label').prop('disabled', true);
                    },
                    success: (data) => {
                        $(".indicator-progress").hide();
                        $(".indicator-label").show();
                        if (data.success == true) {
                            alert('Data Updated Successfully');
                            location.reload();
                        } else if (data.success == false) {
                            alert(data.error_message);
                        } else {
                            alert('something wrong');
                        }
                    }
                });
            });
            return false;
        });

        ///update edd on add driver draft modal ///////
        function showLibrary() {
            // $('.new_edd').blur(function() {
            jQuery(document).on("change", ".new_edd", function() {
                var consignment_id = $(this).attr('data-id');
                var drs_edd = $(this).val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "update-edd",
                    method: "POST",
                    data: {
                        drs_edd: drs_edd,
                        consignment_id: consignment_id,
                        _token: _token
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(result) {

                    }
                })
            });
        }

        // delivery status //
        $(document).on('click', '.delivery_status', function() {

            var draft_id = $(this).val();
            $('#delivery').modal('show');

            $.ajax({
                type: "GET",
                url: "update-delivery/" + draft_id,
                data: {
                    draft_id: draft_id
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        $('#delivery_status').dataTable().fnClearTable();
                        $('#delivery_status').dataTable().fnDestroy();
                    },
                success: function(data) {
                    var re = jQuery.parseJSON(data)
                    var consignmentID = [];
                    $.each(re.fetch, function(index, value) {
                        var alldata = value;
                        consignmentID.push(alldata.consignment_no);

                        $('#delivery_status tbody').append("<tr><td>" + value.consignment_no +
                            "</td><td><input type='date' name='delivery_date[]' data-id=" +
                            value.consignment_no + " class='delivery_d' value='" + value
                            .dd +
                            "'></td><td><button type='button'  data-id=" + value
                            .consignment_no +
                            " class='btn btn-primary remover_lr'>remove</button></td></tr>");

                    });
                    $("#drs_status").val(consignmentID);
                    get_delivery_date();
                }
            });
        });

        // Update Delivery Status //
        $('#update_delivery_status').submit(function(e) {
            e.preventDefault();
            var consignmentID = [];
            $('input[name="delivery_date[]"]').each(function() {
                if (this.value == '') {
                    alert('Please enter Delivery Date');
                    exit;
                }
                consignmentID.push(this.value);
            });
            $.ajax({
                url: "update-delivery-status",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function() {

                },
                success: (data) => {
                    if (data.success == true) {

                        alert('Data Updated Successfully');
                        location.reload();
                    } else {
                        alert('something wrong');
                    }
                }
            });
        });

        ///////////
        function get_delivery_date() {
            $('.delivery_d').blur(function() {
                var consignment_id = $(this).attr('data-id');
                var delivery_date = $(this).val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "update-delivery-date",
                    method: "POST",
                    data: {
                        delivery_date: delivery_date,
                        consignment_id: consignment_id,
                        _token: _token
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(result) {

                    }
                })
            });
        }

        // Remove Lr From DRS //
        $(document).on('click', '.remover_lr', function() {
            var consignment_id = $(this).attr('data-id');
            $.ajax({
                type: "GET",
                url: "remove-lr",
                data: {
                    consignment_id: consignment_id
                },
                beforeSend: //reinitialize Datatables
                    function() {

                    },
                success: function(data) {
                    var re = jQuery.parseJSON(data)
                    if (re.success == true) {
                        $('#opm').modal('hide');
                        swal('success', 'LR Removed Successfully', 'success');
                        location.reload();
                    } else {
                        swal('error', 'something wrong', 'error');
                    }
                }
            });
        });

        /////////////
        $(document).on('click', '#addlr', function() {
            const drsId = $(this).attr('data-drsId');
            $("#mainLoader").show();
            $(".loader").show();
            $(this).addClass('activeTab');
            $('#addDriver').removeClass('activeTab');
            $.ajax({
                type: "post",
                url: "get-add-lr",
                data: {
                    add_drs: 'add_drs'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        // $('#unverifiedlrlist').dataTable().fnClearTable();             
                        // $('#unverifiedlrlist').dataTable().fnDestroy();
                    },
                success: function(data) {
                    let addLrtable = `
                                    <input type="hidden" class="form-control" id="current_drs" name=""
                                        value="">
                                    <button type="button" disabled class="btn btn-warning disableDrs mt-3"
                                        id="add_unverified_lr" data-drsid="${drsId}" onclick="addUnverifiedLr(${drsId})" style="font-size: 11px; margin: 0px 0px 10px 15px;">
                                        Create DRS
                                    </button>
                                    <div class="col-sm-12">
                                        <table id="unverifiedlrlist" class="table table-hover"
                                            style="width:100%; text-align:left;">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" name="" id="ckbCheckAll" style="width: 18px; height:18px;" /></th>
                                                    <th>LR No</th>
                                                    <th>Consignment Date</th>
                                                    <th>Consignor Name</th>
                                                    <th>Consignee</th>
                                                    <th>City</th>
                                                    <th>District</th>
                                                    <th>Pin Code</th>
                                                    <th>Zone</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.lrlist.map((value) => `
                                                                                    <tr>
                                                                                        <td><input type='checkbox' name='checked_consign[]' class='chkBoxClass ddd' value="${value.id}" style='width: 18px; height: 18px;'></td>
                                                                                        <td>${value.id}</td>
                                                                                        <td>${value.consignment_date}</td>
                                                                                        <td>${value.consigner_id}</td>
                                                                                        <td>${value.consignee_id}</td>
                                                                                        <td>${value.consignee_city}</td>
                                                                                        <td>${value.consignee_district}</td>
                                                                                        <td>${value.pincode}</td>
                                                                                        <td>${value.zone}</td>
                                                                                    </tr>
                                                                                `).join('')}
                                            </tbody>

                                        </table>
                                    </div>`;
                    document.getElementById('addLrDiv').innerHTML = addLrtable;
                    $("#mainLoader").hide();
                    $(".loader").hide();
                }

            });
        });

        $(document).on('click', '#discarddraftForm', function() {
            $('#addLrDiv').html('');
            $('#addLrDiv').html('');
            $('#opm').modal('hide');

        });

        // $('#add_unverified_lr').click(function() {

        // });
        function addUnverifiedLr(drs_no) {
            // var drs_no = $(this).attr("data-drsid");
            var consignmentID = [];
            $(':checkbox[name="checked_consign[]"]:checked').each(function() {
                consignmentID.push(this.value);
            });
            $.ajax({
                url: "add-unverified-lr",
                method: "POST",
                data: {
                    consignmentID: consignmentID,
                    drs_no: drs_no
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                beforeSend: function() {
                    $('.disableDrs').prop('disabled', true);

                },
                complete: function(response) {
                    $('.disableDrs').prop('disabled', true);
                },
                success: function(data) {
                    if (data.success == true) {
                        swal('success', 'Drs Created Successfully', 'success');
                        fetchLrDetails(drs_no);
                        $('#addLrDiv').html('');

                        // window.location.href = "transaction-sheet";

                    } else {
                        swal('error', 'something wrong', 'error');
                    }
                }
            })
        }

        jQuery(document).on('click', '#ckbCheckAll', function() {
            if (this.checked) {
                $('.disableDrs').removeAttr('disabled');
                jQuery('.chkBoxClass').each(function() {
                    this.checked = true;
                });
            } else {
                jQuery('.chkBoxClass').each(function() {
                    this.checked = false;
                });
                $('.disableDrs').attr('disabled', true);
            }
        });

        jQuery(document).on('click', '.chkBoxClass', function() {
            if ($('.chkBoxClass:checked').length == $('.chkBoxClass').length) {
                $('#ckbCheckAll').prop('checked', true);
                $('.disableDrs').removeAttr('disabled');
            } else {
                var checklength = $('.chkBoxClass:checked').length;
                if (checklength < 1) {
                    $('.disableDrs').attr('disabled', true);
                } else {
                    $('.disableDrs').removeAttr('disabled');
                }

                $('#ckbCheckAll').prop('checked', false);
            }
        });

        // Remove Lr From The Draft //
        function catagoriesCheck(that) {
            if (that.value == "Successful") {
                document.getElementById("opi").style.display = "block";
            } else {
                document.getElementById("opi").style.display = "none";
            }
        }
    </script>
@endsection
