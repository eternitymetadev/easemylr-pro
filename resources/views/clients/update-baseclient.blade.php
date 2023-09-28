@extends('layouts.main')
@section('content')
<style>
.row.layout-top-spacing {
    width: 80%;
    margin: auto;

}

.imageInput {
    position: relative;
    max-width: 300px;
    height: 150px;
    border-radius: 12px;
    overflow: hidden;
    outline: 1px solid;
    cursor: pointer;
}

.imageInput img {
    width: 100%;
    max-height: 150px;
    object-fit: contain;
}

.imageInput input {
    position: absolute;
    inset: 0;
    height: 100%;
    width: 100%;
    opacity: 0;
    z-index: 1;
}

.imageInput svg {
    transition: all 300ms ease-in-out;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background: #ffffffa8;
    height: 60px;
    width: 60px;
    color: #208120;
    padding: 12px;
    border-radius: 50vh;
    cursor: pointer;
    box-shadow: 0 0 12px inset;
    opacity: 0;
    z-index: 0;
}

.imageInput:hover svg {
    opacity: 1;
}

</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update Base Client</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/update-base-client')}}" id="update_baseclient">
                            <input type="hidden" name="base_client" value="{{$baseClient->id}}">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Client Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="client_name" placeholder="" value="{{old('client_name',isset($baseClient->client_name)?$baseClient->client_name:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">GST Number</label>
                                    <input type="text" class="form-control"  minlength="15" maxlength="15" name="gst_no" placeholder="" value="{{old('gst_no',isset($baseClient->gst_no)?$baseClient->gst_no:'')}}">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">PAN</label>
                                    <input type="text" class="form-control"  minlength="10" maxlength="10" name="pan" placeholder="" value="{{old('pan',isset($baseClient->pan)?$baseClient->pan:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">TAN</label>
                                    <input type="text" class="form-control"  minlength="10" maxlength="10" name="tan" placeholder="" value="{{old('tan',isset($baseClient->tan)?$baseClient->tan:'')}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Upload Gst</label>
                                    <div class="imageInput">
                                        <input type="file" name="upload_gst" accept="image/png, image/jpeg, image/jpg"
                                            class="imgInput"
                                            value="{{old('upload_gst', isset($baseClient->upload_gst) ? $baseClient->upload_gst : '')}}">
                                            <?php 
                                                $awsUrl = env('AWS_S3_URL');
                                                if(!empty($baseClient->upload_gst))
                                                { 
                                                    $image_url = $awsUrl."/client_images/".$baseClient->upload_gst;
                                                }else{
                                                    $image_url = "";
                                                }
                                            ?>
                                        <img src="{{$image_url}}"
                                            class="imagePreview" alt="" />
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Upload Pan</label>
                                    <div class="imageInput">
                                        <input type="file" name="upload_pan" accept="image/png, image/jpeg, image/jpg"
                                            class="imgInput"
                                            value="{{old('upload_pan', isset($baseClient->upload_pan) ? $baseClient->upload_pan : '')}}">
                                            <?php 
                                                if(!empty($baseClient->upload_pan))
                                                { 
                                                    $image_url = $awsUrl."/client_images/".$baseClient->upload_pan;
                                                }else{
                                                    $image_url = "";
                                                }
                                            ?>
                                        <img src="{{$image_url}}"
                                            class="imagePreview" alt="" />
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Upload Tan</label>
                                    <div class="imageInput">
                                        <input type="file" name="upload_tan" accept="image/png, image/jpeg, image/jpg"
                                            class="imgInput"
                                            value="{{old('upload_tan', isset($baseClient->upload_tan) ? $baseClient->upload_tan : '')}}">
                                            <?php 
                                                if(!empty($baseClient->upload_tan))
                                                { 
                                                    $image_url = $awsUrl."/client_images/".$baseClient->upload_tan;
                                                }else{
                                                    $image_url = "";
                                                }
                                            ?>
                                        <img src="{{$image_url}}"
                                            class="imagePreview" alt="" />
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Upload Moa</label>
                                    <div class="imageInput">
                                        <input type="file" name="upload_moa" accept="image/png, image/jpeg, image/jpg"
                                            class="imgInput"
                                            value="{{old('upload_moa', isset($baseClient->upload_moa) ? $baseClient->upload_moa : '')}}">
                                            <?php 
                                                if(!empty($baseClient->upload_moa))
                                                { 
                                                    $image_url = $awsUrl."/client_images/".$baseClient->upload_moa;
                                                }else{
                                                    $image_url = "";
                                                }
                                            ?>
                                        <img src="{{$image_url}}"
                                            class="imagePreview" alt="" />
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Update</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/clients') }}"> Back</a>
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
$('.imgInput').change(function() {
    const file = this.files[0]
    console.log('sss', file)
    if (file) {
        console.log('img', $(this).siblings('.imagePreview'))
        $(this).siblings('.imagePreview').attr('src', URL.createObjectURL(file))
    }
})
</script>
@endsection