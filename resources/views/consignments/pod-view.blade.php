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
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <h2>POD View</h2>
                <input type="text" class="form-control" placeholder="Search" id="search"
        data-action="<?php echo url()->current(); ?>" style="width: min(100%, 250px);"/>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    <h5 class="limitmessage text-danger" style="display: none;">
                        You cannot download more than 30,000 records. Please select Filters.
                    </h5>
                    @csrf
                    <div class="main-table table-responsive">
                        @include('consignments.pod-view-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: max-content">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="#" id="toggledImageView" style="max-height: 90vh; max-width: 90vw"/>
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

jQuery(document).on('click', '.viewImageInNewTab', function() {

    let toggledImage = $(this).attr('src');
    $('#toggledImageView').attr('src', toggledImage);
});
</script>
@endsection