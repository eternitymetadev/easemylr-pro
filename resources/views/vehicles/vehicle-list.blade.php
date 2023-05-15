@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->    
<style>
div.relative {
    position: absolute;
    left: 276px;
    top: 24px;
    z-index: 1;
    width: 95px;
    height: 34px;
}
div.relat {
    position: absolute;
    left: 173px;
    top: 24px;
    z-index: 1;
    width: 95px;
    height: 34px;
}

</style>
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Vehicles</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Vehicle List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    
                        <!-- <div class="btn-group relative">
                            <a href="{{'vehicles/create'}}" class="btn btn-primary pull-right" style="font-size: 12px; padding: 8px 0px;"><span><i class="fa fa-plus" ></i> Add New</span></a>
                        </div>
                        <?php //$authuser = Auth::user();
                        //if($authuser->role_id ==1 ){ ?>
                        <div class="btn-group relat">
                            <a style="font-size: 12px; padding: 8px 0px;" href="<?php //echo URL::to($prefix.'/'.$segment.'/export/excel'); ?>" class="downloadEx btn btn-primary pull-right" data-action="<?php //echo URL::to($prefix.'vehicles/export/excel'); ?>" download>
                            <span><i class="fa fa-download"></i> Export</span></a>
                        </div>
                        <?php //} ?> -->


                        <div class="container-fluid">
                        <div class="row winery_row_n spaceing_2n mb-3">
                            <div class="col d-flex pr-0">
                                <div class="search-inp w-100">
                                    <form class="navbar-form" role="search">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search" id="search"
                                                data-action="<?php echo url()->current(); ?>">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg lead_bladebtop1_n pl-0">
                                <div class="btn-group relative">
                                    <a href="{{'vehicles/create'}}" class="btn btn-primary pull-right" style="font-size: 12px; padding: 8px 0px;"><span><i class="fa fa-plus" ></i> Add New</span></a>
                                </div>
                                <?php $authuser = Auth::user();
                                if($authuser->role_id ==1 ){ ?>
                                <div class="btn-group relat">
                                    <a style="font-size: 12px; padding: 8px 0px;" href="<?php echo URL::to($prefix.'/'.$segment.'/export/excel'); ?>" class="downloadEx btn btn-primary pull-right" data-action="<?php echo URL::to($prefix.'vehicles/export/excel'); ?>" download>
                                    <span><i class="fa fa-download"></i> Export</span></a>
                                </div>
                                <?php } ?>
                                <div class="winery_btn_n btn-section px-0 text-right">
                                    
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                                        style="font-size: 15px; padding: 9px;"
                                        data-action="<?php echo url()->current(); ?>"><span><i
                                                class="fa fa-refresh"></i> Reset Filters</span></a>
                                </div>
                            </div>
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
@include('models.delete-vehicle')
@endsection
