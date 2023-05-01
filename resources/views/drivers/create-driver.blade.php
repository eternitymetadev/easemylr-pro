@extends('layouts.main')
@section('content')
<style>
.row.layout-top-spacing {
    width: 80%;
    margin: auto;

}
</style>

<div class="layout-px-spacing">
    <!-- <div class="row layout-top-spacing"> -->
    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Drivers</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create
                            Driver</a></li>
                </ol>
            </nav>
        </div>
        <div class="widget-content widget-content-area br-6">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <!-- <div class="breadcrumb-title pe-3"><h5>Create Driver</h5></div> -->
            </div>
            <div class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <form class="general_form" method="POST" action="{{url($prefix.'/drivers')}}" id="createdriver">
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Driver Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Driver Phone<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control mbCheckNm" name="phone" id="phone"
                                    placeholder="Phone" maxlength="10">
                            </div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Driver License Number<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="license_number" placeholder="">
                            </div>

                        </div>

                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Driver License File(Optional)</label>
                                <input type="file" class="form-control license_image" name="license_image"
                                    accept="image/*">
                            </div>
                            <div class="form-group col-md-6">
                                <div class="image_upload"><img src="{{url("/assets/img/upload-img.png")}}"
                                        class="licenseshow image-fluid" id="img-tag" width="320" height="240"></div>
                            </div>
                        </div>

                        <div class="form-row mb-0">
                            <div class="form-group col-md-6 d-flex align-items-center" style="gap: 1.5rem">
                                <label for="exampleFormControlInput2">App Access</label>
                                <div class="check-box d-flex align-items-center" style="gap: 1rem">
                                    <div class="checkbox radio">
                                        <label class="check-label d-flex align-items-center" style="gap: 0.2rem">
                                            <input type="radio" value='1' name="access_status">
                                            <span class="checkmark"></span>
                                            Yes
                                        </label>
                                    </div>
                                    <div class="checkbox radio">
                                        <label class="check-label d-flex align-items-center" style="gap: 0.2rem">
                                            <input type="radio" name="access_status" value='0' checked>
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
                                    <label for="exampleFormControlInput2">Login Id</label>
                                    <input type="text" class="form-control" name="login_id" id="login_id" placeholder=""
                                        readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="">
                                </div>
                            </div>
                            <label for="exampleFormControlInput2">Select Branch</label>
                            <select class="form-control tagging" id="select_consigner" multiple="multiple"
                                name="branch_id[]">
                                <option disabled>Select</option>
                                @foreach($branchs as $branch)
                                <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                        <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- </div> -->
</div>

@endsection
@section('js')
<script>
$('input[type=radio][name=access_status]').change(function() {
    if (this.value == '1') {
        $('#driver_detail').show();
        $("#password").val('');
    } else {
        $('#driver_detail').hide();
    }
});

$("#password").val('');

$("#phone").blur(function() {
    $("#login_id").val($(this).val());
});

$(document).on("click", ".remove_licensefield", function(e) { //user click on remove text
    var getUrl = window.location;
    var baseurl = getUrl.origin + '/' + getUrl.pathname.split('/')[0];
    var imgurl = baseurl + 'assets/img/upload-img.png';

    $(this).parent().children(".image_upload").children().attr('src', imgurl);
    $(this).parent().children("input").val('');;
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

$(document).on("change", '.license_image', function(e) {
    var fileName = this.files[0].name;
    // $(this).parent().parent().find('.file_graph').text(fileName);

    readURL1(this);
});
</script>
@endsection