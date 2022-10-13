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
                        <table id="admin-report1" class="table table-hover" style="width:100%">
                            <div class="btn-group relative">
                          
                                <!-- <button type="button" class="btn btn-warning" id="launch_model" data-toggle="modal" data-target="#exampleModal" disabled="disabled" style="font-size: 11px;">

                            Create DSR
                            </button> -->
                            </div>
                            <thead>
                                <tr>
                                        <th>Sr No</th>
                                        <th>Base Client</th>
                                        <th>Regional Client</th>
                                        <th>Consigner Nick Name</th>
                                        <th>Contact Person Name</th>
                                        <th>Mobile No.</th>
                                        <th>Pin Code</th> 
                                        <th>District</th> 
                                        <th>State</th> 
                                        <th>Consignee Nick Name</th>
                                        <th>Contact Person Name</th>
                                        <th>Mobile No.</th>
                                        <th>Pin Code</th> 
                                        <th>District</th> 
                                        <th>State</th> 
                                        
                                       
                                </tr>
                             </thead>
                            <tbody>
                                <?php $count=0; ?>
                                @foreach($adminrepo as $report)
                                <?php $count ++ ; ?> 
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>{{ $report->baseclient_name ?? "-" }}</td>
                                    <td>{{ $report->regional_clientname ?? "-" }}</td>
                                    <td>{{ $report->nick_name ?? "-" }}</td>
                                    <td>{{ $report->contact_name ?? "-" }}</td>
                                    <td>{{ $report->phone ?? "-" }}</td>
                                    <td>{{ $report->postal_code ?? "-" }}</td>
                                    <td>{{ $report->district ?? "-" }}</td>
                                    <td>{{ $report->consigner_state ?? "-" }}</td>
                                    <td>{{ $report->consignee_nick_name ?? "-" }}</td>
                                    <td>{{ $report->consignee_contact_name ?? "-" }}</td>
                                    <td>{{ $report->consignee_phone ?? "-" }}</td>
                                    <td>{{ $report->consignee_postal_code ?? "-" }}</td>
                                    <td>{{ $report->consignee_district ?? "-" }}</td>
                                    <td>{{ $report->consignee_state ?? "-" }}</td>

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
    $('#admin-report1').DataTable({
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