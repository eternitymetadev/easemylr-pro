@extends('layouts.main')
@section('content')
<style>
.dt--top-section {
    margin: none;
}

div.relative {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 145px;
    height: 38px;
}

/* .table > tbody > tr > td {
    color: #4361ee;
} */
.dt-buttons .dt-button {
    width: 83px;
    height: 38px;
    font-size: 13px;
}

.btn-group>.btn,
.btn-group .btn {
    padding: 0px 0px;
    padding: 10px;
}

.btn {

    font-size: 10px;
}

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
</style>
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Payment
                                Sheet</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
            <button type="button" class="btn btn-warning">Warning</button>
                <div class="table-responsive mb-4 mt-4">
                    @csrf
                    <div class="main-table table-responsive">
                            @include('vendors.drs-paymentlist-ajax')
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.payment-model')
@endsection
@section('js')
<script>
$(document).on('click', '.payment', function() {
    $('#payment_form')[0].reset();
    var drsno = $(this).val();
    $('#pymt_modal').modal('show');
    $.ajax({
        type: "GET",
        url: "get-drs-details",
        data: {
            drsno: drsno
        },
        beforeSend: //reinitialize Datatables
            function() {

            },
        success: function(data) {
            // console.log(data.get_data.consignment_detail.purchase_price);
            $('#drs_no').val(data.get_data.drs_no);
            $('#purchase_amount').val(data.get_data.consignment_detail.purchase_price);

        }

    });

});
// ============================================================== //
$('#vendor').change(function() {
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


        },
        success: function(res) {
            if (res.success === true) {
                jQuery('#crt_pytm').prop('disabled', false);
                var simp = jQuery.parseJSON(res.vendor_details.bank_details);
                $('#bank_acc').val(simp.account_no);
                $('#ifsc_code').val(simp.ifsc_code);
                $('#bank_name').val(simp.bank_name);
                $('#vendor_no').val(res.vendor_details.vendor_no);
                $('#name').val(res.vendor_details.name);
                $('#beneficiary_name').val(res.vendor_details.name);
                $('#email').val(res.vendor_details.email);
            } else {
                $('#bank_acc').val('');
                $('#ifsc_code').val('');
                $('#bank_name').val('');
                $('#vendor_no').val('');
                $('#name').val('');
                $('#beneficiary_name').val('');
                $('#email').val('');
                jQuery('#crt_pytm').prop('disabled', true);
                swal('error', 'account not verified', 'error');
            }

        }
    });

});

$("#amt").keyup(function() {

    var firstInput = document.getElementById("purchase_amount").value;
    var secondInput = document.getElementById("amt").value;

    if (parseInt(firstInput) < parseInt(secondInput)) {
        $('#amt').val('');
        swal('error', 'amount must be greater than purchase price', 'error')
    } else if (parseInt(firstInput) == '') {
        $('#amt').val('');
        jQuery('#amt').prop('disabled', true);
    }
});
$("#purchase_amount").keyup(function() {

    var firstInput = document.getElementById("purchase_amount").value;
    var secondInput = document.getElementById("amt").value;

    if (parseInt(firstInput) < parseInt(secondInput)) {
        $('#amt').val('');
    } else if (parseInt(firstInput) == '') {
        $('#amt').val('');
        $('#amt').attr('disabled', 'disabled');
    }
});
</script>
@endsection