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
                        @csrf
                        <table id="consignment_report" class="table table-hover" style="width:100%">
                            <div class="btn-group relative">
                                <!-- <a href="{{'consignments/create'}}" class="btn btn-primary pull-right" style="font-size: 13px; padding: 6px 0px;">Create Consignment</a> -->
                            </div>
                            <thead>
                                <tr>
                                    <!-- <th> </th> -->
                                    <th>LR No</th>
                                    <th>LR Date</th>
                                    <th>Order No</th>
                                    <th>Base Client</th>
                                    <th>Regional Client</th>
                                    <th>Consigner</th>
                                    <th>Consigner City</th>
                                    <th>Consignee Name</th>
                                    <th>City</th>
                                    <th>Pin Code</th> 
                                    <th>District</th>
                                    <th>State</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Invoice Amount</th>
                                    <th>Vehicle No</th>
                                    <th>Boxes</th>
                                    <th>Net Weight</th>
                                    <th>Gross Weight</th>
                                    <th>Driver Name</th>
                                    <th>Driver Number</th>
                                    <th>Driver Fleet</th>
                                    <th>LR Status</th>
                                    <th>Dispatch Date</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery Status</th>
                                    <th>TAT</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                           
                                @foreach($consignments as $consignment)
                                <?php
                                    $start_date = strtotime($consignment->consignment_date);
                                    $end_date = strtotime($consignment->delivery_date);
                                    $tat = ($end_date - $start_date)/60/60/24;
                                ?>
                                <tr>
                                    <td>{{ $consignment->id ?? "-" }}</td>
                                    <td>{{ $consignment->consignment_date ?? "-" }}</td>
                                    <td>{{ $consignment->order_id ?? "-" }}</td>
                                    <td>{{ $consignment->baseclient_name ?? "-" }}</td>
                                    <td>{{ $consignment->regional_name ?? "-" }}</td>
                                    <td>{{ $consignment->consigner_nickname ?? "-" }}</td>
                                    <td>{{ $consignment->consigners_city ?? "-" }}</td>
                                    <td>{{ $consignment->consignee_nickname ?? "-" }}</td>
                                    <td>{{ $consignment->city ?? "-" }}</td>
                                    <td>{{ $consignment->pincode ?? "-" }}</td>
                                    <td>{{ $consignment->district ?? "-" }}</td>
                                    <td>{{ $consignment->state ?? "-" }}</td>
                                    <td>{{ $consignment->invoice_no ?? "-" }}</td>
                                    <td>{{ $consignment->invoice_date ?? "-" }}</td>
                                    <td>{{ $consignment->invoice_amount ?? "-" }}</td>
                                    <td>{{ $consignment->vechile_number ?? "Pending" }}</td>
                                    <td>{{ $consignment->total_quantity ?? "-" }}</td>
                                    <td>{{ $consignment->total_weight ?? "-" }}</td>
                                    <td>{{ $consignment->total_gross_weight ?? "-" }}</td>
                                    <td>{{ $consignment->drivers_name ?? "-" }}</td>
                                    <td>{{ $consignment->drivers_phone ?? "-" }}</td>
                                    <td>{{ $consignment->fleet ?? "-" }}</td>

                                    <?php 
                                    if($consignment->status == 0){ ?>
                                        <td>Cancel</td>
                                    <?php }elseif($consignment->status == 1){ ?>
                                        <td>Active</td>
                                        <?php }elseif($consignment->status == 2){ ?>
                                        <td>Unverified</td>
                                        <?php } ?>
                                    <td>{{ $consignment->consignment_date ?? "-" }}</td>
                                    <td>{{ $consignment->delivery_date ?? "-" }}</td>
                                    <?php 
                                    if($consignment->delivery_status == 'Assigned'){ ?>
                                        <td>Assigned</td>
                                        <?php }elseif($consignment->delivery_status == 'Started'){ ?>
                                    <td>Started</td>
                                    <?php }elseif($consignment->delivery_status == 'Successful'){ ?>
                                        <td>Successful</td>
                                    <?php }else{?>
                                        <td>Unknown</td>
                                    <?php }?>
                                    <?php if($consignment->delivery_date == ''){?>
                                        <td> - </td>
                                    <?php }else{?>
                                    <td>{{ $tat }}</td>
                                    <?php }?>
                                </tr>
                                @endforeach
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