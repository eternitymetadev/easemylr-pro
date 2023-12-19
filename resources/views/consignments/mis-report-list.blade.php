@extends('layouts.main')
@section('content')
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <style>
        .select2Branch .select2-container--default .select2-selection--multiple{
            padding: 4px 12px !important;
            margin-bottom: 0 !important;
        }
        .select2Branch span.select2.select2-container.mb-4.select2-container--default{
            margin-bottom: 0 !important;
        }
        </style>

    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="page-header">
                    <nav class="breadcrumb-one" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">MIS
                                    Reports1</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="widget-content widget-content-area br-6">
                    <div class="mb-4 mt-4">
                        <h5 class="limitmessage text-danger" style="display: none;">You cannot download more than 30,000
                            records. Please select Filters.</h5>

                        <div class="row px-3 mx-0 mt-4 justify-content-between" style="gap: 12px; flex-wrap: wrap">
                            <div class="d-flex align-items-end" style="gap: 12px; flex-wrap: wrap">
                                <?php $authuser = Auth::user(); 
                                if($authuser->role_id == 3 || $authuser->role_id == 5){ ?>
                                <div style="flex: 1" class="select2Branch">
                                    <label>Select Branch</label>
                                    <select class="form-control tagging" multiple="multiple" id="branch_filter"
                                        name="branch_id" data-action="<?php echo url()->current(); ?>"
                                        placeholder="Search By Status">
                                        {{-- <option value="" selected>--select status--</option> --}}
                                        <?php foreach($branchs as $branch){ ?>
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        <?php } ?>
                                        
                                    </select>
                                </div>
                                <?php } ?>
                                <div style="width: 180px">
                                    <label>from</label>
                                    <input type="date" id="startdate" class="form-control" name="startdate" onkeydown="return false">
                                </div>
                                <div style="width: 180px">
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
                                    
                            <a href="<?php echo URL::to($prefix . '/reports/export1'); ?>" data-url="<?php echo URL::to($prefix . '/consignment-misreport'); ?>"
                                class="consignmentReportEx btn btn-white btn-cstm"
                                style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px; align-self: flex-end"
                                data-action="<?php echo URL::to($prefix . '/reports/export1'); ?>" download><span><i class="fa fa-download"></i>
                                    Export</span></a>
                        </div>
                        @csrf
                        <div class="main-table table-responsive">
                            @include('consignments.mis-report-list-ajax')
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
            var branch_id = jQuery('#branch_filter').val();

            jQuery.ajax({
                type: 'get',
                url: 'consignment-misreport',
                data: {
                    startdate: startdate,
                    enddate: enddate,
                    search: search,
                    branch_id: branch_id
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
            var getbranch_id = jQuery('#branch_filter').val();
            if(typeof(getbranch_id) === "undefined"){
                var branch_id = '';
            }else{
                var branch_id = getbranch_id;
            }

            var search = jQuery('#search').val();

            var url = jQuery('#search').attr('data-url');
            
            geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate + '&branch_id=' + branch_id;

            jQuery.ajax({
                url: url,
                type: 'get',
                cache: false,
                data: {
                    startdate: startdate,
                    enddate: enddate,
                    branch_id: branch_id
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

        // branch filter
        $('#branch_filter').change(function() {
            var branch_id = $(this).val();
            let url = $(this).attr('data-action');
            $.ajax({
                url: url,
                type: "get",
                cache: false,
                data: {
                    branch_id: branch_id
                },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr("content"),
                },
                beforeSend: function() {

                },
                success: function(res) {
                    if (res.html) {
                        jQuery('.main-table').html(res.html);
                    }
                },
            });
            return false;
        });
    </script>
@endsection
