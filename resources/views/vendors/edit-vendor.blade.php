@extends('layouts.main')
@section('content')
<?php
// echo'<pre>'; print_r($getvendor->declaration_available); die;
?>
<style>
.row.layout-top-spacing {
    width: 80%;
    margin: auto;
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

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Vendors</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create
                                Vendors</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Create Vehicle</h5></div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form id="update_vendor" method="POST" action="{{url($prefix.'/vendor/update-vendor')}}">
                            @csrf
                            <h3>Vendor Contact Details</h3>
                            <input type="hidden" class="form-control" name="vendor_id" placeholder="" value="{{$getvendor->id}}">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Vendor Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="vendor_name" name="name" placeholder=""
                                    value="{{old('name',isset($getvendor->name)?$getvendor->name:'')}}" >
                                </div>
                                <?php $otherdetails = json_decode(old('other_details',isset($getvendor->other_details)?$getvendor->other_details:''));
                                // 
                                ?>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Transporter Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="transporter_name" placeholder="" value="{{old('$otherdetails->transporter_name',isset($otherdetails->transporter_name)?$otherdetails->transporter_name:'')}}">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Driver </label>
                                    <select class="form-control  my-select2" id="driver_id" name="driver_id"
                                        tabindex="-1">
                                        <option selected disabled>Select</option>
                                        @foreach($drivers as $driver)
                                        <option value="{{$driver->id}}" {{ $driver->id == $getvendor->driver_id ? 'selected' : ''}}>{{$driver->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Contact Number<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="contact_person_number" placeholder="" value="{{old('$otherdetails->contact_person_number',isset($otherdetails->contact_person_number)?$otherdetails->contact_person_number:'')}}">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Contact Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="" value="{{old('email',isset($getvendor->email)?$getvendor->email:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Vendor Type<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control  my-select2" id="vendor_type" name="vendor_type"
                                        tabindex="-1">
                                        <option selected disabled>Select</option>
                                        <option value="Individual" {{$getvendor->vendor_type == 'Individual' ? 'selected' : ''}}>Individual </option>
                                        <option value="Proprietorship" {{$getvendor->vendor_type == 'Proprietorship' ? 'selected' : ''}}>Proprietorship</option>
                                        <option value="Company" {{$getvendor->vendor_type == 'Company' ? 'selected' : ''}}>Company</option>
                                        <option value="Firm" {{$getvendor->vendor_type == 'Firm' ? 'selected' : ''}}>Firm</option>
                                        <option value="HUF" {{$getvendor->vendor_type == 'HUF' ? 'selected' : ''}}>HUF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Declaration Available<span
                                            class="text-danger">*</span></label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input no_decl" type="radio"
                                            name="decalaration_available" id="cds" value="1" {{$getvendor->declaration_available == '1' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input no_decl" type="radio"
                                            name="decalaration_available" id="" value="2" {{$getvendor->declaration_available == '2' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                    <input type="file" class="form-control" id="declaration_file"
                                        name="declaration_file" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">TDS Rate applicacle</label>
                                    <input type="text" class="form-control" id="tds_rate" name="tds_rate" placeholder="" value="{{old('tds_rate',isset($getvendor->tds_rate)?$getvendor->tds_rate:'')}}"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Branch Location</label>
                                    <select class="form-control  my-select2" id="branch_id" name="branch_id"
                                        tabindex="-1">
                                        <option selected disabled>Select</option>
                                        @foreach($branchs as $branch)
                                        <option value="{{$branch->id}}"{{ $branch->id == $getvendor->branch_id ? 'selected' : ''}}>{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <h3>Vendor NEFT details</h3>
                            <?php $bankdetails = json_decode(old('bank_details',isset($getvendor->bank_details)?$getvendor->bank_details:''));
                                ?>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Account Holder Name </label>
                                    <input type="text" class="form-control" name="acc_holder_name" placeholder="" value="{{old('$bankdetails->acc_holder_name',isset($bankdetails->acc_holder_name)?$bankdetails->acc_holder_name:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Account No.</label>
                                    <input type="text" class="form-control" id="account_no" name="account_no" placeholder="" value="{{old('$bankdetails->account_no',isset($bankdetails->account_no)?$bankdetails->account_no:'')}}">
                                </div>
                            </div>

                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Ifsc Code</label>
                                    <input type="text" class="form-control" id="" name="ifsc_code" placeholder="" value="{{old('$bankdetails->ifsc_code',isset($bankdetails->ifsc_code)?$bankdetails->ifsc_code:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" placeholder="" value="{{old('$bankdetails->bank_name',isset($bankdetails->bank_name)?$bankdetails->bank_name:'')}}">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Branch Name</label>
                                    <input type="text" class="form-control" name="branch_name" placeholder="" value="{{old('$bankdetails->branch_name',isset($bankdetails->branch_name)?$bankdetails->branch_name:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Cancel Cheaque</label>
                                    <input type="file" class="form-control" name="cancel_cheaque" placeholder="">
                                </div>
                            </div>

                            <h3>Vendor Documents</h3>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pan</label>
                                    <input type="text" class="form-control" name="pan" placeholder="" value="{{old('pan',isset($getvendor->pan)?$getvendor->pan:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pan Upload</label>
                                    <input type="file" class="form-control" name="pan_upload" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">GST</label>
                                    <select class="form-control  my-select2" id="gst_register" name="gst_register"
                                        tabindex="-1">
                                        <option value="Unregistered" {{$getvendor->gst_register == 'Unregistered' ? 'selected' : ''}}>Unregistered </option>
                                        <option value="Registered" {{$getvendor->gst_register == 'Registered' ? 'selected' : ''}}>Registered </option>

                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Gst No</label>
                                    <input type="text" class="form-control" id="gst_no" name="gst_no" placeholder=""
                                    value="{{old('gst_no',isset($getvendor->gst_no)?$getvendor->gst_no:'')}}" disabled>
                                </div>
                            </div>

                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Update</button>
                         
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script>
$('#vendor_type').change(function() {
    var v_typ = $(this).val();
    var declaration = ($('input[name=decalaration_available]:checked').val());
    if (declaration == '2') {
        if (v_typ == 'Individual') {
            $('#tds_rate').val('1');
        } else if (v_typ == 'Proprietorship') {
            $('#tds_rate').val('1');
        } else if (v_typ == 'Company') {
            $('#tds_rate').val('2');
        } else if (v_typ == 'Firm') {
            $('#tds_rate').val('2');
        } else if (v_typ == 'HUF') {
            $('#tds_rate').val('2');
        }
    } else {
        $('#tds_rate').val('0');
    }

});

////////////////////////
$('.no_decl').on('change', function() {
    var declaration = ($('input[name=decalaration_available]:checked').val());
    if (declaration == 1) {
        $('#tds_rate').val('0');
    } else if (declaration == 2) {
        $('#vendor_type').val('');
        $('#tds_rate').val('');
    }
});
////////////////////////

$('#gst_register').on('change', function() {

    var g_typ = $(this).val();
    if (g_typ == 'Registered') {
        $("#gst_no").prop('disabled', false);
    } else {
        $("#gst_no").prop('disabled', true);
    }
});
//////////
$('#account_no').blur(function() {

    var acc_no = $(this).val();
    var _token = $('input[name="_token"]').val();
    $.ajax({
        url: "check-account-no",
        method: "POST",
        data: {
            acc_no: acc_no,
            _token: _token
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(result) {
            if(result.success == false){
                swal('Error',result.success_message,'error')
                $('#account_no').val('');
            }

        }
    })
});
</script>


@endsection