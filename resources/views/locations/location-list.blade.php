@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
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
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Branch Locations</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Location List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="table-responsive mb-4 mt-4">
                    @csrf
                    <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                    <div class="btn-group relative">
                            <a class="btn-primary btn-cstm btn w-100" id="add_location" data-toggle="modal" data-target="#location-modal" href="#" style="font-size: 12px; padding: 8px 0px;"><span><i class="fa fa-plus"></i> Add New</span></a>
                        </div>
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>Team Id</th>
                                <th>Series No</th>
                                <th>Email Id</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(count($locations)>0) {
                                foreach ($locations as $key => $value) {  
                            ?> 
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ ucwords($value->name ?? '-') }}</td>
                                <td>{{ ucwords($value->nick_name ?? '-') }}</td>
                                <td>{{ $value->team_id ?? '-' }}</td>
                                <td>{{ $value->consignment_no ?? '-' }}</td>
                                <td>{{ $value->email ?? '-' }}</td>
                                <td>{{ $value->phone ?? '-' }}</td>
                                <td>
                                    <a class="btn btn-primary editlocation" href="javascript:void(0)" data-action = "<?php echo URL::to($prefix.'/locations/get-location'); ?>" data-id="{{ $value->id }}" data-toggle="modal" data-target="#location-updatemodal"><span><span><i class="fa fa-edit"></i></span></span></a>
                                    <?php $authuser = Auth::user();
                                    if($authuser->role_id ==1) { ?>
                                        <button type="button" class="btn btn-danger delete_location" data-id="{{ $value->id }}" data-action="<?php echo URL::to($prefix.'/locations/delete-location'); ?>">
                                            <span><i class="fa fa-trash"></i></span></button>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('models.create-location')
@include('models.update-location')
@include('models.delete-location')
@endsection
