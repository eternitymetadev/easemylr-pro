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
.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
}
.move{
    cursor : move;
}
</style>
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->  
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="page-header">
                    <nav class="breadcrumb-one" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Transaction Sheet</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="widget-content widget-content-area br-6">
                    <div class="table-responsive mb-4 mt-4">
                        @csrf
                        <table id="" class="table table-hover" style="width:100%">
                            <div class="btn-group relative">
                                <!-- <a href="{{'consignments/create'}}" class="btn btn-primary pull-right" style="font-size: 13px; padding: 6px 0px;">Create Consignment</a> -->
                            </div>
                            <thead>
                                <tr>
                                    <th>DRS NO</th>
                                    <th>Status</th>
                                    <th>Purchase Amount</th>
                                    <th>Payment</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($drslist as $list)
                              <tr>
                                <td>DRS-{{$list->drs_no}}</td>
                                <td></td>
                                <td></td>
                                <td> <button type="button" class="btn btn-danger payment" value="{{$list->drs_no}}" style="margin-right:4px;">Create Payment</button> </td>
                                <td></td>
                                <td></td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                        {!! $drslist->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('models.payment-model')
@endsection
@section('js')
<script>
$(document).on('click','.payment', function(){
            var drsno = $(this).val();
            $('#pymt_modal').modal('show');
            $.ajax({
                type: "GET",
                url: "view-transactionSheet/"+cat_id,
                data: {drsno:drsno},
                beforeSend:                      //reinitialize Datatables
                function(){   
                
                },
                success: function(data){
                
				}
                
			});
			
		});
</script>
@endsection