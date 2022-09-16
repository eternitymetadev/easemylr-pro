@extends('layouts.main')
@section('content')
<style>
        .dt--top-section {
    margin:none;
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
.btn-group > .btn, .btn-group .btn {
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
                    
                    <h4 style="text-align: center"> <b>Last One Week Report </b></h4>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search" name="search" id="search" data-action="<?php echo URL::to($prefix.'/secondary_reports'); ?>" data-url="<?php echo URL::to($prefix.'/secondary_reports/exports'); ?>">
                        <div class="input-group-btn">
                           <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                     </div>
                    <form id="">
                        <div class="row mt-4" style="margin-left: 193px;">
                            <div class="col-sm-4">
                                <label>from</label>
                                <input type="date" id="startdate" class="form-control" name="startdate">
                            </div>
                            <div class="col-sm-4">
                                <label>To</label>
                                <input type="date" id="enddate" class="form-control" name="enddate">
                            </div>
                            <div class="col-4">
                            <button type="button" id="filter_reportall" class="btn btn-primary" style="margin-top: 31px; font-size: 15px; padding: 9px; width: 130px">
                                <span class="indicator-label">Filter Data</span>
                                <span class="indicator-progress" style="display: none;">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button> 
                            </div>
                        </div>
                        <div class="exportExcel ml-2">
                            <a href="<?php echo URL::to($prefix.'/secondary_reports/exports'); ?>" data-url="<?php echo URL::to($prefix.'/secondary_reports'); ?>" class="downloadSecEx btn btn-white btn-cstm" data-action="<?php echo URL::to($prefix.'/secondary_reports/exports'); ?>" download><span><i class="fa fa-download"></i> Export</span></a>
                        </div>
                        <div class="exportExcel ml-2">
                            <a href="<?php echo url()->current(); ?>" class="btn btn-primary btn-cstm secreport_reset_button" data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset Filters</span> </a>
                        </div>
                    </form>
                        @csrf
                        <div class="table-responsive">
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
   jQuery(document).on('click','#filter_reportall',function(){
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var search = jQuery('#search').val();
    var url =  jQuery('#search').attr('data-action');
    jQuery.ajax({
       type      : 'get',
       url       : 'consignment-report2',
       data      : {startdate:startdate,enddate:enddate,search:search},
       headers   : {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       dataType  : 'json',
       success:function(response){
         if(response.html){
           jQuery('.table-responsive').html(response.html);
         }
       }
     });
     return false;
   });


   jQuery(document).on('change', '.report_perpage', function(){
   var startdate = jQuery('.exp_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
   var enddate = jQuery('.exp_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
   if(startdate == enddate)
   {
     startdate = "";
     enddate = "";
   }
    var url     = jQuery(this).attr('data-action');
    var peritem = jQuery(this).val();
    var search  = jQuery('#search').val();
      //var getpagetext =  jQuery('.page-item.active span').text();
      var wineactivestock =  jQuery('#winestocktypetabs li a.active').attr('id');
      //console.log(activestock);
      //return false;
      jQuery.ajax({
        type      : 'get',
        url       : url,
        data      : {peritem:peritem,search:search,startdate:startdate,enddate:enddate,wineactivestock:wineactivestock},
        headers   : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType  : 'json',
        success:function(response){
          if(response.html){
            if(wineactivestock == 'winesecstock-tab'){
             // jQuery('#winesecstock-tab .table-responsive').html(response.html);
               jQuery('#wineprimarystock-tab .table-responsive').html(response.html);
            }else if(wineactivestock == 'wineprimarystock-tab'){
              jQuery('#wineprimarystock-tab .table-responsive').html(response.html);
            } else if(response.page == 'lead_note'){
             jQuery('#Note .table-responsive').html(response.html);
           }
           else if(response.page == 'wine'){
             jQuery('.table-responsive').html(response.html);
             setTimeout(function(){
               if(totalFuturePrice)
              $('.totalFuturecount').html('£'+totalFuturePrice);
             },200);
           }
           else{
            jQuery('.table-responsive').html(response.html);
          }

        }
      }
    });
      return false;
    });


   //// on change per page
   jQuery(document).on('change', '.perpage', function(){
    var url     = jQuery(this).attr('data-action');
    var peritem = jQuery(this).val();
    var search  = jQuery('#search').val();
      //var getpagetext =  jQuery('.page-item.active span').text();
      var wineactivestock =  jQuery('#winestocktypetabs li a.active').attr('id');
      //console.log(activestock);
      //return false;
      jQuery.ajax({
        type      : 'get',
        url       : url,
        data      : {peritem:peritem,search:search,wineactivestock:wineactivestock},
        headers   : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType  : 'json',
        success:function(response){
          if(response.html){
            if(wineactivestock == 'winesecstock-tab'){
             // jQuery('#winesecstock-tab .table-responsive').html(response.html);
               jQuery('#wineprimarystock-tab .table-responsive').html(response.html);
            }else if(wineactivestock == 'wineprimarystock-tab'){
              jQuery('#wineprimarystock-tab .table-responsive').html(response.html);
            } else if(response.page == 'lead_note'){
             jQuery('#Note .table-responsive').html(response.html);
           }
           else if(response.page == 'wine'){
             jQuery('.table-responsive').html(response.html);
             setTimeout(function(){
               if(totalFuturePrice)
              $('.totalFuturecount').html('£'+totalFuturePrice);
             },200);
           }
           else{
            jQuery('.table-responsive').html(response.html);
          }

        }
      }
    });
      return false;
    });

    jQuery('body').on('click', '.pagination a', function(){
        jQuery('.pagination li.active').removeClass('active');
        jQuery(this).parent('li').addClass('active');
        var page = jQuery(this).attr('href').split('page=')[1];
        var pageUrl = jQuery(this).attr('href');
        var emails = localStorage.getItem('importids');
        var activeattribute =  jQuery('#myTab li a.active').attr('href');
        history.pushState({page: page}, "title "+page, "?page="+page)
        var pagination = "pagination";
        var currency = $('.currency_change :selected').val();

        $.ajax({
            type      : 'GET',
            cache     : false,
            url       : pageUrl,
            data      : {page:page,importids:emails,currency:currency},
            headers   : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:   'json',
            success:function(response){
                if(response.html){
                    if(response.page == 'lead_note'){
                        jQuery('#Note .table-responsive').html(response.html);
                    }
                    else if(response.page == 'wine'){
                        jQuery('.table-responsive').html(response.html);
                        setTimeout(function(){
                            if(totalFuturePrice)
                            $('.totalFuturecount').html('£'+totalFuturePrice);
                        },200);
                    }
                    else{
                        jQuery('.table-responsive').html(response.html);
                    }
                }
            }
        });
        return false;
    });

</script>


@endsection