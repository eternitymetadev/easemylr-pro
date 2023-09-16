@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<style>
.dt--top-section {
    margin: 0;
}

div.relative {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 83px;
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

.select2-container {
    z-index: 99999;
}

.select2-dropdown {
    margin-top: 3rem;
    margin-left: 2rem;
}

table{
    font-size: 16px;
    font-family: Arial, Helvetica, sans-serif;
}
th{
    font-size: 18px;
    font-weight: 700;
}
th, td{
    padding: 6px 10px;
}
</style>

<div class="layout-px-spacing">
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">Route List</h2>
        <!-- <div class="d-flex align-content-center" style="gap: 1rem;">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter"
                style="font-size: 11px;">
                Add Branch Connectivity
            </button>
        </div> -->
    </div>

    <div class="widget-content widget-content-area br-6">
            @csrf
        <div class="table-responsive mb-4 mt-4 px-3">
            @include('branch.route-list-ajax')
        </div>
    </div>
</div>

@endsection
@section('js')
<script>

</script>

@endsection