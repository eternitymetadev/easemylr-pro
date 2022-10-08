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
 
    /* .table > tbody > tr > td {
    color: #4361ee;
} */
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Client Reports</a></li> 
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4">
                    <form id="filter_report">
                        <div class="row mt-2" style="margin-left: 8px; margin-bottom: 15px">
                            <div class="col-sm-2">
                                <label>Regional Client</label>
                                <select class="form-control" id="select_regclient" name="regionalclient_id">
                                    <option value="">Select Client</option>
                                    <?php 
                                    if(count($regionalclients)>0) {
                                        foreach ($regionalclients as $key => $client) {
                                    ?>
                                    <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">{{ucwords($client->name)}}</option>
                                    <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label>from</label>
                                <input type="date" class="form-control" id="startdate" name="startdate">
                            </div>
                            <div class="col-sm-2">
                                <label>To</label>
                                <input type="date" class="form-control" id="enddate" name="enddate">
                            </div>
                            <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="search_clientreport" style="margin-top: 31px; font-size: 15px; padding: 9px; width: 111px">Search</button>
                            </div>
                            <div class="col-4" style="text-align: right;padding-right: 30px;">
                                <a class="downloadClientEx btn btn-white btn-cstm" style="margin-top: 31px; font-size: 15px; padding: 9px; width: 111px;margin-right: 12px;" data-url="<?php echo URL::to($prefix.'/client-report'); ?>" data-action="<?php echo URL::to($prefix.'/clients/export'); ?>" download><span><i class="fa fa-download"></i> Export</span></a>

                                <a href="<?php echo url()->current(); ?>" class="btn btn-primary btn-cstm reset_filter" style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px;" data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset Filters</span> </a>
                            </div>
                            
                        </div>
                    </form>
                    @csrf
                    <div class="main-table table-responsive">
                        @include('clients.client-report-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    jQuery(document).on('click','#search_clientreport',function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var search = jQuery('#search').val();
        var regclient = jQuery('#select_regclient').val();
        
        jQuery.ajax({
            type      : 'get',
            url       : 'client-report',
            data      : {regclient:regclient,startdate:startdate,enddate:enddate,search:search},
            headers   : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType  : 'json',
            success:function(response){
                if(response.html){
                jQuery('.main-table').html(response.html);
                }
            }
        });
        return false;
    });

    jQuery(document).on('click', '.downloadClientEx', function(event) {
    event.preventDefault();
        var geturl = jQuery(this).attr('data-action');
        var regclient = jQuery('#select_regclient').val();
        var startdate = jQuery('#startdate').val();
        var enddate = jQuery('#enddate').val();

        var search = jQuery('#search').val();
        var url = jQuery('#search').attr('data-url');
        if (startdate)
            geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate + '&regclient=' + regclient;
        else if (search)
            geturl = geturl + '?search=' + search;

        jQuery.ajax({
            url: geturl,
            type: 'get',
            cache: false,
            data: {
                regclient: regclient,
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

    jQuery(document).on('change', '.report_perpage', function() {
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var regclient = jQuery('#select_regclient').val();

        if (startdate == enddate) {
            startdate = "";
            enddate = "";
        }
        var url = jQuery(this).attr('data-action');
        var peritem = jQuery(this).val();
        jQuery.ajax({
            type      : 'get', 
            url       : url,
            data      : {peritem:peritem,startdate:startdate,enddate:enddate,regclient:regclient},
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
</script>

@endsection