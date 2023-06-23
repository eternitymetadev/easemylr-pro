@extends('layouts.main')
@section('content')
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
                                
                                <div class="btn-group relat">
                                    
                                </div>
                                <div class="winery_btn_n btn-section px-0 text-right">
                                <?php $authuser = Auth::user();
                                    if($authuser->role_id ==1 ){ ?>
                                        <a style="font-size: 15px; padding: 9px;" href="<?php echo URL::to($prefix.'/'.$segment.'/export/excel'); ?>" class="downloadEx btn btn-primary" data-action="<?php echo URL::to($prefix.'consignees/export/excel'); ?>" download>
                                        <span><i class="fa fa-download"></i> Export</span></a>
                                    <?php } ?>
                                    <a href="{{'consignees/create'}}" class="btn btn-primary" style="font-size: 15px; padding: 9px;"><span><i class="fa fa-plus" ></i> Add New</span></a>

                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2" style="font-size: 15px; padding: 9px;" data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset Filters</span></a>
                                </div>
                            </div>
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
@endsection