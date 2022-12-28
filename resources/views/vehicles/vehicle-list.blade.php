@extends('layouts.main')
@section('content')
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
                <button class="btn btn-primary" data-target="#createVehicleModal" data-toggle="modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Vehicle
                </button>
                        <div class="inputDiv d-flex justify-content-center align-items-center" style="flex: 1;max-width: 300px; border-radius: 12px; position: relative">
                            <input type="text" class="form-control" placeholder="Search" id="search" style="width: 100%; height: 38px; border-radius: 12px;" data-action="<?php echo url()->current(); ?>">
                            <span class="reset_filter clearIcon" data-action="<?php echo url()->current(); ?>">x</span>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('vehicles.vehicle-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.delete-user')
@include('models.delete-vehicle')
@include('vehicles.crud-models')
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

    // view
    jQuery(document).on('click', '.VehicleView', function(event) {
        event.preventDefault();

        var vehicle_id = $(this).attr('data-id');
        // alert(vehicle_id)
        var myarr = {};
        $('#vehicleDetailsModal').modal('show');
        var url = window.location.origin;
        $.ajax({
            type: "GET",
            url: "vehicles/show",
            data: {
                vehicle_id: vehicle_id
            },
            beforeSend: //reinitialize Datatables
                function() {
                    myarr = {};

                },
            success: function(data) {

                // console.log(data)
                // var address = data.getconsigner.address_line1+', '+data.getconsigner.address_line2+', '+data.getconsigner.address_line3;
                myarr = data.getvehicle;
                // console.log(myarr)
                var pic_url = url+'/storage/images/vehicle_rc_images/'+myarr.rc_image;
                // alert(myarr.tonnage_capacity);
                $('#vehicle_reg_no').html(myarr.regn_no ? myarr.regn_no : '-NA-')
                $('#vehicle_reg_date').html(myarr.regndate)
                $('#vehicle_eng_no').html(myarr.engine_no)
                $('#vehicle_chassis_no').html(myarr.chassis_no)
                $('#vehicle_ownership').html(myarr.ownership)
                $('#vehicle_owner_name').html(myarr.owner_name)
                $('#vehicle_owner_phone').html(myarr.owner_phone)
                $('#vehicle_hypothecation').html(myarr.hypothecation)
                $('#vehicle_manufacturar').html(myarr.mfg)
                $('#vehicle_model').html(myarr.make)
                $('#vehicle_body').html(myarr.body_type)
                $('#vehicle_gross_wt').html(myarr.gross_vehicle_weight)
                $('#vehicle_unladen_wt').html(myarr.unladen_weight ? myarr.unladen_weight : '-' )
                $('#vehicle_tonnage_capacity').html(myarr.tonnage_capacity ? myarr.tonnage_capacity : '-')
                $('#view_src_pc').attr('src', pic_url);
                $('#vehicle_state_name').html(data.get_state.name)
                
            }

        });

    });


    jQuery(document).on('click', '.editVehicleBtn', function(event) {
        event.preventDefault();

        var vehicle_id = $(this).attr('data-id');
        var myarr = {};
        var url = window.location.origin;
        // alert(driver_id)
        $('#VehicleDetailsEditModal').modal('show');
        
        $.ajax({
            type: "GET",
            url: "vehicles/edit",
            data: {
                vehicle_id: vehicle_id
            },
            beforeSend: //reinitialize Datatables
                function() {
                    myarr = {};

                },
            success: function(data) {
                myarr = data.getvehicle;
                console.log(myarr);
                var pic_url = url+'/storage/images/vehicle_rc_images/'+myarr.rc_image;
// alert(myarr.state_id)
                $('#edit_vehicle_reg_no').val(myarr.regn_no ? myarr.regn_no : '-NA-')
                $('#edit_vehicle_reg_date').val(myarr.regndate)
                $('#edit_vehicle_eng_no').val(myarr.engine_no)
                $('#edit_vehicle_chassis_no').val(myarr.chassis_no)
                $('#edit_vehicle_reg_state').val(myarr.state_id)
                $('#edit_vehicle_ownership').val(myarr.ownership)
                $('#edit_vehicle_owner_name').val(myarr.owner_name)
                $('#edit_vehicle_owner_phone').val(myarr.owner_phone)
                $('#edit_vehicle_hypothecation').val(myarr.hypothecation)
                $('#edit_vehicle_manufacturar').val(myarr.mfg)
                $('#edit_vehicle_model').val(myarr.make)
                $('#edit_vehicle_body').val(myarr.body_type)
                $('#edit_vehicle_gross_wt').val(myarr.gross_vehicle_weight)
                $('#edit_vehicle_unladen_wt').val(myarr.unladen_weight ? myarr.unladen_weight : '-' )
                $('#edit_vehicle_tonnage_capacity').val(myarr.tonnage_capacity ? myarr.tonnage_capacity : '-')
                $('#edit_view_src_pc').attr('src', pic_url);
                $('#edit_vehicle_id').val(myarr.id)
                $('#edit_state_id').val(myarr.state_id)
                
                
                
            }

        });

    });
</script>
@endsection