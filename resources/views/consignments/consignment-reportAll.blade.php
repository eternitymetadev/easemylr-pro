@extends('layouts.main')
@section('content')
<style>
.dt--top-section {
    margin: none;
}

div.relative {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 145px;
    height: 38px;
}

.dt-buttons .dt-button {
    width: 83px;
    height: 38px;
    font-size: 13px;
}

.btn-group>.btn,
.btn-group .btn {
    padding: 0px 0px;
    padding: 10px;
}

.btn {

    font-size: 10px;
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
                    <h5 class="limitmessage text-danger" style="display: none;">You cannot download more than 30,000
                        records. Please select Filters.</h5>
                    <div class="row mt-4" style="margin-left: 193px; margin-bottom:15px;">
                        <div class="col-sm-3">
                            <label>from</label>
                            <input type="date" id="startdate" class="form-control" name="startdate">
                        </div>
                        <div class="col-sm-3">
                            <label>To</label>
                            <input type="date" id="enddate" class="form-control" name="enddate">
                        </div>
                        <div class="col-6">
                            <button type="button" id="filter_reportall" class="btn btn-primary"
                                style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px">
                                <span class="indicator-label">Filter Data</span>
                            </button>
                            <a href="<?php echo URL::to($prefix.'/reports/export2'); ?>"
                                data-url="<?php echo URL::to($prefix.'/consignment-report2'); ?>"
                                class="consignmentReportEx btn btn-white btn-cstm"
                                style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px"
                                data-action="<?php echo URL::to($prefix.'/reports/export2'); ?>" download><span><i class="fa fa-download"></i> Export</span></a>
                            <a href="javascript:void();" style="margin-top: 31px; font-size: 15px; padding: 9px;" class="btn btn-primary btn-cstm ml-2 reset_filter" data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset Filters</span></a>
                        </div>
                    </div>
                    @csrf
                    <div class="main-table table-responsive">
                        @include('consignments.consignment-reportAll-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.delete-user')
@include('models.common-confirm')
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
</script>
@endsection