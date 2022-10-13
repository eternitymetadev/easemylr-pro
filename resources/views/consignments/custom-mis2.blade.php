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
                    <h4 style="text-align: center"> <b>Export Report Between Two Date </b></h4>
                    <form method="post" action="<?php echo URL::to($prefix.'/export-mis'); ?>" >
                    @csrf
                        <div class="row mt-4" style="margin-left: 193px; margin-bottom: 28px;">
                            <div class="col-sm-4">
                                <label>from</label>
                                <input type="date" class="form-control" name="first_date" required>
                            </div>
                            <div class="col-sm-4">
                                <label>To</label>
                                <input type="date" class="form-control" name="last_date" required>
                            </div>
                            <div class="col-4">
                            <button type="submit" class="btn btn-primary" style="margin-top: 31px; font-size: 15px;
                                padding: 9px; width: 130px"><span class="indicator-label">Export Data</span>
                               <span class="indicator-progress" style="display: none;">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button> 
                                <!-- <button type="submit" class="btn btn-primary" style="margin-top: 31px; font-size: 15px;
                                padding: 9px; width: 111px">Filter Data</button> -->
                            </div>
                        </div>
                    </form>
                        @csrf
                        <table id="" class="table table-hover table-responsive" style="width:100%">
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
                                    <th>Ship To Name</th>
                                    <th>Ship To City</th>
                                    <th>Ship To pin code</th>
                                    <th>Ship To District</th>
                                    <th>Ship To State</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Invoice Amount</th>
                                    <th>Vehicle No</th>
                                    <th>Vehicle Type</th>
                                    <th>Transporter Name</th>
                                    <th>Purchase Price</th>
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
                                    <th>Delivery Mode</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                           
                           @foreach($consignments as $consignment)
                           <?php
                           //   echo'<pre>'; print_r($consignment);die;
                               $start_date = strtotime($consignment['consignment_date']);
                               $end_date = strtotime($consignment['delivery_date']);
                               $tat = ($end_date - $start_date)/60/60/24;
                           ?>
                           <tr>
                               <td>{{ $consignment['id'] ?? "-" }}</td>
                               <td>{{ Helper::ShowDayMonthYearslash($consignment['consignment_date'] ?? "-" )}}</td>
                               <?php if(empty($consignment['order_id'])){ 
                                if(count($consignment['ConsignmentItems'])>0){
                               //    echo'<pre>'; print_r($consignment['consignment_items']); die;
                               $order = array();
                               $invoices = array();
                               $inv_date = array();
                               $inv_amt = array();
                               foreach($consignment['ConsignmentItems'] as $orders){ 
                                   
                                   $order[] = $orders['order_id'];
                                   $invoices[] = $orders['invoice_no'];
                                   $inv_date[] = Helper::ShowDayMonthYearslash($orders['invoice_date']);
                                   $inv_amt[] = $orders['invoice_amount'];
                               }
                               //echo'<pre>'; print_r($order); die;
                               $order_item['orders'] = implode(',', $order);
                               $order_item['invoices'] = implode(',', $invoices);
                               $invoice['date'] = implode(',', $inv_date);
                               $invoice['amt'] = implode(',', $inv_amt);?>

                               <td>{{ $order_item['orders'] ?? "-" }}</td>

                           <?php }else{ ?>
                               <td>-</td>
                          <?php } }else{ ?>
                           <td>{{ $consignment['order_id'] ?? "-" }}</td>
                           <?php  } ?>
                               <td>{{ $consignment['ConsignerDetail']['GetRegClient']['BaseClient']['client_name'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsignerDetail']['GetRegClient']['name'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsignerDetail']['nick_name'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsignerDetail']['city'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsigneeDetail']['nick_name'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsigneeDetail']['city'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsigneeDetail']['postal_code'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsigneeDetail']['district'] ?? "-" }}</td>
                               <td>{{ $consignment['ConsigneeDetail']['Zone']['state'] ?? "-" }}</td>
                               <td>{{ $consignment['ShiptoDetail']['nick_name'] ?? "-" }}</td>
                               <td>{{ $consignment['ShiptoDetail']['city'] ?? "-" }}</td>
                               <td>{{ $consignment['ShiptoDetail']['postal_code'] ?? "-" }}</td>
                               <td>{{ $consignment['ShiptoDetail']['district'] ?? "-" }}</td>
                               <td>{{ $consignment['ShiptoDetail'][Zone]['state'] ?? "-" }}</td>
                               <?php if(empty($consignment['invoice_no'])){ ?>
                               <td>{{ $order_item['invoices'] ?? "-" }}</td>
                               <td>{{ $invoice['date'] ?? '-'}}</td>
                               <td>{{ $invoice['amt'] ?? '-' }}</td>
                          <?php  } else{ ?>
                               <td>{{ $consignment['invoice_no'] ?? "-" }}</td>
                               <td>{{ Helper::ShowDayMonthYearslash($consignment['invoice_date'] ?? "-" )}}</td>
                               <td>{{ $consignment['invoice_amount'] ?? "-" }}</td>
                           <?php  } ?>
                               <td>{{ $consignment['VehicleDetail']['regn_no'] ?? "Pending" }}</td> 
                               <td>{{ $consignment['vehicletype']['name'] ?? "-" }}</td>
                               <td>{{ $consignment['transporter_name'] ?? "-" }}</td>
                               <td>{{ $consignment['purchase_price'] ?? "-" }}</td>
                               <td>{{ $consignment['total_quantity'] ?? "-" }}</td>
                               <td>{{ $consignment['total_weight'] ?? "-" }}</td>
                               <td>{{ $consignment['total_gross_weight'] ?? "-" }}</td>
                               <td>{{ $consignment['DriverDetail']['name'] ?? "-" }}</td>
                               <td>{{ $consignment['DriverDetail']['phone'] ?? "-" }}</td>
                               <td>{{ $consignment['DriverDetail']['fleet_id'] ?? "-" }}</td>

                               <?php 
                               if($consignment['status'] == 0){ ?>
                                   <td>Cancel</td>
                               <?php }elseif($consignment['status'] == 1){ ?>
                                   <td>Active</td>
                                   <?php }elseif($consignment['status'] == 2){ ?>
                                   <td>Unverified</td>
                                   <?php } ?>
                               <td>{{ Helper::ShowDayMonthYearslash($consignment['consignment_date'] )}}</td>
                               <td>{{ Helper::ShowDayMonthYearslash($consignment['delivery_date'] )}}</td>
                               <?php 
                               if($consignment['delivery_status'] == 'Assigned'){ ?>
                                   <td>Assigned</td>
                                   <?php }elseif($consignment['delivery_status'] == 'Unassigned'){ ?>
                                   <td>Unassigned</td>
                                   <?php }elseif($consignment['delivery_status'] == 'Started'){ ?>
                                   <td>Started</td>
                               <?php }elseif($consignment['delivery_status'] == 'Successful'){ ?>
                                   <td>Successful</td>
                               <?php }else{?>
                                   <td>Unknown</td>
                               <?php }?>
                               <?php if($consignment['delivery_date'] == ''){?>
                                   <td> - </td>
                               <?php }else{?>
                               <td>{{ $tat }}</td>
                               <?php } if($consignment['job_id']== ''){?>
                                   <td>Manual</td>
                                 <?php }else{?>
                                    <td>Shadow</td>
                                <?php } ?>
                           </tr>
                           @endforeach
                       </tbody>
                        </table>
                        {{ $consignments->links() }}
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
</script>


@endsection