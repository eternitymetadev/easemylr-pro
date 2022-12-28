@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <style>
        .wrapText {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

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

        .vehicleMoreDetails {
            border-radius: 12px;
            border: 1px solid #83838360;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .vehicleMoreDetails p, .vehicleDetails p {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            font-size: 14px;
            gap: 8px;
            margin-bottom: 4px;
        }

        .vehicleDetails p svg {
            height: 14px;
            width: 14px;
        }

        .vehicleMoreDetails p .textHeading, .vehicleDetails p .textHeading {
            font-weight: 500;
        }

        .vehicleMoreDetails p .textValue, .vehicleDetails p .textValue {
            font-weight: 700;
        }

        .vehicleDetails img {
            height: 90px;
            width: 150px;
            border-radius: 8px;
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
    </style>


    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Vehicle List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <a href="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>"
                   class="downloadEx btn btn-primary pull-right"
                   data-action="<?php echo URL::to($prefix . 'vehicles/export/excel'); ?>" download>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-download-cloud">
                        <polyline points="8 17 12 21 16 17"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                    </svg>
                    Excel
                </a>
                <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createVehicleModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Vehicle
                </button>
            </div>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4" style="min-height: min(60vh, 600px)">
                @csrf
                <table id="vehicletable" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>Vehicle Number</th>
                        <th>Reg. Date</th>
                        <th>Manufacture</th>
                        <th>Make</th>
                        <th>Body Type</th>
                        <th>Loading Cap.</th>
                        <th>RC Image</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{--    Create Vehicle Modal--}}
    <div class="modal fade" id="createVehicleModal" tabindex="-1" role="dialog"
         aria-labelledby="createVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createVehicleModalLabel">Add Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST"
                          action="{{url($prefix.'/vehicles')}}"
                          id="createvehicle">

                        <div class="form-row align-items-end" style="box-shadow: none">
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="{{url("/assets/img/upload-img.png")}}"
                                         class="rcshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="rc_image">
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
                                               class="form-control form-control-sm form-control form-control-sm-sm rc_image"
                                               id="rc_image" name="rc_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label class="text-center" style="width: 100%">RC Image</label>

                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Registration No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="regn_no" name="regn_no" placeholder=""
                                       maxlength="12">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Engine No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="engine_no" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Chassis No.</label>
                                <input type="text" class="form-control" name="chassis_no" placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Vehicle Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Manufacturer<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mfg" placeholder="Mahindra, Tata, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Make<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="make"
                                       placeholder="407, Supro Maxi, Truck, Pickup, Ace, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Body Type</label>
                                <select class="form-control" name="body_type">
                                    <option value="Container">Container</option>
                                    <option value="Open Body">Open Body</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Gross Vehicle Weight</label>
                                <input type="text" class="form-control" id="gross_vehicle_weight"
                                       name="gross_vehicle_weight" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Unladen Weight</label>
                                <input type="text" class="form-control" id="unladen_weight" name="unladen_weight"
                                       placeholder="" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Tonnage Capacity</label>
                                <input type="text" class="form-control" id="tonnage_capacity" name="tonnage_capacity"
                                       value="" placeholder="" readonly>
                            </div>
                        </div>

                        <div class="form-row mb-0">
                            <h6 class="col-12">Registration Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">State(Regd)</label>
                                <select class="form-control" name="state_id">
                                    <option value="">Select</option>
                                    {{--                                    @if(count($states) > 0)--}}
                                    {{--                                        @foreach($states as $key => $state)--}}
                                    {{--                                            <option value="{{ $key }}">{{ucwords($state)}}</option>--}}
                                    {{--                                        @endforeach--}}
                                    {{--                                    @endif--}}
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Regn. Date</label>
                                <input type="date" class="form-control" name="regndate" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Hypothecation</label>
                                <input type="text" class="form-control" name="hypothecation"
                                       placeholder="Name of Financer | N/A">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Ownership</label>
                                <select class="form-control" name="ownership">
                                    <option value="Self Owned">Self Owned</option>
                                    <option value="Company Owned">Company Owned</option>
                                    <option value="Transporter Owned">Transporter Owned</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Mobile No.</label>
                                <input type="text" class="form-control" name="owner_phone" placeholder="">
                            </div>
                        </div>


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

    {{--    view Vehicle Modal--}}
    <div class="modal fade" id="vehicleDetailsModal" tabindex="-1" role="dialog"
         aria-labelledby="vehicleDetailsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 700px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehicleDetailsModalLabel">Vehicle Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4" style="min-height: 320px">

                    <div class="vehicleDetails mb-3 d-flex align-items-center justify-content-between flex-wrap"
                         style="width: 100%;">
                        <img src="s" alt="RC photo"/>

                        <div class="flex-grow-1">
                            <p>
                                <span class="textHeading">Registration No:</span>
                                <span class="textValue">HR20AJ7830</span>
                            </p>
                            <p>
                                <span class="textHeading">Registration Date:</span>
                                <span class="textValue">27-07-2017</span>
                            </p>
                            <p>
                                <span class="textHeading">Engine No:</span>
                                <span class="textValue">R32456DC</span>
                            </p>
                            <p>
                                <span class="textHeading">Chassis No:</span>
                                <span class="textValue">REWDCV5632456DC</span>
                            </p>
                        </div>

                    </div>

                    <div class="vehicleMoreDetails">
                        <p style="width: 100%">
                            <span class="textHeading">Regd. State :</span>
                            <span class="textValue text-uppercase">Haryana</span>
                        </p>
                        <p>
                            <span class="textHeading">Ownership:</span>
                            <span class="textValue">Transporter Owned</span>
                        </p>
                        <p>
                            <span class="textHeading">Owner Name:</span>
                            <span class="textValue">Owner Fullname</span>
                        </p>
                        <p>
                            <span class="textHeading">Owner Mobile:</span>
                            <span class="textValue">+91-8989876909</span>
                        </p>
                        <p>
                            <span class="textHeading">Hypothecation:</span>
                            <span class="textValue">Bajaj Finserve</span>
                        </p>
                    </div>

                    <div class="vehicleMoreDetails mt-4">
                        <p style="width: 100%">
                            <span class="textHeading">Manufacturar:</span>
                            <span class="textValue text-uppercase">TATA MOTORS LIMITED</span>
                        </p>
                        <p style="min-width: 40%; flex: 1">
                            <span class="textHeading">Model:</span>
                            <span class="textValue">LPT 3118</span>
                        </p>
                        <p>
                            <span class="textHeading">Body Type:</span>
                            <span class="textValue">Open Body</span>
                        </p>
                        <div class="d-flex flex-wrap justify-content-between align-items-center flex-grow-1">
                            <p>
                                <span class="textHeading">Gross Weight:</span>
                                <span class="textValue">3500kg</span>
                            </p>
                            <p>
                                <span class="textHeading">Unladen Weight:</span>
                                <span class="textValue">1500kg</span>
                            </p>
                            <p>
                                <span class="textHeading">Tonnage Weight:</span>
                                <span class="textValue">2000kg</span>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                    <button type="button" style="width: 80px" class="btn btn-outline-primary" onclick="clickEditDriverModal()">
                        Edit
                    </button>
                    <button type="button" style="width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    {{--    Edit Vehicle Modal--}}
    <div class="modal fade" id="editVehicleModal" tabindex="-1" role="dialog"
         aria-labelledby="editVehicleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleModalLabel">Edit Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST"
                          action="{{url($prefix.'/vehicles')}}"
                          id="createvehicle">

                        <div class="form-row align-items-end" style="box-shadow: none">
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="{{url("/assets/img/upload-img.png")}}"
                                         class="rcshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="rc_image">
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
                                               class="form-control form-control-sm form-control form-control-sm-sm rc_image"
                                               id="rc_image" name="rc_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label class="text-center" style="width: 100%">RC Image</label>

                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Registration No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="regn_no" name="regn_no" placeholder=""
                                       maxlength="12">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Engine No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="engine_no" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Chassis No.</label>
                                <input type="text" class="form-control" name="chassis_no" placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Vehicle Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Manufacturer<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mfg" placeholder="Mahindra, Tata, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Make<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="make"
                                       placeholder="407, Supro Maxi, Truck, Pickup, Ace, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Body Type</label>
                                <select class="form-control" name="body_type">
                                    <option value="Container">Container</option>
                                    <option value="Open Body">Open Body</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Gross Vehicle Weight</label>
                                <input type="text" class="form-control" id="gross_vehicle_weight"
                                       name="gross_vehicle_weight" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Unladen Weight</label>
                                <input type="text" class="form-control" id="unladen_weight" name="unladen_weight"
                                       placeholder="" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Tonnage Capacity</label>
                                <input type="text" class="form-control" id="tonnage_capacity" name="tonnage_capacity"
                                       value="" placeholder="" readonly>
                            </div>
                        </div>

                        <div class="form-row mb-0">
                            <h6 class="col-12">Registration Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">State(Regd)</label>
                                <select class="form-control" name="state_id">
                                    <option value="">Select</option>
                                    {{--                                    @if(count($states) > 0)--}}
                                    {{--                                        @foreach($states as $key => $state)--}}
                                    {{--                                            <option value="{{ $key }}">{{ucwords($state)}}</option>--}}
                                    {{--                                        @endforeach--}}
                                    {{--                                    @endif--}}
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Regn. Date</label>
                                <input type="date" class="form-control" name="regndate" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Hypothecation</label>
                                <input type="text" class="form-control" name="hypothecation"
                                       placeholder="Name of Financer | N/A">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Ownership</label>
                                <select class="form-control" name="ownership">
                                    <option value="Self Owned">Self Owned</option>
                                    <option value="Company Owned">Company Owned</option>
                                    <option value="Transporter Owned">Transporter Owned</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Mobile No.</label>
                                <input type="text" class="form-control" name="owner_phone" placeholder="">
                            </div>
                        </div>


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

    {{--    RC view modal--}}
    <div class="modal fade" id="rcViewModal" tabindex="-1" role="dialog"
         aria-labelledby="rcViewModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rcViewModalLabel">Registeration Certificate</h5>
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


    @include('models.delete-vehicle')


@endsection
@section('js')
    <script>
        $(document).ready(function () {
            var table = $('#vehicletable').DataTable({
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
                // "pageLength": 30,

                "pageLength": 100,
                ajax: "{{ url('vehicles/list') }}",

                columns: [
                    {data: 'regn_no', name: 'regn_no'},
                    {data: 'regndate', name: 'regndate'},
                    {data: 'mfg', name: 'mfg', orderable: false},
                    {data: 'make', name: 'make', orderable: false},
                    {data: 'body_type', name: 'body_type', orderable: false},
                    {data: 'tonnage_capacity', name: 'tonnage_capacity', orderable: false},
                    {data: 'rc_image', name: 'rc_image', orderable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}

                ]
            });
        });


        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.rcshow').attr('src', e.target.result);
                    // $(".remove_licensefield").css("display", "block");
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on("change", '.rc_image', function (e) {
            var fileName = this.files[0].name;
            readURL1(this);
        });

        function clickEditDriverModal() {
            $('#vehicleDetailsModal').modal('hide');
            setTimeout(()=>{
                $('#editVehicleModalIcon').click();
            }, 400)
        }
    </script>

@endsection
