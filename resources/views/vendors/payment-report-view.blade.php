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

.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
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
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Request
                                List</a></li>
                    </ol>
                </nav>
            </div>

            <div class="widget-content widget-content-area br-6">
                <a class="btn btn-success ml-2 mt-3" href="{{ url($prefix.'/payment-reportExport') }}">Export
                    data</a>
                <div class=" mb-4 mt-4">
                    @csrf
                    <table id="unverified-table" class="table table-hover" style="width:100%">

                </div>
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Transaction id</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Depot</th>
                        <th>Station</th>
                        <th>Drs No</th>
                        <th>LR No</th>
                        <th>Invoice No</th>
                        <th>Type of Vehicle </th>
                        <th>No. of Cartons</th>
                        <th>Net Weight</th>
                        <th>Gross weight</th>
                        <th>Truck No.</th>
                        <th>Vendor Name</th>
                        <th>Vendor Type</th>
                        <th>Declaration</th>
                        <th>TDS Rate</th>
                        <th>Bank Name</th>
                        <th>Account No.</th>
                        <th>IFSC Code</th>
                        <th>Vendor Pan</th>
                        <th>Purchase Freight</th>
                        <th>Paid Amount</th>
                        <th>Tds Amount</th>
                        <th>Balance Due</th>
                        <th>Advance</th>
                        <th>Payment Date</th>
                        <th>Ref. No</th>
                        <th>Blance Amount</th>
                        <th>payment date</th>
                        <th>Ref. No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;?>
                    @foreach($payment_lists as $payment_list)
                    <?php
                    $i++;
                    $bankdetails = json_decode($payment_list->PaymentRequest[0]->VendorDetails->bank_details);
                        
                        $date = date('d-m-Y',strtotime($payment_list->created_at));
                        $lr_arra = array();
                        $consigneecity = array();
                        $itm_arra = array();
                        $qty = array();
                        $totlwt = array();
                        $grosswt = array();
                        $drsvehicel = array();
                        $vel_type = array();
                        $regn_clt = array();
                        foreach($payment_list->PaymentRequest as $lr_no){

                            $drsvehicel[] = $lr_no->vehicle_no;
                            $qty[] = Helper::totalQuantity($lr_no->drs_no);
                            $totlwt[] = Helper::totalWeight($lr_no->drs_no);
                            $grosswt[] = Helper::totalGrossWeight($lr_no->drs_no);
                            foreach($lr_no->TransactionDetails as $lr_group){
                                $lr_arra[] = $lr_group->consignment_no;
                               $consigneecity[] = @$lr_group->ConsignmentNote->ShiptoDetail->city;
                               $vel_type[] = @$lr_group->ConsignmentNote->vehicletype->name;
                               $regn_clt[] = @$lr_group->ConsignmentNote->RegClient->name;
                            }
                            
                            foreach($lr_group->ConsignmentNote->ConsignmentItems as $lr_no_item){
                                $itm_arra[] = $lr_no_item->invoice_no;
                            }
                        }
                        $csd = array_unique($vel_type);
                        $group_vehicle_type = implode('/',$csd);
                        $group_vehicle = implode('/',$drsvehicel);
                        // $ttqty = implode('/', $qty);
                        $totalqty = array_sum($qty);
                        $groupwt = array_sum($totlwt);
                        $groupgross = array_sum($grosswt);
                        // $groupwt = implode('/', $totlwt);
                        // $groupgross = implode('/', $grosswt);
                        $city = implode('/', $consigneecity);
                        $multilr = implode('/', $lr_arra);
                        $lr_itm = implode('/', $itm_arra);

                        $unique_regn = array_unique($regn_clt);
                        $regn = implode('/', $unique_regn);

                        if($payment_list->PaymentRequest[0]->VendorDetails->declaration_available == 1){
                            $decl = 'Yes';
                        }else{
                            $decl = 'No';
                        }

                        $exp_drs = explode(',',$payment_list->drs_no);
                        $exp_arra = array();
                        foreach($exp_drs as $exp){
                             $exp_arra[] = 'DRS-'.$exp;
                        }
                        $newDrs = implode(',',$exp_arra);

                        $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $payment_list->transaction_id)->get();
                        $histrycount = count($trans_id);
                        if($histrycount > 1){
                           $paid_amt = $trans_id[0]->tds_deduct_balance + $trans_id[1]->tds_deduct_balance ;
                           $curr_paid_amt = $trans_id[1]->current_paid_amt;
                           $paymt_date_2 = $trans_id[1]->payment_date;
                           $ref_no_2 = $trans_id[1]->bank_refrence_no;
                           $tds_amt = $payment_list->PaymentRequest[0]->total_amount - $paid_amt ;

                           $sumof_paid_tds = $paid_amt + $tds_amt ;
                           $balance_due =  $payment_list->PaymentRequest[0]->total_amount - $sumof_paid_tds ;

                        }else{
                            $paid_amt = $trans_id[0]->tds_deduct_balance ;
                            $curr_paid_amt = '';
                            $paymt_date_2 = '';
                            $ref_no_2 = '';
                            if($payment_list->payment_type == 'Balance'){
                                $tds_amt =  $payment_list->balance - $payment_list->tds_deduct_balance ;
                            }else{
                            $tds_amt =  $payment_list->advance - $payment_list->tds_deduct_balance ;
                            }
                            $sumof_paid_tds = $paid_amt + $tds_amt ;
                            $balance_due =  $payment_list->PaymentRequest[0]->total_amount - $sumof_paid_tds ;
                        }
                    ?>

                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$payment_list->transaction_id ?? '-'}}</td>
                        <td>{{$date}}</td>
                        <td>{{$regn ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->Branch->nick_name ?? '-'}}</td>
                        <td>{{$city ?? '-'}}</td>
                        <td>{{$newDrs ?? '-'}}</td>
                        <td>{{$multilr ?? '-'}}</td>
                        <td>{{$lr_itm ?? '-'}}</td>
                        <td>{{$group_vehicle_type ?? '-'}}</td>
                        <td>{{$totalqty}}</td>
                        <td>{{$groupwt}}</td>
                        <td>{{$groupgross}}</td>
                        <td>{{$group_vehicle}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->name ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->vendor_type ?? '-'}}</td>
                        <td>{{$decl}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->tds_rate ?? '-'}}</td>
                        <td>{{$bankdetails->bank_name ?? '-'}}</td>
                        <td>{{$bankdetails->account_no ?? '-'}}</td>
                        <td>{{$bankdetails->ifsc_code ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->pan ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->total_amount ?? '-'}}</td>
                        <td>{{$paid_amt}}</td>
                        <td>{{$tds_amt}}</td>
                        <td>{{$balance_due}}</td>
                        <?php if($payment_list->payment_type == 'Balance'){ ?>
                        <td>{{$payment_list->tds_deduct_balance ?? '-'}}</td>
                        <?php }else{ ?>
                        <td>{{$payment_list->tds_deduct_balance ?? '-'}}</td>
                        <?php } ?>
                        <td>{{$payment_list->payment_date ?? '-'}}</td>
                        <td>{{$payment_list->bank_refrence_no ?? '-'}}</td>
                        <?php
                        $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $payment_list->transaction_id)->get();
                        $histrycount = count($trans_id);
                        if($histrycount > 1){
                        ?>
                        <td>{{$trans_id[1]->tds_deduct_balance ?? '-'}}</td>
                        <td>{{$trans_id[1]->payment_date ?? '-'}}</td>
                        <td>{{$trans_id[1]->bank_refrence_no ?? '-'}}</td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php  } ?>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('js')
<script>
$('#unverified-table').DataTable({

    "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
        "<'table-responsive'tr>" +
        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
    buttons: {
        buttons: [
            // { extend: 'copy', className: 'btn btn-sm' },
            // { extend: 'csv', className: 'btn btn-sm' },
            // {
            //     extend: 'excel',
            //     className: 'btn btn-sm',
            //     title: '',
            // },
            // { extend: 'print', className: 'btn btn-sm' }
        ]
    },
    "oLanguage": {
        "oPaginate": {
            "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
            "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
        },
        "sInfo": "Showing page PAGE of _PAGES_",
        "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        "sSearchPlaceholder": "Search...",
        "sLengthMenu": "Results :  _MENU_",
    },

    "ordering": true,
    "paging": false,
    "pageLength": 100,

});
</script>
@endsection