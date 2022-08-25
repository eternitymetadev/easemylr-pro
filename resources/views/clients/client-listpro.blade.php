@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<style>
<style>
        .dt--top-section {
    margin:none;
}
div.relative {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 83px;
    height: 38px;
}
div.relat {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 240px;
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
    </style>
    </style>

    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="page-header">
                    <nav class="breadcrumb-one" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Clients</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Client List</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="widget-content widget-content-area br-6">
                    <div style="margin-left:9px;" class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                        <div class="ms-auto">
                        </div>
                    </div>
                    <div class="table-responsive mb-4 mt-4">
                        @csrf
                        <table id="clienttable" class="table table-hover get-datatable" style="width:100%">
                        <div class="btn-group relat">
                        <a href="{{'clients/create'}}" class="btn btn-primary pull-right" >Create Client</a>
                        <a href="{{'reginal-clients'}}" class="btn btn-primary pull-right" style="margin-left:7px;">Regional Client List</a>
                        </div>
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Base Client</th>
                                    <th>Regional Client</th>
                                    <th>Consigner</th>
                                    <th>Consignee</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($clients as $key => $value) { 
                                ?> 
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ ucwords($value->client_name ?? "-")}}</td>
                                    <td>
                                        <table>
                                            <!-- <tr>
                                                <td>Location</td>
                                                <td>Regional Client</td>
                                                <td>Consigner</td>
                                                <td>Consignee</td>
                                            </tr> -->
                                            @foreach ($value->RegClients as $key => $value)
                                                <tr>
                                                    <td>{{ $value->location->name }}</td>
                                                    <td>{{ $value->name }}</td>
                                                    <td>{{ Helper::regclientCoinsigner($value->id) }}</td>
                                                    <td>{{ Helper::regclientCoinsignee($value->id) }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                    
                                                                       
                                </tr>
                                <?php 
                                    } ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection