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
</style>
<div class="layout-px-spacing">

    <div class="page-header layout-spacing">
        <h2 class="pageHeading">Consignor List</h2>
        <div class="d-flex align-content-center" style="gap: 1rem;">
            <a href="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>"
                class="downloadEx btn btn-primary pull-right"
                data-action="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>" download>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-download-cloud">
                    <polyline points="8 17 12 21 16 17"></polyline>
                    <line x1="12" y1="12" x2="12" y2="21"></line>
                    <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                </svg>
                Excel
            </a>
            <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createConsigner">
                {{--                <a class="btn btn-primary" id="add_role" href="{{'consigners/create'}}">--}}
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-plus">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Consignor
            </button>
        </div>
    </div>

    <div class="widget-content widget-content-area br-6 px-3">

        <div class="mb-4 mt-4" style="min-height: 350px">
            @csrf
            <table id="consignerstable" class="table table-hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>Cnr ID</th>
                        <th>Client Name</th>
                        <th>Location</th>
                        <th>Contact Person</th>
                        <th>Mobile No.</th>
                        {{--                        <th>PIN Code</th>--}}
                        {{--                        <th>City</th>--}}
                        {{--                        <th>District</th>--}}
                        {{--                        <th>State</th>--}}
                        <th style="text-align: center">Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    {{--modal for create consigner--}}
    <div class="modal fade" id="createConsigner" tabindex="-1" role="dialog" aria-labelledby="createConsignerLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createConsignerLabel">Create Consignor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="general_form" method="POST" action="{{url($prefix.'/consigners')}}"
                        id="createconsigner">
                        <div class="form-row mb-0">
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Regional Client<span
                                        class="text-danger">*</span></label>
                                <?php $authuser = Auth::user();
                                    if($authuser->role_id == 4){
                                    ?>
                                <select class="form-control form-control-sm" id="regionalclient_id"
                                    name="regionalclient_id">
                                    <option value="">Select</option>
                                    <?php
                                        if(count($regclients) > 0) {
                                        foreach ($regclients as $key => $client) {
                                        ?>
                                    <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">
                                        {{ucwords($client->name)}}</option>
                                    <?php
                                        }
                                        }
                                        ?>
                                </select>
                                <input type="hidden" name="branch_id" value="{{$client->location_id}}">
                                <?php } else { ?>
                                <select class="form-control form-control-sm" id="regionalclient_id"
                                    name="regionalclient_id">
                                    <option value="">Select</option>
                                    <?php
                                        if(count($regclients) > 0) {
                                        foreach ($regclients as $key => $client) {
                                        ?>
                                    <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">
                                        {{ucwords($client->name)}}</option>
                                    <?php
                                        }
                                        }
                                        ?>
                                </select>
                                <input type="hidden" name="branch_id" id="location_id">
                                <?php } ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Consignor Nick Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="nick_name" placeholder=""
                                    required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Consignor Legal Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="legal_name" placeholder=""
                                    required>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">GST No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="gst_number"
                                    name="gst_number" placeholder="" maxlength="15" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Contact Person Name</label>
                                <input type="text" class="form-control form-control-sm" name="contact_name"
                                    placeholder="Contact Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Mobile No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                    placeholder="Enter 10 digit mobile no" maxlength="10" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Email ID</label>
                                <input type="email" class="form-control form-control-sm" name="email" placeholder="">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Pincode<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="postal_code"
                                    name="postal_code" placeholder="" maxlength="6" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">City<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="city" name="city"
                                    placeholder="" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">District<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="district" name="district"
                                    placeholder="" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">State<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="state" name="state_id"
                                    placeholder="" readonly required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 1</label>
                                <input type="text" class="form-control form-control-sm" name="address_line1"
                                    placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 2</label>
                                <input type="text" class="form-control form-control-sm" name="address_line2"
                                    placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 3</label>
                                <input type="text" class="form-control form-control-sm" name="address_line3"
                                    placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 4</label>
                                <input type="text" class="form-control form-control-sm" name="address_line4"
                                    placeholder="">
                            </div>
                            <label for="exampleFormControlSelect1">Email Sent</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_email_sent"
                                    value="1">
                                <label class="form-check-label" for="inlineRadio1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_email_sent" id="inlineRadio2"
                                    value="0" checked>
                                <label class="form-check-label" for="inlineRadio2">No</label>
                            </div>
                            

                            <div class="col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close
                                </button>
                                <button type="submit" style="min-width: 120px" class="btn btn-primary">Submit
                                </button>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{--modal for view consigner--}}
    <div class="modal fade" id="consignerDetailsModal" tabindex="-1" role="dialog"
        aria-labelledby="consignerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consignerDetailsModalLabel">Consignor Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="d-flex flex-wrap justify-content-between px-4 pt-4 pb-1"
                        style="gap: 1rem; row-gap: 1.5rem">
                        <div class="d-flex flex-wrap col-md-7 detailsBlock">
                            <p>
                                <span class="detailKey">Legal Name: </span>
                                <span class="detailValue text-uppercase">FRONTIER AGROTECH PRIVATE LIMITED</span>
                            </p>
                            <p>
                                <span class="detailKey">Nick Name: </span>
                                <span class="detailValue text-capitalize">Agrotech DD GZB</span>
                            </p>
                            <p>
                                <span class="detailKey">Regional Client: </span>
                                <span class="detailValue text-capitalize">Agrotech SD-1 GZB</span>
                            </p>
                            <p>
                                <span class="detailKey">Contact Person: </span>
                                <span class="detailValue text-uppercase">ABHISHEK SHARMA</span>
                            </p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center detailsBlock contactDetails">
                            <p>
                                <span class="detailKey">EMAIL: </span>
                                <span class="detailValue">ghaiabadstc@frontierag.com</span>
                            </p>
                            <p>
                                <span class="detailKey">PHONE: </span>
                                <span class="detailValue text-uppercase">9115115604</span>
                            </p>
                            <p>
                                <span class="detailKey">GSTIN: </span>
                                <span class="detailValue text-uppercase">09AACCF3772B1ZU</span>
                            </p>
                        </div>
                        <div class="d-flex flex-wrap col-md-12 detailsBlock addressBlock py-4">
                            <p style="flex: 1;">
                                <span class="detailKey">Pincode: </span>
                                <span class="detailValue text-uppercase">201003</span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">City: </span>
                                <span class="detailValue text-uppercase">GHAZIABAD</span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">District: </span>
                                <span class="detailValue text-uppercase">GHAZIABAD</span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">State: </span>
                                <span class="detailValue text-uppercase">Uttar Pradesh</span>
                            </p>
                            <p class="mt-2" style="width:  100%;">
                                <span class="detailKey">Address: </span>
                                <span class="detailValue text-capitalize">
                                    KHASRA NO. 938, MORTA, MEERUT ROAD, Ghaziabad - 201003, Uttar Pradesh
                                </span>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center mt-3 pt-3" style="gap: 1rem;">
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                        onclick="closeConsignerDetaislModal()" data-dismiss="modal">
                        Edit
                    </button>
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{--modal for Edit consigner--}}
    <div class="modal fade" id="consignerDetailsEditModal" tabindex="-1" role="dialog"
        aria-labelledby="consignerDetailsEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consignerDetailsEditModalLabel">Edit Consignor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="general_form" method="POST" action="{{url($prefix.'/consigners')}}"
                        id="createconsigner">
                        <div class="form-row mb-0">
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Regional Client<span
                                        class="text-danger">*</span></label>
                                <?php $authuser = Auth::user();
                                    if($authuser->role_id == 4){
                                    ?>
                                <select class="form-control form-control-sm" id="regionalclient_id"
                                    name="regionalclient_id">
                                    <option value="">Select</option>
                                    <?php
                                        if(count($regclients) > 0) {
                                        foreach ($regclients as $key => $client) {
                                        ?>
                                    <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">
                                        {{ucwords($client->name)}}</option>
                                    <?php
                                        }
                                        }
                                        ?>
                                </select>
                                <input type="hidden" name="branch_id" value="{{$client->location_id}}">
                                <?php } else { ?>
                                <select class="form-control form-control-sm" id="regionalclient_id"
                                    name="regionalclient_id">
                                    <option value="">Select</option>
                                    <?php
                                        if(count($regclients) > 0) {
                                        foreach ($regclients as $key => $client) {
                                        ?>
                                    <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">
                                        {{ucwords($client->name)}}</option>
                                    <?php
                                        }
                                        }
                                        ?>
                                </select>
                                <input type="hidden" name="branch_id" id="location_id">
                                <?php } ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Consignor Nick Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="nick_name" placeholder=""
                                    required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Consignor Legal Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="legal_name" placeholder=""
                                    required>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">GST No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="gst_number"
                                    name="gst_number" placeholder="" maxlength="15" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Contact Person Name</label>
                                <input type="text" class="form-control form-control-sm" name="contact_name"
                                    placeholder="Contact Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Mobile No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                    placeholder="Enter 10 digit mobile no" maxlength="10" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Email ID</label>
                                <input type="email" class="form-control form-control-sm" name="email" placeholder="">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Pincode<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="postal_code"
                                    name="postal_code" placeholder="" maxlength="6" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">City<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="city" name="city"
                                    placeholder="" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">District<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="district" name="district"
                                    placeholder="" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">State<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="state" name="state_id"
                                    placeholder="" readonly required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 1</label>
                                <input type="text" class="form-control form-control-sm" name="address_line1"
                                    placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 2</label>
                                <input type="text" class="form-control form-control-sm" name="address_line2"
                                    placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 3</label>
                                <input type="text" class="form-control form-control-sm" name="address_line3"
                                    placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Address Line 4</label>
                                <input type="text" class="form-control form-control-sm" name="address_line4"
                                    placeholder="">
                            </div>

                            <div class="col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close
                                </button>
                                <button type="submit" style="min-width: 120px" class="btn btn-primary">Update
                                </button>
                            </div>

                        </div>

                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center mt-3 pt-3" style="gap: 1rem;">
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Edit
                    </button>
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@include('models.delete-consigner')
@endsection
@section('js')
<script>
var table = $('#consignerstable').DataTable({
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
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered');
    },

    columns: [{
            data: 'id',
            name: 'id',
            defaultContent: '-'
        },
        {
            data: 'regclient',
            name: 'regclient',
            defaultContent: '-'
        },
        {
            data: 'location',
            name: 'location',
            defaultContent: '-'
        },
        {
            data: 'contact_name',
            name: 'contact_name',
            defaultContent: '-'
        },
        {
            data: 'phone',
            name: 'phone',
            defaultContent: '-'
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        }
    ]

    // columns: [
    //     {data: 'id', name: 'id', defaultContent: '-'},
    //     {data: 'regclient', name: 'regclient', defaultContent: '-'},
    //     {data: 'nick_name', name: 'nick_name', defaultContent: '-'},
    //     {data: 'contact_name', name: 'contact_name', defaultContent: '-'},
    //     {data: 'phone', name: 'phone', defaultContent: '-'},
    //     {data: 'postal_code', name: 'postal_code', defaultContent: '-'},
    //     {data: 'city', name: 'city', defaultContent: '-'},
    //     {data: 'district', name: 'district', defaultContent: '-'},
    //     {data: 'state', name: 'state', defaultContent: '-'},
    //     {data: 'action', name: 'action', orderable: false, searchable: false}
    // ]

});


function closeConsignerDetaislModal() {
    document.getElementById('editConsignerIcon').click();
}
</script>
@endsection