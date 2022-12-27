@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <style>
        .userIcon {
            background: #ededed;
            border-radius: 50vh;
            padding: 2px;
            outline: 1px solid;
            color: #adadad;
            outline-offset: 2px;
        }

        span.select2.select2-container.mb-4.select2-container--default {
            margin-bottom: 0 !important;
        }

        .detailsBlock p {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 14px;
            line-height: 17px;
            font-weight: 600;
            margin-bottom: 0;
        }

        .detailsBlock p .detailKey {
            font-size: 14px;
            line-height: 17px;
            font-weight: 400;
            flex: 1;
        }

        .detailValue {
            flex: 2;
        }


    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">User List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createUser">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    User
                </button>
            </div>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">
                @csrf
                <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Login ID</th>
                        <th>Password</th>
                        <th>Email</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(count($data) > 0) {
                    foreach ($data as $key => $user) {
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 8px">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                     viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-user userIcon">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <p class="mb-0 textWrap"
                                   style="max-width: 240px;font-size: 14px;color: black;font-weight: 600;padding: 4px 0;line-height: 19px;">
                                    {{ ucwords($user->name ?? "-")}}<br/>
                                    <span
                                        style="font-size: 12px;padding: 0px 6px; border-radius: 4px; background: #d4ffd8;">
                                                {{ ucwords($user->UserRole->name ?? "-") }}
                                            </span>
                                </p>
                            </div>
                        </td>
                        <td>{{ $user->login_id ?? "-"}}</td>
                        <td>{{ $user->user_password ?? "-"}}</td>
                        <td>{{ $user->email ?? "-" }}</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                <button class="edit editIcon" style="border: none" id="editUserIcon"
                                        data-toggle="modal" data-target="#updateUser">
{{--                                <a class="edit editIcon" id="editUserIcon"--}}
{{--                                   href="{{url($prefix.'/users/'.Crypt::encrypt($user->id).'/edit')}}">--}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round"
                                         class="feather feather-edit">
                                        <path
                                            d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path
                                            d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <button class="view viewIcon" style="border: none"
                                        data-toggle="modal" data-target="#viewUser">
                                    {{--                                   href="{{url($prefix.'/users/'.Crypt::encrypt($user->id))}}">--}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round"
                                         class="feather feather-eye">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                                <?php $authuser = Auth::user();?>
                                @if($authuser->role_id == 1)
                                    <button type="button" class="deleteIcon delete delete_user"
                                            data-id="{{ $user->id }}" style="border: none"
                                            data-action="<?php echo URL::to($prefix . '/users/delete-user'); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round"
                                             class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <?php }} ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    @include('models.delete-user')


    {{--modal for create user--}}
    <div class="modal fade" id="createUser" tabindex="-1" role="dialog" aria-labelledby="createUserLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserLabel">Create User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="general_form px-2" method="POST" action="{{url($prefix.'/users')}}" id="createuser">
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Login ID<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="login_id" id="login_id"
                                       placeholder="Login ID">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlSelect1">Select Role</label>
                                <select name="role_id" class="form-control" id="role_id">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($getroles) > 0) {
                                    foreach ($getroles as $key => $getrole) {
                                    ?>
                                    <option value="{{ $getrole->id }}">{{ucwords($getrole->name)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Email Address<span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Password<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="password" id="password"
                                       placeholder="Password">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Phone</label>
                                <input type="text" class="form-control mbCheckNm" name="phone" id="phone" placeholder=""
                                       maxlength="10">
                            </div>

                            <div class="col-md-4 form-group baseclient" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Base Client</label>
                                <select class="form-control" id="baseclient_id" name="baseclient_id">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($baseclients) > 0) {
                                    foreach ($baseclients as $key => $baseclient) {
                                    ?>
                                    <option value="{{ $baseclient->id }}">{{ucwords($baseclient->client_name)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group singleLocation">
                                <label for="exampleFormControlSelect1">Select Location</label>
                                <select class="form-control" id="branch_id" name="branch_id[]">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($branches) > 0) {
                                    foreach ($branches as $key => $branch) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group multiLocation" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Location</label>
                                <select class="form-control tagging" multiple="multiple" name="branch_id[]">
                                    <option value="" disabled>Select</option>
                                    <?php
                                    if(count($branches) > 0) {
                                    foreach ($branches as $key => $branch) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group selectClient" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Regional Clients</label>
                                <select class="form-control tagging" multiple="multiple" name="regionalclient_id[]"
                                        id="select_regclient">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($branches) > 0) {
                                    foreach ($branches as $key => $branch) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <h4 class="mb-3">Permissions</h4>
                                <div class="permis">
                                    <div class="row justify-content-between align-items-center p-3"
                                         style="background: #f0f0f0; border-radius: 12px">
                                        <div class="checkbox px-0 col-12 selectAll">
                                            <input id="ckbCheckAll" type="checkbox">
                                            <label for="ckbCheckAll" class="check-label">All</label>
                                            <span class="checkmark"></span>
                                        </div>
                                        @if(count($getpermissions) > 0)
                                            @foreach ($getpermissions as $key => $getpermission)
                                                <div class="checkbox mr-2">
                                                    <input type="checkbox" name="permisssion_id[]"
                                                           id="{{ $getpermission->id }}"
                                                           value="{{ $getpermission->id }}" class="chkBoxClass"/>
                                                    <label class="check-label" for="{{ $getpermission->id }}">
                                                        {{ucfirst($getpermission->name)}}
                                                    </label>
                                                    <span class="checkmark"></span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex justify-content-end align-items-center p-3" style="gap: 1rem;">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" style="min-width: 120px" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--modal for edit user--}}
    <div class="modal fade" id="updateUser" tabindex="-1" role="dialog" aria-labelledby="updateUserLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateUserLabel">Update User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="general_form px-2" method="POST" action="{{url($prefix.'/users')}}" id="updateuser">
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Login ID<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="login_id" id="login_id"
                                       placeholder="Login ID">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlSelect1">Select Role</label>
                                <select name="role_id" class="form-control" id="role_id">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($getroles) > 0) {
                                    foreach ($getroles as $key => $getrole) {
                                    ?>
                                    <option value="{{ $getrole->id }}">{{ucwords($getrole->name)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Email Address<span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Password<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="password" id="password"
                                       placeholder="Password">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="exampleFormControlInput2">Phone</label>
                                <input type="text" class="form-control mbCheckNm" name="phone" id="phone" placeholder=""
                                       maxlength="10">
                            </div>

                            <div class="col-md-4 form-group baseclient" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Base Client</label>
                                <select class="form-control" id="baseclient_id" name="baseclient_id">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($baseclients) > 0) {
                                    foreach ($baseclients as $key => $baseclient) {
                                    ?>
                                    <option value="{{ $baseclient->id }}">{{ucwords($baseclient->client_name)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group singleLocation">
                                <label for="exampleFormControlSelect1">Select Location</label>
                                <select class="form-control" id="branch_id" name="branch_id[]">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($branches) > 0) {
                                    foreach ($branches as $key => $branch) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group multiLocation" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Location</label>
                                <select class="form-control tagging" multiple="multiple" name="branch_id[]">
                                    <option value="" disabled>Select</option>
                                    <?php
                                    if(count($branches) > 0) {
                                    foreach ($branches as $key => $branch) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group selectClient" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Regional Clients</label>
                                <select class="form-control tagging" multiple="multiple" name="regionalclient_id[]"
                                        id="select_regclient">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($branches) > 0) {
                                    foreach ($branches as $key => $branch) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <h4 class="mb-3">Permissions</h4>
                                <div class="permis">
                                    <div class="row justify-content-between align-items-center p-3"
                                         style="background: #f0f0f0; border-radius: 12px">
                                        <div class="checkbox px-0 col-12 selectAll">
                                            <input id="ckbCheckAll" type="checkbox">
                                            <label for="ckbCheckAll" class="check-label">All</label>
                                            <span class="checkmark"></span>
                                        </div>
                                        @if(count($getpermissions) > 0)
                                            @foreach ($getpermissions as $key => $getpermission)
                                                <div class="checkbox mr-2">
                                                    <input type="checkbox" name="permisssion_id[]"
                                                           id="{{ $getpermission->id }}"
                                                           value="{{ $getpermission->id }}" class="chkBoxClass"/>
                                                    <label class="check-label" for="{{ $getpermission->id }}">
                                                        {{ucfirst($getpermission->name)}}
                                                    </label>
                                                    <span class="checkmark"></span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex justify-content-end align-items-center p-3" style="gap: 1rem;">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" style="min-width: 120px" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--modal for view user--}}
    <div class="modal fade" id="viewUser" tabindex="-1" role="dialog" aria-labelledby="viewUserLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserLabel">View User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between flex-wrap detailsBlock" style="gap: 6px">
                        <p>
                            <span class="detailKey">Name: </span>
                            <span class="detailValue text-uppercase">FRONTIER AGROTECH</span>
                        </p>
                        <p>
                            <span class="detailKey">Role: </span>
                            <span class="detailValue text-uppercase">Branch Manager</span>
                        </p>
                        <p>
                            <span class="detailKey">Login Id: </span>
                            <span class="detailValue text-capitalize">mohali@demo</span>
                        </p>
                        <p>
                            <span class="detailKey">Phone: </span>
                            <span class="detailValue text-uppercase">+91-87652342847</span>
                        </p>
                        <p>
                            <span class="detailKey">Email: </span>
                            <span class="detailValue text-capitalize">mohali@demo.com</span>
                        </p>
                        <p>
                            <span class="detailKey">Passowrd: </span>
                            <span class="detailValue text-capitalize">Demo@123</span>
                        </p>
                        <p>
                            <span class="detailKey">Loaction: </span>
                            <span class="detailValue text-capitalize">Loaction 1, Location 2, Location 3</span>
                        </p>

                        <p>
                            <span class="detailKey">Permissions: </span>
                            <span class="detailValue text-capitalize">Permission 1, Permission 2, Permission 3</span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center mt-3 pt-3"
                     style="gap: 1rem;">
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                            onclick="closeUserDetaislModal()" >
                        Edit
                    </button>
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                            data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        //multiple select //
        var ss = $(".basic").select2({
            tags: true,
        });
        //

        $('#role_id').change(function () {
            var role_id = $(this).val();
            var checkbox = $('.chkBoxClass').val();

            if (role_id == 1) {            //role_id = 1 for Admin
                $('.multiLocation').hide();
                $('.singleLocation').show();
                $('.selectClient').hide();
                $('.baseclient').hide();

                $('#ckbCheckAll').attr('checked', true);
                $('.chkBoxClass[value="1"]').prop('checked', true)
                $('.chkBoxClass[value="2"]').prop('checked', true)
                $('.chkBoxClass').prop('checked', true);
            } else if (role_id == 2) {     //role_id = 2 for Branch Manager
                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', true)
                $('.chkBoxClass[value="1"]').prop('checked', false)
                $('.chkBoxClass[value="2"]').prop('checked', false)

                $('.multiLocation').hide();
                $('.singleLocation').show();
                $('.selectClient').hide();
                $('.baseclient').hide();
            } else if (role_id == '') {
                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', false)
            } else if (role_id == 3) {            //role_id = 3 for regional manager
                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', true)
                $('.chkBoxClass[value="1"]').prop('checked', false)
                $('.chkBoxClass[value="2"]').prop('checked', false)

                $('.multiLocation').show();
                $('.singleLocation').hide();
                $('.selectClient').hide();
                $('.baseclient').hide();
            } else if (role_id == 4) {            //role_id = 4 for branch User
                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', true)
                $('.chkBoxClass[value="1"]').prop('checked', false)
                $('.chkBoxClass[value="2"]').prop('checked', false)

                $('.selectClient').show();
                $('.singleLocation').show();
                $('.multiLocation').hide();
                $('.baseclient').hide();
            } else if (role_id == 6) {            //role_id = 6 for client account
                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', false)
                $('.chkBoxClass[value="7"]').prop('checked', true)

                $('.baseclient').show();
                $('.selectClient').hide();
                $('.singleLocation').hide();
                $('.multiLocation').hide();
            } else if (role_id == 7) {            //role_id = 7 for client user
                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', false)
                $('.chkBoxClass[value="7"]').prop('checked', true)

                $('.selectClient').show();
                $('.singleLocation').show();
                $('.multiLocation').hide();
                $('.baseclient').hide();
            } else if (role_id == 5) {
                $('.multiLocation').show();
                $('.singleLocation').hide();
                $('.selectClient').hide();
                $('.baseclient').hide();

                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', true)
                $('.chkBoxClass[value="1"]').prop('checked', false)
                $('.chkBoxClass[value="2"]').prop('checked', false)
            } else {
                $('.multiLocation').hide();
                $('.singleLocation').show();
                $('.selectClient').hide();
                $('.baseclient').hide();

                $('#ckbCheckAll').attr('checked', false);
                $('.chkBoxClass').prop('checked', true)
                $('.chkBoxClass[value="1"]').prop('checked', false)
                $('.chkBoxClass[value="2"]').prop('checked', false)
            }
        });

        $('#branch_id').change(function () {
            $('#select_regclient').empty();
            let branch_id = $(this).val();
            $.ajax({
                type: 'get',
                url: APP_URL + '/get_regclients',
                data: {branch_id: branch_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (res) {
                    console.log(res);
                    if (res.data) {
                        $('#select_regclient').append('<option value="">Select regional client</option>');
                        $.each(res.data, function (key, value) {
                            $('#select_regclient').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                }
            });
        });

        function closeUserDetaislModal() {
            $('#viewUser').modal('hide');
            setTimeout(()=>{
                document.getElementById('editUserIcon').click();
            }, 400)
        }
    </script>
@endsection
