@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url($prefix.'/clients')}}">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update Details</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <form class="contact-info" method="POST" action="{{url($prefix.'/regclient-detail/update-detail')}}" id="updateregclientdetail">
                <input type="hidden" name="regclientdetail_id" value="{{$getClientDetail->id}}">
                    <div class="row">
                        <div class="col-md-4">
                            <p><b>Regional Client Name:</b> <input class="form-control" name="regclient_name" value="{{old('name',isset($getClientDetail->RegClient->name)?$getClientDetail->RegClient->name:'')}}" readonly></p>
                            <input type="hidden" name="regclient_id" value="{{$getClientDetail->RegClient->id ?? ''}}">
                        </div>
                        <div class="col-md-4">
                            <p><b>Docket Charge:</b> <input class="form-control" name="docket_price" value="{{old('docket_price',isset($getClientDetail->docket_price)?$getClientDetail->docket_price:'')}}"></p>
                        </div>
                    </div>
                    <div class="mt-3 proposal_detail_box">
                        <div class="table-responsive">
                            <table id="myTable" class="table">
                                <tr>
                                    <th>Source</th>
                                    <th>Destination</th>
                                    <th>Price/(Kg)</th>
                                    <th>Open Delivery Charge</th>                                        
                                    <th>Action</th>
                                </tr>
                                <?php
                                $i=0;
                                foreach($getClientDetail->ClientPriceDetails as $key=>$clientpricedata){ 
                                ?>
                                <input type="hidden" name="data[{{$i}}][hidden_id]" value="{!! $clientpricedata->id !!}">
                                <tr class="rowcls">
                                    <td>
                                    <select class="form-control" name="data[{{$i}}][from_state]">
                                        <option value="">Select</option>
                                        @foreach($zonestates as $key => $state)
                                        <option value="{{ $state }}" {{$clientpricedata->from_state == $state ? 'selected' : ''}}>{{ucwords($state)}}</option>
                                        @endforeach
                                    </select>
                                    </td>
                                    <td>
                                    <select class="form-control" name="data[{{$i}}][to_state]">
                                        <option value="">Select</option>
                                        @foreach($zonestates as $key => $state)
                                        <option value="{{ $state }}" {{$clientpricedata->to_state == $state ? 'selected' : ''}}>{{ucwords($state)}}</option>
                                        @endforeach
                                    </select>
                                    </td>
                                    <td><input class="form-control" type="text" name="data[{{$i}}][price_per_kg]" value="{{old('price_per_kg',isset($clientpricedata->price_per_kg)?$clientpricedata->price_per_kg:'')}}"></td>
                                    <td><input class="form-control" type="text" name="data[{{$i}}][open_delivery_price]" value="{{old('open_delivery_price',isset($clientpricedata->open_delivery_price)?$clientpricedata->open_delivery_price:'')}}"></td>
                                    <td>
                                        <button type="button" class="btn btn-primary" id="addRow" onclick="addrow()"><i class="fa fa-plus-circle"></i></button>

                                        <button type="button" class="btn btn-danger delete_client" data-id="{{ $clientpricedata->id }}" data-action="<?php echo URL::to($prefix.'/clientdetails/delete-client'); ?>"><i class="fa fa-minus-circle"></i></button>
                                    </td>
                                </tr>
                                <?php $i++; } ?>
                            </table>
                        </div>
                        <div class="btn-section mt-60">
                            <button type="submit" class="btn-primary btn-cstm btn mr-4" ><span>Save</span></button>

                            <a class="btn-white btn-cstm btn" href="{{url($prefix.'/reginal-clients')}}"><span>Cancel</span></a>
                        </div>
                    </div>
                </form>
            
            </div>
        </div>
    </div>
</div>
@include('models.delete-client')
@endsection
@section('js')
<script>
    // $("a").click(function(){
    function addrow(){
        var i = $('.rowcls').length;
        // i  = i + 1;
        var rows = '';

        rows+= '<tr class="rowcls">';
        rows+= '<td>';
        rows+= '<select class="form-control" name="data['+i+'][from_state]">';
        rows+= '<option value="">Select</option>@foreach($zonestates as $key => $state)<option value="{{ $state }}">{{ucwords($state)}}</option>@endforeach';
        rows+= '</select>';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<select class="form-control" name="data['+i+'][to_state]">';
        rows+= '<option value="">Select</option>@foreach($zonestates as $key => $state)<option value="{{ $state }}">{{ucwords($state)}}</option>@endforeach';
        rows+= '</select>';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<input class="form-control" type="text" name="data['+i+'][price_per_kg]" value="">';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<input class="form-control" type="text" name="data['+i+'][open_delivery_price]" value="">';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<button type="button" class="btn btn-danger removeRow"><i class="fa fa-minus-circle"></i></button>';
        rows+= '</td>';
        rows+= '</tr>';

        $('#myTable tbody').append(rows);

    }

    $(document).on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
    });
</script>
@endsection