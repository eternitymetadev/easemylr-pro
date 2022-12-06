@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <style>
        td p {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        p.consigner {
            border-radius: 6px;
            padding: 2px 6px;
            background: #f5f5f5;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        td p.consigner span {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td p.consigner span.legalName {
            font-weight: 700;
            font-size: 14px;
        }

        .textOverflow {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            white-space: nowrap;
        }

        .detailsBlock {
            padding: 1rem;
            gap: 0.5rem;
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
        }

        .contactDetails {
            min-width: 250px;
            flex: 1;
            border-radius: 12px;
            border: 1px solid;
        }

        .contactDetails p .detailKey {
            min-width: 50px;
        }

        .addressBlock {
            border-radius: 12px;
            background: #f9b80820;
        }

        .driverBankDetails {
            border-radius: 12px;
            border: 1px solid #83838360;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .driverBankDetails p, .driverPersonalDetails p {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            font-size: 14px;
            gap: 8px;
            margin-bottom: 4px;
        }

        .driverPersonalDetails p svg {
            height: 14px;
            width: 14px;
        }

        .driverBankDetails p .textHeading, .driverPersonalDetails p .textHeading {
            font-weight: 500;
        }

        .driverBankDetails p .textValue, .driverPersonalDetails p .textValue {
            font-weight: 700;
        }

        .driverPersonalDetails img {
            height: 90px;
            width: 90px;
            border-radius: 50vh;
            background: rgba(248, 183, 9, 0.16);
            margin-right: 2rem;
            margin-left: 1rem;
        }

        .licenceViewLink {
            color: #f8b709;
            cursor: pointer;
            transition: all 150ms ease-in-out;
        }

        .licenceViewLink:hover {
            color: #c69200;
            font-weight: bold;
        }

        /*for image upload*/
        .image_upload:hover .imageUploadInput {
            visibility: visible;

        }

        .image_upload img {
            border: 1px dashed;
            object-fit: cover !important;
        }

        .imageUploadInput {
            position: absolute;
            height: 100%;
            width: 100%;
            background-image: radial-gradient(black, transparent);
            color: #fff;
            top: 0;
            border-radius: 8px;
            visibility: hidden;
        }

        .imageUploadInput label {
            cursor: pointer;
        }

        /*.imageUploadInput:hover label {*/
        /*    visibility: visible;*/
        /*}*/
        .imageUploadInput label svg {
            color: #fff;
        }

        .form-row {
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 0 3px #83838360;
            margin-bottom: 1rem;
        }

        .form-row h6 {
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .appLoginDetailsBlock {
            width: 100%;
            background: #ffcf0026;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #ffcf00;
        }

        #toggleCreateUsernameView {
            color: #000;
            font-weight: 600;
            cursor: pointer;
        }
    </style>


    <div class="layout-px-spacing">

        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Driver List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <a href="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>"
                   class="downloadEx btn btn-primary pull-right"
                   data-action="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>"
                   download>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-download-cloud">
                        <polyline points="8 17 12 21 16 17"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                    </svg>
                    Excel
                </a>
                <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createDriverModal">
                    {{--                <a class="btn btn-primary" id="add_role" href="{{'consigners/create'}}">--}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Driver
                </button>
            </div>
        </div>

        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4" style="min-height: min(60vh, 600px)">
                @csrf
                <table id="drivertable" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <!-- <th>S No.</th> -->
                        <th>Driver Name</th>
                        <th>Driver Phone</th>
                        <th>Driver License Number</th>
                        <th>Licence Image</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{--    Create Driver Modal--}}
    <div class="modal fade" id="createDriverModal" tabindex="-1" role="dialog"
         aria-labelledby="createDriverModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDriverModalLabel">Create Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST" action="{{url($prefix.'/drivers')}}" id="createdriver">

                        <div class="form-row align-items-end" style="box-shadow: none">

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Phone<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                       placeholder="Phone"
                                       maxlength="10">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver License Number<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="license_number"
                                       placeholder="">
                            </div>
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="{{url("/assets/img/upload-img.png")}}"
                                         class="licenseshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="license_image">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-edit">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                </path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                </path>
                                            </svg>
                                        </label>
                                        <input type="file"
                                               class="form-control form-control-sm form-control form-control-sm-sm license_image"
                                               id="license_image" name="license_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label>Driveing Licence Image</label>

                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Shadow Details</h6>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Team Id</label>
                                <input type="text" class="form-control form-control-sm" name="team_id" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Fleet ID</label>
                                <input type="text" class="form-control form-control-sm" name="fleet_id" placeholder="">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Login Id</label>
                                <input type="text" class="form-control form-control-sm" name="login_id" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Password</label>
                                <input type="password" class="form-control form-control-sm" name="password"
                                       placeholder="">
                            </div>
                        </div>

                        <div class="form-group col-12 d-flex align-items-center" style="gap: 8px">
                            <input style="height: 1rem; width: 1rem" type="checkbox" class="" name="enableAppAccess"
                                   id="enableAppAccess"/>
                            <label for="enableAppAccess">Enable App Access to Driver</label>
                        </div>

                        <div id="enabledAppAccess" class="form-row" style="display: none">

                            <div class="col-12 p-2">
                                <div class="form-group mb-0">
                                    <label>Tagged Branch</label>
                                    <select id="branch_id" class="form-control tagging" multiple="multiple" name="branch_id[]">
                                        <option disabled>Select</option>
                                        <option>None</option>
                                        <option value="AL">Alabama</option>
                                        <option value="dWY">Wyoming</option>
                                        <option value="fY">Wyoming</option>
                                        <option value="WdfY">Wyoming</option>
                                    </select>
                                </div>
                            </div>

                            <div class="appLoginDetailsBlock">
                                @if(true)
                                    <div class="d-flex flex-wrap justify-content-between align-items-center"
                                         style="gap: 1rem">
                                        <p id="createUsernameInfo"
                                           style="font-size: 18px;font-weight: 600; margin-bottom: 0;">
                                            No username & password created yet.
                                        </p>
                                        <span id="toggleCreateUsernameView">
                                            <span id="toggleCreateUsernameLabel">Create</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-chevron-down">
                                                <polyline points="6 9 12 15 18 9"></polyline>
                                            </svg>
                                        </span>
                                    </div>
                                @endif

                                <div id="createUsernameView"
                                     class="mx-auto mt-2 row align-items-center justify-content-between"
                                     style="width: 100%; @if(true)display: none;@endif">
                                    <div class="form-group col-md-4">
                                        <label for="nickName">Nick Name</label>
                                        <input class="form-control form-control-sm" name="nickName" id="nickName"
                                               placeholder="Driver Nick Name"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="loginId">App Login ID</label>
                                        <input class="form-control form-control-sm" name="loginId" id="loginId"
                                               placeholder="Username"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="appPassword">Password</label>
                                        <input class="form-control form-control-sm" name="appPassword" id="appPassword"
                                               placeholder="password"/>
                                    </div>
                                </div>
                            </div>

                        </div>


                        {{--                        <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>--}}
                        {{--                        <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>--}}


                        <div class="col-md-12 d-flex justify-content-end align-items-center"
                             style="gap: 1rem; margin-top: 3rem;">
                            <button type="button" style="width: 80px" class="btn btn-outline-primary"
                                    data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" style="width: 80px" class="btn btn-primary">
                                Submit
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{--    view Driver Modal--}}
    <div class="modal fade" id="driverDetailsModal" tabindex="-1" role="dialog"
         aria-labelledby="driverDetailsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="driverDetailsModalLabel">Driver Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4" style="min-height: 320px">

                    <div class="driverPersonalDetails mb-5 d-flex align-items-center justify-content-between flex-wrap"
                         style="width: 100%;">
                        <img src="s" alt="driver photo"/>

                        <div class="flex-grow-1">
                            <p>
                                <span class="textValue" style="font-size: 1rem">Driver Name</span>
                            </p>
                            <p>
                                <span class="textValue">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-phone">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                    +91-9879871243
                                </span>
                            </p>
                            <p>
                                <span class="textHeading">Licence No.:</span>
                                <span class="textValue">HR2022029830TGF</span>
                                <span class="licenceViewLink">View</span>
                            </p>
                        </div>

                    </div>

                    <label class="ml-2">Bank Details</label>
                    <div class="driverBankDetails">
                        <p style="width: 100%">
                            <span class="textHeading">Account Holder Name:</span>
                            <span class="textValue">Narayan Swami</span>
                        </p>
                        <p>
                            <span class="textHeading">Account No.:</span>
                            <span class="textValue">0363550000213</span>
                        </p>
                        <p>
                            <span class="textHeading">IFSC:</span>
                            <span class="textValue">SBIN000463</span>
                        </p>
                        <p>
                            <span class="textHeading">Bank Name:</span>
                            <span class="textValue">State Bank of India</span>
                        </p>
                        <p style="width: 100%">
                            <span class="textHeading">Branch:</span>
                            <span class="textValue">Jalandhar, Punjab</span>
                        </p>


                    </div>

                </div>
                <div class="modal-footer col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                    <button type="button" style="width: 80px" class="btn btn-outline-primary"
                            onclick="clickEditDriverModal()">
                        Edit
                    </button>
                    <button type="button" style="width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    {{--    Edit Driver Modal--}}
    <div class="modal fade" id="editDriverModal" tabindex="-1" role="dialog"
         aria-labelledby="editDriverModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDriverModalLabel">Edit Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST" action="{{url($prefix.'/drivers/update-driver')}}"
                          id="updatedriver">

                        @csrf
                        {{--                        <input type="hidden" name="driver_id" value="{{$getdriver->id}}">--}}

                        <div class="form-row align-items-end" style="box-shadow: none">

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Phone<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                       placeholder="Phone"
                                       maxlength="10">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver License Number<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="license_number"
                                       placeholder="">
                            </div>
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="{{url("/assets/img/upload-img.png")}}"
                                         class="licenseshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="license_image">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-edit">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                </path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                </path>
                                            </svg>
                                        </label>
                                        <input type="file"
                                               class="form-control form-control-sm form-control form-control-sm-sm license_image"
                                               id="license_image" name="license_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label>Driveing Licence Image</label>

                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Bank Details</h6>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Account Holder Name</label>
                                <input type="text" class="form-control form-control-sm" name="account_holdername"
                                       placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Account No</label>
                                <input type="text" class="form-control form-control-sm" name="account_number"
                                       placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">IFSC</label>
                                <input type="text" class="form-control form-control-sm" name="ifsc" placeholder="">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Bank Name</label>
                                <input type="text" class="form-control form-control-sm" name="bank_name" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Branch Name</label>
                                <input type="text" class="form-control form-control-sm" name="branch_name"
                                       placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Shadow Details</h6>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Team Id</label>
                                <input type="text" class="form-control form-control-sm" name="team_id" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Fleet ID</label>
                                <input type="text" class="form-control form-control-sm" name="fleet_id" placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Login Details</h6>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Login Id</label>
                                <input type="text" class="form-control form-control-sm" name="login_id" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Password</label>
                                <input type="password" class="form-control form-control-sm" name="password"
                                       placeholder="">
                            </div>
                        </div>


                        {{--                        <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>--}}
                        {{--                        <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>--}}


                        <div class="col-md-12 d-flex justify-content-end align-items-center"
                             style="gap: 1rem; margin-top: 3rem;">
                            <button type="button" style="width: 80px" class="btn btn-outline-primary"
                                    data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" style="width: 80px" class="btn btn-primary">
                                Update
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{--    DL view modal--}}
    <div class="modal fade" id="dlViewModal" tabindex="-1" role="dialog"
         aria-labelledby="dlViewModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dlViewModalLabel">Driving Licence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="min-height: 240px">
                    <div class="col-md-12 bg-black">
                        <img id="view-src"
                             src="view_file_name"
                             alt="sample image"
                             style="width: 100%; max-height: 300px; border-radius: 12px;"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-outline-primary" href="view_file_name" target="_blank"
                       style="border-radius: 8px; display: flex;align-items: center;gap: 6px;">
                        Open in New Tab
                        <svg xmlns="http://www.w3.org/2000/svg" width="4" height="14"
                             viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="feather feather-external-link" style="height: 14px; width: 14px">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </div>



    @include('models.delete-driver')
@endsection
@section('js')
    <script>
        var table = $('#drivertable').DataTable({
            processing: true,
            serverSide: true,

            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
                "sLengthMenu": "Results :  _MENU_",
            },

            "stripeClasses": [],
            "pageLength": 30,
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered');
            },

            columns: [
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone', orderable: false},
                {data: 'license_number', name: 'license_number', orderable: false},
                {data: 'licence', name: 'licence', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}

            ]
        });

        function clickEditDriverModal() {
            $('#driverDetailsModal').modal('hide');
            setTimeout(() => {
                $('#editDriverModalIcon').click();
            }, 400)
        }

    </script>

    {{--    for create driver--}}
    <script>
        $(document).on("click", ".remove_licensefield", function (e) { //user click on remove text
            var getUrl = window.location;
            var baseurl = getUrl.origin + '/' + getUrl.pathname.split('/')[0];
            var imgurl = baseurl + 'assets/img/upload-img.png';

            $(this).parent().children(".image_upload").children().attr('src', imgurl);
            $(this).parent().children("input").val('');
            $(this).css("display", "none");
        });

        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.licenseshow').attr('src', e.target.result);
                    $(".remove_licensefield").css("display", "block");
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on("change", '.license_image', function (e) {
            var fileName = this.files[0].name;
            // $(this).parent().parent().find('.file_graph').text(fileName);

            readURL1(this);
        });

        $('#enableAppAccess').click(function () {
            $('.taggedBranches').select2();

            if (this.checked) {
                $('#enabledAppAccess').show();
            } else {
                $('#enabledAppAccess').hide();
            }
        });

        $('#toggleCreateUsernameView').click(function () {
            $('#createUsernameView').toggle();
            if ($('#createUsernameView').is(":visible")) {
                $('#toggleCreateUsernameLabel').html('Cancel');
                $('#createUsernameInfo').html('Create Username & Password');
                $('#createUsernameView').children().children('input').attr('required', true);
            } else {
                $('#toggleCreateUsernameLabel').html('Create');
                $('#createUsernameInfo').html('No username & password created yet.');
                $('#createUsernameView').children().children('input').attr('required', false);
            }
        });

        // $(document).ready(function() {
        //     $('.taggedBranches').select2();
        // });
        //
        // $(document).ready(function() {
        //     $('.js-example-basic-multiple').select2();
        // });

    </script>

@endsection
