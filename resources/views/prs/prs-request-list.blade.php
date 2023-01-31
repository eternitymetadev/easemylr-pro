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
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">PRS Request List</h2>
    </div>

    <div class="widget-content widget-content-area br-6" style="min-height: min(80vh, 600px)">

        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center" style="gap: 1rem;">

            <div>
                <input class="form-control" placeholder="Vehicle Number Search" id="search"
                    data-action="<?php echo url()->current(); ?>"
                    style="height: 36px; max-width: 250px; width: 300px;" />
            </div>
        </div>

        @csrf
        <div class="main-table table-responsive">
            @include('prs.prs-request-list-ajax')
        </div>
    </div>
</div>
@include('models.prs-addpayment')
@endsection
@section('js')
<script>
$(document).on('click', '.approve', function() {
    var transaction_id = $(this).val();
    $('#approver_model_prs').modal('show');
    // alert(transaction_id);
    $.ajax({
        type: "GET",
        url: "get-vender-req-details-prs",
        data: {
            transaction_id: transaction_id
        },
        beforeSend: function() {

        },
        success: function(data) {

            var bank_details = JSON.parse(data.req_data[0].vendor_details.bank_details);

            $('#prs_number').val(data.prs_no);
            $('#transaction_no').val(data.req_data[0].transaction_id);
            $('#vendor_num').val(data.req_data[0].vendor_details.vendor_no);
            $('#transaction_id_2').val(data.req_data[0].transaction_id);
            $('#v_name').val(data.req_data[0].vendor_details.name);
            $('#email').val(data.req_data[0].vendor_details.email);
            $('#beneficiary_name').val(bank_details.acc_holder_name);
            $('#bank_acc').val(bank_details.account_no);
            $('#ifsc_code').val(bank_details.ifsc_code);
            $('#bank_name').val(bank_details.bank_name);
            $('#branch_name').val(bank_details.branch_name);
            $('#total_clam_amt').val(data.req_data[0].total_amount);
            $('#pan').val(data.req_data[0].vendor_details.pan);
            $('#tds_deduct_balance').val(data.req_data[0].tds_deduct_balance);
            $('#final_payable_amount').val(data.req_data[0].current_paid_amt);
            $('#pymt_type').val(data.req_data[0].payment_type);
            $('#branch_id_app').val(data.req_data[0].branch_id);
            $('#user_id').val(data.req_data[0].user_id);
            $('#advance').val(data.req_data[0].amt_without_tds);

            $('.req_amt').html(data.req_data[0].current_paid_amt);
            $('.req_vendor').html(data.req_data[0].vendor_details.name);
            $('.req_trans_id').html(data.req_data[0].transaction_id);

        }
    });
});
///
////////////////// RM Approver Request ////////////
$("#prs_rm_aprover").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: "prs-rm-approver",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
            $('.disableme').prop('disabled', true);
        },
        success: (data) => {
            $('.disableme').prop('disabled', true);
            $(".indicator-progress").hide();
            $(".indicator-label").show();

            if (data.success == true) {
                swal("success", data.message, "success");
                window.location.reload();
            } else {
                swal("error", data.message, "error");
            }

        },
    });
});

//////////// Payment request sent model
$(document).on('click', '.second_payment_prs', function() {
    // $("#second_payment_form")[0].reset();
    var trans_id = $(this).val();
    $('#pymt_request_modal_prs').modal('show');
    $.ajax({
        type: "GET",
        url: "get-second-pymt-details-prs",
        data: {
            trans_id: trans_id
        },
        beforeSend: //reinitialize Datatables
            function() {
                $('#p_type').empty();

            },
        success: function(data) {

            var bank_details = JSON.parse(data.req_data[0].vendor_details.bank_details);

            $('#hrs_no_request').val(data.prs_no);
            $('#vendor_no_request').val(data.req_data[0].vendor_details.vendor_no);
            $('#transaction_id_2').val(data.req_data[0].transaction_id);
            $('#name').val(data.req_data[0].vendor_details.name);
            $('#email_second').val(data.req_data[0].vendor_details.email);
            $('#beneficiary_name_second').val(bank_details.acc_holder_name);
            $('#bank_acc_second').val(bank_details.account_no);
            $('#ifsc_code_second').val(bank_details.ifsc_code);
            $('#bank_name_second').val(bank_details.bank_name);
            $('#branch_name_second').val(bank_details.branch_name);
            $('#total_clam_amt_second').val(data.req_data[0].total_amount);
            $('#tds_rate_second').val(data.req_data[0].vendor_details.tds_rate);
            $('#pan_second').val(data.req_data[0].vendor_details.pan);

            $('#p_type_second').append('<option value="Fully">Fully Payment</option>');
            //check balance if null or delevery successful
            if (data.req_data[0].balance == '' || data.req_data[0].balance == null) {
                $('#amt_second').val(data.req_data[0].total_amount);
                var amt = $('#amt_second').val();
                var tds_rate = $('#tds_rate_second').val();
                var cal = (tds_rate / 100) * amt;
                var final_amt = amt - cal;
                $('#tds_dedut_second').val(final_amt);
                $('#amt_second').attr('readonly', true);

            } else {
                $('#amt_second').val(data.req_data[0].balance);
                var amt = $('#amt_second').val();
                //calculate
                var tds_rate = $('#tds_rate_second').val();
                var cal = (tds_rate / 100) * amt;
                var final_amt = amt - cal;
                $('#tds_dedut_second').val(final_amt);
                // $('#amt').attr('disabled', 'disabled');
                $('#amt_second').attr('readonly', true);

            }

        }

    });

});
//////Second Payment
$("#second_payment_form_prs").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var tds_rate = $("#tds_rate").val();

    $.ajax({
        url: "second-payment-prs",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal("success", data.message, "success");
                $("#second_payment_form")[0].reset();
            } else {
                swal("error", data.message, "error");
            }
        },
    });
});
//////
$(document).on('click', '.show-prs', function() {
    var trans_id = $(this).attr('data-id');
    $('#show_prs_model').modal('show');
    $.ajax({
        type: "GET",
        url: "show-prs",
        data: {
            trans_id: trans_id
        },
        beforeSend: //reinitialize Datatables
            function() {
                $('#show_prs_table').dataTable().fnClearTable();
                $('#show_prs_table').dataTable().fnDestroy();
            },
        success: function(data) {
            // console.log(data.)
            $.each(data.getprs, function(index, value) {

                $('#show_prs_table tbody').append("<tr><td>" + value.prs_no + "</td></tr>");

            });
        }

    });
});
/////////////////////////////////////////////////////////////////
</script>
@endsection