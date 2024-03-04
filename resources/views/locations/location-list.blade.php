@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <style>
        .dt--top-section {
            margin: 0;
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

    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Location List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <button type="button" class="btn btn-primary" id="add_location" data-toggle="modal"
                        data-target="#location-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Location
                </button>
            </div>
        </div>

        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4 px-3">
                @csrf
                <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Team Id</th>
                        <th>Series No</th>
                        <th>Email Id</th>
                        <th>Phone</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($locations) > 0)
                        @foreach($locations as $key => $value)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $value->id ?? '-' }}</td>
                                <td>{{ ucwords($value->name ?? '-') }}</td>
                                <td>{{ ucwords($value->nick_name ?? '-') }}</td>
                                <td>{{ $value->team_id ?? '-' }}</td>
                                <td>{{ $value->consignment_no ?? '-' }}</td>
                                <td>{{ $value->email ?? '-' }}</td>
                                <td>{{ $value->phone ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-content-center justify-content-center" style="gap: 6px">
                                        <a class="editIcon editlocation" href="javascript:void(0)"
                                           data-action="<?php echo URL::to($prefix . '/locations/get-location'); ?>"
                                           data-id="{{ $value->id }}" data-toggle="modal"
                                           data-target="#location-updatemodal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round" class="feather feather-edit">
                                                <path
                                                    d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path
                                                    d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <?php $authuser = Auth::user();
                                        if($authuser->role_id == 1) { ?>
                                        <a class="deleteIcon delete_location"
                                           data-id="{{ $value->id }}"
                                           data-action="<?php echo URL::to($prefix . '/locations/delete-location'); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round" class="feather feather-trash-2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('models.create-location')
    @include('models.update-location')
    @include('models.delete-location')
@endsection 
