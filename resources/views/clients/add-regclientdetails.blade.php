@extends('layouts.main')
@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="page-header">
        <nav class="breadcrumb-one" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url($prefix.'/reginal-clients')}}">Regional Clients</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Add Regional Client Detail</a></li>
            </ol>
        </nav>
    </div>
    <div class="statbox widget box box-shadow">
        <!-- <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Animated Line</h4>
                </div>
            </div>
        </div> -->
        
        <div class="widget-content widget-content-area animated-underline-content">
            
            <ul class="nav nav-tabs  mb-3" id="animateLine" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active adddetail-tab" id="animated-underline-home-tab" data-toggle="tab" href="#animated-underline-home" role="tab" aria-controls="animated-underline-home" aria-selected="true"><b> Home</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link regbank-detail" id="animated-underline-profile-tab" data-toggle="tab" href="#animated-underline-profile" role="tab" aria-controls="animated-underline-profile" aria-selected="false"><b> Add Bank Details</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="animated-underline-contact-tab" data-toggle="tab" href="#animated-underline-contact" role="tab" aria-controls="animated-underline-contact" aria-selected="false"><b> Contact</b></a>
                </li>
            </ul>

            <div class="tab-content" id="animateLineContent-4">
                <div class="tab-pane fade show active regclient-detail" id="animated-underline-home" role="tabpanel" aria-labelledby="animated-underline-home-tab">
                    <form class="contact-info" method="POST" action="{{url($prefix.'/save-regclient-detail')}}" id="createregclientdetail">
                        <div class="row">
                            <div class="col-md-4">
                                <p><b>Regional Client Name:</b> <input class="form-control" name="regclient_name" value="{{ucfirst($regclient_name->name ?? '-')}}" readonly></p>
                                <input type="hidden" name="regclient_id" value="{{$regclient_name->id ?? ''}}">
                            </div>
                            <!-- <div class="col-md-4">
                                <p><b>Open Delivery Charge:</b> <input name="" value="250.00"></p>
                            </div> -->
                            <div class="col-md-4">
                                <p><b>Docket Charge:</b> <input class="form-control" name="docket_price" value=""></p>
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
                                            <option value="{{ $state }}">{{ucwords($state)}}</option>
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
                <div class="tab-pane fade Bankdetail" id="animated-underline-profile" role="tabpanel" aria-labelledby="animated-underline-profile-tab">
                    <div class="media">
                        <div class="media-body">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" placeholder=""> 
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Branch Name</label>
                                    <input type="text" class="form-control" name="branch_name" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">IFSC</label>
                                    <input type="text" class="form-control" name="ifsc" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Account No</label>
                                    <input type="text" class="form-control" name="account_number" placeholder="">
                                </div>                                
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Account Holder Name</label>
                                    <input type="text" class="form-control" name="account_holdername" placeholder="">
                                </div>
                                
                            </div>

                            <!-- <div class="btn-section mt-60">
                                <button type="button" class="checkForm btn-primary btn-cstm btn mr-4 kyc-click"><span>Next</span></button>

                                <a class="btn-white btn-cstm btn" href="{{url($prefix.'/reginal-clients')}}"><span>Cancel</span></a>
                            </div> -->
                            
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="animated-underline-contact" role="tabpanel" aria-labelledby="animated-underline-contact-tab">
                    <p class="dropcap  dc-outline-primary">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
@section('js')
<script>
    // $("a").click(function(){
    function addrow(){
        var i = $('.rowcls').length;
        i  = i + 1;
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