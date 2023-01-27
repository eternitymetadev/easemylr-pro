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
                        @include('hub-transportation.outgoing-hrs-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('models.incoming-hrs-models')
@endsection
@section('js')
<script>
    $(document).on('click', '.view-lr', function () {
            var hrs_id = $(this).val();
            $('#view_lr_in_hrs').modal('show');
            $.ajax({
                type: "GET",
                url: "view-lr-hrs/" + hrs_id,
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
                            "</td></tr>"
                        );
                    });
                }
            });
        });
// ///////////////
$(document).on('click', '.receive_hrs', function () {
            var hrs_id = $(this).val();
            $('#receive_hrs_model').modal('show');
            $.ajax({
                type: "GET",
                url: "receving-hrs-details/" + hrs_id,
                data: {
                    hrs_id: hrs_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('.receving_hrs_details').dataTable().fnClearTable();
                        $('.receving_hrs_details').dataTable().fnDestroy();
                        $("#sss").empty();
                        $("#ppp").empty();
                        $("#nnn").empty();
                        $("#drsdate").empty();
                    },
                success: function (data) {
                    var re = jQuery.parseJSON(data)
                    var totalBox = 0;
                    var total = (re.fetch).length;
                    var consignmentID = [];
                    $.each(re.fetch, function (index, value) {
                        totalBox += parseInt(value.consignment_detail.total_quantity);
                        consignmentID.push(value.consignment_id);
                    });
                    $('.receving_hrs_details tbody').append("<tr><td><input type='text' name='hrs_id' id='hrs_no' readonly/></td><td><input type='text' name='no_of_lr' id='no_of_lr' readonly/></td><td><input type='text' name='total_box' id='total_box' readonly /></td><td><input type='text' name='receive_quantity' id='receive_quantity' /></td><td style='text-align: right'><input type='text' name='remarks'/></td><td style='text-align: right'><button type='submit' class='btn btn-primary'>Receive</button></td></tr>"
                        );
                        $('#hrs_no').val(hrs_id);
                        $('#total_box').val(totalBox);
                        $('#no_of_lr').val(total);
                        $('#lr_no').val(consignmentID);
                }
            });
        });
    /////
    $("#save_receving_details").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "update_hrs_receving_details",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal("success!", data.success_message, "success");
                window.location.reload();
            } else {
                swal("error", data.error_message, "error");
            }
        },
    });
});
/////
$("#receive_quantity").keyup(function() {
  alert('k');
var firstInput = document.getElementById("total_clam_amt_1").value;
var secondInput = document.getElementById("amt_1").value;

if (parseInt(firstInput) < parseInt(secondInput)) {
    $('#amt_1').val('');
    $('#tds_dedut_1').val('');
    swal('error', 'amount must be greater than purchase price', 'error')
} else if (parseInt(firstInput) == '') {
    $('#amt_1').val('');
    jQuery('#amt_1').prop('disabled', true);
}
// Calculate tds
var tds_rate = $('#tds_rate_1').val();

var cal = (tds_rate / 100) * secondInput;
var final_amt = secondInput - cal;
$('#tds_dedut_1').val(final_amt);

});
</script>
@endsection