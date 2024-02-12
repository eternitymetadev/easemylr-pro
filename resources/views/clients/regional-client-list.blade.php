@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<style>
.dt--top-section {
    margin: none;
}

div.relative {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 83px;
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
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{'clients'}}">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Regional
                                Client List</a></li>
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
                            <a href="{{'create-regional-client'}}" class="btn btn-primary pull-right"
                                style="margin-left:7px;">Create Regional Client</a>
                        </div>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Base Client Name</th>
                                <th>Location Id</th>
                                <th>Gst</th>
                                <th style="display:none">Email</th>
                                <th style="display:none">Secondary Email</th>
                                <th style="">Order Email Status</th>
                                <th style="">MIS Report Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(count($regclients)>0) {
                                    foreach ($regclients as $key => $value) {  
                                ?>
                            <tr>
                                <td>{{ $value->id ?? "-" }}</td>
                                <td>{{ ucwords($value->name ?? "-")}}
                                    {{-- <a
                                        href="{{url($prefix.'/'.$segment.'/add-regclient-detail/'.Crypt::encrypt($value->id))}}">{{ ucwords($value->name ?? "-")}}</a> --}}
                                </td>
                                <td>{{ ucwords($value->BaseClient->client_name ?? "-")}}</td>
                                <td>{{$value->Location->name ?? "-"}}</td>
                                <td>{{$value->gst_no ?? "-"}}</td>
                                <td style="display:none">{{$value->email ?? "-"}}</td>
                                <td style="display:none">{{$value->secondary_email ?? "-"}}</td>
                                <?php if($value->is_email_sent == 1){
                                    $order_status = 'Yes';
                                 }else{
                                    $order_status = 'No';
                                 } ?>
                                <td style="">{{$order_status}}</td>
                                <?php if($value->is_misemail == 1){
                                    $mis_status = 'Yes';
                                 }else{
                                    $mis_status = 'No';
                                 } ?>
                                <td style="">{{$mis_status }}</td>
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{url($prefix.'/regclient-detail/'.Crypt::encrypt($value->id).'/edit')}}"><span><i
                                                class="fa fa-edit" aria-hidden="true"></i> Edit<span></a>
                                </td>
                            </tr>
                            <?php 
                                    }
                                } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection