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
            <div class="row">
                <div class="col-md-4">
                    <p><b>Regional Client Name: </b>{{ucfirst($getClientDetail->RegClient->name ?? '-')}}</p>
                </div>
                <!-- <div class="col-md-4">
                    <p><b>Open Delivery Charge:</b> <input name="" value="250.00"></p>
                </div> -->
                <div class="col-md-4">
                    <p><b>Docket Charge: </b> {{ $getClientDetail->docket_price ?? '-'}}</p>
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
                        </tr>
                        @if(!empty($getClientDetail->ClientPriceDetails))
                        @foreach($getClientDetail->ClientPriceDetails as $key => $value)
                        <tr class="rowcls">
                            <td>{{ $value->from_state ?? '-'}}</td>
                            <td>{{ $value->to_state ?? '-'}}</td>
                            <td>{{ $value->price_per_kg ?? '-'}}</td>
                            <td>{{ $value->open_delivery_price ?? '-'}}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr class="rowcls">
                            <td colspan="4" class="text-center">No Record Found </td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="btn-section mt-60">
                    <a class="btn-white btn-cstm btn" href="{{url($prefix.'/reginal-clients')}}"><span>Back</span></a>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection