@extends('layouts.main')
@section('content')
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

    .driverBankDetails p,
    .driverPersonalDetails p {
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

    .driverBankDetails p .textHeading,
    .driverPersonalDetails p .textHeading {
        font-weight: 500;
    }

    .driverBankDetails p .textValue,
    .driverPersonalDetails p .textValue {
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Drivers</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Driver List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    
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
                        <div class="inputDiv d-flex justify-content-center align-items-center" style="flex: 1;max-width: 300px; border-radius: 12px; position: relative">
                            <input type="text" class="form-control" placeholder="Search" id="search" style="width: 100%; height: 38px; border-radius: 12px;" data-action="<?php echo url()->current(); ?>">
                            <span class="reset_filter clearIcon" data-action="<?php echo url()->current(); ?>">x</span>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('drivers.driver-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.delete-user')
@include('models.delete-driver')
@include('drivers.crud-models')
@endsection
@section('js')
<script>
    jQuery(document).on('click', '#filter_reportall', function() {
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var search = jQuery('#search').val();

        jQuery.ajax({
            type: 'get',
            url: 'consignment-report2',
            data: {
                startdate: startdate,
                enddate: enddate,
                search: search
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.html) {
                    jQuery('.main-table').html(response.html);
                }
            }
        });
        return false;
    });

    jQuery(document).on('change', '.report_perpage', function() {
        var startdate = jQuery('#startdate').val();
        var enddate = jQuery('#enddate').val();
        if (startdate == enddate) {
            startdate = "";
            enddate = "";
        }
        var url = jQuery(this).attr('data-action');
        var peritem = jQuery(this).val();
        var search = jQuery('#search').val();
        jQuery.ajax({
            type: 'get',
            url: url,
            data: {
                peritem: peritem,
                search: search,
                startdate: startdate,
                enddate: enddate
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.html) {
                    if (response.page == 'lead_note') {
                        jQuery('#Note .main-table').html(response.html);
                    } else {
                        jQuery('.main-table').html(response.html);
                    }
                }
            }
        });
        return false;
    });

    jQuery(document).on('click', '.consignmentReportEx', function(event) {
        event.preventDefault();

        var totalcount = jQuery('.totalcount').text();
        if (totalcount > 30000) {
            jQuery('.limitmessage').show();
            setTimeout(function() {
                jQuery('.limitmessage').fadeOut();
            }, 5000);
            return false;
        }

        var geturl = jQuery(this).attr('data-action');
        var startdate = jQuery('#startdate').val();
        var enddate = jQuery('#enddate').val();

        var search = jQuery('#search').val();
        var url = jQuery('#search').attr('data-url');
        if (startdate)
            geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate;
        else if (search)
            geturl = geturl + '?search=' + search;

        jQuery.ajax({
            url: url,
            type: 'get',
            cache: false,
            data: {
                startdate: startdate,
                enddate: enddate
            },
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="_token"]').attr('content')
            },
            processData: true,
            beforeSend: function() {
                //jQuery(".load-main").show();
            },
            complete: function() {
                //jQuery(".load-main").hide();
            },
            success: function(response) {
                // jQuery(".load-main").hide();
                setTimeout(() => {
                    window.location.href = geturl
                }, 10);
            }
        });
    });
    // 
    jQuery(document).on('click', '.DriverView', function(event) {
        event.preventDefault();

        var driver_id = $(this).attr('data-id');
        var myarr = {};
        $('#driverDetailsModal').modal('show');
        var url = window.location.origin;
        $.ajax({
            type: "GET",
            url: "drivers/show",
            data: {
                driver_id: driver_id
            },
            beforeSend: //reinitialize Datatables
                function() {
                    myarr = {};

                },
            success: function(data) {


                // var address = data.getconsigner.address_line1+', '+data.getconsigner.address_line2+', '+data.getconsigner.address_line3;
                myarr = data.getdriver;
                // console.log(myarr)
                var pic_url = url+'/storage/images/driverlicense_images/'+myarr.license_image;
                // alert(pic_url);

                $('#DriverName').html(myarr.name ? myarr.name : '-NA-')
                $('#DriverPhone').html(myarr.phone)
                $('#LicenseNum').html(myarr.license_number)
                $('#acc_holder_name').html(myarr.bank_detail.account_holdername)
                $('#acc_num').html(myarr.bank_detail.account_number)
                $('#acc_ifsc').html(myarr.bank_detail.ifsc)
                $('#bank_name').html(myarr.bank_detail.bank_name)
                $('#bank_branch').html(myarr.bank_detail.branch_name)
                $('#view_src_pc').attr('src', pic_url);
            }

        });

    });


    jQuery(document).on('click', '.editDriverBtn', function(event) {
        event.preventDefault();

        var driver_id = $(this).attr('data-id');
        var myarr = {};
        var url = window.location.origin;
        // alert(driver_id)
        $('#DriverDetailsEditModal').modal('show');
        
        $.ajax({
            type: "GET",
            url: "drivers/edit",
            data: {
                driver_id: driver_id
            },
            beforeSend: //reinitialize Datatables
                function() {
                    myarr = {};

                },
            success: function(data) {
                myarr = data.getdriver;
                console.log(myarr)
                var pic_url = url+'/storage/images/driverlicense_images/'+myarr.license_image;

                $('#edit_driver_name').val(myarr.name ? myarr.name : '')
                $('#edit_driver_phone').val(myarr.phone)
                $('#edit_license_number').val(myarr.license_number)
                $('#edit_acc_holder_name').val(myarr.bank_detail.account_holdername)
                $('#edit_acc_no').val(myarr.bank_detail.account_number)
                $('#edit_acc_ifsc').val(myarr.bank_detail.ifsc)
                $('#edit_bank_name').val(myarr.bank_detail.bank_name)
                $('#edit_branch_name').val(myarr.bank_detail.branch_name)
                $('#edit_team_id').val(myarr.team_id)
                $('#edit_fleet_id').val(myarr.fleet_id)
                $('#edit_login_id').val(myarr.login_id)
                $('#edit_password').val(myarr.password)
                $('#edit_driver_id').val(myarr.id)
                $('#edit_src_pc').attr('src', pic_url);
                
            }

        });

    });
</script>
@endsection