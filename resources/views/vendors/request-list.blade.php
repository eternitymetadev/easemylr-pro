@extends('layouts.main')
@section('content')

<style>
        .dt--top-section {
    margin:none;
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
.btn-group > .btn, .btn-group .btn {
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
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Unverified Lr</a></li>
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
                                        <th>Vendor</th>
                                        <th>Total Amount</th>
                                        <th>Adavanced</th>
                                        <th>Balance</th>
                                        <th>Create Payment</th>
                                        <th>Status</th> 
                                       
                                </tr>
                             </thead>
                            <tbody>
                                @foreach($requestlists as $requestlist)
                                <tr>

                                    <td>{{ $requestlist->transaction_id ?? "-" }}</td>
                                    <td>{{ $requestlist->VendorDetails->name ?? "-"}}</td>
                                    <td>{{ $requestlist->total_amount ?? "-"}}</td>
                                    <td>{{ $requestlist->advanced ?? "-"}}</td>
                                    <td>{{ $requestlist->balance ?? "-" }}</td>
                                    <td><button class="btn btn-warning payment_button">Create Payment</button></td>
                                    <td>{{ $requestlist->payment_status ?? "-" }}</td>
                                  
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
    // $('#create_request_form')[0].reset();
    $('#p_type').empty();
    $('#pymt_request_modal').modal('show');
    return false;
    var drs_no = [];
    var tdval = [];
    $(':checkbox[name="checked_drs[]"]:checked').each(function() {
        drs_no.push(this.value);
        var cc = $(this).attr('data-price');
        tdval.push(cc);
    });
    $('#drs_no').val(drs_no);

    var toNumbers = tdval.map(Number);
    var sum = toNumbers.reduce((x, y) => x + y);
    $('#purchase_amount').val(sum);

    $.ajax({
        type: "GET",
        url: "get-drs-details",
        data: {
            drs_no: drs_no
        },
        beforeSend: //reinitialize Datatables
            function() {

            },
        success: function(data) {
            console.log(data.get_status);
            // $('#drs_no').val(data.get_data.drs_no);
            // $('#purchase_amount').val(data.get_data.consignment_detail.purchase_price);
            if (data.get_status == 'Successful') {
                $('#p_type').append('<option value="Balance">Balance</option>');
                //check balance if null or delevery successful
                if (data.get_data.balance == '' || data.get_data.balance == null) {
                    var tdval = [];
                    $(':checkbox[name="checked_drs[]"]:checked').each(function() {
                        drs_no.push(this.value);
                        var cc = $(this).attr('data-price');
                        tdval.push(cc);
                    });

                    var toNumbers = tdval.map(Number);
                    var sum = toNumbers.reduce((x, y) => x + y);
                    $('#amt').val(sum);
                    ////
                    // var amt = $('#amt').val(data.get_data.consignment_detail.purchase_price);
                } else {
                    var amt = $('#amt').val(data.get_data.balance);
                    //calculate
                    var tds_rate = $('#tds_rate').val();
                    var cal = (tds_rate / 100) * amt;
                    var final_amt = amt - cal;
                    $('#tds_dedut').val(final_amt);
                }
            } else {
                $('#p_type').append(
                    '<option value="" selected disabled>Select</option><option value="Advance">Advance</option><option value="Balance">Balance</option>'
                    );
            }

        }

    });

});
/////////////////////////////////////////////////////////////////
$('#unverified-table').DataTable( {
            
            "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
        "<'table-responsive'tr>" +
        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [
                    // { extend: 'copy', className: 'btn btn-sm' },
                    // { extend: 'csv', className: 'btn btn-sm' },
                    { extend: 'excel', className: 'btn btn-sm' },
                    // { extend: 'print', className: 'btn btn-sm' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page PAGE of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            
            "ordering": true,
            "paging": false,
             "pageLength": 100,

        } );
</script>
@endsection