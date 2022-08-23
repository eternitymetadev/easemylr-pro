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
                    <form id="filter_reportall">
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
                                padding: 9px; width: 111px">Filter Data</button>
                            </div>
                        </div>
                    </form>
                        @csrf
                        <table id="consignment_reportall" class="table table-hover" style="width:100%">
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
                                    <td>{{ Helper::ShowDayMonthYear($consignment->consignment_date ?? "-" )}}</td>
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
                                    <td>{{ $consignment->ship_nick ?? "-" }}</td>
                                    <td>{{ $consignment->ship_city ?? "-" }}</td>
                                    <td>{{ $consignment->invoice_no ?? "-" }}</td>
                                    <td>{{ Helper::ShowDayMonthYear($consignment->invoice_date ?? "-" )}}</td>
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
                                    <td>{{ Helper::ShowDayMonthYear($consignment->consignment_date ?? "-" )}}</td>
                                    <td>{{ Helper::ShowDayMonthYear($consignment->delivery_date ?? "-" )}}</td>
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
@section('js')
<script>
    $('#filter_reportall').submit(function (e) {
        // alert('k');
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "get-filter-reportall",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#consignment_reportall').dataTable().fnClearTable();
                $('#consignment_reportall').dataTable().fnDestroy();
            },
            success: (data) => {
                // console.log(data.fetch); return false;
                $.each(data.fetch, function (key, value) {

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
                        var ddate = value.delivery_date;
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
                    //////Driver name///////
                    if(value.drivers_name == null || value.drivers_name == ''){
                        var driverName = '-';
                    }else{
                        var driverName = value.drivers_name;
                    }
                    ///////////Driver Number///////
                    if(value.drivers_phone == null || value.drivers_phone == ''){
                        var driverPhon = '-';
                    }else{
                        var driverPhon = value.drivers_phone;
                    }
                    ///////////Driver Fleet///////
                    if(value.fleet == null || value.fleet == ''){
                        var fleet = '-';
                    }else{
                        var fleet = value.fleet;
                    }
                    ///////////state id///////

                    $('#consignment_reportall tbody').append("<tr><td>" + value.id + "</td><td>" + value.consignment_date + "</td><td>" + value.order_id + "</td><td>" + value.baseclient_name + "</td><td>" + value.regional_name + "</td><td>" + value.consigner_nickname + "</td><td>" + value.consigners_city + "</td><td>" + value.consignee_nickname + "</td><td>" + value.city + "</td><td>" + value.pincode + "</td><td>" + value.district + "</td><td>" + value.state + "</td><td>" + value.ship_nick + "</td><td>" + value.ship_city + "</td><td>" + value.invoice_no + "</td><td>" + value.invoice_date + "</td><td>" + value.invoice_amount + "</td><td>" + value.vechile_number + "</td><td>" + value.total_quantity + "</td><td>" + value.total_weight + "</td><td>" + value.total_gross_weight + "</td><td>" + driverName + "</td><td>" + driverPhon + "</td><td>" + fleet + "</td><td>" + lrstatus + "</td><td>" + value.consignment_date + "</td><td>" + ddate + "</td><td>" + value.delivery_status + "</td><td>" + nodat + "</td></tr>");

                });
                $('#consignment_reportall').DataTable({
                    "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                        "<'table-responsive'tr>" +
                        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                    buttons: {
                        buttons: [
                            // { extend: 'copy', className: 'btn btn-sm' },
                            // { extend: 'csv', className: 'btn btn-sm' },
                            { extend: 'excel', className: 'btn btn-sm' },
                            // { extend: 'print', className: 'btn btn-sm' }
                        ]
                    },
                    "oLanguage": {
                        "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
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