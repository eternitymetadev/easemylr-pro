@extends('layouts.main')
@section('content')
<style>
     .row.layout-top-spacing {
    width: 80%;
    margin: auto;
}
    </style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Vehicles</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create Vehicle</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Create Vehicle</h5></div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/vehicles')}}" id="createvehicle">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Registration No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="regn_no" name="regn_no" placeholder="" maxlength="12">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Manufacturer<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="mfg" placeholder="Mahindra, Tata, etc.">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Make<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="make" placeholder="407, Supro Maxi, Truck, Pickup, Ace, etc.">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Engine No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="engine_no" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">     
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Chassis No.</label>
                                    <input type="text" class="form-control" name="chassis_no" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Gross Vehicle Weight</label>
                                    <input type="text" class="form-control" id="gross_vehicle_weight" name="gross_vehicle_weight" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Unladen Weight</label>
                                    <input type="text" class="form-control" id="unladen_weight" name="unladen_weight" placeholder="" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Tonnage Capacity</label>
                                    <input type="text" class="form-control" id="tonnage_capacity" name="tonnage_capacity" value="" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">State(Regd)</label>
                                    <select class="form-control" name="state_id">
                                        <option value="">Select</option>
                                        <?php 
                                        if(count($states)>0) {
                                            foreach ($states as $key => $state) {
                                        ?>
                                            <option value="{{ $key }}">{{ucwords($state)}}</option>
                                            <?php 
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Regn. Date</label>
                                    <input type="date" class="form-control" name="regndate" placeholder="" onkeydown="return false">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Hypothecation</label>
                                    <input type="text" class="form-control" name="hypothecation" placeholder="Name of Financer | N/A">
                                </div>
                                <?php
                                    $authuser = Auth::user();
                                    if($authuser->role_id == 1){
                                ?>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Ownership</label>
                                    <select class="form-control" name="ownership">
                                        <option value="Self Owned">Self Owned</option>
                                        <option value="Company Owned">Company Owned</option>
                                        <option value="Transporter Owned">Transporter Owned</option>
                                    </select>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Owner Name</label>
                                    <input type="text" class="form-control" name="owner_name" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Owner Mobile No.</label>
                                    <input type="text" class="form-control" name="owner_phone" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Body Type</label>
                                    <select class="form-control" name="body_type">
                                        <option value="Container">Container</option>
                                        <option value="Open Body">Open Body</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Vehicle RC File(Optional)</label>
                                    <input type="file" class="form-control rc_image" name="rc_image" accept="image/*">
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="image_upload"><img src="{{url("/assets/img/upload-img.png")}}" class="rcshow image-fluid" id="img-tag" width="120" height="100"></div>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Second Vehicle RC File(Optional)</label>
                                    <input type="file" class="form-control second_rc_image" name="second_rc_image" accept="image/*">
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="image_upload"><img src="{{url("/assets/img/upload-img.png")}}" class="second_rcshow image-fluid" id="img-tag" width="120" height="100"></div>
                                </div>
                            </div>
                            
                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/vehicles')}}"> Back</a>
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
    function readURL1(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('.rcshow').attr('src', e.target.result);
                // $(".remove_licensefield").css("display", "block");
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function readURL2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('.second_rcshow').attr('src', e.target.result);
                // $(".remove_licensefield").css("display", "block");
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).on("change",'.rc_image', function(e){
        var fileName = this.files[0].name;
        readURL1(this);
    });
    ///
    $(document).on("change",'.second_rc_image', function(e){
        var fileName = this.files[0].name;
        readURL2(this);
    });


</script>
@endsection