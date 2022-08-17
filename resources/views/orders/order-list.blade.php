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
                                    <?php
                                    if($consignment->status==1){
                                        $status = 'Active';
                                        $class = "btn-success";
                                    }elseif($consignment->status==2){
                                        $status = 'Unverified';
                                        $class = "btn-warning";
                                    }else{
                                        $status = 'Cancel';
                                        $class = "btn-danger";
                                    }
                                    ?>
                                    <!-- <td>@if($consignment->status == 1)
                                        <a class="inactivestatus btn {{$class}}" data-id = "{{$consignment->id}}" data-text="consignment" data-status = "0" data-action = "<?php //echo URL::current();?>"><span><i class="fa fa-check-circle-o"></i> {{ $status }}</span></a>
                                        @else
                                        <a class="btn {{$class}}" data-id = "{{$consignment->id}}" data-text="consignment" data-status = "1" data-action = ""><span>  {{ $status }}</span></a>
                                        @endif
                                    </td> -->
                                    <td>
                                        <!-- <a class="btn btn-primary" href="{{url($prefix.'/edit-order/'.$consignment->id)}}" ><span><i class="fa fa-eye"></i></span></a> -->
                                         <a class="btn btn-primary" href="{{url($prefix.'/orders/'.Crypt::encrypt($consignment->id).'/edit')}}" ><span><i class="fa fa-edit"></i></span></a>
                                        <!-- <a href="Javascript:void();" class="btn btn-danger delete_consignment" data-id="{{ $consignment->id }}" data-action="<?php// echo URL::to($prefix.'/consignments/delete-consignment'); ?>"><span><i class="fa fa-trash"></i></span></a> -->
                                    </td>
                                    <!-- <?php 
                                   // if($consignment->delivery_status==1){ ?>
                                        <td><button class="btn btn-danger">UnDelivered</button></td>
                                    <?php //}elseif($consignment->delivery_status==2){ ?>
                                        <td><button class="btn btn-warning">Out For Delivery</button></td>
                                    <?php //}else{ ?>
                                        <td><button class="btn btn-success">Delivered</button></td>
                                    <?php// } ?> -->
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