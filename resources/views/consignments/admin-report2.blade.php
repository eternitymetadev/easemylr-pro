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
                        <table id="admin-report2" class="table table-hover" style="width:100%">
                            <div class="btn-group relative">
                          
                                <!-- <button type="button" class="btn btn-warning" id="launch_model" data-toggle="modal" data-target="#exampleModal" disabled="disabled" style="font-size: 11px;">

                            Create DSR
                            </button> -->
                            </div>
                            <thead>
                                <tr>
                                        <th>Lr No</th>
                                        <th>Lr Date</th>
                                        <th>Month</th>
                                        <th>Regional Location</th>
                                        <th>Base Client</th>
                                        <th>Regional Client</th>
                                        <th>Consigner </th> 
                                        <th>Lr Status</th> 
                                        <th>Dispatch Date</th> 
                                        <th>Delivery Date</th>
                                        <th>Delivery Status</th>
                                        <th>TAT</th>
                                      
                                </tr>
                             </thead>
                             <tbody>
                                @foreach ($lr_data as $lr)
                                <?php if($lr->status == 0){ 
                                    $status = 'Cancel';
                                }else if($lr->status == 1){
                                    $status = 'Active';
                                }else if($lr->status == 2){
                                    $status = 'Unverified'; 
                                }else{
                                    $status = 'Unknown';
                                } 
                                /////
                                $date = $lr->consignment_date;
                                $newDate = date('F', strtotime($date));
                               ///////
                               if(!empty($lr->delivery_date)){
                               $start_date = strtotime($lr->consignment_date);
                               $end_date = strtotime($lr->delivery_date);
                               $tat = ($end_date - $start_date)/60/60/24;
                               }else{
                                $tat = '-';
                               }
                                ?>
                                <tr>
                                    <td>{{ $lr->id ?? "-"}}</td>
                                    <td>{{  Helper::ShowDayMonthYear($lr->consignment_date ?? "-" )}}</td>
                                    <td>{{ $newDate ?? "-"}}</td>
                                    <td>{{ $lr->locations_name ?? "-"}}</td>
                                    <td>{{ $lr->base_client_name ?? "-"}}</td>
                                    <td>{{ $lr->regional_client_name ?? "-"}}</td>
                                    <td>{{ $lr->consigner_nickname ?? "-"}}</td> 
                                    <td>{{ $status ?? "-"}}</td>
                                    <td>{{  Helper::ShowDayMonthYear($lr->consignment_date ?? "-" )}}</td>
                                    <td>{{  Helper::ShowDayMonthYear($lr->delivery_date ?? "-")}}</td>
                                    <td>{{ $lr->delivery_status ?? "-"}}</td>
                                    <td>{{ $tat ?? "-"}}</td>
                                
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
    $('#admin-report2').DataTable({
        "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        buttons: {
            buttons: [
                // { extend: 'copy', className: 'btn btn-sm' },
                // { extend: 'csv', className: 'btn btn-sm' },
                { extend: 'excel', className: 'btn btn-sm', title: '', },
                // { extend: 'print', className: 'btn btn-sm' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page _PAGE_ of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
            "sLengthMenu": "Results :  _MENU_",
        },

        "ordering": true,
        "paging": true,
        "pageLength": 80,

    });

</script>
@endsection