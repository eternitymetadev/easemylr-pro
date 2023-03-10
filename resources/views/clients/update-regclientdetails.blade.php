@extends('layouts.main')
@section('content')


<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url($prefix.'/clients')}}">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update
                                Details</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <form class="contact-info" method="POST" action="{{url($prefix.'/regclient-detail/update-detail')}}"
                    id="updateregclientdetail">
                    <input type="hidden" name="regclientdetail_id" value="{{$regclient_name->id}}">

                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Client Name<span class="text-danger">*</span></label>
                            <select class="form-control  my-select2" id="base_client_id" name="base_client_id"
                                tabindex="-1">
                                <option selected disabled>select..</option>
                                @foreach($base_clients as $base_client)
                                <option value="{{$base_client->id}}"
                                    {{ $base_client->id == $regclient_name->baseclient_id ? 'selected' : ''}}>
                                    {{$base_client->client_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlSelect1">Branch Location</label>
                            <select class="form-control  my-select2" id="branch_id" name="branch_id" tabindex="-1">
                                <option selected disabled>select..</option>
                                @foreach($locations as $location)
                                <option value="{{$location->id}}"
                                    {{ $location->id == $regclient_name->location_id ? 'selected' : ''}}>
                                    {{$location->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlSelect1">Regional Client Name</label>
                            <input type="text" class="form-control" id="regional_client_name" name="name" placeholder=""
                                value="{{old('name',isset($regclient_name->name)?$regclient_name->name:'')}}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Regional Client Nick Name<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="regional_client_nick_name" placeholder=""
                                value="{{old('regional_client_nick_name',isset($regclient_name->regional_client_nick_name)?$regclient_name->regional_client_nick_name:'')}}">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlSelect1">Email</label>
                            <input type="email" class="form-control" name="email" placeholder=""
                                value="{{old('email',isset($regclient_name->email)?$regclient_name->email:'')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Phone<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="phone" placeholder=""
                                value="{{old('phone',isset($regclient_name->phone)?$regclient_name->phone:'')}}">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">GST Number<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="gst_no" placeholder=""
                                value="{{old('gst_no',isset($regclient_name->gst_no)?$regclient_name->gst_no:'')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">PAN<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pan" placeholder=""
                                value="{{old('pan',isset($regclient_name->pan)?$regclient_name->pan:'')}}">
                        </div>
                    </div>
                    <!-- <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Upload GST RC<span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="upload_gst" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Upload PAN<span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="upload_pan" placeholder="">
                        </div>
                    </div> -->
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Multiple Invoice<span
                                    class="text-danger">*</span></label>
                            <select class="form-control is_multiple_invoice" name="is_multiple_invoice">
                                <option value="">Select</option>
                                <option value="1" {{$regclient_name->is_multiple_invoice == 1 ? 'selected' : ''}}>Per
                                    invoice-Item wise</option>
                                <option value="2" {{$regclient_name->is_multiple_invoice == 2 ? 'selected' : ''}}>
                                    Multiple Invoice-Item wise</option>
                                <option value="3" {{$regclient_name->is_multiple_invoice == 3 ? 'selected' : ''}}>per
                                    invoice-Without Item</option>
                                <option value="4" {{$regclient_name->is_multiple_invoice == 4 ? 'selected' : ''}}>LR
                                    Multiple invoice-Without item</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Pickup not Rquired<span
                                    class="text-danger">*</span></label>
                            <div class="check-box d-flex">
                                <div class="checkbox radio">
                                    <label class="check-label">Yes
                                        <input type="radio" value='1' name="is_prs_pickup"
                                            {{ ($regclient_name->is_prs_pickup=="1")? "checked" : "" }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">No
                                        <input type="radio" name="is_prs_pickup" value='0'
                                            {{ ($regclient_name->is_prs_pickup=="0")? "checked" : "" }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Email Sent<span class="text-danger">*</span></label>
                            <div class="check-box d-flex">
                                <div class="checkbox radio">
                                    <label class="check-label">Yes
                                        <input type="radio" value='1' name="is_email_sent"
                                            {{ ($regclient_name->is_email_sent=="1")? "checked" : "" }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">No
                                        <input type="radio" name="is_email_sent" value='0'
                                            {{ ($regclient_name->is_email_sent=="0")? "checked" : "" }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php 
                          $payment_term = explode(',',$regclient_name->payment_term);
                         ?>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Select Payment Terms<span
                                    class="text-danger">*</span></label>
                            <div class="check-box d-flex" style="margin: 6px 0 0 6px">
                                <div class="checkbox radio">
                                    <label class="check-label d-flex align-items-center" style="gap: 6px">
                                        <span class="checkmark"></span>
                                        <input type="checkbox" value='To be Billed' name="payment_term[]"
                                            {{in_array("To be Billed", $payment_term) ? 'checked' : '' }} />
                                        TBB
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label d-flex align-items-center" style="gap: 6px">
                                        <span class="checkmark"></span>
                                        <input type="checkbox" name="payment_term[]" value='To Pay'
                                            {{in_array("To Pay", $payment_term) ? 'checked' : '' }} />
                                        To Pay
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label d-flex align-items-center" style="gap: 6px">
                                        <span class="checkmark"></span>
                                        <input type="checkbox" name="payment_term[]" value='Paid'
                                            {{in_array("Paid", $payment_term) ? 'checked' : '' }} />
                                        Paid
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-section mt-60">
                        <button type="submit" class="btn-primary btn-cstm btn mr-4"><span>Save</span></button>

                        <a class="btn-white btn-cstm btn"
                            href="{{url($prefix.'/reginal-clients')}}"><span>Cancel</span></a>
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
$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
});
</script>
@endsection