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
                            <!-- <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Client Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="client_name" id="client_name" placeholder="">
                            </div> -->
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
                            <!-- <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Upload GST RC</label>
                                    <input type="file" class="form-control" name="upload_gst" value="{{old('upload_gst',isset($baseClient->upload_gst)?$baseClient->upload_gst:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Upload PAN</label>
                                    <input type="file" class="form-control" name="upload_pan" value="{{old('upload_pan',isset($baseClient->upload_pan)?$baseClient->upload_pan:'')}}" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Upload TAN</label>
                                    <input type="file" class="form-control" name="upload_tan" value="{{old('upload_tan',isset($baseClient->upload_tan)?$baseClient->upload_tan:'')}}" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Upload MOA</label>
                                    <input type="file" class="form-control" name="upload_moa" value="{{old('upload_moa',isset($baseClient->upload_moa)?$baseClient->upload_moa:'')}}" placeholder="">
                                </div> -->
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
</script>
@endsection