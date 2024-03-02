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

        .highlight-on-hover {
            cursor: pointer;
        }

        .highlight-on-hover:hover {
            background-color: lightgrey;
        }
    </style>
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_html5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css') }}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="page-header">
                    <nav class="breadcrumb-one" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Request List</a></li>
                        </ol>
                    </nav>
                </div>

                <div class="widget-content widget-content-area br-6">

                    <div class="row px-3 mx-0 mt-4 justify-content-between" style="gap: 12px; flex-wrap: wrap">
                        <div class="d-flex align-items-end" style="gap: 12px; flex-wrap: wrap">
                            <div style="width: 300px">
                                <label>Search</label>
                                <input type="text" class="form-control" placeholder="Transaction Id" id="search"
                                    data-action="<?php echo url()->current(); ?>">
                            </div>

                            <div style="width: 156px">
                                <label>From</label>
                                <input type="date" id="startdate" class="form-control" name="startdate" onkeydown="return false">
                            </div>
                            <div style="width: 156px">
                                <label>To</label>
                                <input type="date" id="enddate" class="form-control" name="enddate" onkeydown="return false">
                            </div>
                            <div style="width: 210px">
                                <label>Payment Status</label>
                                <select class="form-control my-select2" id="paymentstatus_filter" name="paymentstatus_id"
                                    data-action="<?php echo url()->current(); ?>" placeholder="Search By Status">
                                    <option value="" selected disabled>--select status--</option>
                                    <option value="1">Fully Paid</option>
                                    <option value="2">Processing</option>
                                    <option value="3">Create payment</option>
                                    <option value="0">Failed</option>
                                </select>
                            </div>

                            <button type="button" id="filter_reportall" class="btn btn-primary"
                                style="margin-top: 31px; font-size: 15px; padding: 9px; width: 100px">
                                <span class="indicator-label">Filter</span>
                            </button>

                            <button type="button" class="btn btn-primary reset_filter"
                                style="margin-top: 31px; font-size: 15px; padding: 9px; width: 100px"
                                data-action="<?php echo url()->current(); ?>">
                                <span class="indicator-label">Reset</span>
                            </button>
                        </div>

                        <a href="<?php echo URL::to($prefix . '/transaction-status-export'); ?>" data-url="<?php echo URL::to($prefix . '/request-list'); ?>"
                            class="consignmentReportEx btn btn-white btn-cstm"
                            style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px"
                            data-action="<?php echo URL::to($prefix . '/transaction-status-export'); ?>" download><span><i class="fa fa-download"></i>
                                Export</span></a>
                    </div>
                    @csrf
                    <div class="main-table table-responsive">
                        @include('vendors.request-list-ajax')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Repay modal -->


    @include('models.payment-model')
@endsection
@section('js')
    <script>
        //////////// Payment request sent model
        $(document).on('click', '.payment_button', function() {
            $("#payment_form")[0].reset();
            var trans_id = $(this).val();

            $.ajax({
                type: "GET",
                url: "get-vender-req-details",
                data: {
                    trans_id: trans_id
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        $('#p_type').empty();

                    },
                success: function(data) {

                    if (data.status == 'Successful') {

                        $('#pymt_request_modal').modal('show');
                        var bank_details = JSON.parse(data.req_data[0].vendor_details.bank_details);

                        $('#drs_no_request').val(data.drs_no);
                        $('#vendor_no_request').val(data.req_data[0].vendor_details.vendor_no);
                        $('#transaction_id_2').val(data.req_data[0].transaction_id);
                        $('#name').val(data.req_data[0].vendor_details.name);
                        $('#email').val(data.req_data[0].vendor_details.email);
                        $('#beneficiary_name').val(bank_details.acc_holder_name);
                        $('#bank_acc').val(bank_details.account_no);
                        $('#ifsc_code').val(bank_details.ifsc_code);
                        $('#bank_name').val(bank_details.bank_name);
                        $('#branch_name').val(bank_details.branch_name);
                        $('#total_clam_amt').val(data.req_data[0].total_amount);
                        $('#tds_rate').val(data.req_data[0].vendor_details.tds_rate);
                        $('#pan').val(data.req_data[0].vendor_details.pan);

                        $('#p_type').append('<option value="Fully">Fully Payment</option>');
                        //check balance if null or delevery successful
                        if (data.req_data[0].balance == '' || data.req_data[0].balance == null) {
                            $('#amt').val(data.req_data[0].total_amount);
                            var amt = $('#amt').val();
                            var tds_rate = $('#tds_rate').val();
                            var cal = (tds_rate / 100) * amt;
                            var final_amt = amt - cal;
                            $('#tds_dedut').val(final_amt);
                            $('#amt').attr('readonly', true);

                        } else {
                            $('#amt').val(data.req_data[0].balance);
                            var amt = $('#amt').val();
                            //calculate
                            var tds_rate = $('#tds_rate').val();
                            var cal = (tds_rate / 100) * amt;
                            var final_amt = amt - cal;
                            $('#tds_dedut').val(final_amt);
                            // $('#amt').attr('disabled', 'disabled');
                            $('#amt').attr('readonly', true);

                        }
                    } else {
                        // $('#pymt_request_modal').modal('hide');
                        swal('error', 'Please update delivey status', 'error');
                        return false;
                        if (data.req_data[0].balance == '' || data.req_data[0].balance == null) {
                            $('#p_type').append(
                                '<option value="" selected disabled>Select</option><option value="Advance">Advance</option><option value="Balance">Balance</option><option value="Fully">Fully Payment</option>'
                            );
                        } else {
                            $('#p_type').append(
                                '<option value=""  disabled>Select</option><option value="Advance">Advance</option><option value="Balance" selected>Balance</option><option value="Fully">Fully Payment</option>'
                            );
                            var amt = $('#amt').val(data.req_data[0].balance);
                            var amt = $('#amt').val();
                            var tds_rate = $('#tds_rate').val();
                            var cal = (tds_rate / 100) * amt;
                            var final_amt = amt - cal;
                            $('#tds_dedut').val(final_amt);
                            // $('#amt').attr('disabled', 'disabled');
                            $('#amt').attr('readonly', true);

                        }
                    }
                }

            });

        });
        ////
        $("#amt").keyup(function() {
            var firstInput = document.getElementById("total_clam_amt").value;
            var secondInput = document.getElementById("amt").value;

            if (parseInt(firstInput) < parseInt(secondInput)) {
                $('#amt').val('');
                $('#tds_dedut').val('');
                swal('error', 'amount must be greater than purchase price', 'error')
            } else if (parseInt(firstInput) == '') {
                $('#amt').val('');
                jQuery('#amt').prop('disabled', true);
            }
            // Calculate tds
            var tds_rate = $('#tds_rate').val();

            var cal = (tds_rate / 100) * secondInput;
            var final_amt = secondInput - cal;
            $('#tds_dedut').val(final_amt);

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
        // ====================================================== //
        $('#p_type').change(function() {
            $('#amt').val('');
            var p_typ = $(this).val();
            var transaction_id = $('#transaction_id_2').val();
            // alert(transaction_id);
            $.ajax({
                type: "GET",
                url: "get-balance-amount",
                data: {
                    transaction_id: transaction_id,
                    p_typ: p_typ
                },
                beforeSend: //reinitialize Datatables
                    function() {

                    },
                success: function(data) {
                    console.log(data.getbalance.balance);
                    if (p_typ == 'Balance') {
                        $('#amt').val(data.getbalance.balance);
                        //calculate
                        var tds_rate = $('#tds_rate').val();
                        var cal = (tds_rate / 100) * data.getbalance.balance;
                        var final_amt = data.getbalance.balance - cal;
                        $('#tds_dedut').val(final_amt);
                        $('#amt').attr('readonly', true);

                    } else if (p_typ == 'Fully') {
                        $('#amt').val(data.getbalance.balance);
                        //calculate
                        var tds_rate = $('#tds_rate').val();
                        var cal = (tds_rate / 100) * data.getbalance.balance;
                        var final_amt = data.getbalance.balance - cal;
                        $('#tds_dedut').val(final_amt);
                        $('#amt').attr('readonly', true);

                    } else {
                        $('#amt').attr('readonly', false);
                    }
                }

            });

        });
        ///////////////////////////////////////////////
        $(document).on('click', '.show-drs', function() {
            var trans_id = $(this).attr('data-id');
            $('#show_drs').modal('show');
            $.ajax({
                type: "GET",
                url: "show-drs",
                data: {
                    trans_id: trans_id
                },
                beforeSend: //reinitialize Datatables
                    function() {
                        $('#show_drs_table').dataTable().fnClearTable();
                        $('#show_drs_table').dataTable().fnDestroy();
                    },
                success: function(data) {
                    if (data.success) {
                        $.each(data.getdrs, function(index, drsPaymentreq) {
                            console.log(drsPaymentreq);
                            var totalQuantitySum = 0;
                            var totalNetweightSum = 0;
                            var totalGrossweightSum = 0;
                            $.each(drsPaymentreq.transaction_details, function(index, drsdetail) {
                                
                                totalQuantitySum += parseInt(drsdetail.consignment_detail.total_quantity);
                                totalNetweightSum += parseInt(drsdetail.consignment_detail.total_weight);
                                totalGrossweightSum += parseInt(drsdetail.consignment_detail.total_gross_weight);
                            });
                            $('#show_drs_table tbody').append("<tr><td>" + drsPaymentreq.drs_no +
                                "</td><td>" + totalQuantitySum +
                                "</td><td>" + totalNetweightSum +
                                "</td><td>" + totalGrossweightSum +
                                "</td></tr>");
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        });

        // payment status filter
        $('#paymentstatus_filter').change(function() {
            var paymentstatus_id = $(this).val();
            let url = $(this).attr('data-action');
            $.ajax({
                url: url,
                type: "get",
                cache: false,
                data: {
                    paymentstatus_id: paymentstatus_id
                },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr("content"),
                },
                beforeSend: function() {

                },
                success: function(res) {
                    if (res.html) {
                        jQuery('.main-table').html(res.html);
                    }
                },
            });
            return false;
        });

        $(document).on('click', '.repay', function() {

            var trans_id = $(this).val();
            $('#repay_modal').modal('show');

            $.ajax({
                type: "GET",
                url: "get-repay-details",
                data: {
                    trans_id: trans_id
                },
                beforeSend: //reinitialize Datatables
                    function() {

                    },
                success: function(data) {

                    console.log(data);

                    var simp = jQuery.parseJSON(data.payment_details.vendor_details.bank_details);
                    $('#repay_bank_acc').val(simp.account_no);
                    $('#repay_ifsc_code').val(simp.ifsc_code);
                    $('#repay_bank_name').val(simp.bank_name);
                    $('#repay_branch_name').val(simp.branch_name);
                    $('#repay_vendor_no').val(data.payment_details.vendor_details.vendor_no);
                    $('#repay_name').val(data.payment_details.vendor_details.name);
                    $('#repay_beneficiary_name').val(simp.acc_holder_name);
                    $('#repay_email').val(data.payment_details.vendor_details.email);
                    $('#repay_tds_rate').val(data.payment_details.vendor_details.tds_rate);
                    $('#repay_pan').val(data.payment_details.vendor_details.pan);
                    $('#repay_p_type').val(data.payment_details.payment_type);
                    $('#repay_total_clam_amt').val(data.payment_details.total_amount);
                    $('#repay_amt').val(data.payment_details.current_paid_amt);
                    $('#repay_vendor_name').val(data.payment_details.vendor_details.name);
                    $('#repay_drs_no').val(data.payment_drs);
                    $('#repay_transaction_id').val(trans_id);
                    $('#repay_vendor_no').val(data.payment_details.vendor_details.vendor_no);

                    //calculate
                    var amt = $('#repay_amt').val();
                    var tds_rate = $('#repay_tds_rate').val();
                    var cal = (tds_rate / 100) * amt;
                    var final_amt = amt - cal;
                    $('#repay_tds_dedut').val(final_amt);
                }

            });

        });
        // 
        ////////////////// reate Drs Payment Request ////////////
        $("#repay_payment_form").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var amt = $("#repay_amt").val();

            if (!amt) {
                swal("Error!", "amount is empty", "error");
                return false;
            }
            var base_url = window.location.origin;
            $.ajax({
                url: "repay-request",
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
                        window.location.href = data.redirect_url;
                    } else if (data.error == true) {
                        swal("error", data.message, "error");
                    } else {
                        swal("error", data.message, "error");
                    }
                },
            });
        });

    jQuery(document).on('click', '#filter_reportall', function() {
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var search = jQuery('#search').val();
        var paymentstatus_id = $("#paymentstatus_filter").val();
        
        jQuery.ajax({
            type: 'get',
            url: 'request-list',
            data: {
                startdate: startdate,
                enddate: enddate,
                search: search,
                paymentstatus_id: paymentstatus_id
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
        var paymentstatus_id = $("#paymentstatus_filter").val();
        var search = jQuery('#search').val();

        var url = jQuery('#search').attr('data-url');
        // if (startdate)
        //     geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate;
        // else if (search)
        //     geturl = geturl + '?search=' + search;
        // else if (paymentstatus_id)
        //     geturl = geturl + '?paymentstatus_id=' + paymentstatus_id;

        if (typeof(paymentstatus_id) === "undefined" || paymentstatus_id == null) {
            var paymentstatus_id = '';
        } else {
            var paymentstatus_id = paymentstatus_id;
        }

        geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate + '&search=' + search + '&paymentstatus_id=' + paymentstatus_id;
        
        jQuery.ajax({
            url: url,
            type: 'get',
            cache: false,
            data: {
                startdate: startdate,
                enddate: enddate,
                search: search,
                paymentstatus_id: paymentstatus_id
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
    </script>
@endsection
