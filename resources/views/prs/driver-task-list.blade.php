@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">PRS</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Driver Task List</a></li>
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
                                    <!-- <a class="btn-primary btn-cstm btn ml-2"
                                        style="font-size: 15px; padding: 9px; width: 130px"
                                        href="{{'prs/create'}}"><span><i class="fa fa-plus"></i> Add
                                            New</span></a> -->
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2" style="font-size: 15px; padding: 9px;" data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset Filters</span></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('prs.driver-task-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.create-prs-drivertask')
@include('models.prs-driverstatus-change')
@endsection

@section('js')
<script>
    $(document).on("click", ".add-taskbtn", function () {
        var prs_id = jQuery(this).attr("data-prsid");
        var drivertask_id = jQuery(this).attr("data-drivertaskid");
        var prsconsigner_id = jQuery(this).attr("data-prsconsignerid");

        $("#drivertask_id").val(drivertask_id);
        $("#prs_id").val(prs_id);

        jQuery.ajax({
            type: "get",
            url: "getlr-item",
            data: { prsconsigner_id: prsconsigner_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            beforeSend: function () {
                $("#create-driver-task").dataTable().fnClearTable();
                $("#create-driver-task").dataTable().fnDestroy();
            },
            success: function (response) {
                var rows = '';
                var i = 0;
                console.log(response.data.consignment_items);
                $.each(response.data, function (index, consignmtvalue) {
                    $.each(consignmtvalue.consignment_items, function (index, value) {
                        i++;

                        rows = '<tr><td><input type="text" class="form-control form-small orderid" name="data['+i+'][order_id]" value='+value.order_id+'></td>';
                        rows += '<td><input type="text" class="form-control form-small orderid" name="data['+i+'][invoice_no]" value='+value.invoice_no+'></td>';
                        rows += '<td><input type="date" class="form-control form-small orderid" name="data['+i+'][invoice_date]" value='+value.invoice_date+'></td>';
                        rows += '<td><input type="text" class="form-control form-small orderid" name="data['+i+'][quantity]" value='+value.quantity+'></td>';
                        rows += '<td><input type="text" class="form-control form-small orderid" name="data['+i+'][net_weight]" value='+value.weight+'></td>';
                        rows += '<td><input type="text" class="form-control form-small orderid" name="data['+i+'][gross_weight]" value='+value.gross_weight+'></td>';
                        rows += '<td> <button type="button" class="btn btn-default btn-rounded insert-moreprs"> + </button><button type="button" class="btn btn-default btn-rounded remove-row"> - </button></td>';
                        rows += '</tr>';
                        
                        $("#create-driver-task tbody").append(rows);
                    });
                });
                
            },
        });

    });
</script>

@endsection