@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Vehicles</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update Vehicle</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Update Vehicle</h5></div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/vehicles/update-vehicle')}}" id="updatevehicle">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{$getvehicle->id}}">

                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Registration No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="regn_no" name="regn_no" value="{{old('regn_no',isset($getvehicle->regn_no)?$getvehicle->regn_no:'')}}" placeholder="" maxlength="12">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Manufacturer<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="mfg" value="{{old('mfg',isset($getvehicle->mfg)?$getvehicle->mfg:'')}}" placeholder="Mahindra, Tata, etc.">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Make<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="make" value="{{old('make',isset($getvehicle->make)?$getvehicle->make:'')}}" placeholder="407, Supro Maxi, Truck, Pickup, Ace, etc.">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Engine No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="engine_no" value="{{old('engine_no',isset($getvehicle->engine_no)?$getvehicle->engine_no:'')}}" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">     
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Chassis No.</label>
                                    <input type="text" class="form-control" name="chassis_no" value="{{old('chassis_no',isset($getvehicle->chassis_no)?$getvehicle->chassis_no:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Gross Vehicle Weight</label>
                                    <input type="text" class="form-control" id="gross_vehicle_weight" name="gross_vehicle_weight" value="{{old('gross_vehicle_weight',isset($getvehicle->gross_vehicle_weight)?$getvehicle->gross_vehicle_weight:'')}}" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Unladen Weight</label>
                                    <input type="text" class="form-control" id="unladen_weight" name="unladen_weight" value="{{old('unladen_weight',isset($getvehicle->unladen_weight)?$getvehicle->unladen_weight:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Tonnage Capacity</label>
                                    <input type="text" class="form-control" id="tonnage_capacity" name="tonnage_capacity" value="{{old('tonnage_capacity',isset($getvehicle->tonnage_capacity)?$getvehicle->tonnage_capacity:'')}}" readonly>
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
                                            <option value="{{ $key }}" {{ $key == $getvehicle->state_id ? 'selected' : ''}}>{{ucwords($state)}}</option>
                                            <?php 
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Regn. Date</label>
                                    <input type="date" class="form-control" name="regndate" value="{{old('regndate',isset($getvehicle->regndate)?$getvehicle->regndate:'')}}" placeholder="" onkeydown="return false">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Hypothecation</label>
                                    <input type="text" class="form-control" name="hypothecation" value="{{old('hypothecation',isset($getvehicle->hypothecation)?$getvehicle->hypothecation:'')}}" placeholder="Name of Financer | N/A">
                                </div>    
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Ownership</label>
                                    <select class="form-control" name="ownership">
                                        <option value="Self Owned" {{$getvehicle->ownership == 'Self Owned' ? 'selected' : ''}}>Self Owned</option>
                                        <option value="Company Owned" {{$getvehicle->ownership == 'Company Owned' ? 'selected' : ''}}>Company Owned</option>
                                        <option value="Transporter Owned" {{$getvehicle->ownership == 'Transporter Owned' ? 'selected' : ''}}>Transporter Owned</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Owner Name</label>
                                    <input type="text" class="form-control" name="owner_name" value="{{old('owner_name',isset($getvehicle->owner_name)?$getvehicle->owner_name:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Owner Mobile No.</label>
                                    <input type="text" class="form-control" name="owner_phone" value="{{old('owner_phone',isset($getvehicle->owner_phone)?$getvehicle->owner_phone:'')}}">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Body Type</label>
                                    <select class="form-control" name="body_type">
                                        <option value="Container" {{$getvehicle->body_type == 'Container' ? 'selected' : ''}}>Container</option>
                                        <option value="Open Body" {{$getvehicle->body_type == 'Open Body' ? 'selected' : ''}}>Open Body</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6 rc-load">
                                    <label for="exampleFormControlInput2">Vehicle RC File(Optional)</label>
                                    <?php if(!empty($getvehicle->rc_image))
                                    { 
                                        $awsUrl = env('AWS_S3_URL');
                                        $image_url = $awsUrl."/vehicle_rc_images/";
                                        ?> 
                                        <input type="file" class="form-control rcfile" name="rc_image" value="" placeholder="">

                                        <div class="image_upload"><img src="{{$image_url.$getvehicle->rc_image}}" class="rcshow image-fluid" id="img-tag" width="320" height="240"></div>
                                    <?php }
                                    else{
                                        ?>  
                                        <input type="file" class="form-control rcfile" name="rc_image" value="" placeholder="">

                                        <div class="image_upload"><img src="{{url("/assets/img/upload-img.png")}}" class="rcshow image-fluid" id="img-tag" width="320" height="240"></div>
                                    <?php
                                    }
                                        ?>
                                    <?php if($getvehicle->rc_image!=null){ ?>
                                        <a class="deletercimg d-block text-center" href="javascript:void(0)" data-action = "<?php echo URL::to($prefix.'/vehicles/update-rc'); ?>" data-rcimg = "del-rcimg" data-id="{{ $getvehicle->id }}" data-name="{{$getvehicle->rc_image}}"><i class="red-text fa fa-trash"></i> </a>
                                    <?php } else { ?>
                                    <a href="javascript:void(0)" class="remove_rcfield" style="display: none;"><i class="red-text fa fa-trash"></i> </a>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-row mb-0">
                                <div class="form-group col-md-6 rc-load">
                                    <label for="exampleFormControlInput2">Second Vehicle RC File(Optional)</label>
                                    <?php if(!empty($getvehicle->second_rc_image))
                                    { ?> 
                                        <input type="file" class="form-control second_rcfile" name="second_rc_image" value="" placeholder="">

                                        <div class="image_upload"><img src="{{$image_url.$getvehicle->second_rc_image}}" class="second_rcshow image-fluid" id="img-tag" width="320" height="240"></div>
                                    <?php }
                                    else{
                                        ?>  
                                        <input type="file" class="form-control second_rcfile" name="second_rc_image" value="" placeholder="">

                                        <div class="image_upload"><img src="{{url("/assets/img/upload-img.png")}}" class="second_rcshow image-fluid" id="img-tag" width="320" height="240"></div>
                                    <?php
                                    }
                                        ?>
                                    <?php if($getvehicle->second_rc_image!=null){ ?>
                                        <a class="deletercimg d-block text-center" href="javascript:void(0)" data-action = "<?php echo URL::to($prefix.'/vehicles/update-rc'); ?>" data-rcimg = "del-rcimg" data-id="{{ $getvehicle->id }}" data-name="{{$getvehicle->second_rc_image}}"><i class="red-text fa fa-trash"></i> </a>
                                    <?php } else { ?>
                                    <a href="javascript:void(0)" class="remove_rcfield" style="display: none;"><i class="red-text fa fa-trash"></i> </a>
                                    <?php } ?>
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
@include('models.delete-vehicle-rcimagepop')
@endsection
@section('js')
<script>
    $(document).on("click",".remove_rcfield", function(e){ //user click on remove text
    var getUrl = window.location;
    var baseurl =  getUrl.origin + '/' +getUrl.pathname.split('/')[0];
    var imgurl = baseurl+'assets/img/upload-img.png';
      
      $(this).parent().children(".image_upload").children().attr('src', imgurl);
      $(this).parent().children("input").val('');;
      // $(this).parent().children('div').children('h4').text('Add Image');
      // $(this).parent().children('div').children('h4').css("display", "block");
      $(this).css("display", "none");
   });

    function readURL1(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('.rcshow').attr('src', e.target.result);
                $(".remove_rcfield").css("display", "block");
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function readURL2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('.second_rcshow').attr('src', e.target.result);
                $(".remove_rcfield").css("display", "block");
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).on("change",'.rcfile', function(e){
        var fileName = this.files[0].name;
        // $(this).parent().parent().find('.file_graph').text(fileName);

        readURL1(this);
    });

    $(document).on("change",'.second_rcfile', function(e){
        var fileName = this.files[0].name;
        // $(this).parent().parent().find('.file_graph').text(fileName);

        readURL2(this);
    });

</script>
@endsection