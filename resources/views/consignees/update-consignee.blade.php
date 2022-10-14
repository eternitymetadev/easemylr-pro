@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="page-header">
                        <nav class="breadcrumb-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Consignees</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);"> Update Consignee</a></li>
                            </ol>
                        </nav>
                    </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Update Consignee</h5></div> -->
                    
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/consignees/update-consignee')}}" id="updateconsignee">
                            @csrf
                            <input type="hidden" name="consignee_id" value="{{$getconsignee->id}}">

                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Consignee Nick Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nick_name" value="{{old('nick_name',isset($getconsignee->nick_name)?$getconsignee->nick_name:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Consignee Legal Name</label>
                                    <input type="text" class="form-control" name="legal_name" value="{{old('legal_name',isset($getconsignee->legal_name)?$getconsignee->legal_name:'')}}" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Contact Person Name</label>
                                    <input type="text" class="form-control" name="contact_name" value="{{old('contact_name',isset($getconsignee->contact_name)?$getconsignee->contact_name:'')}}" placeholder="Contact Name">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Email ID</label>
                                    <input type="email" class="form-control" name="email" value="{{old('email',isset($getconsignee->email)?$getconsignee->email:'')}}" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Mobile No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mbCheckNm" name="phone" value="{{old('phone',isset($getconsignee->phone)?$getconsignee->phone:'')}}" placeholder="Phone" maxlength="10">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Consigner</label>
                                    <select class="form-control" name="consigner_id">
                                        <option value="">Select</option>
                                        <?php 
                                        if(count($consigners)>0) {
                                            foreach ($consigners as $k => $consigner) { 
                                        ?>
                                            <option value="{{ $k }}" {{ $k == $getconsignee->consigner_id ? 'selected' : ''}}>{{ucwords($consigner)}}</option>
                                            <?php 
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Type Of Dealer</label>
                                    <select class="form-control" id="dealer_type" name="dealer_type">
                                        <option value="">Select</option>
                                        <option value="1" {{$getconsignee->dealer_type == '1' ? 'selected' : ''}}>Registered</option>
                                        <option value="0" {{$getconsignee->dealer_type == '0' ? 'selected' : ''}}>Unregistered</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">GST No.</label>
                                    <input type="text" class="form-control" id="gst_number" name="gst_number" value="{{old('gst_number',isset($getconsignee->gst_number)?$getconsignee->gst_number:'')}}" placeholder="" maxlength="15" disabled>
                                    <p class="gstno_error text-danger" style="display: none; color: #ff0000; font-weight: 500;">Please enter GST no.</p>
                                </div>
                            </div>
                           
                         
                            <div class="form-row mb-0">      
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pincode</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{old('postal_code',isset($getconsignee->postal_code)?$getconsignee->postal_code:'')}}" placeholder="Pincode" maxlength="6">
                                </div> 
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Village/City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{old('city',isset($getconsignee->city)?$getconsignee->city:'')}}" placeholder="City">
                                </div>                                  
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">District</label>
                                    <input type="text" class="form-control" id="district" name="district" value="{{old('district',isset($getconsignee->GetZone->district)?$getconsignee->GetZone->district:'')}}" placeholder="District">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Select State</label>
                                    <input type="text" class="form-control" id="state" name="state_id" value="{{old('state_id',isset($getconsignee->GetZone->state)?$getconsignee->GetZone->state:'')}}" placeholder="" readonly>
                                
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Primary Zone</label>
                                    <input type="text" class="form-control" id="zone_name" name="zone_name" disabled value="{{old('zone_id',isset($getconsignee->GetZone->primary_zone)?$getconsignee->GetZone->primary_zone:'No Zone Assigned')}}" placeholder="">
                                </div>
                                <input type="hidden" id="zone_id" name="zone_id" value="{{old('zone_id',isset($getconsignee->GetZone->id)?$getconsignee->GetZone->id:'')}}">
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 1</label>
                                    <input type="text" class="form-control" name="address_line1" value="{{old('address_line1',isset($getconsignee->address_line1)?$getconsignee->address_line1:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 2</label>
                                    <input type="text" class="form-control" name="address_line2" value="{{old('address_line2',isset($getconsignee->address_line2)?$getconsignee->address_line2:'')}}" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 3</label>
                                    <input type="text" class="form-control" name="address_line3" value="{{old('address_line3',isset($getconsignee->address_line3)?$getconsignee->address_line3:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 4</label>
                                    <input type="text" class="form-control" name="address_line4" value="{{old('address_line3',isset($getconsignee->address_line4)?$getconsignee->address_line4:'')}}" placeholder="">
                                </div>
                            </div>
                          
                            <div class="form-row mb-0">         
                                            
                              
                                <!-- <div class="form-row col-md-6">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Status</label>
                                    <div class="check-box d-flex">
                                        <div class="checkbox radio">
                                            <label class="check-label">Active
                                                <input type="radio" value="1" name="status" class=""  {{ ($getconsignee->status=="1")? "checked" : "" }}>
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                        <div class="checkbox radio">
                                            <label class="check-label">Deactive
                                                <input type="radio" name="status" value="0" {{ ($getconsignee->status=="0")? "checked" : "" }}>
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>                                    
                                </div>
                            </div> -->
                            </div>

                            <input type="submit" class="mt-4 mb-4 btn btn-primary" value="Update">
                            <a class="btn btn-danger" href="{{url($prefix.'/consignees') }}"> Back</a>
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
jQuery(document).ready(function(){
    // $('#dealer_type').change(function (e) {
        // e.preventDefault();
        var valueSelected = $('#dealer_type').val();
        var gstno = $("#gst_number").val();
        if(valueSelected==1 && gstno == ''){
            $("#gst_number").attr("disabled", false);
            $('.gstno_error').show();
            return false;
        }else if(valueSelected==1){
            $("#gst_number").attr("disabled", false);
        }else{
            $("#gst_number").val('');
            $("#gst_number").attr("disabled", true);
            $('.gstno_error').hide();
        }
    // });
});
</script>
@endsection