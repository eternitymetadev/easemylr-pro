@extends('layouts.main')
@section('content')
    <style>
        .pageContainer {
            min-height: min(75vh, 600px);
            box-shadow: 0 0 16px -3px #83838370;
            border-radius: 12px;
        }
    </style>

    <div class="layout-px-spacing">

        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Branch Address</h2>
            <a href="javascript:void(0)"
               class="btn btn-primary editBranchadd">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="feather feather-edit">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>

        </div>

        <div class="pageContainer widget-content widget-content-area">
            <form class="general_form" method="POST"
                  action="{{url($prefix.'/settings/branch-address')}}" id="createbranchadd"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="branchadd_id" value="{{$branchaddvalue->id}}">

                <div class="form-row">
                    <div class="col-md-12 form-group">
                        <label for="exampleFormControlInput2">Name<span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm" name="name" id="name" placeholder=""
                               value="{{old('name',isset($branchaddvalue->name)?$branchaddvalue->name:'')}}"
                               disabled>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="exampleFormControlInput2">Email Address<span
                                class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" placeholder=""
                               value="{{old('email',isset($branchaddvalue->email)?$branchaddvalue->email:'')}}"
                               disabled>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="exampleFormControlInput2">Phone<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control mbCheckNm" name="phone" id="phone"
                               placeholder=""
                               value="{{old('phone',isset($branchaddvalue->phone)?$branchaddvalue->phone:'')}}"
                               maxlength="10" disabled>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="exampleFormControlInput2">GST Number<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="gst_number" id="gst_number"
                               placeholder=""
                               value="{{old('gst_number',isset($branchaddvalue->gst_number)?$branchaddvalue->gst_number:'')}}"
                               disabled>
                    </div>

                    <div class="col-md-12 form-group">
                        <label for="exampleFormControlInput2">Address<span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" cols="5" rows="2"
                                  placeholder=""
                                  disabled>{{old('address',isset($branchaddvalue->address)?$branchaddvalue->address:'')}}</textarea>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="exampleFormControlInput2">Postal Code<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control mbCheckNm" name="postal_code"
                               id="postal_code" placeholder=""
                               value="{{old('postal_code',isset($branchaddvalue->postal_code)?$branchaddvalue->postal_code:'')}}"
                               maxlength="7" disabled>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="exampleFormControlInput2">City</label>
                        <input type="text" class="form-control" name="city" id="city" placeholder=""
                               value="{{old('city',isset($branchaddvalue->city)?$branchaddvalue->city:'')}}"
                               disabled>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="exampleFormControlInput2">District</label>
                        <input type="text" class="form-control" name="district" id="district"
                               placeholder=""
                               value="{{old('district',isset($branchaddvalue->district)?$branchaddvalue->district:'')}}"
                               disabled>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="exampleFormControlInput2">State<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="state" id="state" placeholder=""
                               value="{{old('state',isset($branchaddvalue->state)?$branchaddvalue->state:'')}}"
                               disabled>
                    </div>

                </div>

                <div class="d-flex justify-content-end align-items-center" style="width: 100%">
                    <button style="display:none; width: 120px" type="submit" class="mt-4 btn btn-primary submitBtn">
                        Submit
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection
