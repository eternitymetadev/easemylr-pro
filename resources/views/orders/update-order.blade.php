@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->

<style>
@media only screen and (max-width: 600px) {
    .checkbox-round {
        margin-left: 1px;
    }

}

h4 {
    font-size: 18px;

}


.checkbox-round {
    width: 2.3em;
    height: 2.3em;
    border-radius: 55%;
    border: 1px solid #ddd;
    margin-left: 103px;
}

p {
    font-size: 11px;
    font-weight: 500;
}


th,
td {
    text-align: left;
    padding: 8px;
    color: black;
}

.cont {
    background: white;
    height: 240px;
    border-style: ridge;
    width: 390px;
    border-radius: 17px;
}

.mini_container {
    margin-top: 8px;
}

.wizard {
    background: #fff;
}

.wizard .nav-tabs {
    position: relative;
    margin: 40px auto;
    margin-bottom: 0;
}

.wizard>div.wizard-inner {
    position: relative;
}

.connecting-line {
    height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 80%;

    top: 42%;

}

.nav-tabs {
    border-bottom: none;
}

.wizard .nav-tabs>li.active>a,
.wizard .nav-tabs>li.active>a:hover,
.wizard .nav-tabs>li.active>a:focus {
    color: #555555;
    cursor: default;
    border: none;
}

span.round-tab {
    width: 50px;
    height: 50px;
    line-height: 51px;
    display: inline-block;
    border-radius: 100px;
    background: #fff;
    border: 2px solid #e0e0e0;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 25px;
}

span.round-tab i {
    color: #555555;
}

.wizard li.active span.round-tab {
    background: #fff;
    border: 2px solid #5bc0de;

}

.wizard li.active span.round-tab i {
    color: #5bc0de;
}

span.round-tab:hover {
    color: #333;
    border: 2px solid #333;
}

.wizard .nav-tabs>li {
    width: 25%;
}

.wizard .nav-tabs>li a {
    width: 48px;
    height: 70px;
    border-radius: 100%;
    padding: 0;
}

@media (max-width: 585px) {

    .wizard {
        width: 90%;
        height: auto !important;
    }

    span.round-tab {
        font-size: 16px;
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard .nav-tabs>li a {
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard li.active:after {
        content: " ";
        position: absolute;
        left: 35%;
    }
}

/* / ////////////////////////////////////////////////////////////////////end wizard / */
.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
}

/*.form-group {*/
/*    margin-bottom: 0;*/
/*}*/

.form-row>.col,
.form-row>[class*=col-] {
    padding-inline: 10px !important;
}

span.select2.select2-container.mb-4 {
    margin-bottom: 0 !important;
}

.form-row {
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 0 3px #83838360;
    margin-bottom: 1rem;
}

.form-row h6 {
    margin-bottom: 1rem;
    font-weight: 700;
}

.mainTr {
    outline: 1px solid #838383;
    background: #f4f4f4;
    border-radius: 12px;
    width: 100%;
}

.childTable {
    background: #F9B60030;
    border-radius: 12px;
}

.addRowButton {
    text-align: right;
    width: 100%;
    font-weight: 800;
    color: #f9b600;
    margin-right: 10px;
    cursor: pointer;
}

.addItem {
    float: right;
    font-weight: 800;
    color: #f9b600;
    margin-right: 10px;
    cursor: pointer;
}

.items_table_body tr {
    position: relative;
}


.main_table_body td {
    min-width: 150px;
}

.main_table_body td:has(div.removeIcon) {
    min-width: 50px;
    width: 50px;
}

.main_table_body td div.removeIcon:has(span) {
    cursor: pointer;
    height: 20px;
    width: 20px;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50vh;
    color: darkred;
    font-weight: 800;
    border: 1px solid darkred;
    transition: all 200ms ease-in-out;
}

.main_table_body td div.removeIcon:has(span):hover {
    background: darkred;
    color: white;
}

.appendedAddress:has(br) {
    padding: 1rem;
    border-radius: 12px;
    margin-top: 1rem;
    background: #f9b60024;
    color: #000;
}
</style>
<?php 
if(!empty($getconsignments->prs_id) || ($getconsignments->prs_id != NULL)){
    $disable = ''; 
} else{
    $disable = 'disabled';
}
?>

<div class="layout-px-spacing">
    {{-- page title--}}
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">Update Order</h2>
    </div>

    <form class="general_form" method="POST" action="{{url($prefix.'/orders/update-order')}}" id="updateorder"
        style="margin: auto; ">
        <input type="hidden" name="consignment_id" value="{{$getconsignments->id}}">
        <input type="hidden" name="booked_drs" value="{{$getconsignments->booked_drs}}">
        <input type="hidden" name="lr_type" value="{{$getconsignments->lr_type}}">

        {{--Branch Location--}}
        <div class="form-row">
            <h6 class="col-12">Branch</h6>
             
            <?php $authuser = Auth::user();
            if($authuser->role_id == 2 || $authuser->role_id == 4)
            {
            ?>
            <div class="form-group col-md-4">
                <label for="exampleFormControlSelect1">
                    Select Branch <span class="text-danger">*</span>
                </label>
                <select class="form-control  my-select2" id="branch_id" name="branch_id" tabindex="-1">
                    @foreach($branchs as $branch)
                    <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                    @endforeach
                </select>
            </div>
            <?php } else { ?>
                <div class="form-group col-md-4">
                <label for="exampleFormControlSelect1">
                    Select Branch <span class="text-danger">*</span>
                </label>
                <select class="form-control  my-select2" id="branch_id" name="branch_id" tabindex="-1">
                    <option value="">Select..</option>
                    @foreach($branchs as $branch)
                    <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                    @endforeach
                </select>
            </div>

                <?php } ?>

        </div>

        {{--bill to info--}}
        <div class="form-row">
            <h6 class="col-12">Bill To Information</h6>

            <div class="form-group col-md-4">
                <label for="exampleFormControlSelect1">
                    Select Bill to Client<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" id="select_regclient" name="regclient_id" disabled>
                    <option value="">Select</option>
                    @if(count($regionalclient) > 0)
                    @foreach($regionalclient as $client)
                    <option value="{{ $client->id }}"
                        {{ $client->id == $getconsignments->regclient_id ? 'selected' : ''}}>{{ucwords($client->name)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <?php $invc_data = DB::table('regional_clients')->select('id','name','is_multiple_invoice')->where('id',$getconsignments->regclient_id)->first(); ?>

                <input type="hidden" name="invoice_check" id="inv_check" value="{{$invc_data->is_multiple_invoice}}">
                <input type="hidden" name="regclient_id" value="{{$getconsignments->regclient_id}}" />
            </div>

            <div class="form-group col-md-3">
                <label for="exampleFormControlSelect1">
                    Payment Term<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" style="width: 160px;" name="payment_type"
                    {{$disable}}>
                    <option value="To be Billed" {{$getconsignments->payment_type == 'To be Billed' ? 'selected' : ''}}>
                        To be Billed</option>
                    <option value="To Pay" {{$getconsignments->payment_type == 'To Pay' ? 'selected' : ''}}>To Pay
                    </option>
                    <option value="Paid" {{$getconsignments->payment_type == 'Paid' ? 'selected' : ''}}>Paid</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="exampleFormControlSelect1">
                    Freight<span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control form-small" style="width: 160px; height: 43px;" name="freight"
                    value="{{old('freight',isset($getconsignments->freight)?$getconsignments->freight:'')}}" {{$disable}}>
            </div>
            <div class="form-group d-flex col-md-3">

                <div class="d-flex align-items-center px-2 pt-2">
                    <label class="mr-4">
                        Sale Return<span class="text-danger">*</span>
                    </label>
                    <div class="checkbox radio">
                        <label class="check-label">Yes
                            <input type="radio" name="is_salereturn" value="1"
                                {{ ($getconsignments->is_salereturn=="1")? "checked" : "" }} {{$disable}}>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="checkbox radio ml-3">
                        <label class="check-label">No
                            <input type="radio" name="is_salereturn" value="0"
                                {{ ($getconsignments->is_salereturn=="0")? "checked" : "" }} {{$disable}}>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" class="form-seteing date-picker" id="consignDate" name="consignment_date" placeholder=""
            value="<?php echo date('d-m-Y'); ?>">


        {{--pickup & drop info--}}
        <div class="form-row">
            <h6 class="col-12">Pickup and Drop Information</h6>

            <div class="form-group col-md-4">
                <label>
                    Select Pickup Location (Consigner)<span class="text-danger">*</span>
                </label>
                <select id="select_consigner" class="my-select2 form-seteing" type="text" name="consigner_id" disabled>
                    <option value="">Select Consigner</option>
                    @if(count($consigners) > 0)
                    @foreach($consigners as $k => $consigner)
                    <option value="{{ $k }}" {{ $k == $getconsignments->consigner_id ? 'selected' : ''}}>
                        {{ucwords($consigner)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <input type="hidden" name="consigner_id" value="{{$getconsignments->consigner_id}}" />
                <div id="consigner_address">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label>
                    Select Drop location (Bill To Consignee)<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" style="width: 328px;" type="text" name="consignee_id" id="select_consignee" {{$disable}}>
                    <option value="">Select Consignee</option>
                    @if(count($consignees) > 0)
                    @foreach($consignees as $k => $consignee)
                    <option value="{{$consignee->id}}" {{ $consignee->id == $getconsignments->consignee_id ? 'selected' : ''}}>
                        {{ucwords($consignee->nick_name)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <input type="hidden" name="consignee_id" value="{{$getconsignments->consignee_id}}" />
                <div class="" id="consignee_address"></div>
            </div>
            <div class="form-group col-md-4">
                <label>
                    Select Drop Location (Ship To Consignee)<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" style="width: 328px;" type="text" name="ship_to_id" id="select_ship_to" {{$disable}}>
                    <option value="">Select Ship To</option>
                    @if(count($consignees) > 0)
                    @foreach($consignees as $k => $consignee)
                    <option value="{{$consignee->id}}" {{ $consignee->id == $getconsignments->ship_to_id ? 'selected' : ''}}>
                        {{ucwords($consignee->nick_name)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <input type="hidden" name="ship_to_id" value="{{$getconsignments->ship_to_id}}" />
                <div id="ship_to_address">

                </div>
            </div>


            {{--order info--}}
            <?php if($invc_data->is_multiple_invoice == 1 || $invc_data->is_multiple_invoice == 2 ){ ?>
            <div class="form-row">
                <h6 class="col-12">Order Information</h6>

                <div style="width: 100%">
                    <div class="d-flex flex-wrap align-items-center form-group form-group-sm">
                        <div class="col-md-3">
                            <label>Item Description</label>
                            <input type="text" class="form-control" value="Pesticide" name="description"
                                list="json-datalist" onkeyup="showResult(this.value)">
                            <datalist id="json-datalist"></datalist>
                        </div>
                        <div class="col-md-3">
                            <label>Mode of Packing</label>
                            <input type="text" class="form-control" value="Case/s" name="packing_type">
                        </div>
                        <!-- <div class="col-md-2">
                            <label>Total Quantity</label>
                            <span id="tot_qty">
                                <?php echo "0";?>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <label>Total Net Weight</label>
                            <span id="total_nt_wt">
                                <?php echo "0";?>
                            </span> Kgs.
                        </div>
                        <div class="col-md-2">
                            <label>Total Gross Weight</label>
                            <span id="total_gt_wt">
                                <?php echo "0";?>
                            </span> Kgs.
                        </div> -->
                    </div>
                </div>


                <div class="maindiv" style="overflow-x:auto; padding: 1rem 8px 0; margin-top: 1rem; width: 100%;">
                    <table style="width: 100%; border-collapse: collapse;" id="items_table" class="items_table">
                        <tbody class="main_table_body">
                            <input type="hidden" id="tid" name="tid" value="1">
                            <?php $i=1;
                            foreach($getconsignments->ConsignmentItems as $item){ 
                                ?>
                            <tr>
                                <td>
                                    <table class="mainTr" id="1">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group form-group-sm">
                                                        <label>Order ID</label>
                                                        <input type="text" class="form-control orderid"
                                                            name="data[{{$i}}][order_id]" value="{{$item->order_id}}"
                                                            {{$disable}}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group form-group-sm">
                                                        <label>Invoice Number</label>
                                                        <input type="text" class="form-control invc_no" id="1"
                                                            name="data[{{$i}}][invoice_no]"
                                                            value="{{$item->invoice_no}}" {{$disable}}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group form-group-sm">
                                                        <label>Invoice Date</label>
                                                        <input type="date" class="form-control invc_date"
                                                            name="data[{{$i}}][invoice_date]"
                                                            value="{{$item->invoice_date}}" {{$disable}}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group form-group-sm">
                                                        <label>Invoice Amount</label>
                                                        <input type="number" class="form-control invc_amt"
                                                            name="data[{{$i}}][invoice_amount]"
                                                            value="{{$item->invoice_amount}}" {{$disable}}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group form-group-sm">
                                                        <label>E-way Bill Number</label>
                                                        <input type="number" class="form-control ew_bill"
                                                            name="data[{{$i}}][e_way_bill]"
                                                            value="{{$item->e_way_bill}}" {{$disable}}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group form-group-sm">
                                                        <label>E-Way Bill Date</label>
                                                        <input type="date" class="form-control ewb_date"
                                                            name="data[{{$i}}][e_way_bill_date]"
                                                            value="{{$item->e_way_bill_date}}" {{$disable}}>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="7">
                                                    <table id="" class="childTable"
                                                        style="width: 85%; min-width: 500px; margin-inline: auto;">
                                                        <tbody class="items_table_body">
                                                            <?php $j=0;
                                                             foreach($getconsignments->ConsignmentItem->ConsignmentSubItems as $subitem){
                                                                ?>
                                                            <tr>
                                                                <td width="200px">
                                                                    <div class="form-group form-group-sm">
                                                                        <label>Item</label>
                                                                        <select class="form-control select_item"
                                                                            name="data[{{$i}}][item_data][{{$j}}][item]"
                                                                            data-action="get-items"
                                                                            onchange="getItem(this);" {{$disable}}>
                                                                            <option value="">Select Item</option>
                                                                            @foreach($itemlists as $item_list)
                                                                            <option value="{{$item_list->id}}"
                                                                                {{ $item_list->id == $subitem->item ? 'selected' : ''}}>
                                                                                {{$item_list->brand_name}}</option>
                                                                            @endforeach

                                                                        </select>

                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-group-sm">
                                                                        <label>Quantity</label>
                                                                        <input type="number" class="form-control qty"
                                                                            name="" value="{{$subitem->quantity}}"
                                                                            {{$disable}}>
                                                                        <input type="hidden" class="form-control"
                                                                            name="data[{{$i}}][item_data][{{$j}}][quantity]"
                                                                            value="{{$subitem->quantity}}" {{$disable}}>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-group-sm">
                                                                        <label>Net Weight</label>
                                                                        <input type="number" class="form-control net"
                                                                            name="data[{{$i}}][item_data][{{$j}}][net_weight]"
                                                                            value="{{$subitem->net_weight}}" {{$disable}}>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-group-sm">
                                                                        <label>Gross Weight</label>
                                                                        <input type="number" class="form-control gross"
                                                                            name="data[{{$i}}][item_data][{{$j}}][gross_weight]"
                                                                            value="{{$subitem->gross_weight}}" {{$disable}}>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-group-sm">
                                                                        <label>Chargeable Weight</label>
                                                                        <input type="number"
                                                                            class="form-control charge_wt"
                                                                            name="data[{{$i}}][item_data][{{$j}}][chargeable_weight]"
                                                                            value="{{$subitem->chargeable_weight}}"
                                                                            {{$disable}}>
                                                                    </div>

                                                                </td>
                                                                <td>
                                                                    <div class="removeIcon"></div>
                                                                </td>
                                                            </tr>
                                                            <?php $j++; } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <div class="removeIcon"></div>
                                </td>
                            </tr>
                            <?php $i++; } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php }else{ ?>
            <div class="col-lg-12 layout-spacing">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <h4><b>Order Information</b></h4>
                        </div>
                    </div>
                </div>
                <table border="1" width="100%">
                    <div class="row">
                        <tr>
                            <th>Item Description</th>
                            <th>Mode of packing</th>
                            <th>Total Quantity</th>
                            <th>Total Net Weight</th>
                            <th>Total Gross Weight</th>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-small"
                                    value="{{old('description',isset($getconsignments->description)?$getconsignments->description:'')}}"
                                    name="description" list="json-datalist" onkeyup="showResult(this.value)"
                                    readonly><datalist id="json-datalist"></datalist></td>
                            <td><input type="text" class="form-control form-small"
                                    value="{{old('packing_type',isset($getconsignments->packing_type)?$getconsignments->packing_type:'')}}"
                                    name="packing_type" readonly></td>
                            <td align="center"><span id="tot_qty">
                                    <?php echo $getconsignments->total_quantity;?>
                                </span></td>
                            <td align="center"><span id="tot_nt_wt">
                                    <?php echo $getconsignments->total_weight;?>
                                </span> Kgs.</td>
                            <td align="center"><span id="tot_gt_wt">
                                    <?php echo $getconsignments->total_gross_weight;?>
                                </span> Kgs.</td>

                            <input type="hidden" name="total_quantity" id="total_quantity"
                                value="{{$getconsignments->total_quantity}}">
                            <input type="hidden" name="total_weight" id="total_weight"
                                value="{{$getconsignments->total_weight}}">
                            <input type="hidden" name="total_gross_weight" id="total_gross_weight"
                                value="{{$getconsignments->total_gross_weight}}">
                            <input type="hidden" name="total_freight" id="total_freight"
                                value="{{$getconsignments->total_freight}}">
                        </tr>
                    </div>
                </table>

            </div>
            <div class="col-lg-12 layout-spacing">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <div style="overflow-x:auto;">
                                <table>
                                    <tr>
                                        <th style="width: 160px">Order ID</th>
                                        <th style="width: 180px">Invoice Number</th>
                                        <th style="width: 160px">Invoice Date</th>
                                        <th style="width: 180px">Invoice Amount</th>
                                        <th style="width: 210px">E-way Bill Number</th>
                                        <th style="width: 200px">E-Way Bill Date</th>
                                        <th style="width: 160px">Quantity</th>
                                        <th style="width: 160px">Net Weight</th>
                                        <th style="width: 160px">Gross Weight</th>

                                    </tr>
                                </table>
                                <table style=" border-collapse: collapse;" id="items_table">
                                    <tbody>
                                        <tr></tr>
                                        <?php 
                                        $i=1;
                                        foreach($getconsignments->ConsignmentItems as $item){ 
                                            ?>
                                        <tr>
                                            <td><input type="text" class="form-control form-small orderid"
                                                    name="data[{{$i}}][order_id]"
                                                    value="{{old('order_id',isset($item->order_id)?$item->order_id:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="text" class="form-control form-small invc_no" id="1"
                                                    name="data[{{$i}}][invoice_no]"
                                                    value="{{old('invoice_no',isset($item->invoice_no)?$item->invoice_no:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="date" class="form-control form-small invc_date"
                                                    name="data[{{$i}}][invoice_date]"
                                                    value="{{old('invoice_date',isset($item->invoice_date)?$item->invoice_date:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="number" class="form-control form-small invc_amt"
                                                    name="data[{{$i}}][invoice_amount]"
                                                    value="{{old('invoice_amount',isset($item->invoice_amount)?$item->invoice_amount:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="number" class="form-control form-small ew_bill"
                                                    name="data[{{$i}}][e_way_bill]"
                                                    value="{{old('e_way_bill',isset($item->e_way_bill)?$item->e_way_bill:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="date" class="form-control form-small ewb_date"
                                                    name="data[{{$i}}][e_way_bill_date]"
                                                    value="{{old('e_way_bill_date',isset($item->e_way_bill_date)?$item->e_way_bill_date:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="number" class="form-control form-small qnt"
                                                    name="data[{{$i}}][quantity]"
                                                    value="{{old('quantity',isset($item->quantity)?$item->quantity:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="number" class="form-control form-small net"
                                                    name="data[{{$i}}][weight]"
                                                    value="{{old('weight',isset($item->weight)?$item->weight:'')}}"
                                                    readonly>
                                            </td>
                                            <td><input type="number" class="form-control form-small gross"
                                                    name="data[{{$i}}][gross_weight]"
                                                    value="{{old('gross_weight',isset($item->gross_weight)?$item->gross_weight:'')}}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-rounded insert-more"
                                                    readonly> + </button>
                                            </td>
                                        </tr>

                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php } ?>
            @if($getconsignments->lr_type == 0)
            {{--vehicle info--}}
            <div class="form-row" style="width: 100%">
                <h6 class="col-12">Vehicle Information</h6>

                <div class="form-group col-md-4">
                    <label>
                        Vehicle Number<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                        <option value="">Select vehicle no</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>
                        Driver Name<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="driver_id" name="driver_id" tabindex="-1">
                        <option value="">Select driver</option>
                        @foreach($drivers as $driver)
                        <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                '-'}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>
                        EDD<span class="text-danger">*</span>
                    </label>
                    <Input type="date" class="form-control form-small" name="edd" />
                </div>

                <div class="form-group col-12">
                    <input type="checkbox" id="chek" style="margin-left:19px;">
                    <label for="chek">Vehicle Purchase Information</label>
                </div>

                <div class="col-12 flex-wrap align-items-center" style="display:none; width: 100%;" id="veh">

                    <div class="form-group col-md-4">
                        <label>Vehicle Name<span class="text-danger">*</span></label>
                        <Input type="text" class="form-control form-small" name="transporter_name" />
                    </div>
                    <div class="form-group col-md-4">
                        <label>
                            Vehicle Type<span class="text-danger">*</span>
                        </label>
                        <select class="form-control my-select2 sete" id="vehicle_type" name="vehicle_type"
                            tabindex="-1">
                            <option value="">Select vehicle type</option>
                            @foreach($vehicletypes as $vehicle)
                            <option value="{{$vehicle->id}}">{{$vehicle->name}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>
                            Purchase Price<span class="text-danger">*</span>
                        </label>
                        <Input type="number" class="form-control form-small" name="purchase_price" />
                    </div>

                </div>

            </div>
            @endif

            <div class=" col-12 d-flex justify-content-end align-items-center" style="gap: 1rem; margin-top: 3rem;">
                {{-- <a class="mt-2 btn btn-outline-primary" href="{{url($prefix.'/consignments') }}"> Back</a>--}}
                <button type="submit" class="mt-2 btn btn-primary disableme"
                    style="height: 40px; width: 200px">Submit</button>
            </div>

    </form>
</div>
<!-- widget-content-area -->

@endsection
@section('js')
<script>
function insertMaintableRow() {
    var tid = $("#tid").val();
    $(".items_table").each(function() {
        var item_no = parseInt(tid) + 1;
        $("#tid").val(item_no);
        var tds = `<tr>
                            <td>
                                <table class="mainTr" id="` + item_no + `">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Order ID</label>
                                                <input type="text" class="form-control orderid" name="data[` +
            item_no + `][order_id]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Number</label>
                                                <input type="text" class="form-control invc_no" id="1"
                                                       name="data[` + item_no + `][invoice_no]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Date</label>
                                                <input type="date" class="form-control invc_date" name="data[` +
            item_no + `][invoice_date]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>Invoice Amount</label>
                                                <input type="number" class="form-control invc_amt" name="data[` +
            item_no + `][invoice_amount]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>E-way Bill Number</label>
                                                <input type="number" class="form-control ew_bill" name="data[` +
            item_no + `][e_way_bill]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-sm">
                                                <label>E-Way Bill Date</label>
                                                <input type="date" class="form-control ewb_date" name="data[` +
            item_no + `][e_way_bill_date]">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <table id="" class="childTable" style="width: 85%; min-width: 500px; margin-inline: auto;">
                                                <tbody class="items_table_body"><tr>
                                                    <td width="200px">
                                                        <div class="form-group form-group-sm">
                                                            <label>Item</label>
                                                            <select class="form-control select_item" name="data[` +
            item_no + `][item_data][0][item]" data-action="get-items" onchange="getItem(this);">
                                                            <option value="" disabled selected>Select</option>
                                                         
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Quantity</label>
                                                            <input type="number" class="form-control qty" name="">
                                                            <input type="hidden" class="form-control" name="data[` +
            item_no +
            `][item_data][0][quantity]">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Net Weight</label>
                                                            <input type="number" class="form-control net" name="data[` +
            item_no +
            `][item_data][0][net_weight]" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Gross Weight</label>
                                                            <input type="number" class="form-control gross" name="data[` +
            item_no +
            `][item_data][0][gross_weight]" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group form-group-sm">
                                                            <label>Chargeable Weight</label>
                                                            <input type="number" class="form-control charge_wt" name="data[` +
            item_no + `][item_data][0][chargeable_weight]" readonly>
                                                        </div>

                                                    </td>
                                                    <td width="50px"><div class="removeIcon"></div></td>
                                                </tr></tbody>
                                            </table>
                                            <span style="margin-right: 8%" class="addItem" onclick="insertItemTableRow()">+ Add Item</span>
                                        </td>
                                    </tr>
                                  </tbody>
                                </table>
                            </td>
                            <td width="50px"><div class="removeIcon removeInvoice"><span>x</span></div></td>
                        </tr>`;

        $(this).append(tds);
    });
};

// $(document).on("click", ".removeInvoice", function () {
//     $(this).closest("tr").remove();
// });



//Reassign the Ids of the row
function reassign_ids() {
    var i = 0;
    var t = document.getElementsByClassName("mainTr");
    var totalMainTr = document.getElementsByClassName("mainTr").length;
    alert(totalMainTr);
    $(".mainTr tr").each(function() {
        var srno = $(t.rows[i].cells[0]).text();
        if (totalMainTr == 1) {
            i++;
        }
        if (parseInt(totalMainTr) >= 2) {
            alert(i + "jk");
            $(t.rows[i])
                .closest("tr")
                .find(".orderid")
                .attr("name", "data[" + i + "][orderid]");
            $(t.rows[i])
                .closest("tr")
                .find(".invc_no")
                .attr("name", "data[" + i + "][invoice_no]");
            $(t.rows[i])
                .closest("tr")
                .find(".invc_date")
                .attr("name", "data[" + i + "][invoice_date]");
            $(t.rows[i])
                .closest("tr")
                .find(".invc_amt")
                .attr("name", "data[" + i + "][invoice_amount]");
            $(t.rows[i])
                .closest("tr")
                .find(".ew_bill")
                .attr("name", "data[" + i + "][e_way_bill]");
            // $(t.rows[i]).closest('tr').find('.frei').attr('name', 'data['+i+'][freight]');
            $(t.rows[i])
                .closest("tr")
                .find(".ewb_date")
                .attr("name", "data[" + i + "][e_way_bill_date]");


            i++;
        }
    });
}

//Reassign the Ids of the row items
function reassign_itemids(_this) {
    var i = 0;
    // var tid = $(_this).closest("#tid").val();
    var tid = document.getElementById("tid");
    // alert(tid+" ll");
    var t = document.getElementsByClassName("childTable");
    // var totalChildTr = document.getElementsByClassName("childTable tr").length;
    var totalChildTr = $(".removeItem").closest('.items_table_body').find('tr').length;
    // console.log(totalChildTr);
    $(".childTable tr").each(function() {
        // var srno = $(t.rows[i].cells[0]).text();
        if (totalChildTr == 1) {
            i++;
        }
        if (parseInt(totalChildTr) >= 2) {
            // alert(i+"kk");
            $(t.rows[i])
                .closest("tr")
                .find(".select_item")
                .attr("name", "data[" + i + "][item_data][" + i + "][item]");
            $(t.rows[i])
                .closest("tr")
                .find(".qty")
                .attr("name", "data[" + i + "][item_data][" + i + "][quantity]");
            $(t.rows[i])
                .closest("tr")
                .find(".net")
                .attr("name", "data[" + i + "][item_data][" + i + "][net_weight]");
            $(t.rows[i])
                .closest("tr")
                .find(".gross")
                .attr("name", "data[" + i + "][item_data][" + i + "][gross_weight]");
            $(t.rows[i])
                .closest("tr")
                .find(".charge_wt")
                .attr("name", "data[" + i + "][item_data][" + i + "][chargeable_weight]");


            i++;
        }
    });
}


// add item row btn
$(document).on("click", ".addItem", function() {
    // var cc = $(this).siblings('.childTable').find('.charge_wt').length;
    // var itemrows = cc;
    var mainrows = $(this).closest('table').attr('id');
    $(this).siblings(".childTable").each(function() {
        var itemrows = $(this).parents('.mainTr').children().children().eq(1).children().children(
            '.childTable').children().children().length;
        var itemTds = `<tr>
                            <td width="200px">
                                <div class="form-group form-group-sm">
                                    <label>Item</label>
                                    <select class="form-control select_item" name="data[` + mainrows +
            `][item_data][` + itemrows + `][item]" data-action="get-items" onchange="getItem(this);">
                                    <option value="" disabled selected>Select</option>
                                     
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group form-group-sm">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control qty" name="">
                                    <input type="hidden" class="form-control" name="data[` + mainrows +
            `][item_data][` + itemrows + `][quantity]">
                                </div>
                            </td>
                            <td>
                                <div class="form-group form-group-sm">
                                    <label>Net Weight</label>
                                    <input type="number" class="form-control net" name="data[` + mainrows +
            `][item_data][` + itemrows + `][net_weight]" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="form-group form-group-sm">
                                    <label>Gross Weight</label>
                                    <input type="number" class="form-control gross" name="data[` + mainrows +
            `][item_data][` + itemrows + `][gross_weight]" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="form-group form-group-sm">
                                    <label>Chargeable Weight</label>
                                    <input type="number" class="form-control charge_wt" name="data[` + mainrows +
            `][item_data][` + itemrows + `][chargeable_weight]" readonly>
                                </div>

                            </td>
                            <td width="50px"><div class="removeIcon removeItem"><span>x</span></div></td>
                        </tr>`;

        // $(this).siblings(".childTable").children('.items_table_body').append(itemTds);
        $(this).closest('.childTable').children('.items_table_body').append(itemTds);
    });
});


$(document).on("click", ".removeItem", function() {
    $(this).closest("tr").remove();
    var _this = $(this);
    // reassign_itemids(_this);            
    calculate_totals(_this);
});

// calculation total
$(document).on("blur", ".qty", function() {
    // var _this = $(this);

    var qty_sum = 0;
    $("input[class *= 'qty']").each(function() {
        qty_sum += +$(this).val();
    });
    $("#tot_qty").html(qty_sum);
    var qty_val = $(this).val();

    $(this).siblings().val(qty_val);

    var netwt_val = $(this).closest('tr').find('td').eq(2).find('input').val();
    var netwt_cal = parseInt(netwt_val) * parseInt(qty_val);

    var grosswt_val = $(this).closest('tr').find('td').eq(3).find('input').val();
    var grosswt_cal = parseInt(grosswt_val) * parseInt(qty_val);

    var chargewt_val = $(this).closest('tr').find('td').eq(4).find('input').val();
    var chargewt_cal = parseInt(chargewt_val) * parseInt(qty_val);

    var check_netwt = $(this).closest('tr').find('td').eq(2).find('input').val(netwt_cal);
    var check_grosswt = $(this).closest('tr').find('td').eq(3).find('input').val(grosswt_cal);
    var check_chargewt = $(this).closest('tr').find('td').eq(4).find('input').val(chargewt_cal);

    var net_sum = 0;
    $("input[class *= 'net']").each(function() {
        net_sum += +$(this).val();
    });
    $("#total_nt_wt").html(net_sum);

    var gross_sum = 0;
    $("input[class *= 'gross']").each(function() {
        gross_sum += +$(this).val();
    });
    $("#total_gt_wt").html(gross_sum);

    $(this).prop('disabled', true);
});

//Remove the current invoice row
$(document).on("click", ".removeInvoice", function() {
    var current_val = $(this).parent().siblings(":first").text();
    $(this).closest("tr").remove();
    // reassign_ids();
    var _this = $(this);
    calculate_totals(_this);
});

// Calculate all totals
function calculate_totals(_this) {
    var qty_sum = 0;
    $("input[class *= 'qty']").each(function() {
        qty_sum += +$(this).val();
    });

    $("#tot_qty").html(qty_sum);
    var qty_val = $(_this).val();

    var netwt_val = $(_this).closest('tr').find('td').eq(2).find('input').val();
    var netwt_cal = parseInt(netwt_val) * parseInt(qty_val);

    var grosswt_val = $(_this).closest('tr').find('td').eq(3).find('input').val();
    var grosswt_cal = parseInt(grosswt_val) * parseInt(qty_val);

    var chargewt_val = $(_this).closest('tr').find('td').eq(4).find('input').val();
    var chargewt_cal = parseInt(chargewt_val) * parseInt(qty_val);

    var check_netwt = $(_this).closest('tr').find('td').eq(2).find('input').val(netwt_cal);
    var check_grosswt = $(_this).closest('tr').find('td').eq(3).find('input').val(grosswt_cal);
    var check_chargewt = $(_this).closest('tr').find('td').eq(4).find('input').val(chargewt_cal);

    var net_sum = 0;
    $("input[class *= 'net']").each(function() {
        net_sum += +$(this).val();
    });
    $("#total_nt_wt").html(net_sum);

    var gross_sum = 0;
    $("input[class *= 'gross']").each(function() {
        gross_sum += +$(this).val();
    });
    $("#total_gt_wt").html(gross_sum);

    $(_this).prop('disabled', true);

}


jQuery(function() {
    $('.my-select2').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            dropdownParent: $(this).parent(), // fix select2 search input focus bug
        })
    })

    // fix select2 bootstrap modal scroll bug
    $(document).on('select2:close', '.my-select2', function(e) {
        var evt = "scroll.select2"
        $(e.target).parents().off(evt)
        $(window).off(evt)
    })
})

// add consignment date
$('#consignDate, #date').val(new Date().toJSON().slice(0, 10));

function showResult(str) {
    if (str.length == 0) {
        $(".search-suggestions").empty();
        $(".search-suggestions").css('border', '0px');
    } else if (str.length > 0) {
        $(".search-suggestions").css('border', 'solid 1px #f6f6f6');
        var options = '';
        options = "<option value='Seeds'>";
        options += "<option value='Chemicals'>";
        options += "<option value='PGR'>";
        options += "<option value='Fertilizer'>";
        options += "<option value='Pesticides'>";
        $('#json-datalist').html(options);
    }
}

$('#chek').click(function() {
    $('#veh').toggleClass('d-flex');
});

function myMap() {
    var mapProp = {
        center: new google.maps.LatLng(51.508742, -0.120850),
        zoom: 5,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
}

// invoice no duplicate validate
$('.invc_no').blur(function() {
    var invc_no = $(this).val();
    var row_id = jQuery(this).attr('id');
    $.ajax({
        url: "/invoice-check",
        method: "get",
        data: {
            invc_no: invc_no
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (response.success == true) {
                swal('error', response.errors, 'error');
                $("#" + row_id).val('');
            }
        }
    })
});

// select item onchange
function getItem(item) {
    var item_val = item.value;
    $.ajax({
        url: '/get-items',
        method: "get",
        data: {
            item_val: item_val
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            console.log(res);
            if (res.success == true) {
                ($(item).closest('tr').find('td').eq(1).find('input').prop('disabled', false));
                ($(item).closest('tr').find('td').eq(1).find('input').val(''));

                ($(item).closest('tr').find('td').eq(2).find('input').val(res.item.net_weight));
                ($(item).closest('tr').find('td').eq(3).find('input').val(res.item.gross_weight));
                ($(item).closest('tr').find('td').eq(4).find('input').val(res.item.chargable_weight));
            }
        }
    });
}

// append address
$(document).ready(function() {
    var regclient_id = $('#select_regclient').val();
    var consigner_id = $('#select_consigner').val();
    var consignee_id = $('#select_consignee').val();
    var shipto_id = $('#select_ship_to').val();
    $.ajax({
        type: 'get',
        url: APP_URL + '/get_consigners',
        data: {
            consigner_id: consigner_id,
            regclient_id: regclient_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            // $('#consigner_address').empty();
            // $('#consignee_address').empty();
            // $('#ship_to_address').empty();

            $('#select_consignee').append('<option value="">Select Consignee</option>');
            $('#select_ship_to').append('<option value="">Select Ship To</option>');
            $.each(res.consignee, function(key, value) {
                $('#select_consignee, #select_ship_to').append('<option value="' + value
                    .id + '">' + value.nick_name + '</option>');
            });
            if (res.data) {
                console.log(res.data);
                if (res.data.address_line1 == null) {
                    var address_line1 = '';
                } else {
                    var address_line1 = res.data.address_line1 + '<br>';
                }
                if (res.data.address_line2 == null) {
                    var address_line2 = '';
                } else {
                    var address_line2 = res.data.address_line2 + '<br>';
                }
                if (res.data.address_line3 == null) {
                    var address_line3 = '';
                } else {
                    var address_line3 = res.data.address_line3 + '<br>';
                }
                if (res.data.address_line4 == null) {
                    var address_line4 = '';
                } else {
                    var address_line4 = res.data.address_line4 + '<br>';
                }
                if (res.data.gst_number == null) {
                    var gst_number = '';
                } else {
                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                }
                if (res.data.phone == null) {
                    var phone = '';
                } else {
                    var phone = 'Phone: ' + res.data.phone;
                }
                $('#consigner_address').append(address_line1 + ' ' + address_line2 + '' +
                    address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
                $("#dispatch").val(res.data.city);
                if (res.data.get_reg_client.name == null) {
                    var regclient = '';
                } else {
                    var regclient = res.data.get_reg_client.name;
                }
                $("#regclient").val(regclient);
                // multiple invoice chaeck on regclicent //
                if (res.regclient == null) {
                    var multiple_invoice = '';
                } else {
                    if (res.regclient.is_multiple_invoice == null || res.regclient
                        .is_multiple_invoice == '') {
                        var multiple_invoice = '';
                    } else {
                        var multiple_invoice = res.regclient.is_multiple_invoice;
                    }
                }
                if (multiple_invoice == 1) {
                    $('.insert-more').attr('disabled', false);
                } else {
                    $('.insert-more').attr('disabled', true);
                }
            }
        }
    });
    ///////get consinee address//
    $.ajax({
        type: 'get',
        url: APP_URL + '/get_consignees',
        data: {
            consignee_id: consignee_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            // $('#consignee_address').empty();
            if (res.data) {
                console.log(res.data);
                if (res.data.address_line1 == null) {
                    var address_line1 = '';
                } else {
                    var address_line1 = res.data.address_line1 + '<br>';
                }
                if (res.data.address_line2 == null) {
                    var address_line2 = '';
                } else {
                    var address_line2 = res.data.address_line2 + '<br>';
                }
                if (res.data.address_line3 == null) {
                    var address_line3 = '';
                } else {
                    var address_line3 = res.data.address_line3 + '<br>';
                }
                if (res.data.address_line4 == null) {
                    var address_line4 = '';
                } else {
                    var address_line4 = res.data.address_line4 + '<br>';
                }
                if (res.data.gst_number == null) {
                    var gst_number = '';
                } else {
                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                }
                if (res.data.phone == null) {
                    var phone = '';
                } else {
                    var phone = 'Phone: ' + res.data.phone;
                }
                $('#consignee_address').append(address_line1 + ' ' + address_line2 + '' +
                    address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
            }
        }
    });
    //////////get ship to address///
    $.ajax({
        type: 'get',
        url: APP_URL + '/get_consignees',
        data: {
            consignee_id: shipto_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            // $('#ship_to_address').empty();
            if (res.data) {
                if (res.data.address_line1 == null) {
                    var address_line1 = '';
                } else {
                    var address_line1 = res.data.address_line1 + '<br>';
                }
                if (res.data.address_line2 == null) {
                    var address_line2 = '';
                } else {
                    var address_line2 = res.data.address_line2 + '<br>';
                }
                if (res.data.address_line3 == null) {
                    var address_line3 = '';
                } else {
                    var address_line3 = res.data.address_line3 + '<br>';
                }
                if (res.data.address_line4 == null) {
                    var address_line4 = '';
                } else {
                    var address_line4 = res.data.address_line4 + '<br>';
                }
                if (res.data.gst_number == null) {
                    var gst_number = '';
                } else {
                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                }
                if (res.data.phone == null) {
                    var phone = '';
                } else {
                    var phone = 'Phone: ' + res.data.phone;
                }
                $('#ship_to_address').append(address_line1 + ' ' + address_line2 + '' +
                    address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
            }
        }
    });
});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ6x_bU2BIZPPsjS8Y8Zs-yM2g2Bs2mnM&callback=myMap">
</script>
@endsection