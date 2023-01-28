@extends('layouts.main')
@section('content')

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
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">PRS</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">PRS
                                List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    <div class="container-fluid">
                        <div class="row winery_row_n spaceing_2n mb-3">
                            <div class="form-group col-md-4">
                                <button type="button" class="btn btn-warning mt-4 ml-4 create_prs_payment"
                                    style="font-size: 12px;">Create Payment</button>
                            </div>
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
                                    <!-- <a class="btn-primary btn-cstm btn ml-2" style="font-size: 15px; padding: 9px; width: 130px" href="{{'prs/create'}}"><span><i class="fa fa-plus"></i> Add
                                            New</span></a> -->
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                                        style="font-size: 15px; padding: 9px;"
                                        data-action="<?php echo url()->current(); ?>"><span><i
                                                class="fa fa-refresh"></i> Reset Filters</span></a>

                                </div>
                            </div>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('prs.prs-paymentlist-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('models.prs-addpayment')
@endsection
@section('js')
<script>
$(document).on('click', '.add-prs-purchase-price', function() {
    var prs_no = $(this).val();
    $('#add_prsamount').modal('show');
    $('#prs_num').val(prs_no);
});

$("#purchase_amt_prs").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "update-purchas-price-prs",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {},
        success: (data) => {
            if (data.success == true) {
                swal("success", data.success_message, "success");
                window.location.reload();
            } else {
                swal("error", data.error_message, "error");
            }
        },
    });
});
// ========================================================= //
$(document).on('click', '.create_prs_payment', function() {
    // $('#prs_pymt_modal')[0].reset();
    $('#p_type_1').empty();
    $('#prs_pymt_modal').modal('show');
    var prs_no = [];
    var tdval = [];
    $(':checkbox[name="checked_drs[]"]:checked').each(function() {
        prs_no.push(this.value);
        var cc = $(this).attr('data-price');
        tdval.push(cc);
    });

    $('#hrs_no_1').val(prs_no);

    var toNumbers = tdval.map(Number);
    var sum = toNumbers.reduce((x, y) => x + y);
    $('#total_clam_amt_1').val(sum);

    $.ajax({
        type: "GET",
        url: "get-prs-details",
        data: {
            prs_no: prs_no
        },
        beforeSend: //reinitialize Datatables
            function() {
                //     $(".indicator-progress").show();
                // $(".indicator-label").hide();
                // $('.disableme').prop('disabled', true);

            },
        success: function(data) {
            // $('.disableme').prop('disabled', true);
            // $(".indicator-progress").hide();
            // $(".indicator-label").show();

            if (data.get_data.status == 5) {
                $('#p_type_1').append('<option value="Fully">Fully Payment</option>');
                //check balance if null or delevery successful
                var total = $('#total_clam_amt_1').val();

                $('#amt_1').val(total);

                var amt = $('#amt_1').val();
                var tds_rate = $('#tds_rate_1').val();
                var cal = (tds_rate / 100) * amt;
                var final_amt = amt - cal;
                $('#tds_dedut_1').val(final_amt);
                $('#amt_1').attr('readonly', true);
            } else {
                $('#p_type_1').append(
                    '<option value="" selected disabled>Select</option><option value="Advance">Advance</option><option value="Fully">Fully Payment</option>'
                );
            }



        }

    });

});
// ============================================================== //
// ============================================================== //
$('#vendor_id_1').change(function() {
    var vendor_id = $(this).val();

    $.ajax({
        type: 'get',
        url: 'vendor-details',
        data: {
            vendor_id: vendor_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        beforeSend: function() {

            $('#tds_dedut').val('');
        },
        success: function(res) {
            if (res.success === true) {
                jQuery('#crt_pytm').prop('disabled', false);
                var simp = jQuery.parseJSON(res.vendor_details.bank_details);
                $('#bank_acc_1').val(simp.account_no);
                $('#ifsc_code_1').val(simp.ifsc_code); 
                $('#bank_name_1').val(simp.bank_name);
                $('#branch_name_1').val(simp.branch_name);
                $('#vendor_no_1').val(res.vendor_details.vendor_no);
                $('#name_1').val(res.vendor_details.name);
                $('#beneficiary_name_1').val(simp.acc_holder_name);
                $('#email_1').val(res.vendor_details.email);
                $('#tds_rate_1').val(res.vendor_details.tds_rate);
                $('#pan_1').val(res.vendor_details.pan);

                //calculate
                var amt = $('#amt_1').val();

                var tds_rate = $('#tds_rate_1').val();
                var cal = (tds_rate / 100) * amt;
                var final_amt = amt - cal;
                $('#tds_dedut_1').val(final_amt);

            } else {
                $('#bank_acc_1').val('');
                $('#ifsc_code_1').val('');
                $('#bank_name_1').val('');
                $('#branch_name_1').val('');
                $('#vendor_no_1').val('');
                $('#name_1').val('');
                $('#beneficiary_name_1').val('');
                $('#email_1').val('');
                $('#tds_rate_1').val('');
                $('#pan_1').val('');
                jQuery('#crt_pytm_1').prop('disabled', true);
                swal('error', 'account not verified', 'error');
            }

        }
    });

});
// ===========================================================//
// ===========================================================//
$("#amt_1").keyup(function() {
//  alert('k');
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

////
$('#p_type_1').change(function() {
    var p_type = $(this).val();
    var total_amt = $("#total_clam_amt_1").val();
     if(p_type == 'Fully'){
        $('#amt_1').val(total_amt);

        var amt = $('#amt_1').val();
        var tds_rate = $('#tds_rate_1').val();
        var cal = (tds_rate / 100) * amt;
        var final_amt = amt - cal;
        $('#tds_dedut_1').val(final_amt);
        $('#amt_1').attr('readonly', true);

     }else{
        $('#amt_1').val('');
        $('#tds_dedut_1').val('');
        $('#amt_1').attr('readonly', false);
     }

});
////////////////// create Hrs Payment Request ////////////
$("#prs_request_form").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var vendor = $("#vendor_id_1").val();
    if (!vendor) {
        swal("Error!", "Please select a vendor", "error");
        return false;
    }
    var base_url = window.location.origin;
    $.ajax({
        url: "prs-payment-request",
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
            $('.disableme').prop('disabled', true);
        },
        success: (data) => {
            $('.disableme').prop('disabled', true);
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal("success", data.message, "success");
                window.location.href = data.redirect_url;
            } else {
                swal("error", data.message, "error");
            }
        },
    });
});
</script>
@endsection