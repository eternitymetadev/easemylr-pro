@extends('layouts.main')
@section('content')
<style>
.dt--top-section {
    margin: none;
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

.btn-group>.btn,
.btn-group .btn {
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
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Consignment
                                Reports</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-3">
                    <h4 style="text-align: center"> <b>Last One Week Report </b></h4>
                    <form id="filter_report">
                        <div class="row mt-4" style="margin-left: 193px;">
                            <div class="col-sm-4">
                                <label>from</label>
                                <input type="date" class="form-control" name="first_date">
                            </div>
                            <div class="col-sm-4">
                                <label>To</label>
                                <input type="date" class="form-control" name="last_date">
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary" style="margin-top: 31px; font-size: 15px;
                                padding: 9px; width: 130px"><span class="indicator-label">Filter Data</span>
                                    <span class="indicator-progress" style="display: none;">Please wait...
                                        <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
                            </div>
                        </div>
                    </form>
                    @csrf
                    <table id="consignment_report" class="table table-hover table-responsive" style="width:100%">

                        <thead>
                            <tr>
                                <!-- <th> </th> -->
                                <th>LR No</th>
                                <th>LR Date</th>
                                <th>Order No</th>
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
                                <th>Vehicle Type</th>
                                <th>Boxes</th>
                                <th>Net Weight</th>
                                <th>Gross Weight</th>
                                <th>LR Status</th>
                                <th>Dispatch Date</th>
                                <th>Delivery Date</th>
                                <th>Delivery Status</th>
                                <th>TAT</th>
                                <th>Delivery Type</th>
                                <th>POD</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($consignments as $consignment)
                            <?php 
                                $start_date = strtotime($consignment['consignment_date']);
                                $end_date = strtotime($consignment['delivery_date']);
                                $tat = ($end_date - $start_date) / 60 / 60 / 24;
                                ?>
                            <tr>
                                <td>{{ $consignment['id'] ?? "-" }}</td>
                                <td>{{ Helper::ShowDayMonthYearslash($consignment['consignment_date'] ?? "-" )}}</td>
                                <?php if(empty($consignment['order_id'])){ 
                                     if(count($consignment['consignment_items'])>0){
                                    //    echo'<pre>'; print_r($consignment['consignment_items']); die;
                                    $order = array();
                                    $invoices = array();
                                    $inv_date = array();
                                    $inv_amt = array();

                                    foreach($consignment['consignment_items'] as $orders){ 
                                        

                                        $order[] = $orders['order_id'];
                                        $invoices[] = $orders['invoice_no'];
                                        $inv_date[] = Helper::ShowDayMonthYearslash($orders['invoice_date']);
                                        $inv_amt[] = $orders['invoice_amount'];
                                    }
                                    //echo'<pre>'; print_r($order); die;
                                    $order_item['orders'] = implode(',', $order);
                                    $order_item['invoices'] = implode(',', $invoices);
                                    $invoice['date'] = implode(',', $inv_date);
                                    $invoice['amt'] = implode(',', $inv_amt);
                                    if(!empty($order_item['orders'])){?>

                                <td>{{ $order_item['orders'] ?? "-" }}</td>
                                <?php }else{?>
                                <td>-</td>
                                <?php  } ?>

                                <?php }else{ ?>
                                <td>-</td>
                                <?php } }else{ ?>
                                <td>{{ $consignment['order_id'] ?? "-" }}</td>
                                <?php  } ?>
                                <td>{{ $consignment['consigner_detail']['get_reg_client']['name'] ?? "-" }}</td>
                                <td>{{ $consignment['consigner_detail']['nick_name'] ?? "-" }}</td>
                                <td>{{ $consignment['consigner_detail']['city'] ?? "-" }}</td>
                                <td>{{ $consignment['consignee_detail']['nick_name'] ?? "-" }}</td>
                                <td>{{ $consignment['consignee_detail']['city'] ?? "-" }}</td>
                                <td>{{ $consignment['consignee_detail']['postal_code'] ?? "-" }}</td>
                                <td>{{ $consignment['consignee_detail']['district'] ?? "-" }}</td>
                                <td>{{ $consignment['consignee_detail']['zone']['state'] ?? "-" }}</td>
                                <?php if(empty($consignment['invoice_no'])){ 
                                    if(!empty( $order_item['invoices'])){?>
                                <td>{{ $order_item['invoices'] ?? "-" }}</td>
                                <?php }else{ ?>
                                <td>-</td>
                                <?php } 
                                   if(!empty($invoice['date'])){?>
                                <td>{{ $invoice['date'] ?? '-'}}</td>
                                <?php }else{ ?>
                                <td>-
                                <td>
                                    <?php } 
                                  if(!empty($invoice['amt'] )){?>
                                <td>{{ $invoice['amt'] ?? '-'}}</td>
                                <?php }else{?>
                                <td>-</td>
                                <?php }  } else{ ?>
                                <td>{{ $consignment['invoice_no'] ?? "-" }}</td>
                                <td>{{ Helper::ShowDayMonthYearslash($consignment['invoice_date'] ?? "-" )}}</td>
                                <td>{{ $consignment['invoice_amount'] ?? "-" }}</td>
                                <?php  } ?>
                                <td>{{ $consignment['vehicle_detail']['regn_no'] ?? "Pending" }}</td>
                                <td>{{ $consignment['vehicletype']['name'] ?? "-" }}</td>
                                <td>{{ $consignment['total_quantity'] ?? "-" }}</td>
                                <td>{{ $consignment['total_weight'] ?? "-" }}</td>
                                <td>{{ $consignment['total_gross_weight'] ?? "-" }}</td>
                                <?php
                                if ($consignment['status'] == 0) {?>
                                <td>Cancel</td>
                                <?php } elseif ($consignment['status'] == 1) {?>
                                <td>Active</td>
                                <?php } elseif ($consignment['status'] == 2) {?>
                                <td>Unverified</td>
                                <?php }?>
                                <td>{{ Helper::ShowDayMonthYearslash($consignment['consignment_date'])}}</td>
                                <td>{{ Helper::ShowDayMonthYearslash($consignment['delivery_date'])}}</td>
                                <?php
                                if ($consignment['delivery_status'] == 'Assigned') {?>
                                <td>Assigned</td>
                                <?php } elseif ($consignment['delivery_status'] == 'Unassigned') {?>
                                <td>Unassigned</td>
                                <?php }elseif ($consignment['delivery_status'] == 'Started') {?>
                                <td>Started</td>
                                <?php } elseif ($consignment['delivery_status'] == 'Successful') {?>
                                <td>Successful</td>
                                <?php } else {?>
                                <td>Unknown</td>
                                <?php }?>
                                <?php if ($consignment['delivery_date'] == '') {?>
                                <td> - </td>
                                <?php } else {?>
                                <td>{{ $tat }}</td>
                                <?php } if($consignment['job_id']== ''){?>
                                <td>Manual</td>
                                <?php }else{?>
                                <td>Online</td>
                                <?php } ?>

                                <?php if(empty($consignment['job_id'])){
                            if(empty($consignment['signed_drs'])){
                            ?>
                                <td>Not Available</td>
                                <?php } else { ?>
                                <td>Avliable</td>
                                <?php } ?>
                                <?php } else { 
                                    $job = DB::table('jobs')->where('job_id', $consignment['job_id'])->orderBy('id','desc')->first();

                            if(!empty($job->response_data)){
                            $trail_decorator = json_decode($job->response_data);
                            $img_group = array();
                            foreach($trail_decorator->task_history as $task_img){
                                if($task_img->type == 'image_added'){
                                    $img_group[] = $task_img->description;
                                  }
                              }
                              if(empty($img_group)){?>
                                <td>Not Available</td>
                                <?php } else{?>
                                <td>Available</td>
                                <?php }
                            }
                            ?>
                                <?php } ?>
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
@section('js')
<script>
$('#filter_report').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: "get-filter-report",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#consignment_report').dataTable().fnClearTable();
            $('#consignment_report').dataTable().fnDestroy();
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();

            $.each(data.fetch, function(key, value) {
                var orderid = [];
                var invno = [];
                var invdate = [];
                var invamt = [];

                $.each(value.consignment_items, function(key, cnitem) {

                    orderid.push(cnitem.order_id);
                    invno.push(cnitem.invoice_no);
                    invdate.push(cnitem.invoice_date);
                    invamt.push(cnitem.invoice_amount);
                });

                if (value.order_id == null || value.order_id == '') {
                    var itm_order = orderid.join(",");
                    var itm_inv = invno.join(",");
                    var itm_invdate = invdate.join(",");
                    var itm_amt = invamt.join(",");
                } else {
                    var itm_order = value.order_id;

                    if (value.invoice_date == null || value.invoice_date == '') {
                        var itm_invdate = '-';
                    } else {
                        var iv = value.invoice_date;
                        var inv_date = iv.split('-');
                        var invoiceDate = inv_date[2] + '/' + inv_date[1] + '/' + inv_date[
                            0];
                        var itm_invdate = invoiceDate;
                    }
                    var itm_inv = value.invoice_no;
                    var itm_amt = value.invoice_amount;

                }
                /////////////////////////////////////////////////////////////////
                if (value.status == 0) {
                    var lrstatus = 'Cancel';
                } else if (value.status == 1) {
                    var lrstatus = 'Active';
                } else if (value.status == 2) {
                    var lrstatus = 'Unverified';
                }
                ///////////
                if (value.delivery_date == null || value.delivery_date == '') {
                    var ddate = '-';
                } else {
                    var ddt = value.delivery_date;
                    var dd_date = ddt.split('-');
                    var ddate = dd_date[2] + '/' + dd_date[1] + '/' + dd_date[0];
                }
                ////////Tat/////
                var start = new Date(value.consignment_date);
                var end = new Date(value.delivery_date);
                var diff = new Date(end - start);
                var days = diff / 1000 / 60 / 60 / 24;
                if (value.delivery_date == null || value.delivery_date == '') {
                    var nodat = '-';
                } else {
                    var nodat = days;
                }
                ///////////format///////
                var cn_date = value.consignment_date;
                var arr = cn_date.split('-');
                var cndate = arr[2] + '/' + arr[1] + '/' + arr[0];
                // var getdate = new Date(cn_date).toLocaleString('de-DE',{day:'numeric', month:'short', year:'numeric'}); 
                // my_list = getdate.split('. ')
                // var cndate = my_list[0]+'-'+my_list[1]+'-'+my_list[2]; 

                // console.log(value.consignee_detail.get_state.name);
                //console.log(value.consignee_detail);
                // =========== consignee check ========================== //
                if (value.consignee_detail == null || value.consignee_detail == '') {
                    var cnee_name = '-';
                    var cnee_city = '-';
                    var cnee_pincode = '-';
                    var cnee_district = '-';
                    var cnee_state = '-';
                } else {
                    if (value.consignee_detail.nick_name == null || value.consignee_detail
                        .nick_name == '') {
                        var cnee_name = '-';
                    } else {
                        var cnee_name = value.consignee_detail.nick_name;
                    }
                    if (value.consignee_detail.city == null || value.consignee_detail
                        .city == '') {
                        var cnee_city = '-';
                    } else {
                        var cnee_city = value.consignee_detail.city;
                    }
                    if (value.consignee_detail.postal_code == null || value.consignee_detail
                        .postal_code == '') {
                        var cnee_pincode = '-';
                    } else {
                        var cnee_pincode = value.consignee_detail.postal_code;
                    }
                    if (value.consignee_detail.district == null || value.consignee_detail
                        .district == '') {
                        var cnee_district = '-';
                    } else {
                        var cnee_district = value.consignee_detail.district;
                    }
                    if (value.consignee_detail.zone == null || value.consignee_detail
                        .zone == '') {
                        var cnee_state = '-';
                    } else {
                        var cnee_state = value.consignee_detail.zone;
                    }
                }
                if (cnee_state == null || cnee_state == '-') {
                    var cnstate = '-';
                } else {
                    var cnstate = value.consignee_detail.zone.state;
                }
                // ===============End Consignee Check ================== //
                //console.log(value.vehicle_detail);
                if (value.vehicle_detail == null || value.vehicle_detail == '') {
                    var vechile_number = '-';
                } else {
                    var vechile_number = value.vehicle_detail.regn_no;
                }
                //////
                if (value.consigner_detail == null || value.consigner_detail == '') {
                    var cnr_name = '-';
                    var cnr_city = '-';

                } else {
                    var cnr_name = value.consigner_detail.nick_name;
                    var cnr_city = value.consigner_detail.city;
                }
                if (value.job_id == null || value.job_id == '') {
                    var delivery_type = 'Manual';
                } else {
                    var delivery_type = 'Online';
                }
                /////
                if (value.consigner_detail.get_reg_client == null || value.consigner_detail
                    .get_reg_client == '') {
                    var rgnl_clt = '-';
                } else {
                    var rgnl_clt = value.consigner_detail.get_reg_client.name;
                }


                /////
                // var iv = value.invoice_date;
                // var inv_date = iv.split('-');
                // var invoiceDate = inv_date[2]+'-'+inv_date[1]+'-'+inv_date[0];
                if (value.vehicletype == null || value.vehicletype == '') {
                    var vechl_typ = '-';
                } else {
                    var vechl_typ = value.vehicletype.name;
                }


                $('#consignment_report tbody').append("<tr><td>" + value.id + "</td><td>" +
                    cndate + "</td><td>" + itm_order + "</td><td>" + rgnl_clt +
                    "</td><td>" + cnr_name + "</td><td>" + cnr_city + "</td><td>" +
                    cnee_name + "</td><td>" + cnee_city + "</td><td>" + cnee_pincode +
                    "</td><td>" + cnee_district + "</td><td>" + cnstate + "</td><td>" +
                    itm_inv + "</td><td>" + itm_invdate + "</td><td>" + itm_amt +
                    "</td><td>" + vechile_number + "</td><td>" + vechl_typ +
                    "</td><td>" + value.total_quantity + "</td><td>" + value
                    .total_weight + "</td><td>" + value.total_gross_weight +
                    "</td><td>" + lrstatus + "</td><td>" + cndate + "</td><td>" +
                    ddate + "</td><td>" + value.delivery_status + "</td><td>" + nodat +
                    "</td><td>" + delivery_type + "</td></tr>");

            });
            $('#consignment_report').DataTable({
                "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                    "<'table-responsive'tr>" +
                    "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                buttons: {
                    buttons: [
                        // { extend: 'copy', className: 'btn btn-sm' },
                        // { extend: 'csv', className: 'btn btn-sm' },
                        {
                            extend: 'excel',
                            className: 'btn btn-sm',
                            title: '',
                        },
                        // { extend: 'print', className: 'btn btn-sm' }
                    ]
                },
                "oLanguage": {
                    "oPaginate": {
                        "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    "sInfo": "Showing page _PAGE_ of _PAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": "Search...",
                    "sLengthMenu": "Results :  _MENU_",
                },

                "ordering": true,
                "paging": true,
                "pageLength": 80,

            });

        }
    });
});
</script>


@endsection