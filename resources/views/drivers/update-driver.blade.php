@extends('layouts.main')
@section('content')

<style>
.taggedBranches {
    background: #f1f2f3;
    display: flex;
    column-gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 12px;

    border: 1px solid #bfc9d4;
    color: #000;
    font-size: 15px;
    font-weight: 600;
    padding: 8px 10px;
    letter-spacing: 1px;
    height: calc(1.4em + 1.4rem + 2px);
    border-radius: 6px;
}
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Drivers</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update
                                Driver</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Update Driver</h5></div> -->

                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/drivers/update-driver')}}"
                            id="updatedriver">
                            @csrf
                            <input type="hidden" name="driver_id" value="{{$getdriver->id}}">

                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Driver Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{old('name',isset($getdriver->name)?$getdriver->name:'')}}"
                                        placeholder="Name">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Driver Phone<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control mbCheckNm" id="phone" name="phone"
                                        value="{{old('phone',isset($getdriver->phone)?$getdriver->phone:'')}}"
                                        placeholder="Phone" maxlength="10">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Driver License Number<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="license_number"
                                        value="{{old('license_number',isset($getdriver->license_number)?$getdriver->license_number:'')}}"
                                        placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6 license-load">
                                    <label for="exampleFormControlInput2">Driver License File(Optional)</label>

                                    <?php if(!empty($getdriver->license_image))
                                    { 
                                        ?>
                                    <input type="file" class="form-control licensefile" name="license_image" value=""
                                        placeholder="">

                                    <div class="image_upload">
                                        <img src="{{$getdriver->license_image}}" class="licenseshow image-fluid" id="img-tag" width="320" height="240"></div>
                                    <?php }
                                    else{
                                        ?>
                                    <input type="file" class="form-control licensefile" name="license_image" value=""
                                        placeholder="">

                                    <div class="image_upload"><img src="{{url("/assets/img/upload-img.png")}}"
                                            class="licenseshow image-fluid" id="img-tag" width="320" height="240"></div>
                                    <?php
                                    }
                                        ?>
                                    <?php if($getdriver->license_image!=null){ ?>
                                    <a class="deletelicenseimg d-block text-center" href="javascript:void(0)"
                                        data-action="<?php echo URL::to($prefix.'/drivers/update-license'); ?>"
                                        data-licenseimg="del-licenseimg" data-id="{{ $getdriver->id }}"
                                        data-name="{{$getdriver->license_image}}"><i class="red-text fa fa-trash"></i>
                                    </a>
                                    <?php } else { ?>
                                    <a href="javascript:void(0)" class="remove_licensefield" style="display: none;"><i
                                            class="red-text fa fa-trash"></i> </a>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-row mb-0">
                                <div class="form-group col-md-6 d-flex align-items-center" style="gap: 1.5rem">
                                    <label for="exampleFormControlInput2">App Access</label>
                                    <div class="check-box d-flex align-items-center" style="gap: 1rem">
                                        <div class="checkbox radio">
                                            <label class="check-label d-flex align-items-center" style="gap: 0.2rem">
                                                <input type="radio" value='1' name="access_status" class="access_status"
                                                    {{ ($getdriver->access_status=="1")? "checked" : "" }}>
                                                <span class="checkmark"></span>
                                                Yes
                                            </label>
                                        </div>
                                        <div class="checkbox radio">
                                            <label class="check-label d-flex align-items-center" style="gap: 0.2rem">
                                                <input type="radio" value='0' name="access_status" class="access_status"
                                                    {{ ($getdriver->access_status=="0")? "checked" : "" }}>
                                                <span class="checkmark"></span>
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    


                    <div id="driver_detail" style="display: none;">
                            <h5 class="form-row mb-2">Login Details</h5>
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Login ID</label>
                                <input type="text" class="form-control" id="login_id" name="login_id"
                                    value="{{old('login_id',isset($getdriver->login_id)?$getdriver->login_id:'')}}"
                                    placeholder="" readonly autocomplete="off">
                            </div>
                            <?php $authuser = Auth::user();
                        if($authuser->role_id==2){
                            $disable = ''; 
                        } else{
                            $disable = 'disable_n';
                        }
                        ?>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Password</label>
                                <input type="text" class="form-control {{$disable}}" id="password" name="password"
                                    value="{{old('driver_password',isset($getdriver->driver_password)?$getdriver->driver_password:'')}}"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>

                        <?php $selected = explode(",", $getdriver->branch_id); ?>
                        <label for="exampleFormControlInput2">Tagged Branches</label>
                        <div class="taggedBranches">
                        @foreach($branches as $branch)
                            @if(in_array($branch->id, $selected))                        
                                <span>{{ (in_array($branch->id, $selected)) ? $branch->name : '' }}</span>
                            @endif
                        @endforeach
                        </div>
                        <input type="hidden" name="branches_id[]" value="{{$getdriver->branch_id}}">


                        <label for="exampleFormControlInput2">Select Branch</label>
                        <select class="form-control tagging" id="select_consigner" multiple="multiple"
                            name="branch_id[]">
                            <option disabled>Select</option>
                            <?php
                                $selected = explode(",", $getdriver->branch_id);
                                ?>
                            @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}"
                                {{ (in_array($branch->id, $selected)) ? 'selected' : '' }}>
                                {{ $branch->name}}</option>
                            @endforeach

                        </select>
                    </div>


                    <input type="submit" class="mt-4 mb-4 btn btn-primary">
                    <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>
                    </form>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@include('models.deletedriverlicenseimagepop')
@endsection
@section('js')
<script>
$('.nSelect').prop('disabled', true);

if ($('.access_status:checked').val() == 1) {
    $('#driver_detail').show();
} else {
    $('#driver_detail').hide();
}

$('input[type=radio][name=access_status]').change(function() {
    if (this.value == '1') {
        $('#driver_detail').show();
        $("#login_id").val($("#phone").val());
    } else {
        $('#driver_detail').hide();
        $("#login_id").val('');
    }
});

$("#phone").blur(function() {
    $("#login_id").val($(this).val());
});

$('input[type=radio][name=access_status]').change(function() {
    if (this.value == '1') {
        $('#password').attr('required',true);
    }else{
        $('#password').removeAttr('required');
    }
});

$(document).on("click", ".remove_licensefield", function(e) { //user click on remove text
    var getUrl = window.location;
    var baseurl = getUrl.origin + '/' + getUrl.pathname.split('/')[0];
    var imgurl = baseurl + 'assets/img/upload-img.png';

    $(this).parent().children(".image_upload").children().attr('src', imgurl);
    $(this).parent().children("input").val('');
    // $(this).parent().children('div').children('h4').text('Add Image');
    // $(this).parent().children('div').children('h4').css("display", "block");
    $(this).css("display", "none");
});

function readURL1(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('.licenseshow').attr('src', e.target.result);
            $(".remove_licensefield").css("display", "block");
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on("change", '.licensefile', function(e) {
    var fileName = this.files[0].name;
    // $(this).parent().parent().find('.file_graph').text(fileName);

    readURL1(this);
});
</script>
@endsection