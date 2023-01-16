@extends('layouts.main')
@section('content')

<style>
    .moreInvoicesView ul {
        padding: 0;
        margin-bottom: 0;
        display: inline-flex;
        width: 200px;
        overflow: hidden;
        white-space: nowrap;
        gap: 2rem;
        text-overflow: ellipsis;
    }
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">PRS</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">PRS
                                List</a></li>
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
                                            <input type="text" class="form-control" placeholder="Search" id="search" data-action="<?php echo url()->current(); ?>">
                                            <!-- <div class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </div> -->
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg lead_bladebtop1_n pl-0">
                                <div class="winery_btn_n btn-section px-0 text-right">
                                    <a class="btn-primary btn-cstm btn ml-2" style="font-size: 15px; padding: 9px; width: 130px" href="{{'prs/create'}}"><span><i class="fa fa-plus"></i> Add
                                            New</span></a>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2" style="font-size: 15px; padding: 9px;" data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset Filters</span></a>

                                    <a href="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>" class="btn btn-primary btn-cstm downloadEx ml-2" style="font-size: 15px; padding: 9px;" data-action="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>" download><span>
                                    <i class="fa fa-download"></i> Export</span></a>

                                    <!-- <a style="font-size: 12px; padding: 8px 0px;" href="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>" class="downloadEx btn btn-primary pull-right" data-action="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>" download>
                                        <span><i class="fa fa-download"></i> Export</span></a> -->

                                </div>
                            </div>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('prs.prs-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
