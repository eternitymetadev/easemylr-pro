@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <style>
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

        .table > tbody > tr > td {
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

        .clearIcon {
            visibility: hidden;
            color: darkred;
            border-radius: 50vh;
            height: 20px;
            width: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            transition: all 150ms ease-in-out;
            cursor: pointer;
            position: absolute;
            right: 1rem;
            font-size: 1rem;

        }

        .inputDiv:hover .clearIcon {
            visibility: visible;
        }

        .clearIcon:hover {
            font-size: 1.2rem;
        }

        input::file-selector-button {
            display: none;
        }

        label svg {
            height: 20px;
            width: 20px;
            margin-right: 8px;
            cursor: pointer;
        }

        .imageLink {
            color: #f9b808 !important;
            padding: 3px 8px;
            border-radius: 2px;
            background-color: rgba(245, 245, 220, 0.02);
            transition: all 200ms ease-in-out;
        }

        .imageLink:hover {
            border-radius: 20px;
            background-color: beige;
        }

        .disabled {
            cursor: not-allowed;
        }

        .textWrap {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #suffle tr:hover td {
            background-color: #f3f3f3;
        }

        #updt_vehicle span.select2.select2-container.mb-4 {
            margin-bottom: 0 !important;
        }

    </style>
    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Download DRS List</h2>
        </div>

        <div class="widget-content widget-content-area br-6" style="min-height: min(80vh, 700px)">
            <div class="mb-4 mt-4">
                <div class="col-12 d-flex justify-content-end align-items-center mb-2" style="gap: 8px;">
                    <div class="inputDiv d-flex justify-content-center align-items-center"
                         style="flex: 1;max-width: 300px; border-radius: 12px; position: relative">
                        <input type="text" class="form-control" placeholder="Search" id="search"
                               style="width: 100%; height: 38px; border-radius: 12px;"
                               data-action="<?php echo url()->current(); ?>">
                        <span class="reset_filter clearIcon" data-action="<?php echo url()->current(); ?>">x</span>
                    </div>
                </div>

                @csrf
                <div class="main-table table-responsive">
                    @include('consignments.download-drs-list-ajax')
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
        $(document).ready(function () {

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

            $('#sheet').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'print'
                ]
            });
        });
        $(document).on('click', '.view-sheet', function () {
            var cat_id = $(this).val();
            $('#opm').modal('show');
            $.ajax({
                type: "GET",
                url: "view-transactionSheet/" + cat_id,
                data: {
                    cat_id: cat_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#sheet').dataTable().fnClearTable();
                        $('#sheet').dataTable().fnDestroy();
                        $("#sss").empty();
                        $("#ppp").empty();
                        $("#nnn").empty();
                        $("#drsdate").empty();
                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    var drs_no = re.fetch[0]['drs_no'];
                    $('#current_drs').val(drs_no);

                    var totalBox = 0;
                    var totalweight = 0;
                    $.each(re.fetch, function (index, value) {
                        var alldata = value;
                        totalBox += parseInt(value.total_quantity);
                        totalweight += parseInt(value.total_weight);

                        $('#sheet tbody').append("<tr id=" + value.id + " class='move'><td>" + value
                                .consignment_no + "</td><td>" + value.consignment_date +
                            "</td><td>" + value.consignee_id + "</td><td>" + value.city +
                            "</td><td>" + value.pincode + "</td><td style='text-align: right'>" + value.total_quantity +
                            "</td><td style='text-align: right'>" + value.total_weight +
                            "</td><td style='text-align: center'><button type='button'  data-id=" + value.consignment_no +
                            " style='border: none; margin-inline: auto;' class='delete deleteIcon remover_lr'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg></button></td></tr>"
                            // " class='delete deleteIcon remover_lr'>remove</button></td></tr>");
                        );
                    });
                    var rowCount = $("#sheet tbody tr").length;
                    $("#total_box").html("Total Boxes: " + totalBox);
                    $("#totalweight").html("Net Weight: " + totalweight);
                    $("#total").html("Total LR's: " + rowCount);
                }
            });
        });

        /////////////Draft Sheet///////////////////
        $(document).on('click', '.draft-sheet', function () {
            $('.inner-tr').hide();
            var draft_id = $(this).val();
            $('#save-draft').modal('show');
            $.ajax({
                type: "GET",
                url: "view-draftSheet/" + draft_id,
                data: {
                    draft_id: draft_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#save-DraftSheet').dataTable().fnClearTable();
                        $('#save-DraftSheet').dataTable().fnDestroy();
                        $("#total_boxes").empty();
                        $("#totalweights").empty();
                        $("#totallr").empty();
                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    console.log(re);
                    var consignmentID = [];
                    var totalBoxes = 0;
                    var totalweights = 0;
                    var i = 0;
                    $.each(re.fetch, function (index, value) {
                        i++;
                        var alldata = value;
                        consignmentID.push(alldata.consignment_no);
                        totalBoxes += parseInt(value.consignment_detail.total_quantity);
                        totalweights += parseInt(value.consignment_detail.total_weight);

                        $('#save-DraftSheet tbody').append(
                            "<tr class='outer-tr' id=" + value.id + ">" +
                            "<td style='line-height: 1rem'><span style='font-weight: 800'>LR: " + value.consignment_no + "</span><br/><span style='font-size: 11px'>Dated: " + value.consignment_date + "</span></td>" +
                            "<td><p class='textWrap' style='max-width: 300px;'>" + value.consignee_id + "</p></td>" +
                            "<td>" + value.city + " - " + value.pincode + "</td>" +
                            "<td>" + value.total_quantity + "</td>" +
                            "<td>" + value.total_weight + "</td>" +
                            "<td><input style='width: 130px; height: 26px; padding-inline: 5px' type='date' name='edd[]' data-id=" + value.consignment_no + " class='form-control form-control-sm new_edd' value='" + value.consignment_detail.edd + "'></td>" +
                            "<td><a href='#' data-toggle='modal' class='btn btn-danger ewayupdate' data-dismiss='modal' data-id=" + value.consignment_no + ">Edit</a></td>" +
                            "</tr>"
                        );
                    });
                    $("#transaction_id").val(consignmentID);
                    var rowCount = $("#save-DraftSheet tbody tr").length;
                    $("#total_boxes").append("Boxes: " + totalBoxes);
                    $("#totalweights").append("Net Weight: " + totalweights + "Kg");
                    $("#totallr").append("LR's: " + rowCount);

                    showLibrary();
                }
            });
        });

        $(document).on('click', '.ewayupdate', function () {

            var consignment_id = $(this).attr('data-id');
            $('#modal-2').modal('show');
            $.ajax({
                type: "GET",
                url: "view_invoices/" + consignment_id,
                data: {
                    consignment_id: consignment_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#view_invoices').dataTable().fnClearTable();
                        $('#view_invoices').dataTable().fnDestroy();
                    },
                success: function (data) {

                    var i = 1;
                    // console.log(data.fetch[0].consignment_id );
                    $('#cn_no').val(data.fetch[0].consignment_id)
                    $.each(data.fetch, function (index, value) {

                        if (value.e_way_bill == null || value.e_way_bill == '') {
                            var billno = "<input type='text' name='data[" + i + "][e_way_bill]' >";
                        } else {
                            var billno = value.e_way_bill;
                        }

                        if (value.e_way_bill_date == null || value.e_way_bill_date == '') {
                            var billdate = "<input style='width: 130px; height: 26px; padding-inline: 5px' type='date' name='data[" + i +
                                "][e_way_bill_date]' >";
                        } else {
                            var billdate = value.e_way_bill_date;
                        }

                        $('#view_invoices tbody').append("<tr><input style='width: 150px; height: 26px; padding-inline: 5px' type='hidden' name='data[" +
                            i + "][id]' value=" + value.id + " ><td>" + value.consignment_id +
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
            update: function (event, ui) {
                var page_id_array = new Array();
                $('#suffle tr').each(function () {
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
                    success: function () {
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

        $('#print').on('click', function () {
            printData();

        })
 
////////////////////////////
$('#updt_vehicle').submit(function(e) {
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
    var rowCount = $("#save-DraftSheet tbody tr").length;

    var vehicle = $('#vehicle_no').val();
    var driver = $('#driver_id').val();
    if (vehicle == '') {
        swal('error', 'Please select vehicle', 'error');
        return false;
    }
    if (driver == '') {
        swal('error', 'Please select driver', 'error');
        return false;
    }

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
            } else if(data.success == false){
                alert(data.error_message);
                } else {
                alert('something wrong');
                    }
                }
            });
        });

        //////////
        function showLibrary() {
            $('.new_edd').blur(function () {
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
                    success: function (result) {

                    }
                })
            });
        }

        // delivery status //
        $(document).on('click', '.delivery_status', function () {

            var draft_id = $(this).val();
            $('#delivery').modal('show');

            $.ajax({
                type: "GET",
                url: "update-delivery/" + draft_id,
                data: {
                    draft_id: draft_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#delivery_status').dataTable().fnClearTable();
                        $('#delivery_status').dataTable().fnDestroy();
                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    var consignmentID = [];
                    $.each(re.fetch, function (index, value) {
                        var alldata = value;
                        consignmentID.push(alldata.consignment_no);

                        $('#delivery_status tbody').append("<tr><td>" + value.consignment_no +
                            "</td><td><input type='date' name='delivery_date[]' data-id=" +
                            value.consignment_no + " class='delivery_d' value='" + value.dd +
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
        $('#update_delivery_status').submit(function (e) {
            e.preventDefault();
            var consignmentID = [];
            $('input[name="delivery_date[]"]').each(function () {
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
                beforeSend: function () {

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
            $('.delivery_d').blur(function () {
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
                    success: function (result) {

                    }
                })
            });
        }

        // Remove Lr From DRS //
        $(document).on('click', '.remover_lr', function () {
            var consignment_id = $(this).attr('data-id');
            $.ajax({
                type: "GET",
                url: "remove-lr",
                data: {
                    consignment_id: consignment_id
                },
                beforeSend: //reinitialize Datatables
                    function () {

                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    if (re.success == true) {
                        swal('success', 'LR Removed Successfully', 'success');
                        location.reload();
                    } else {
                        swal('error', 'something wrong', 'error');
                    }
                }
            });
        });


        $('#opm').on('hidden.bs.modal', function (e) {
            $('#addlr').show();
            $('#unverifiedlist').hide();
            $('#unverifiedlrlist').dataTable().fnDestroy();
        })

        /////////////
        $(document).on('click', '#addlr', function () {
            $(this).hide();
            // $(this).css("display", "none");
            $('#unverifiedlist').show();
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
                    function () {
                        $('#unverifiedlrlist').dataTable().fnClearTable();
                        $('#unverifiedlrlist').dataTable().fnDestroy();
                    },
                success: function (data) {
                    $.each(data.lrlist, function (index, value) {
                        $('#unverifiedlrlist tbody').append(
                            "<tr><td><input type='checkbox' name='checked_consign[]' class='chkBoxClass ddd' value=" +
                            value.id + " style='width: 16px; height:16px;'></td><td>" + value
                                .id + "</td><td>" + value.consignment_date + "</td><td>" + value
                                .consigner_id + "</td><td>" + value.consignee_id + "</td>" +
                            "<td>" + value.consignee_city + " - " + value.pincode + "<br/>" +
                            "Dist: " + value.consignee_district + " - " + value.zone + "</td>");
                    });
                }
            });
        });

        //////
        $('#add_unverified_lr').click(function () {
            var drs_no = $('#current_drs').val();
            var consignmentID = [];
            $(':checkbox[name="checked_consign[]"]:checked').each(function () {
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
                beforeSend: function () {
                    $('.disableDrs').prop('disabled', true);

                },
                complete: function (response) {
                    $('.disableDrs').prop('disabled', true);
                },
                success: function (data) {
                    if (data.success == true) {
                        swal('success', 'Drs Created Successfully', 'success');
                        window.location.href = "transaction-sheet";
                    } else {
                        swal('error', 'something wrong', 'error');
                    }
                }
            })
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
