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

                </div>
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Transaction id</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Depot</th>
                        <th>Station</th>
                        <th>Drs No</th>
                        <th>LR No</th>
                        <th>Invoice No</th>
                        <th>Type of Vehicle </th>
                        <th>No. of Cartons</th>
                        <th>Net Weight</th>
                        <th>Gross weight</th>
                        <th>Truck No.</th>
                        <th>Vendor Name</th>
                        <th>Bank Name</th>
                        <th>Account No.</th>
                        <th>IFSC Code</th>
                        <th>Vendor Pan</th>
                        <th>Purchase Freight</th>
                        <th>Advance</th>
                        <th>Payment Date</th>
                        <th>Blance Amount</th>
                        <th>payment date</th>
                        <th>Total Amount Paid</th>
                        <th>Debit Bank</th>
                        <th>Ref.No.</th>
                        <th>Balance Due</th>
                        <th>Tds Amount.</th>
                        <th>TDS Status YES/NO</th>
                        <th>Declaration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;
                     
                    ?>
                   
                    @foreach($payment_lists as $payment_list)
                    <?php
                    $drs = explode(',', $payment_list->drs_no);
                     $i++;
                    $bankdetails = json_decode($payment_list->PaymentRequest->VendorDetails->bank_details);
                     $lr_arra = array();
                     $itm_arra = array();
                     foreach($payment_list->PaymentRequest->TransactionDetails as $lr_no){
                              $lr_arra[] = $lr_no->consignment_no;

                        foreach($lr_no->ConsignmentNote->ConsignmentItems as $lr_no_item){
                            $itm_arra[] = $lr_no_item->invoice_no;
                        }


                     }
                       $multilr = implode(',', $lr_arra);
                       $lr_itm = implode(',', $itm_arra);
                    //    $regn_lr = implode(',', $regn_arra);
                     $date = date('d-m-Y',strtotime($payment_list->created_at));
                    ?>
                    <tr>
                        <td>{{$i}}</td> 
                        <td>{{$payment_list->transaction_id ?? '-'}}</td> 
                        <td>{{$date}}</td>
                        <td>{{$lr_no->ConsignmentNote->RegClient->name ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest->Branch->nick_name ?? '-'}}</td>
                        <td>-</td>
                        <td>DRS-{{$payment_list->drs_no ?? '-'}}</td>
                        <td>{{$multilr ?? '-'}}</td>
                        <td>{{$lr_itm ?? '-'}}</td>
                        <td>{{$lr_no->ConsignmentNote->vehicletype->name ?? '-'}}</td>
                        <td>{{ Helper::totalQuantity($payment_list->drs_no) ?? "-"}}</td>
                        <td>{{ Helper::totalWeight($payment_list->drs_no) ?? "-"}}</td>
                        <td>{{ Helper::totalGrossWeight($payment_list->drs_no) ?? "-"}}</td>
                        <td>{{$payment_list->PaymentRequest->vehicle_no ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest->VendorDetails->name ?? '-'}}</td>
                        <td>{{$bankdetails->bank_name ?? '-'}}</td>
                        <td>{{$bankdetails->account_no ?? '-'}}</td>
                        <td>{{$bankdetails->ifsc_code ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest->VendorDetails->pan ?? '-'}}</td>
                        <td>{{$payment_list->purchase_amount ?? '-'}}</td>
                        <td>{{$payment_list->advance ?? '-'}}</td>
                        <td>{{$payment_list->payment_date ?? '-'}}</td>
                        <td>{{$payment_list->balance ?? '-'}}</td>
                        <td>-</td>
                        <td>{{$payment_list->advance ?? '-'}}</td>
                        <td>HDFC</td>
                        <td>{{$payment_list->bank_refrence_no ?? '-'}}</td>
                        <td>{{$payment_list->balance ?? '-'}}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('js')
<script>
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