@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

<style>
.update_purchase_price svg {
    height: 14px;
    width: 14px;
    margin-left: 8px;
    cursor: pointer;
}

.update_purchase_price svg:hover {
    color: #f9b600;
}

.pointer {
    cursor: pointer;
}

.drsStatus {
    user-select: none;
    cursor: default;
    text-align: center;
    width: 110px;
    border-radius: 50vh;
    padding: 6px 8px;
    font-size: 11px;
    line-height: 11px;
    font-size: 11px;
    color: #ffffff;
}

.green {
    background: #148b00;
}

.orange {
    background: #e2a03f;
}

.extra2 {
    background: #1abc9c;
}

#create_request_form span.select2 {
    margin-bottom: 0 !important;
}

input[readonly].styledInput {
    border: none;
    background-color: transparent !important;
    color: #000;
    font-weight: 700;
}
</style>

<div class="layout-px-spacing">
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">HRS Payment Sheet</h2>
    </div>

    <div class="widget-content widget-content-area br-6" style="min-height: min(80vh, 600px)">

        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center" style="gap: 1rem;">
            <div>
                <?php $authuser = Auth::user();
                    if ($authuser->role_id == 2 || $authuser->role_id == 3) {?>
                <button type="button" class="btn btn-warning payment" style="font-size: 12px; height: 36px" disabled>
                    Create Payment
                </button>
                <?php }?>

                <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                    data-action="<?php echo url()->current(); ?>"
                    style="font-size: 12px; width: 130px; height: 36px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa fa-refresh"></i> Reset Filters
                </a>
            </div>
            <div>
                <input class="form-control" placeholder="Vehicle Number Search" id="search"
                    data-action="<?php echo url()->current(); ?>"
                    style="height: 36px; max-width: 250px; width: 300px;" />
            </div>
        </div>

        @csrf
        <div class="main-table table-responsive">
            @include('hub-transportation.hrs-payment-list-ajax')
        </div>
    </div>
</div>
@include('models.hrs-payment-models')
@endsection
@section('js')
<script>
function testFunction() {
    alert('test')
}

jQuery(function() {
    $('.my-select2').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            dropdownParent: $(this).parent(), // fix select2 search input focus bug
        })
    })

    $(document).ready(function() {
        $('.my-select3').select2();
    });

    // fix select2 bootstrap modal scroll bug
    $(document).on('select2:close', '.my-select2', function(e) {
        var evt = "scroll.select2"
        $(e.target).parents().off(evt)
        $(window).off(evt)
    })
})

/////////
$(document).on('click', '.hrs_lr', function() {
    var hrs_lr = $(this).attr('hrs-no');
    $('#view_hrs_lrmodel').modal('show');
    $.ajax({
        type: "GET",
        url: "view-hrslr/" + hrs_lr,
        data: {
            hrs_lr: hrs_lr
        },
        beforeSend: function() {
            $('#view_hrs_lrtable').dataTable().fnClearTable();
            $('#view_hrs_lrtable').dataTable().fnDestroy();
            $("#total_boxes").empty();
            $("#totalweights").empty();
            $("#totallr").empty();

        },
        success: function(data) {
            var re = jQuery.parseJSON(data)
            console.log(re.fetch);
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


                $('#view_hrs_lrtable tbody').append("<tr id=" + value.id +
                    "><td>" + value.consignment_id +
                    "</td><td>" + value.consignment_detail.consignment_date + "</td><td>" + value.consignment_detail
                    .consignee_detail.nick_name + "</td><td>" + value.consignment_detail
                    .consignee_detail.city + "</td><td>" + value.consignment_detail
                    .consignee_detail.postal_code + "</td><td>" + value.consignment_detail.total_quantity + "</td><td>" + value
                    .consignment_detail.total_weight + "</td></tr>");
            });
            var rowCount = $("#view_hrs_lrtable tbody tr").length;

            $("#total_boxes").append("No Of Boxes: " + totalBoxes);
            $("#totalweights").append("Net Weight: " + totalweights);
            $("#totallr").append(rowCount);

        }
    });
});
</script>
@endsection