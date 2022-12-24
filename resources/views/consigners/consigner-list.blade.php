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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Consignment Report</a></li>
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
                <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createConsigner">
                    {{--                <a class="btn btn-primary" id="add_role" href="{{'consigners/create'}}">--}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Consigner
                </button>
            </div>
                    
                    @csrf
                    <div class="main-table table-responsive">
                        @include('consigners.consigner-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.delete-user')
@include('consigners.crud-models')
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
    var search  = jQuery('#search').val();
        jQuery.ajax({
            type      : 'get', 
            url       : url,
            data      : {peritem:peritem,search:search,startdate:startdate,enddate:enddate},
            headers   : {
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
jQuery(document).on('click', '.consignerView', function(event) {
    event.preventDefault();

    var consigner_id = $(this).attr('data-id');
    $('#consignerDetailsModal').modal('show');
    $.ajax({
                type: "GET",
                url: "consigners/show",
                data: {
                    consigner_id: consigner_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#legal_name').empty()
                        $('#nick_name').empty()
                        $('#regional_client').empty()
                        $('#contact_person').empty()
                        $('#cong_email').empty()
                        $('#cnr_phone').empty()
                        $('#cnr_gst').empty()
                        $('#cnr_pin').empty()
                        $('#cnr_city').empty()
                        $('#cnr_district').empty()
                        $('#cnr_state').empty()
                        $('#cnr_address').empty()
                        
                    },
                success: function (data) {

                    var address = data.getconsigner.address_line1+', '+data.getconsigner.address_line2+', '+data.getconsigner.address_line3;

                    $('#legal_name').html(data.getconsigner.legal_name ? data.getconsigner.legal_name : '-NA-')
                    $('#nick_name').html(data.getconsigner.nick_name)
                    $('#regional_client').html(data.getconsigner.get_reg_client.name)
                    $('#contact_person').html(data.getconsigner.contact_name)
                     $('#cnr_email').html(data.getconsigner.email)
                     $('#cnr_phone').html(data.getconsigner.phone)
                     $('#cnr_gst').html(data.getconsigner.gst_number)
                    $('#cnr_pin').html(data.getconsigner.postal_code)
                    $('#cnr_city').html(data.getconsigner.city)
                    $('#cnr_district').html(data.getconsigner.district)
                    $('#cnr_state').html(data.getconsigner.state ? data.getconsigner.state : '-NA-')
                    $('#cnr_address').html(address)
                }

            });

});

jQuery(document).on('click', '.consignerView', function(event) {
    event.preventDefault();

    var consigner_id = $(this).attr('data-id');
    $('#consignerDetailsModal').modal('show');
    $.ajax({
                type: "GET",
                url: "consigners/show",
                data: {
                    consigner_id: consigner_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#legal_name').empty()
                        $('#nick_name').empty()
                        $('#regional_client').empty()
                        $('#contact_person').empty()
                        $('#cong_email').empty()
                        $('#cnr_phone').empty()
                        $('#cnr_gst').empty()
                        $('#cnr_pin').empty()
                        $('#cnr_city').empty()
                        $('#cnr_district').empty()
                        $('#cnr_state').empty()
                        $('#cnr_address').empty()
                        
                    },
                success: function (data) {

                    var address = data.getconsigner.address_line1+', '+data.getconsigner.address_line2+', '+data.getconsigner.address_line3;

                    $('#legal_name').html(data.getconsigner.legal_name ? data.getconsigner.legal_name : '-NA-')
                    $('#nick_name').html(data.getconsigner.nick_name)
                    $('#regional_client').html(data.getconsigner.get_reg_client.name)
                    $('#contact_person').html(data.getconsigner.contact_name)
                     $('#cnr_email').html(data.getconsigner.email)
                     $('#cnr_phone').html(data.getconsigner.phone)
                     $('#cnr_gst').html(data.getconsigner.gst_number)
                    $('#cnr_pin').html(data.getconsigner.postal_code)
                    $('#cnr_city').html(data.getconsigner.city)
                    $('#cnr_district').html(data.getconsigner.district)
                    $('#cnr_state').html(data.getconsigner.state ? data.getconsigner.state : '-NA-')
                    $('#cnr_address').html(address)
                }

            });

});

jQuery(document).on('click', '.editconsigner', function(event) {
    event.preventDefault();

    var consigner_id = $(this).attr('data-id');
    $('#consignerDetailsEditModal').modal('show');
    $.ajax({
                type: "GET",
                url: "consigners/consigners/edit",
                data: {
                    consigner_id: consigner_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#edit_nick_name').empty()
                        $('#edit_legal_name').empty()
                        $('.edit_gst').empty()
                        $('#edit_contact_person').empty()
                        $('#edit_phone').empty()
                        $('#edit_email').empty()
                        $('.edit_pin').empty()
                        $('.edit_city').empty()
                        $('.edit_district').empty()
                        $('.edit_state').empty()
                        $('#edit_address1').empty()
                        $('#edit_address2').empty()
                        $('#edit_address3').empty()
                        $('#edit_address4').empty()
                        $('#edit_cnr_id').empty()
                       
                        
                    },
                success: function (data) {

                    // var address = data.getconsigner.address_line1+', '+data.getconsigner.address_line2+', '+data.getconsigner.address_line3;
                    console.log(data.getconsigner.regionalclient_id);

                    $('#edit_legal_name').val(data.getconsigner.legal_name ? data.getconsigner.legal_name : '')
                    $('#edit_nick_name').val(data.getconsigner.nick_name)
                    $('#regional_client').val(data.getconsigner.get_reg_client.name)
                    $('#edit_contact_person').val(data.getconsigner.contact_name)
                     $('#edit_email').val(data.getconsigner.email)
                     $('#edit_phone').val(data.getconsigner.phone)
                     $('.edit_gst').val(data.getconsigner.gst_number)
                    $('.edit_pin').val(data.getconsigner.postal_code)
                    $('.edit_city').val(data.getconsigner.city)
                    $('.edit_district').val(data.getconsigner.district)
                    $('.edit_state').val(data.getconsigner.state ? data.getconsigner.state : '')
                    $('#edit_address1').val(data.getconsigner.address_line1 ? data.getconsigner.address_line1 : '')
                    $('#edit_address2').val(data.getconsigner.address_line2 ? data.getconsigner.address_line2 : '')
                    $('#edit_address3').val(data.getconsigner.address_line3 ? data.getconsigner.address_line3 : '')
                    $('#edit_address4').val(data.getconsigner.address_line4 ? data.getconsigner.address_line4 : '')
                    $(".edit_regin").val(data.getconsigner.regionalclient_id).change();
                    $('#edit_cnr_id').val(data.getconsigner.id)

                }

            });

});

</script>
@endsection