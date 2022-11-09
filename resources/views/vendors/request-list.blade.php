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
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Request
                                List</a></li>
                    </ol>
                </nav>
            </div>

            <div class="widget-content widget-content-area br-6">
                <div class=" mb-4 mt-4">
                    @csrf
                    <table id="unverified-table" class="table table-hover" style="width:100%">
                        <!-- <button type="button" class="btn btn-warning" id="launch_model" data-toggle="modal" data-target="#exampleModal" disabled="disabled" style="font-size: 11px;">

                            Create DSR
                            </button> -->
                </div>
                <thead>
                    <tr>
                        <th>Transaction Id</th>
                        <th>Date</th>
                        <th>Total Drs</th>
                        <th>Vendor</th>
                        <th>Total Amount</th>
                        <th>Adavanced</th>
                        <th>Balance</th>
                        <th>Branch </th>
                        <th>Create Payment</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($requestlists as $requestlist)
                    <?php
                          $date = date('d-m-Y',strtotime($requestlist->created_at));
                    ?>
                    <tr>

                        <td>{{ $requestlist->transaction_id ?? "-" }}</td>
                        <td>{{ $date }}</td>
                        <td class="show-drs" data-id="{{$requestlist->transaction_id}}">
                            {{ Helper::countDrsInTransaction($requestlist->transaction_id) ?? "" }}</td>
                        <td>{{ $requestlist->VendorDetails->name ?? "-"}}</td>
                        <td>{{ $requestlist->total_amount ?? "-"}}</td>
                        <td>{{ $requestlist->advanced ?? "-"}}</td>
                        <td>{{ $requestlist->balance ?? "-" }}</td>
                        <td>{{ $requestlist->Branch->nick_name ?? "-" }}</td>
                        <?php if($requestlist->payment_status == 1){?>
                        <td><button class="btn btn-warning" value="{{$requestlist->transaction_id}}" disabled>Fully
                                Paid</button></td>
                        <?php }elseif($requestlist->payment_status == 2 || $requestlist->payment_status == 1){ ?>
                        <td><button class="btn btn-warning payment_button" value="{{$requestlist->transaction_id}}"
                                disabled>Processing...</button></td>
                        <?php } else if($requestlist->payment_status == 0){ ?>
                        <td><button class="btn btn-warning" value="{{$requestlist->transaction_id}}" disabled>Create
                                Payment</button></td>
                        <?php }else{ ?>
                        <td><button class="btn btn-warning payment_button"
                                value="{{$requestlist->transaction_id}}">Create Payment</button></td>
                        <?php } ?>

                        <!-- payment Status -->
                        <?php if($requestlist->payment_status == 0){ ?>
                        <td> <label class="badge badge-dark">Faild</label>
                        </td>
                        <?php } elseif($requestlist->payment_status == 1) { ?>
                        <td> <label class="badge badge-success">Paid</label> </td>
                        <?php } elseif($requestlist->payment_status == 2) { ?>
                        <td> <label class="badge badge-dark">Sent to Account</label>
                        </td>
                        <?php } elseif($requestlist->payment_status == 3) { ?>
                        <td><label class="badge badge-primary">Partial Paid</label></td>
                        <?php } else{?>
                        <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Unknown</button>
                        </td>
                        <?php } ?>
                        <!-- end payment -->

                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
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
                swal('error', 'Please update delivey status','error');
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
    // alert(show);
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
            // console.log(data.)
            $.each(data.getdrs, function(index, value) {

                $('#show_drs_table tbody').append("<tr><td>" + value.drs_no + "</td></tr>");

            });
        }

    });
});
/////////////////////////////////////////////////////////////////
$('#unverified-table').DataTable({

    "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
        "<'table-responsive'tr>" +
        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
    buttons: {
        buttons: [
            // { extend: 'copy', className: 'btn btn-sm' },
            // { extend: 'csv', className: 'btn btn-sm' },
            {
                extend: 'excel',
                className: 'btn btn-sm'
            },
            // { extend: 'print', className: 'btn btn-sm' }
        ]
    },
    "oLanguage": {
        "oPaginate": {
            "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
            "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
        },
        "sInfo": "Showing page PAGE of _PAGES_",
        "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        "sSearchPlaceholder": "Search...",
        "sLengthMenu": "Results :  _MENU_",
    },

    "ordering": true,
    "paging": false,
    "pageLength": 100,

});
</script>
@endsection