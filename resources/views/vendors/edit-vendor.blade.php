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
.pan {
    text-transform: uppercase;
}
</style> 

<style>
    .outerContainer {
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
    justify-content: space-between;
    border-radius: 18px;
    padding: 8px;
    gap: 1rem;
    border: 1px solid #f1be34;
    }
    .outerContainer .innerContainer {
    flex: 1 1 300px;
    display: flex;
    flex-flow: column;
    gap: 8px;
    }
    .outerContainer .innerContainer select {
    font-size: 14px;
    border: none;
    border-radius: 12px;
    max-width: min(80%, 200px);
    color: #f1be34;
    }
    .outerContainer .innerContainer .selectedDistricts {
        flex: 1;
    font-size: 12px;
    background: rgba(241, 190, 52, 0.1607843137);
    border-radius: 10px;
    padding: 8px;
    min-height: 40px;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    align-content: flex-start;
    justify-content: flex-start;
    gap: 6px;
    }
    .outerContainer .innerContainer .selectedDistricts span {
    outline: 1px solid;
    border-radius: 16px;
    padding: 0 6px;
    min-width: 70px;
    text-align: center;
    color: #6e6757;
    font-size: 12px;
    }

    #fetchedDistricts {
    min-height: 120px;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 6px;
    }
    #fetchedDistricts input[type=checkbox] {
    display: none;
    }
    #fetchedDistricts input[type=checkbox] + label {
    -webkit-user-select: none;
        -moz-user-select: none;
            user-select: none;
    color: #f11e3e;
    padding: 4px;
    border-radius: 8px;
    box-shadow: 0 0 13px -5px inset #ff9292;
    }
    #fetchedDistricts input[type=checkbox] + label span.check {
    display: none;
    }
    #fetchedDistricts input[type=checkbox] + label span.unCheck {
    display: inline;
    }
    #fetchedDistricts input[type=checkbox]:checked + label {
    color: #49a80a;
    box-shadow: 0 0 13px -5px inset #ceff92;
    }
    #fetchedDistricts input[type=checkbox]:checked + label span.check {
    display: inline;
    }
    #fetchedDistricts input[type=checkbox]:checked + label span.unCheck {
    display: none;
    }


    #routesContainer{
        position: relative;
        padding: 8px;
        background: #83838312;
        border-radius: 24px;
    }
    .addRoute, .removeRoute{
        position: absolute;
        bottom: -12px;
        right: 14px;
        cursor: pointer;
        background: #f9b808;
        padding: 2px 12px;
        border-radius: 12px;
        color: #000;
        font-size: 12px;
        font-weight: 600;
    }
    .removeRoute{
        right: 120px;
        background: #f96808;
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
                                    value="{{old('name',isset($getvendor->name)?$getvendor->name:'')}}" readonly>
                                </div>
                                <?php $otherdetails = json_decode(old('other_details',isset($getvendor->other_details)?$getvendor->other_details:''));
                                // 
                                ?>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Transporter Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="transporter_name" placeholder="" value="{{old('$otherdetails->transporter_name',isset($otherdetails->transporter_name)?$otherdetails->transporter_name:'')}}" readonly>
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
                            <div class="form-row mb-0">
                                <div class="col-md-12" id="routesContainer">
                                    <p style="font-size: 14px; font-weight: 600; padding-left: 16px;">Routes</p>

                                    {{-- <div class="outerContainer form-group col-md-12" data-routeCount="1">
                                        <div class="innerContainer">
                                            <select class="stateClass" name="data[1][pickup_state]">
                                                @foreach($getState as $state)
                                                    <option value="{{ $state }}">{{ucwords($state)}}</option>
                                                @endforeach  
                                            </select>
                                            <input type="hidden" id="pick-1" name="data[1][pickup_district]" />
                                            <div class="selectedDistricts" data-id="pick-1">
                                            </div>
                                        </div>
                                        <div class="innerContainer">
                                            <select class="stateClass" name="data[1][drop_state]">
                                                @foreach($getState as $state)
                                                    <option value="{{ $state }}">{{ucwords($state)}}</option>
                                                @endforeach  
                                            </select>
                                            <input type="hidden" id="drop-1" name="data[1][drop_district]" />
                                            <div class="selectedDistricts" data-id="drop-1">
                                            </div>
                                        </div>
                                    </div> --}}

                                    @foreach($getvendor->VendorAvailabilities as $key => $route)
                                    <div class="outerContainer form-group col-md-12" data-routeCount="{{ $key + 1 }}">
                                        <input type="hidden" name="data[{{ $key + 1 }}][id]"
                                                value="{{old('id',isset($route->id)?$route->id:'')}}">
                                        <div class="innerContainer">
                                            <select class="stateClass" name="data[{{ $key + 1 }}][pickup_state]">
                                                @foreach($getState as $state)
                                                <option value="{{ $state }}" {{ $route['pickup_state'] == $state ? 'selected' : '' }}>
                                                    {{ ucwords($state) }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="pick-{{ $key + 1 }}" name="data[{{ $key + 1 }}][pickup_district]"
                                                value="{{ $route['pickup_district'] }}" />
                                            <div class="selectedDistricts" data-id="pick-{{ $key + 1 }}">
                                                <!-- Display selected districts for pickup -->
                                                @php
                                                    $pickupDistrict = $route['pickup_district'];
                                                    $pickupWords = explode(',', $pickupDistrict);
                                                @endphp
                                                @foreach($pickupWords as $word)
                                                    <span>{{ trim($word) }}</span>
                                                @endforeach                                                     
                                            </div>
                                        </div>
                                        <div class="innerContainer">
                                            <select class="stateClass" name="data[{{ $key + 1 }}][drop_state]">
                                                @foreach($getState as $state)
                                                <option value="{{ $state }}" {{ $route['drop_state'] == $state ? 'selected' : '' }}>
                                                    {{ ucwords($state) }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="drop-{{ $key + 1 }}" name="data[{{ $key + 1 }}][drop_district]"
                                                value="{{ $route['drop_district'] }}" />
                                            <div class="selectedDistricts" data-id="drop-{{ $key + 1 }}">
                                                <!-- Display selected districts for drop -->
                                                @php
                                                    $dropDistrict = $route['drop_district'];
                                                    $dropWords = explode(',', $dropDistrict);
                                                @endphp
                                                @foreach($dropWords as $word)
                                                    <span>{{ trim($word) }}</span>
                                                @endforeach    
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <span class="removeRoute">Remove Last</span>
                                    <span class="addRoute">Add Route</span>
                                </div>
                            </div>

                            <h3>Vendor NEFT details</h3>
                            <?php $bankdetails = json_decode(old('bank_details',isset($getvendor->bank_details)?$getvendor->bank_details:''));
                                ?>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Account Holder Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="acc_holder_name" placeholder="" value="{{old('$bankdetails->acc_holder_name',isset($bankdetails->acc_holder_name)?$bankdetails->acc_holder_name:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Account No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="account_no" name="account_no" placeholder="" value="{{old('$bankdetails->account_no',isset($bankdetails->account_no)?$bankdetails->account_no:'')}}">
                                </div>
                            </div>

                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Ifsc Code<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="" name="ifsc_code" placeholder="" value="{{old('$bankdetails->ifsc_code',isset($bankdetails->ifsc_code)?$bankdetails->ifsc_code:'')}}" maxlength="11" minlength="11">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Bank Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="bank_name" placeholder="" value="{{old('$bankdetails->bank_name',isset($bankdetails->bank_name)?$bankdetails->bank_name:'')}}">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Branch Name<span
                                            class="text-danger">*</span></label>
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
                                    <label for="exampleFormControlInput2">Pan<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control pan" id="pan_no" name="pan" placeholder="" value="{{old('pan',isset($getvendor->pan)?$getvendor->pan:'')}}">
                                    <label id="lblPANCard" class="error" style="display: none">Invalid PAN Number</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pan Upload<span
                                            class="text-danger">*</span></label>
                                    <input id="pan_upload" type="file" class="form-control" name="pan_upload" placeholder="">
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


<div class="modal" id="distrcitsList" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="failedData" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="distrcitsListLabel"></h6>
            </div>
            <div class="modal-body">
                <div id="fetchedDistricts">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal" aria-label="Close">Close</button>
                <button type="button" class="btn btn-sm btn-primary" id="setDistricts">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function(){
        var panVal = $(this).val().toUpperCase();

        
        var v_typ = $("#vendor_type").val();
        if(v_typ != ''){
            $("#pan_no").prop('disabled', false);
        }else{
            $("#pan_no").prop('disabled', true);
        }

        $(document).on("keyup blur", "#pan_no", function () {
            var vendor_type = $("#vendor_type").val();
            var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
            var panVal = $(this).val().toUpperCase();

            if(panVal.length >= 4){
                var strval =  panVal.charAt(4 - 1);
                if(strval != 'F' && vendor_type == "Firm"){
                    $(this).val(panVal.slice(0, 3))
                    return false;
                }if(strval != 'P' && (vendor_type == "Individual" || vendor_type == "Proprietorship") ){
                    $(this).val(panVal.slice(0, 3))
                    return false;
                }if(strval != 'C' && vendor_type == "Company"){
                    $(this).val(panVal.slice(0, 3))
                    return false;
                }if(strval != 'H' && vendor_type == "HUF"){
                    $(this).val(panVal.slice(0, 3))
                    return false;
                }
            }

            if (regpan.test(panVal)) {
                $("#lblPANCard").hide();
            } else {
                $("#lblPANCard").show();
            }

        })
    });

    $('#vendor_type').change(function() {
        $("#pan_no").val('');
        var v_typ = $(this).val();
        if(v_typ != ''){
            $("#pan_no").prop('disabled', false);
        }else{
            $("#pan_no").prop('disabled', true);
        }
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


<script>
    $('.addRoute').on('click', function(){
        let totalRoutes = +($('.outerContainer:last').attr('data-routeCount') ?? 1);
        const routeHTML =  `
            <div class="outerContainer form-group col-md-12" data-routeCount="${totalRoutes + 1}">
                <div class="innerContainer">
                    <select class="stateClass" name="data[${totalRoutes + 1}][pickup_state]">
                        @foreach($getState as $state)
                            <option value="{{ $state }}">{{ucwords($state)}}</option>
                        @endforeach  
                    </select>
                    <input type="hidden" id="pick-${totalRoutes + 1}" name="data[${totalRoutes + 1}][pickup_district]" />
                    <div class="selectedDistricts" data-id="pick-${totalRoutes + 1}">
                    </div>
                </div>
                <div class="innerContainer">
                    <select class="stateClass" name="data[${totalRoutes + 1}][drop_state]">
                        @foreach($getState as $state)
                            <option value="{{ $state }}">{{ucwords($state)}}</option>
                        @endforeach  
                    </select>
                    <input type="hidden" id="drop-${totalRoutes + 1}" name="data[${totalRoutes + 1}][drop_district]"/>
                    <div class="selectedDistricts" data-id="drop-${totalRoutes + 1}">
                    </div>
                </div>
            </div>
        `;

        $('#routesContainer').append(routeHTML);
    })

    $('.removeRoute').on('click', function(){
        $('.outerContainer').last().remove();
    })

    let sectionToWork = ''

    $(document).on('click', '.selectedDistricts', function(){
        sectionToWork = $(this).data("id");
        $('#distrcitsListLabel').html($(this).siblings('select').val());
        $('#fetchedDistricts').html('fetching Districts')
        $('#distrcitsList').modal('show')
        $.ajax({
                url: "/get-districts",
                type: "get",
                cache: false,
                data: { state_name: $(this).siblings('select').val() },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr("content"),
                },
                success: function (res) {
                    console.log(res.data_district);
                    let availableDistrict = (res?.data_district ?? [])?.map((item, index)=>(
                        `
                        <input id="district-${index}" class="checkedDisticts" type="checkbox" value="${item}"
                        ${$(`#${sectionToWork}`).val()?.includes(item) && 'checked'}/>
                        <label for="district-${index}">${item?.toLowerCase()?.replace(/\b[a-z]/g, (letter) => letter.toUpperCase())} <span class="check">🟢</span><span class="unCheck">🔴</span></label>
                        `
                    ))
                    $('#fetchedDistricts').html(availableDistrict)
                },
            });


   
    })


    $(document).on('click','#setDistricts', function(){
        let selectedDistricts1 = ``
        $("input:checkbox.checkedDisticts:checked").each(function(){
            selectedDistricts1 += `<span>${$(this).val()}</span>`
        })
        $(`#${sectionToWork}`).val($("input:checkbox.checkedDisticts:checked").map((_, checkbox) => checkbox.value).get().join(','));
        $(`*[data-id="${sectionToWork}"]`).html(selectedDistricts1)
        $('#distrcitsList').modal('hide')
        sectionToWork = ''
    })


    $(document).on('change', 'select.stateClass', function(){
        $(this).siblings('input').val('')
        $(this).siblings('.selectedDistricts').html('')
    })

   
</script>


@endsection