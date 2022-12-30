@extends('layouts.main')
@section('content')
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
        td p {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }
        p.consigner {
            border-radius: 6px;
            padding: 2px 6px;
            background: #f5f5f5;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        td p.consigner span {
            overflow: hidden;
            text-overflow: ellipsis;
        }
        td p.consigner span.legalName {
            font-weight: 700;
            font-size: 14px;
        }
        .textOverflow {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            white-space: nowrap;
        }
        .detailsBlock {
            padding: 1rem;
            gap: 0.5rem;
        }
        .detailsBlock p {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 14px;
            line-height: 17px;
            font-weight: 600;
            margin-bottom: 0;
        }
        .detailsBlock p .detailKey {
            font-size: 14px;
            line-height: 17px;
            font-weight: 400;
        }
        .contactDetails {
            min-width: 250px;
            flex: 1;
            border-radius: 12px;
            border: 1px solid;
        }
        .contactDetails p .detailKey {
            min-width: 50px;
        }
        .addressBlock {
            border-radius: 12px;
            background: #f9b80820;
        }
    </style>
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">HRS SHEET</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                <div class="d-flex align-content-center" style="gap: 1rem;">
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
                        @include('hub-transportation.hrs-sheet-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('models.hrs-models')
@endsection
@section('js')
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
jQuery(document).on('click', '#filter_reportall', function() {
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var search = jQuery('#search').val();
    
    jQuery.ajax({
        type: 'get',
        url: 'consignment-report2',
        data: {
            startdate: startdate,
            enddate: enddate,
            search: search
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.html) {
                jQuery('.main-table').html(response.html);
            }
        }
    });
    return false;
});
jQuery(document).on('change', '.report_perpage', function() {
    var startdate = jQuery('#startdate').val();
    var enddate = jQuery('#enddate').val();
    if (startdate == enddate) {
        startdate = "";
        enddate = "";
    }
    var url = jQuery(this).attr('data-action');
    var peritem = jQuery(this).val();
    var search  = jQuery('#search').val();
        jQuery.ajax({
            type      : 'get', 
            url       : url,
            data      : {peritem:peritem,search:search,startdate:startdate,enddate:enddate},
            headers   : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.html) {
                if (response.page == 'lead_note') {
                    jQuery('#Note .main-table').html(response.html);
                } else {
                    jQuery('.main-table').html(response.html);
                }
            }
        }
    });
    return false;
});
jQuery(document).on('click', '.consignmentReportEx', function(event) {
    event.preventDefault();
    var totalcount = jQuery('.totalcount').text();
    if (totalcount > 30000) {
        jQuery('.limitmessage').show();
        setTimeout(function() {
            jQuery('.limitmessage').fadeOut();
        }, 5000);
        return false;
    }
    var geturl = jQuery(this).attr('data-action');
    var startdate = jQuery('#startdate').val();
    var enddate = jQuery('#enddate').val();
    var search = jQuery('#search').val();
    var url = jQuery('#search').attr('data-url');
    if (startdate)
        geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate;
    else if (search)
        geturl = geturl + '?search=' + search;
    jQuery.ajax({
        url: url,
        type: 'get',
        cache: false,
        data: {
            startdate: startdate,
            enddate: enddate
        },
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="_token"]').attr('content')
        },
        processData: true,
        beforeSend: function() {
            //jQuery(".load-main").show();
        },
        complete: function() {
            //jQuery(".load-main").hide();
        },
        success: function(response) {
            // jQuery(".load-main").hide();
            setTimeout(() => {
                window.location.href = geturl
            }, 10);
        }
    });
});
///
$(document).on('click', '.save_hrs', function () {
            var hrs_id = $(this).val();
            $('#save_hrs_details_model').modal('show');
            $("#hrs_id").val(hrs_id);
            $.ajax({
                type: "GET",
                url: "view-hrsdetails/" + hrs_id,
                data: {
                    hrs_id: hrs_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#save-HrsDraftSheet').dataTable().fnClearTable();
                        $('#save-HrsDraftSheet').dataTable().fnDestroy();
                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    console.log(re);
                 var consignmentID = [];
                    // var totalBoxes = 0;
                    // var totalweights = 0;
                    var i = 0;
                    $.each(re.fetch, function (index, value) {
                        i++;
                        var alldata = value;
                        consignmentID.push(alldata.consignment_id);
                        // totalBoxes += parseInt(value.consignment_detail.total_quantity);
                        // totalweights += parseInt(value.consignment_detail.total_weight);

                        $('#save-HrsDraftSheet tbody').append(
                            "<tr><td>" +
                            value.consignment_id +
                            "</td><td>" +
                            value.consignment_detail.consignee_detail.nick_name +
                            "</td><td>" +
                            value.consignment_detail.consignee_detail.city +
                            "</td><td>" +
                            value.consignment_detail.total_quantity +
                            "</td><td>" +
                            value.consignment_detail.total_weight +
                            "</td></tr>"
                        );
                    });
                     $("#transaction_id").val(consignmentID);
                    // var rowCount = $("#save-DraftSheet tbody tr").length;
                    // $("#total_boxes").append("Boxes: " + totalBoxes);
                    // $("#totalweights").append("Net Weight: " + totalweights + "Kg");
                    // $("#totallr").append("LR's: " + rowCount);
                }
            });
        });

    ///////////////////////////
$('#updt_hrs_details').submit(function(e) {
    e.preventDefault();

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
        url: "update_vehicle_hrs",
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
    ////
    $(document).on('click', '.view-sheet', function () {
            var hrs_id = $(this).val();
            $('#draft_hrs').modal('show');
            $('#current_hrs').val(hrs_id);
            $.ajax({
                type: "GET",
                url: "view-hrsSheetDetails/" + hrs_id,
                data: {
                    hrs_id: hrs_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('.hrs_details_table').dataTable().fnClearTable();
                        $('.hrs_details_table').dataTable().fnDestroy();
                        $("#sss").empty();
                        $("#ppp").empty();
                        $("#nnn").empty();
                        $("#drsdate").empty();
                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    // var drs_no = re.fetch[0]['drs_no'];
                    // $('#current_drs').val(drs_no);

                    // var totalBox = 0;
                    // var totalweight = 0;
                    $.each(re.fetch, function (index, value) {
                        var alldata = value;
                        // totalBox += parseInt(value.total_quantity);
                        // totalweight += parseInt(value.total_weight);

                        $('.hrs_details_table tbody').append("<tr id=" + value.id + " class='move'><td>" + value
                                .consignment_id + "</td><td>" + value.consignment_detail.consignee_detail.nick_name + "</td><td>" + value.consignment_detail.consignee_detail.city +
                            "</td><td>" + value.consignment_detail.consignee_detail.postal_code + "</td><td style='text-align: right'>" + value.consignment_detail.total_quantity +
                            "</td><td style='text-align: right'>" + value.consignment_detail.total_weight +
                            "</td><td style='text-align: center'><button type='button'  data-id=" + value.consignment_no +
                            " style='border: none; margin-inline: auto;' class='delete deleteIcon remover_lr'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg></button></td></tr>"
                        );
                    });
                    // var rowCount = $("#sheet tbody tr").length;
                    // $("#total_box").html("Total Boxes: " + totalBox);
                    // $("#totalweight").html("Net Weight: " + totalweight);
                    // $("#total").html("Total LR's: " + rowCount);
                }
            });
        });
    /////
    $(document).on('click', '#addlr_in_hrs', function () {
            $(this).hide();
            // $(this).css("display", "none");
            $('#unverifiedlist').show();
            $.ajax({
                type: "post",
                url: "get-add-lr-hrs",
                data: {
                    add_drs: 'add_drs'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#unverifiedlrlist_hrs').dataTable().fnClearTable();
                        $('#unverifiedlrlist_hrs').dataTable().fnDestroy();
                    },
                success: function (data) {

                    $.each(data.lrlist, function (index, value) {
                        console.log(value);
                        $('#unverifiedlrlist_hrs tbody').append(
                            "<tr><td><input type='checkbox' name='checked_consign[]' class='chkBoxClass ddd' value=" +
                            value.id + " style='width: 16px; height:16px;'></td><td>" +
                            value.id+
                            "</td><td>" +
                            value.consigner_detail.nick_name+
                            "</td><td>" +
                            value.consignee_detail.nick_name+
                            "</td><td>" +
                            value.consignee_detail.city+
                            "</td></tr>");
                    });
                }
            });
        });

        ////
        $('#add_unverified_lr_hrs').click(function () {
            var hrs_no = $('#current_hrs').val();
            var consignmentID = [];
            $(':checkbox[name="checked_consign[]"]:checked').each(function () {
                consignmentID.push(this.value);
            });
            
            $.ajax({
                url: "created-lr-hrs",
                method: "POST",
                data: {
                    consignmentID: consignmentID,
                    hrs_no: hrs_no
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
                        window.location.href = "hrs-sheet";
                    } else {
                        swal('error', 'something wrong', 'error');
                    }
                }
            })
        });

</script>
@endsection