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
.move{
    cursor : move;
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
</style>
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Download DRS
                                List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                <!-- <a class="btn btn-success ml-2 mt-3" href="{{ url($prefix.'/export-drs-table') }}">Export
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
                                            <input type="text" class="form-control" placeholder="Search" id="search"
                                                data-action="<?php echo url()->current(); ?>">
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
                                        href="{{'consignments/create'}}"><span><i class="fa fa-plus"></i> Add
                                            New</span></a> -->
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2" style="font-size: 15px; padding: 9px;" data-action="<?php echo url()->current(); ?>"><span>
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
$(document).ready(function() {

    jQuery(function() {
        $('.my-select2').each(function() {
            $(this).select2({
                theme: "bootstrap-5",
                dropdownParent: $(this).parent(), // fix select2 search input focus bug
            })
        })

        // fix select2 bootstrap modal scroll bug
        $(document).on('select2:close', '.my-select2', function(e) {
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
$(document).on('click', '.view-sheet', function() {
    var cat_id = $(this).val();
    $('#opm').modal('show');
    $.ajax({
        type: "GET",
        url: "view-transactionSheet/" + cat_id,
        data: {
            cat_id: cat_id
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
            var re = jQuery.parseJSON(data)
            var drs_no = re.fetch[0]['drs_no'];
            $('#current_drs').val(drs_no);

            var totalBox = 0;
            var totalweight = 0;
            $.each(re.fetch, function(index, value) {
                var alldata = value;
                totalBox += parseInt(value.total_quantity);
                totalweight += parseInt(value.total_weight);

                $('#sheet tbody').append("<tr id=" + value.id + " class='move'><td>" + value
                    .consignment_no + "</td><td>" + value.consignment_date +
                    "</td><td>" + value.consignee_id + "</td><td>" + value.city +
                    "</td><td>" + value.pincode + "</td><td>" + value.total_quantity +
                    "</td><td>" + value.total_weight +
                    "</td><td><button type='button'  data-id=" + value.consignment_no +
                    " class='btn btn-primary remover_lr'>remove</button></td></tr>");

            });
            var rowCount = $("#sheet tbody tr").length;
            $("#total_box").html("No Of Boxes: " + totalBox);
            $("#totalweight").html("Net Weight: " + totalweight);
            $("#total").html(rowCount);
        }
    });
});

/////////////Draft Sheet///////////////////
$(document).on('click', '.draft-sheet', function() {
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
            function() {
                $('#save-DraftSheet').dataTable().fnClearTable();
                $('#save-DraftSheet').dataTable().fnDestroy();
                $("#total_boxes").empty();
                $("#totalweights").empty();
                $("#totallr").empty();
            },
        success: function(data) {
            var re = jQuery.parseJSON(data)
            console.log(re);
            var consignmentID = [];
            var totalBoxes = 0;
            var totalweights = 0;
            var i = 0;
            $.each(re.fetch, function(index, value) {
                i++;
                var alldata = value;
                consignmentID.push(alldata.consignment_no);
                totalBoxes += parseInt(value.consignment_detail.total_quantity);
                totalweights += parseInt(value.consignment_detail.total_weight);

                $('#save-DraftSheet tbody').append("<tr class='outer-tr' id=" + value.id +
                    "><td><a href='#' data-toggle='modal' class='btn btn-danger ewayupdate' data-dismiss='modal' data-id=" +
                    value.consignment_no +
                    ">Edit</a></td><td><input type='date' name='edd[]' data-id=" + value
                    .consignment_no + " class='new_edd' value='" + value
                    .consignment_detail.edd + "'></td><td>" + value.consignment_no +
                    "</td><td>" + value.consignment_date + "</td><td>" + value
                    .consignee_id + "</td><td>" + value.city + "</td><td>" + value
                    .pincode + "</td><td>" + value.total_quantity + "</td><td>" + value
                    .total_weight + "</td></tr>");
            });
            $("#transaction_id").val(consignmentID);
            var rowCount = $("#save-DraftSheet tbody tr").length;
            $("#total_boxes").append("No Of Boxes: " + totalBoxes);
            $("#totalweights").append("Net Weight: " + totalweights);
            $("#totallr").append(rowCount);

            showLibrary();
        }
    });
});

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
                    var billno = "<input type='text' name='data[" + i + "][e_way_bill]' >";
                } else {
                    var billno = value.e_way_bill;
                }

                if (value.e_way_bill_date == null || value.e_way_bill_date == '') {
                    var billdate = "<input type='date' name='data[" + i +
                        "][e_way_bill_date]' >";
                } else {
                    var billdate = value.e_way_bill_date;
                }

                $('#view_invoices tbody').append("<tr><input type='hidden' name='data[" +
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
            } else {
                alert('something wrong');
            }
        }
    });
});
//////////
function showLibrary() {
    $('.new_edd').blur(function() {
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
            function() {
                // $('#unverifiedlrlist').dataTable().fnClearTable();             
                // $('#unverifiedlrlist').dataTable().fnDestroy();
            },
        success: function(data) {
            $.each(data.lrlist, function(index, value) {
                $('#unverifiedlrlist tbody').append(
                    "<tr><td><input type='checkbox' name='checked_consign[]' class='chkBoxClass ddd' value=" +
                    value.id + " style='width: 30px; height:30px;'></td><td>" + value
                    .id + "</td><td>" + value.consignment_date + "</td><td>" + value
                    .consigner_id + "</td><td>" + value.consignee_id + "</td><td>" +
                    value.consignee_city + "</td><td>" +
                    value.consignee_district + "</td><td>" + value.pincode +
                    "</td><td>" + value.zone + "</td></tr>");
            });
        }
    });
});

//////
$('#add_unverified_lr').click(function() {
    var drs_no = $('#current_drs').val();
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