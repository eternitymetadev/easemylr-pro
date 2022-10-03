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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Order Bookings</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Order List</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="widget-content widget-content-area br-6">
                    <div class="mb-4 mt-4">
                        @csrf
                        <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                            <div class="btn-group relative">
                                <a href="{{'orders/create'}}" class="btn btn-primary pull-right" style="font-size: 13px; padding: 6px 0px;">Create Order</a>
                            </div>
                            <thead>
                                <tr>
                                    <!-- <th> </th> -->
                                    <th>LR No</th>
                                    <th>Consigner Name</th>
                                    <th>Consignee Name</th>
                                    <th>City</th>
                                    <!-- <th>Pin Code</th> 
                                    <th>Boxes</th>
                                    <th>Net Weight</th>
                                    <th>EDD</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($consignments as $key => $consignment) {  
                                ?> 
                                <tr>
                                  <!-- <td class="dt-control">+</td> -->
                                    <td>{{ $consignment->id ?? "-" }}</td>
                                    <td>{{ $consignment->consigner_id}}</td>
                                    <td>{{ $consignment->consignee_id}}</td>
                                    <td>{{ $consignment->city ?? "-" }}</td>
                                    <!-- <td>{{ $consignment->pincode ?? "-" }}</td>
                                    <td>{{ $consignment->total_quantity ?? "-" }}</td>
                                    <td>{{ $consignment->total_weight ?? "-" }}</td>
                                    <td>{{ $consignment->edd ?? "-" }}</td> -->
                                    
                                    <td>
                                        <a class="orderstatus btn btn-danger" data-id = "{{$consignment->id}}" data-action = "<?php echo URL::current();?>"><span><i class="fa fa-ban"></i> Cancel</span></a>
                                        <a class="btn btn-primary" href="{{url($prefix.'/orders/'.Crypt::encrypt($consignment->id).'/edit')}}" ><span><i class="fa fa-edit"></i></span></a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                ?>
                            </tbody>
                        </table>
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
 // Order list status change onchange
 jQuery(document).on('click','.orderstatus',function(event){
        event.stopPropagation();

        let order_id   = jQuery(this).attr('data-id');
        var dataaction = jQuery(this).attr('data-action');
        var updatestatus = 'updatestatus';
        var status = 0;

       
        jQuery('#commonconfirm').modal('show');
        jQuery( ".commonconfirmclick").one( "click", function() {

            var reason_to_cancel = jQuery('#reason_to_cancel').val();
            var data =  {id:order_id,updatestatus:updatestatus, status:status, reason_to_cancel:reason_to_cancel};
            
            jQuery.ajax({
                url         : dataaction,
                type        : 'get',
                cache       : false,
                data        :  data,
                dataType    :  'json',
                headers     : {
                    'X-CSRF-TOKEN': jQuery('meta[name="_token"]').attr('content')
                },
                processData: true,
                beforeSend  : function () {
                    // jQuery("input[type=submit]").attr("disabled", "disabled");
                },
                complete: function () {
                    //jQuery("#loader-section").css('display','none');
                },

                success:function(response){
                    if(response.success){
                        jQuery('#commonconfirm').modal('hide');
                        if(response.page == 'order-statusupdate'){
                            setTimeout(() => {window.location.href = response.redirect_url},10);
                        }
                    }
                }
            });
        });
    });
</script>
@endsection