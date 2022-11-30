@extends('layouts.main')
@section('content')

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Create Consigner</h2>
        </div>

        <div class="widget-content widget-content-area shadow-lg " style="border-radius: 12px">
            <form class="general_form" method="POST" action="{{url($prefix.'/consigners')}}" id="createconsigner">
                <div class="form-row mb-0">
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlSelect1">Regional Client<span
                                class="text-danger">*</span></label>
                        <?php $authuser = Auth::user();
                        if($authuser->role_id == 4){
                        ?>
                        <select class="form-control" id="regionalclient_id" name="regionalclient_id">
                            <option value="">Select</option>
                            <?php
                            if(count($regclients) > 0) {
                            foreach ($regclients as $key => $client) {
                            ?>
                            <option data-locationid="{{$client->location_id}}"
                                    value="{{ $client->id }}">{{ucwords($client->name)}}</option>
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
                            if(count($regclients) > 0) {
                            foreach ($regclients as $key => $client) {
                            ?>
                            <option data-locationid="{{$client->location_id}}"
                                    value="{{ $client->id }}">{{ucwords($client->name)}}</option>
                            <?php
                            }
                            }
                            ?>
                        </select>
                        <input type="hidden" name="branch_id" id="location_id">
                        <?php } ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Consigner Nick Name<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nick_name" placeholder="" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Consigner Legal Name<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="legal_name" placeholder="" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">GST No.<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="gst_number" name="gst_number"
                               placeholder="" maxlength="15" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">Contact Person Name</label>
                        <input type="text" class="form-control" name="contact_name"
                               placeholder="Contact Name">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">Mobile No.<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control mbCheckNm" name="phone"
                               placeholder="Enter 10 digit mobile no" maxlength="10" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">Email ID</label>
                        <input type="email" class="form-control" name="email" placeholder="">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">Pincode<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code"
                               placeholder="" maxlength="6" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">City<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput2">District<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="district" name="district" placeholder="" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlSelect1">State<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="state" name="state_id" placeholder=""
                               readonly required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Address Line 1</label>
                        <input type="text" class="form-control" name="address_line1" placeholder="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2" placeholder="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Address Line 3</label>
                        <input type="text" class="form-control" name="address_line3" placeholder="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Address Line 4</label>
                        <input type="text" class="form-control" name="address_line4" placeholder="">
                    </div>

                    <div class="col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                        <a class="btn btn-outline-primary" href="{{url($prefix.'/consigners') }}"> View Consigners</a>

                        <button type="submit" style="min-width: 120px" name="time" class="mt-4 mb-4 btn btn-primary">
                            Submit
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
