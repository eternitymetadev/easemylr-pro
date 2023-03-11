@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->

<style>
td p {
    margin-bottom: 0;
}

.lrStatus {
    border-radius: 4px;
    border: 1px solid;
    padding: 2px 5px;
    font-size: 10px !important;
    font-weight: 400;
    line-height: 10px;
    color: white !important;
    margin-bottom: 0 !important;
}

.dlMode {
    border-radius: 4px;
    border: 1px solid;
    padding: 0 5px;
    user-select: none;
}

.pointer {
    cursor: pointer;
}

.notAllowed {
    cursor: not-allowed;
}

label.statusLabel {
    font-size: 12px !important;
    color: #fff !important;
    letter-spacing: 0px;
    font-weight: 500;
    padding: 1px 6px;
    border-radius: 30px;
    width: 90px;
    text-align: center;
    margin-bottom: 0 !important;
}

.viewAllInvoices {
    position: relative;
    cursor: pointer;
    color: #f9b808;
    float: right;
}

.viewAllInvoices:hover {
    color: #715200;
}

.moreInvoicesView {
    padding: 1rem 1rem 1rem 2rem;
    width: 200px;
    position: absolute;
    top: calc(100%);
    right: calc(100% + 5px);
    background-color: #ffffff;
    color: #494949;
    box-shadow: 0 0 8px rgb(68 154 1 / 58%);
    display: none;
    flex-wrap: wrap;
    font-weight: 500;
    font-size: 14px;
    line-height: 1rem;
    transition: all 200ms ease-in-out;
    border-radius: 12px 0 12px 12px;
}

.viewAllInvoices:hover .moreInvoicesView {
    display: flex;
}

#exampleModal img {
    max-height: 100% !important;
    max-width: 100% !important;
}
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">

                    <h5 class="limitmessage text-danger" style="display: none;">
                        You cannot download more than 30,000 records. Please select Filters.
                    </h5>
                    <div class="row mt-4" style="margin-inline: auto; margin-bottom:15px;">
                        <div class="page-header flex-wrap" style="width: 100%">
                            <h2>POD View</h2>
                            <input type="text" class="form-control" placeholder="Search" id="search"
                                data-action="<?php echo url()->current(); ?>" style="width: min(100%, 250px);" />
                        </div>

                        <div class="row justify-content-center" style="width: 100%">
                            <div class="col-sm-3">
                                <label>from</label>
                                <input type="date" id="startdate" class="form-control" name="startdate">
                            </div>
                            <div class="col-sm-3">
                                <label>To</label>
                                <input type="date" id="enddate" class="form-control" name="enddate">
                            </div>
                            <div class="col-4 d-flex align-items-end">
                                <button type="button" id="filter_reportall" class="btn btn-primary"
                                    style=" font-size: 15px; padding: 9px; width: 130px">
                                    <span class="indicator-label">Filter Data</span>
                                </button>
                                <a href="<?php echo URL::to($prefix.'/pod-export'); ?>"
                                    data-url="<?php echo URL::to($prefix.'/consignment-report2'); ?>"
                                    class="consignmentReportEx btn btn-white btn-cstm"
                                    style=" font-size: 15px; padding: 9px; width: 130px"
                                    data-action="<?php echo URL::to($prefix.'/pod-export'); ?>" download><span><i
                                            class="fa fa-download"></i> Export</span></a>
                                <a href="javascript:void();" style=" font-size: 15px; padding: 9px;"
                                    class="btn btn-primary btn-cstm ml-2 reset_filter"
                                    data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i>
                                        Reset
                                        Filters</span></a>
                            </div>
                        </div>

                    </div>
                    @csrf
                    <div class="main-table table-responsive">
                        @include('vendors.drswise-payment-report-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@section('js')
<script>

jQuery(document).on('click', '#filter_reportall', function() {
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var search = jQuery('#search').val();

    jQuery.ajax({
        type: 'get',
        url: 'pod-view',
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
</script>
@endsection