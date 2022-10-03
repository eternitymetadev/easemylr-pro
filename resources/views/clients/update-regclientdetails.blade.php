@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url($prefix.'/clients')}}">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Regional Client Details</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <form class="contact-info" method="POST" action="{{url($prefix.'/save-regclient-detail')}}" id="createregclientdetail">
                    <div class="row">
                        <div class="col-md-4">
                            <p><b>Regional Client Name:</b> <input class="form-control" name="regclient_name" value="{{old('name',isset($getClientDetail->RegClient->name)?$getClientDetail->RegClient->name:'')}}{{ucfirst($getClientDetail->RegClient->name ?? '-')}}" readonly></p>
                            <input type="hidden" name="regclient_id" value="{{$getClientDetail->RegClient->id ?? ''}}">
                        </div>
                        <div class="col-md-4">
                            <p><b>Docket Charge:</b> <input class="form-control" name="docket_price" value="{{old('docket_price',isset($getClientDetail->docket_price)?$getClientDetail->docket_price:'')}}{{ucfirst($getClientDetail->docket_price ?? '-')}}"></p>
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
                                <tr class="rowcls">
                                    <td>
                                    <select class="form-control" name="data[1][from_state]">
                                        <option value="">Select</option>
                                        @foreach($zonestates as $key => $state)
                                        <option value='{{$key}}' {{$getClientDetail->ClientPriceDetails->ZoneFromState->id == $key ? 'selected' : ''}} >{{ucfirst($state)}}</option>
                                        @endforeach
                                    </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="data[1][to_state]">
                                            <option value="">Select</option>
                                            @foreach($zonestates as $key => $state)
                                            <option value="{{ $state }}">{{ucwords($state)}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input class="form-control" type="text" name="data[1][price_per_kg]" value=""></td>
                                    <td><input class="form-control" type="text" name="data[1][open_delivery_price]" value=""></td>
                                    <td>
                                        <button type="button" class="btn btn-primary" id="addRow" onclick="addrow()"><i class="fa fa-plus-circle"></i></button>
                                    </td>
                                </tr>
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

@endsection