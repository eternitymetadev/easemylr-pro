@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

<style>
.update_purchase_price svg {
    height: 14px;
    width: 14px;
    margin-left: 8px;
    cursor: pointer;
}

.update_purchase_price svg:hover {
    color: #f9b600;
}

.pointer {
    cursor: pointer;
}

.drsStatus {
    user-select: none;
    cursor: default;
    text-align: center;
    width: 110px;
    border-radius: 50vh;
    padding: 6px 8px;
    font-size: 11px;
    line-height: 11px;
    font-size: 11px;
    color: #ffffff;
}

.green {
    background: #148b00;
}

.orange {
    background: #e2a03f;
}

.extra2 {
    background: #1abc9c;
}

#create_request_form span.select2 {
    margin-bottom: 0 !important;
}

input[readonly].styledInput {
    border: none;
    background-color: transparent !important;
    color: #000;
    font-weight: 700;
}
.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
}

</style>

<div class="layout-px-spacing">
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">HRS Payment Sheet</h2>
    </div>

    <div class="widget-content widget-content-area br-6" style="min-height: min(80vh, 600px)">

        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center" style="gap: 1rem;">
           
            <div>
                <input class="form-control" placeholder="Vehicle Number Search" id="search"
                    data-action="<?php echo url()->current(); ?>"
                    style="height: 36px; max-width: 250px; width: 300px;" />
            </div>
        </div>

        @csrf
        <div class="main-table table-responsive">
            @include('hub-transportation.hrs-request-list-ajax')
        </div>
    </div>
</div>
@include('models.hrs-payment-models')
@endsection
@section('js')
<script>
</script>
@endsection