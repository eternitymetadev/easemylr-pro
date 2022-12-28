
    {{--    Create Vehicle Modal--}}
    <div class="modal fade" id="createVehicleModal" tabindex="-1" role="dialog"
         aria-labelledby="createVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createVehicleModalLabel">Add Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST"
                          action="{{url($prefix.'/vehicles')}}"
                          id="createvehicle">

                        <div class="form-row align-items-end" style="box-shadow: none">
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="{{url("/assets/img/upload-img.png")}}"
                                         class="rcshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="rc_image">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-edit">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                </path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                </path>
                                            </svg>
                                        </label>

                                        <input type="file"
                                               class="form-control form-control-sm form-control form-control-sm-sm rc_image"
                                               id="rc_image" name="rc_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label class="text-center" style="width: 100%">RC Image</label>

                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Registration No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="regn_no" name="regn_no" placeholder=""
                                       maxlength="12">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Engine No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="engine_no" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Chassis No.</label>
                                <input type="text" class="form-control" name="chassis_no" placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Vehicle Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Manufacturer<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mfg" placeholder="Mahindra, Tata, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Make<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="make"
                                       placeholder="407, Supro Maxi, Truck, Pickup, Ace, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Body Type</label>
                                <select class="form-control" name="body_type">
                                    <option value="Container">Container</option>
                                    <option value="Open Body">Open Body</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Gross Vehicle Weight</label>
                                <input type="text" class="form-control" id="gross_vehicle_weight"
                                       name="gross_vehicle_weight" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Unladen Weight</label>
                                <input type="text" class="form-control" id="unladen_weight" name="unladen_weight"
                                       placeholder="" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Tonnage Capacity</label>
                                <input type="text" class="form-control" id="tonnage_capacity" name="tonnage_capacity"
                                       value="" placeholder="" readonly>
                            </div>
                        </div>

                        <div class="form-row mb-0">
                            <h6 class="col-12">Registration Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">State(Regd)</label>
                                <select class="form-control" name="state_id">
                                    <option value="">Select</option>
                                        @if(count($states) > 0)
                                            @foreach($states as $key => $state)
                                                <option value="{{ $key }}">{{ucwords($state->name)}}</option>
                                            @endforeach
                                        @endif
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Regn. Date</label>
                                <input type="date" class="form-control" name="regndate" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Hypothecation</label>
                                <input type="text" class="form-control" name="hypothecation"
                                       placeholder="Name of Financer | N/A">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Ownership</label>
                                <select class="form-control" name="ownership">
                                    <option value="Self Owned">Self Owned</option>
                                    <option value="Company Owned">Company Owned</option>
                                    <option value="Transporter Owned">Transporter Owned</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Mobile No.</label>
                                <input type="text" class="form-control" name="owner_phone" placeholder="">
                            </div>
                        </div>


                        <div class="col-md-12 d-flex justify-content-end align-items-center"
                             style="gap: 1rem; margin-top: 3rem;">
                            <button type="button" style="width: 80px" class="btn btn-outline-primary"
                                    data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" style="width: 80px" class="btn btn-primary">
                                Submit
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{--    view Vehicle Modal--}}
    <div class="modal fade" id="vehicleDetailsModal" tabindex="-1" role="dialog"
         aria-labelledby="vehicleDetailsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 700px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehicleDetailsModalLabel">Vehicle Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4" style="min-height: 320px">

                    <div class="vehicleDetails mb-3 d-flex align-items-center justify-content-between flex-wrap"
                         style="width: 100%;">
                        <img src="s" id="view_src_pc" alt="RC photo"/>

                        <div class="flex-grow-1">
                            <p>
                                <span class="textHeading">Registration No:</span>
                                <span class="textValue" id="vehicle_reg_no"></span>
                            </p>
                            <p>
                                <span class="textHeading">Registration Date:</span>
                                <span class="textValue" id="vehicle_reg_date"></span>
                            </p>
                            <p>
                                <span class="textHeading">Engine No:</span>
                                <span class="textValue" id="vehicle_eng_no"></span>
                            </p>
                            <p>
                                <span class="textHeading">Chassis No:</span>
                                <span class="textValue" id="vehicle_chassis_no"></span>
                            </p>
                        </div>

                    </div>

                    <div class="vehicleMoreDetails">
                        <p style="width: 100%">
                            <span class="textHeading">Regd. State :</span>
                            <span class="textValue text-uppercase" id="vehicle_state_name"></span>
                        </p>
                        <p>
                            <span class="textHeading">Ownership:</span>
                            <span class="textValue" id="vehicle_ownership"></span>
                        </p>
                        <p>
                            <span class="textHeading">Owner Name:</span>
                            <span class="textValue" id="vehicle_owner_name"></span>
                        </p>
                        <p>
                            <span class="textHeading">Owner Mobile:</span>
                            <span class="textValue" id="vehicle_owner_phone"></span>
                        </p>
                        <p>
                            <span class="textHeading">Hypothecation:</span>
                            <span class="textValue" id="vehicle_hypothecation"></span>
                        </p>
                    </div>

                    <div class="vehicleMoreDetails mt-4">
                        <p style="width: 100%">
                            <span class="textHeading">Manufacturar:</span>
                            <span class="textValue text-uppercase" id="vehicle_manufacturar"></span>
                        </p>
                        <p style="min-width: 40%; flex: 1">
                            <span class="textHeading">Model:</span>
                            <span class="textValue" id="vehicle_model"></span>
                        </p>
                        <p>
                            <span class="textHeading">Body Type:</span>
                            <span class="textValue" id="vehicle_body"></span>
                        </p>
                        <div class="d-flex flex-wrap justify-content-between align-items-center flex-grow-1">
                            <p>
                                <span class="textHeading">Gross Weight:</span>
                                <span class="textValue" id="vehicle_gross_wt"></span>
                            </p>
                            <p>
                                <span class="textHeading">Unladen Weight:</span>
                                <span class="textValue" id="vehicle_unladen_wt"></span>
                            </p>
                            <p>
                                <span class="textHeading">Tonnage Weight:</span>
                                <span class="textValue" id="vehicle_tonnage_capacity"></span>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                    <button type="button" style="width: 80px" class="btn btn-outline-primary" onclick="clickEditDriverModal()">
                        Edit
                    </button>
                    <button type="button" style="width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    {{--    Edit Vehicle Modal--}}
    <div class="modal fade" id="VehicleDetailsEditModal" tabindex="-1" role="dialog"
         aria-labelledby="editVehicleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleModalLabel">Edit Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST"
                          action="{{url($prefix.'/vehicles')}}"
                          id="createvehicle">

                        <div class="form-row align-items-end" style="box-shadow: none">
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="#" id="edit_view_src_pc"
                                         class="rcshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="rc_image">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-edit">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                </path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                </path>
                                            </svg>
                                        </label>

                                        <input type="file"
                                               class="form-control form-control-sm form-control form-control-sm-sm rc_image"
                                               id="rc_image" name="rc_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <input type="hidden" name="driver_id" id="edit_vehicle_id" value="">
                                <label class="text-center" style="width: 100%">RC Image</label>

                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Registration No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_vehicle_reg_no" name="regn_no" placeholder=""
                                       maxlength="12">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Engine No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="engine_no" id="edit_vehicle_eng_no" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlSelect1">Chassis No.</label>
                                <input type="text" class="form-control" id="edit_vehicle_chassis_no" name="chassis_no" placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Vehicle Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Manufacturer<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mfg" id="edit_vehicle_manufacturar" placeholder="Mahindra, Tata, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Make<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="make" id="edit_vehicle_model"
                                       placeholder="407, Supro Maxi, Truck, Pickup, Ace, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Body Type</label>
                                <select class="form-control" name="body_type" id="edit_vehicle_body">
                                    <option value="Container">Container</option>
                                    <option value="Open Body">Open Body</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Gross Vehicle Weight</label>
                                <input type="text" class="form-control" id="edit_vehicle_gross_wt"
                                       name="gross_vehicle_weight" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Unladen Weight</label>
                                <input type="text" class="form-control" id="edit_vehicle_unladen_wt" name="unladen_weight"
                                       placeholder="" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Tonnage Capacity</label>
                                <input type="text" class="form-control" id="edit_vehicle_tonnage_capacity" name="tonnage_capacity"
                                       value="" placeholder="" readonly>
                            </div>
                        </div>

                        <div class="form-row mb-0">
                            <h6 class="col-12">Registration Details</h6>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">State(Regd)</label>
                                <select class="form-control" name="state_id" id="edit_state_id">
                                    <option value="">Select</option>
                                                                        @if(count($states) > 0)
                                                                            @foreach($states as $key => $state)
                                                                                <option value="{{ $key }}">{{ucwords($state->name)}}</option>
                                                                            @endforeach
                                                                        @endif
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlSelect1">Regn. Date</label>
                                <input type="date" class="form-control" id="edit_vehicle_reg_date" name="regndate" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Hypothecation</label>
                                <input type="text" class="form-control" name="hypothecation" id="edit_vehicle_hypothecation"
                                       placeholder="Name of Financer | N/A">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Ownership</label>
                                <select class="form-control" name="ownership" id="edit_vehicle_ownership">
                                    <option value="Self Owned">Self Owned</option>
                                    <option value="Company Owned">Company Owned</option>
                                    <option value="Transporter Owned">Transporter Owned</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Name</label>
                                <input type="text" class="form-control" id="edit_vehicle_owner_name" name="owner_name" placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Owner Mobile No.</label>
                                <input type="text" class="form-control" id="edit_vehicle_owner_phone" name="owner_phone" placeholder="">
                            </div>
                        </div>


                        <div class="col-md-12 d-flex justify-content-end align-items-center"
                             style="gap: 1rem; margin-top: 3rem;">
                            <button type="button" style="width: 80px" class="btn btn-outline-primary"
                                    data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" style="width: 80px" class="btn btn-primary">
                                Submit
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{--    RC view modal--}}
    <div class="modal fade" id="rcViewModal" tabindex="-1" role="dialog"
         aria-labelledby="rcViewModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rcViewModalLabel">Registeration Certificate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="min-height: 240px">
                    <div class="col-md-12 bg-black">
                        <img id="view-src"
                             src="view_file_name"
                             alt="sample image"
                             style="width: 100%; max-height: 300px; border-radius: 12px;"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-outline-primary" href="view_file_name" target="_blank"
                       style="border-radius: 8px; display: flex;align-items: center;gap: 6px;">
                        Open in New Tab
                        <svg xmlns="http://www.w3.org/2000/svg" width="4" height="14"
                             viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="feather feather-external-link" style="height: 14px; width: 14px">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </div>