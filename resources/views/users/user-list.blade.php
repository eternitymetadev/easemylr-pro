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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">User List</a></li>
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
                        <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                        <div class="btn-group relative">
                        <a href="{{'users/create'}}" class="btn btn-primary pull-right" >Create User</a>
                        </div>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Login ID</th>
                                    <th>Password</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th style="display: none;">Password</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(count($data)>0) {
                                    foreach ($data as $key => $user) {  
                                ?> 
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ ucwords($user->name ?? "-")}}</td>
                                    <td>{{ $user->login_id ?? "-"}}</td>
                                    <td>{{ $user->user_password ?? "-"}}</td>
                                    <td>{{ $user->email ?? "-" }}</td>
                                    <td>{{ ucwords($user->UserRole->name ?? "-") }}</td>
                                    <td style="display: none;">{{ $user->user_password ?? "-" }}</td>
                                    <td>
                                        <a class="btn btn-primary" href="{{url($prefix.'/users/'.Crypt::encrypt($user->id).'/edit')}}" ><span><i class="fa fa-edit"></i></span></a>
                                        <a class="btn btn-primary" href="{{url($prefix.'/users/'.Crypt::encrypt($user->id))}}" ><span><i class="fa fa-eye"></i></span></a>
                                        <?php $authuser = Auth::user();
                                        if($authuser->role_id ==1) { ?>
                                            <button type="button" class="btn btn-danger delete_user" data-id="{{ $user->id }}" data-action="<?php echo URL::to($prefix.'/users/delete-user'); ?>">
                                                <span><i class="fa fa-trash"></i></span></button>
                                        <?php } ?>
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
@include('models.delete-user')
@endsection