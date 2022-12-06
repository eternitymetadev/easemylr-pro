@extends('layouts.main')
@section('content')

    <style>
        .lowPaddingCol {
            text-align: right;
            width: 80px !important;
            padding: 0 8px !important;
            vertical-align: middle !important;
        }

        .statusButton {
            border-radius: 50vh;
            width: 100px;
            margin: auto;
            padding: 3px 0;
            color: white;
            font-size: 11px;
        }

        .statusButton svg {
            height: 14px;
            width: 14px;
        }

        .green {
            background: #126600;
        }

        .orange {
            background: #e2a03f;
        }

        .red {
            background: #e23f3f;
        }

        .extra {
            background: #f9b600;
        }

        .extra2 {
            background: #1abc9c;
        }

        td p {
            margin-bottom: 0;
        }

        .drsInfoIcon {
            height: 16px;
            width: 1rem;
            color: #838383;
            cursor: pointer;
        }

        .drsInfoIcon:hover {
            color: blue;
        }

        #drsTable {
            min-height: 150px;
            padding: 1rem;
            padding-left: 2rem;
            color: #4f4f4f;
            font-weight: 600;
        }

    </style>
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Request List</h2>
        </div>

        <div class="widget-content widget-content-area br-6">
            <div class=" mb-4 mt-4">
                @csrf
                <table id="unverified-table" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>Transaction</th>
                        <th class="text-center">Status</th>
                        <th>Branch</th>
                        <th>Vendor</th>
                        <th class="lowPaddingCol text-center">Total Drs</th>
                        <th class="lowPaddingCol text-right">Total Amt.</th>
                        <th class="lowPaddingCol text-right">Advance</th>
                        <th class="lowPaddingCol text-right">Balance</th>
                        <th class="text-center">Payment</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($requestlists as $requestlist)
                        <?php $date = date('d-m-Y', strtotime($requestlist->created_at));?>
                        <tr>
                            <td>
                                <p>
                                    Id:
                                    <span style="font-size: 15px; font-weight: 600; color: #000">
                                        {{ $requestlist->transaction_id ?? "-" }}
                                    </span>
                                    <br/>
                                    Dated: {{ $date }}
                                </p>
                            </td>

                            <td class="text-center ">
                                @if($requestlist->payment_status == 0)<p class="statusButton red">Failed</p>
                                @elseif($requestlist->payment_status == 1)<p class="statusButton green">Paid</p>
                                @elseif($requestlist->payment_status == 2)
                                    <p class="statusButton orange">Sent to Account</p>
                                @elseif($requestlist->payment_status == 3)<p class="statusButton extra">Partial Paid</p>
                                @else<p class="statusButton extra2">Unknown</p>
                                @endif
                            </td>

                            <td>{{ $requestlist->Branch->nick_name ?? "-" }}</td>

                            <td>
                                <p class="textWrap" style="max-width: 250px;"
                                   title="{{ $requestlist->VendorDetails->name ?? "-"}}">
                                    {{ $requestlist->VendorDetails->name ?? "-"}}
                                </p>
                            </td>

                            <td class="show-drs text-center" data-id="{{$requestlist->transaction_id}}">
                                <p class="d-flex justify-content-center align-items-center" style="gap: 4px">
                                    {{ Helper::countDrsInTransaction($requestlist->transaction_id) ?? "" }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-info drsInfoIcon">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </p>
                            </td>

                            <td style="text-align: right">{{ $requestlist->total_amount ?? "-"}}</td>

                            <td style="text-align: right">{{ $requestlist->advanced ?? "-"}}</td>

                            <td style="text-align: right">{{ $requestlist->balance ?? "-" }}</td>

                            <td class="text-center">
                                @if($requestlist->payment_status == 1)
                                    <p class="statusButton green">Paid</p>
                                @elseif($requestlist->payment_status == 2)
                                    <p class="statusButton orange ">Processing</p>
                                @elseif($requestlist->payment_status == 3)
                                    <button class="statusButton extra payment_button" style="border: none"
                                            value="{{$requestlist->transaction_id}}">
                                        Unpaid
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round"
                                             class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                @else
                                    <p class="statusButton extra2">Unknown</p>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('models.payment-model')
@endsection
@section('js')

    <script>
        //////////// Payment request sent model
        $(document).on('click', '.payment_button', function () {
            $("#payment_form")[0].reset();
            var trans_id = $(this).val();


            $.ajax({
                type: "GET",
                url: "get-vender-req-details",
                data: {
                    trans_id: trans_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#p_type').empty();

                    },
                success: function (data) {

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
        $("#amt").keyup(function () {

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
        $("#purchase_amount").keyup(function () {
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
        $('#p_type').change(function () {
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
                    function () {

                    },
                success: function (data) {
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
        $(document).on('click', '.show-drs', function () {

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
                    function () {
                        $('#show_drs_table').dataTable().fnClearTable();
                        $('#show_drs_table').dataTable().fnDestroy();
                    },
                success: function (data) {
                    $('#transAgainstDrs').html(trans_id);

                    $.each(data.getdrs, function (index, value) {
                        $('#drsTable').append("<li>DRS No.: " + value.drs_no + "</li>");
                    });
                }

            });
        });
        /////////////////////////////////////////////////////////////////
        $('#unverified-table').DataTable({
            columnDefs: [
                {orderable: false, targets: [4, 5, 6, 7]}
            ],

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
