@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="page-header">
                        <nav class="breadcrumb-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Farmers</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);"> Create Farmer</a></li>
                            </ol>
                        </nav>
                    </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Create Consignee</h5></div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/consignees')}}" id="createconsignee">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Farmer Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nick_name" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Mobile No.<span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control mbCheckNm" name="phone" placeholder="Enter 10 digit mobile no" maxlength="10">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                            
                                
                            </div>
                          
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pincode</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Pincode" maxlength="6">
                                </div> 
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Village/City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                </div>
                              
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Select State</label>
                                    <input type="text" class="form-control" id="state" name="state_id" placeholder="" readonly>
                                </div>               
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">District</label>
                                    <input type="text" class="form-control" id="district" name="district" placeholder="District">
                                </div>
                            </div>   
                            <div class="form-row mb-0">
                                <div class="form-group col-md-8">
                                    <label for="exampleFormControlInput2">Address</label>
                                    <textarea type="text" class="form-control" name="address_line1" placeholder=""></textarea>
                                </div>       
                                <!-- <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 2</label>
                                    <input type="text" class="form-control" name="address_line2" placeholder="">
                                </div>  -->
                            </div>
                            <!-- <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 3</label>
                                    <input type="text" class="form-control" name="address_line3" placeholder="">
                                </div>                 
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 4</label>
                                    <input type="text" class="form-control" name="address_line4" placeholder="">
                                </div>
                            </div> -->
                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/consignees') }}"> Back</a>
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

    // if ($('input').val().replace(/[\s]/, '') == '') {
    //     alert('Input is not filled!');
    // }

    // on ready function for create/update consignee page
    // var gstno = $("#gst_number").val().length;
    
    // if(gstno > 0){
    //     $('#dealer_type option[value="1"]').prop('selected', true);
    // }else{
    //     $('#dealer_type option[value="0"]').prop('selected', true);
    // }
    
    
});
</script>
@endsection