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
                <a class="btn btn-success ml-2 mt-3" href="{{ url($prefix.'/export-drswise-report') }}">Export
                    data</a>
                    @csrf
                    <table id="unverified-table" class="table table-hover" style="width:100%">

                </div>
                <thead>
                    <tr>
                        
                        <th>Sr No</th>
                        <th>Drs No</th>
                        <th>Date</th>
                        <th>Vehicle No</th>
                        <th>Vehicle Type</th>
                        <th>Purchase Amount</th>
                        <th>Transaction Id</th>
                        <th>Transaction Id Amount</th>
                        <th>Paid Amount</th>
                        <th>Clients</th>
                        <th>Locations</th>
                        <th>LRs No</th>
                        <th>No. of cases</th>
                        <th>Net Weight</th>
                        <th>Gross Wt</th>
                      
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($drswiseReports as $drswiseReport)
                        
                    <?php $i++; 
                     $date = date('d-m-Y',strtotime($drswiseReport->created_at));
                     $no_ofcases = Helper::totalQuantity($drswiseReport->drs_no);
                     $totlwt = Helper::totalWeight($drswiseReport->drs_no);
                     $grosswt = Helper::totalGrossWeight($drswiseReport->drs_no);
                    $lrgr = array();
                    $regnclt = array();
                    $vel_type = array();
                        foreach($drswiseReport->TransactionDetails as $lrgroup){
                               $lrgr[] =  $lrgroup->ConsignmentNote->id;
                               $regnclt[] = @$lrgroup->ConsignmentNote->RegClient->name;
                               $vel_type[] = @$lrgroup->ConsignmentNote->vehicletype->name;
                               $purchase = @$lrgroup->ConsignmentDetail->purchase_price;
                        }
                        $lr = implode('/', $lrgr);
                        $unique_regn = array_unique($regnclt);
                        $regn = implode('/', $unique_regn);

                        $unique_veltype = array_unique($vel_type);
                        $vehicle_type = implode('/', $unique_veltype);
                        $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $drswiseReport->transaction_id)->get();
                        $histrycount = count($trans_id);
                        
                        if($histrycount > 1){
                           $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance + $drswiseReport->PaymentHistory[1]->tds_deduct_balance;
                        }else{
                            $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance;
                        }

                    ?>
                    <tr>
                        <td>{{$i}}</td>
                        <td>DRS-{{$drswiseReport->drs_no}}</td>
                        <td>{{$date}}</td>
                        <td>{{$drswiseReport->vehicle_no}}</td>
                        <td>{{$vehicle_type}}</td>
                        <td>{{$purchase}}</td>
                        <td>{{$drswiseReport->transaction_id}}</td>
                        <td>{{$drswiseReport->total_amount}}</td>
                        <td>{{$paid_amt}}</td>
                        <td>{{$regn}}</td>
                        <td>{{@$drswiseReport->Branch->name}}</td>
                        <td>{{$lr}}</td>
                        <td>{{$no_ofcases}}</td>
                        <td>{{$totlwt}}</td>
                        <td>{{$grosswt}}</td>
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
            // {
            //     extend: 'excel',
            //     className: 'btn btn-sm',
            //     title: '',
            // },
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