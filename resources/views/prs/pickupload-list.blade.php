@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Pickup Load List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    <div class="container-fluid">
                        <div class="row px-3 mx-0 mt-4 justify-content-between" style="gap: 12px; flex-wrap: wrap">
                            <div class="d-flex align-items-end" style="gap: 12px; flex-wrap: wrap">
                                <div style="width: 300px">
                                    <label>Search</label>
                                    <input type="text" class="form-control" placeholder="Search" id="search"
                                        data-action="<?php echo url()->current(); ?>">
                                </div>
    
                                <div style="width: 156px">
                                    <label>From</label>
                                    <input type="date" id="startdate" class="form-control" name="startdate" onkeydown="return false">
                                </div>
                                <div style="width: 156px">
                                    <label>To</label>
                                    <input type="date" id="enddate" class="form-control" name="enddate" onkeydown="return false">
                                </div>
                          
    
                                <button type="button" id="filter_reportall" class="btn btn-primary"
                                    style="margin-top: 31px; font-size: 15px; padding: 9px; width: 100px">
                                    <span class="indicator-label">Filter</span>
                                </button>
    
                                <button type="button" class="btn btn-primary reset_filter"
                                    style="margin-top: 31px; font-size: 15px; padding: 9px; width: 100px"
                                    data-action="<?php echo url()->current(); ?>">
                                    <span class="indicator-label">Reset</span>
                                </button>
                            </div>
    
                            <a href="<?php echo URL::to($prefix . '/pickup-loads/export'); ?>" data-url="<?php echo URL::to($prefix . '/pickup-loads'); ?>"
                                class="consignmentReportEx btn btn-white btn-cstm"
                                style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px"
                                data-action="<?php echo URL::to($prefix . '/pickup-loads/export'); ?>" download><span><i class="fa fa-download"></i>
                                    Export</span></a>          
                        </div>                       
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('prs.pickupload-list-ajax')
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
            url: 'pickup-loads',
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
                enddate: enddate,
                search: search
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
                setTimeout(() => {
                    window.location.href = geturl
                }, 10);
            }
        });
    });
</script>
@endsection