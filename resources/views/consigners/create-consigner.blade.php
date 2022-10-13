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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consigner</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create
                                Consigner</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/consigners')}}"
                            id="createconsigner">
                            <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Regional Client<span
                                            class="text-danger">*</span></label>
                                    <?php $authuser = Auth::user();
                                    if($authuser->role_id == 4){
                                    ?>
                                    <select class="form-control" id="regionalclient_id" name="regionalclient_id">
                                    <option value="">Select</option>
                                        <?php 
                                        if(count($regclients)>0) {
                                            foreach ($regclients as $key => $client) {
                                        ?>
                                        <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">{{ucwords($client->name)}}</option>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="branch_id" value="{{$client->location_id}}">
                                    <?php } else { ?>
                                        <select class="form-control" id="regionalclient_id" name="regionalclient_id">
                                        <option value="">Select</option>
                                        <?php 
                                        if(count($regclients)>0) {
                                            foreach ($regclients as $key => $client) {
                                        ?>
                                        <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">{{ucwords($client->name)}}</option>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="branch_id" id="location_id">
                                    <?php } ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Consigner Nick Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nick_name" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Consigner Legal Name</label>
                                    <input type="text" class="form-control" name="legal_name" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Contact Person Name</label>
                                    <input type="text" class="form-control" name="contact_name"
                                        placeholder="Contact Name">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Email ID</label>
                                    <input type="email" class="form-control" name="email" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Mobile No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control mbCheckNm" name="phone"
                                        placeholder="Enter 10 digit mobile no" maxlength="10">
                                </div>                                
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">GST No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="gst_number" name="gst_number"
                                        placeholder="" maxlength="15">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pincode</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="" maxlength="6">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">District</label>
                                    <input type="text" class="form-control" id="district" name="district" placeholder="">
                                </div>

                                <!-- <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Status</label>
                                    <div class="check-box d-flex">
                                        <div class="checkbox radio">
                                            <label class="check-label">Active
                                                <input type="radio" value="1" name="status" class=""  checked="">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                        <div class="checkbox radio">
                                            <label class="check-label">Deactive
                                                <input type="radio" name="status" value="0">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>                             
                                </div>   -->
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">State</label>
                                    <input type="text" class="form-control" id="state" name="state_id" placeholder="" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 1</label>
                                    <input type="text" class="form-control" name="address_line1" placeholder="">
                                </div>
                            </div>
                            
                            <div class="form-row mb-0"> 
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 2</label>
                                    <input type="text" class="form-control" name="address_line2" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 3</label>
                                    <input type="text" class="form-control" name="address_line3" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">   
                            <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Address Line 4</label>
                                    <input type="text" class="form-control" name="address_line4" placeholder="">
                                </div>
                            </div>
                            <button type="submit" name="time" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/consigners') }}"> Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection