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
        <h2 class="pageHeading">HRS Payment Sheet</h2>
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
            @include('hub-transportation.hrs-request-list-ajax')
        </div>
    </div>
</div>
@include('models.hrs-payment-models')
@endsection
@section('js')
<script>
    $(document).on('click', '.approve', function() {
    var transaction_id = $(this).val();
    $('#approver_model').modal('show');
    $.ajax({
        type: "GET",
        url: "get-vender-req-details-hrs",
        data: {
            transaction_id: transaction_id
        },
        beforeSend: function() {

        },
        success: function(data) {
             console.log(data);

            var bank_details = JSON.parse(data.req_data[0].vendor_details.bank_details);

                $('#hrs_num').val(data.hrs_no);
                $('#vendor_no_request').val(data.req_data[0].vendor_details.vendor_no);
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

        }
    });
});
</script>
@endsection