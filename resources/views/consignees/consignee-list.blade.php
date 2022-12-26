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
                <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createConsignee">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Consignee
                </button>
                <div class="inputDiv d-flex justify-content-center align-items-center"
                     style="flex: 1;max-width: 300px; border-radius: 12px; position: relative">
                    <input type="text" class="form-control" placeholder="Search" id="search"
                           style="width: 100%; height: 38px; border-radius: 12px;"
                           data-action="<?php echo url()->current(); ?>">
                    <span class="reset_filter clearIcon" data-action="<?php echo url()->current(); ?>">x</span>
                </div>
            </div>
                    
                    @csrf
                    <div class="main-table table-responsive">
                        @include('consignees.consignee-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.delete-user')
@include('consignees.crud-models')
@include('models.delete-consignee')
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
jQuery(document).on('click', '.consigneeView', function(event) {
    event.preventDefault();

    var consignee_id = $(this).attr('data-id');
    var myarr={};
    // alert(consignee_id)
    // return 1;
    $('#consigneeDetailsModal').modal('show');
    $.ajax({
                type: "GET",
                url: "consignees/show",
                data: {
                    consignee_id: consignee_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        // $('#legal_name').empty()
                        // $('#nick_name').empty()
                        // $('#consigner_name').empty()
                        // $('#contact_person').empty()
                        // $('#cong_email').empty()
                        // $('#cnr_phone').empty()
                        // $('#cnr_gst').empty()
                        // $('#cnr_pin').empty()
                        // $('#cnr_city').empty()
                        // $('#cnr_district').empty()
                        // $('#cnr_state').empty()
                        // $('#cnr_address').empty()
                        myarr={};
                      
                        
                    },
                success: function (data) {

                    $('#cne_address').html(myarr.address)
                    $('#cne_address').html(myarr.address)
                    $('#cne_address').html(myarr.address)
                    myarr=data.getconsignee;
                    var address = myarr.address_line1+', '+myarr.address_line2+', '+myarr.address_line3+','+myarr.address_line4;
                    // console.log(myarr)
                    // alert(myarr.address_line1)
                    $('#legal_name').html(myarr.legal_name ? myarr.legal_name : '-NA-')
                    $('#nick_name').html(myarr.nick_name)
                    $('#contact_person').html(myarr.contact_name)
                    $('#consignee_name').html(myarr.get_consigner.nick_name)
                     $('#cne_email').html(myarr.email)
                     $('#cne_phone').html(myarr.phone)
                     $('#dealer_type').html(myarr.dealer_type)
                     $('#cne_gst').html(myarr.gst_number)
                     $('#zone').html("Zone")
                    $('#cne_pin').html(myarr.postal_code)
                    $('#cne_city').html(myarr.city)
                    $('#cne_district').html(myarr.district)
                    $('#cne_state').html(myarr.state_id ? myarr.state_id : '-NA-')
                    $('#cne_address').html(address)
                }

            });

});


jQuery(document).on('click', '.editconsignee', function(event) {
    event.preventDefault();

    var consignee_id = $(this).attr('data-id');
    // alert(consignee_id)
    var myarr={};
    $('#consigneeDetailsEditModal').modal('show');
    $.ajax({
                type: "GET",
                url: "consignees/edit",
                data: {
                    consignee_id: consignee_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        myarr={};
                        // $('#edit_nick_name').empty()
                        // $('#edit_legal_name').empty()
                        // $('.edit_gst').empty()
                        // $('#edit_contact_person').empty()
                        // $('#edit_phone').empty()
                        // $('#edit_email').empty()
                        // $('.edit_pin').empty()
                        // $('.edit_city').empty()
                        // $('.edit_district').empty()
                        // $('.edit_state').empty()
                        // $('#edit_address1').empty()
                        // $('#edit_address2').empty()
                        // $('#edit_address3').empty()
                        // $('#edit_address4').empty()
                        // $('#edit_cnr_id').empty()
                       
                        
                    },
                success: function (data) {
                    myarr=data.getconsignee;
                    console.log(myarr)
                    // alert(myarr.dealer_type);
                    $('#edit_legal_name').val(myarr.legal_name ? myarr.legal_name : '');
                    $('#edit_nick_name').val(myarr.nick_name);
                    // $('#regional_client').val(myarr.get_reg_client.name);
                    $('#edit_contact_person').val(myarr.contact_name);
                     $('#edit_email').val(myarr.email);
                     $('#add_phone').val(myarr.phone);
                     $('#gst_edit').val(myarr.gst_number);
                    $('#pin_code').val(myarr.postal_code);
                    $('.edit_city').val(myarr.city);
                    $('.edit_district').val(myarr.district);
                    $('.edit_state').val(myarr.state_id ? myarr.state_id : '');
                    $('#edit_address1').val(myarr.address_line1 ? myarr.address_line1 : '');
                    $('#edit_address2').val(myarr.address_line2 ? myarr.address_line2 : '');
                    $('#edit_address3').val(myarr.address_line3 ? myarr.address_line3 : '');
                    $('#edit_address4').val(myarr.address_line4 ? myarr.address_line4 : '');
                    $(".edit_cnr").val(myarr.consigner_id).change();
                    $('#edit_cne_id').val(myarr.id);
                    $('#edit_dealer_type').val(myarr.dealer_type).change();
                    

                }

            });

});

</script>
@endsection