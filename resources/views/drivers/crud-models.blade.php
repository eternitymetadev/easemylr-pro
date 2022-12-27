
    {{--    Create Driver Modal--}}
    <div class="modal fade" id="createDriverModal" tabindex="-1" role="dialog"
         aria-labelledby="createDriverModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDriverModalLabel">Create Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST" action="{{url($prefix.'/drivers')}}" id="createdriver">

                        <div class="form-row align-items-end" style="box-shadow: none">

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Phone<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                       placeholder="Phone"
                                       maxlength="10">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver License Number<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="license_number"
                                       placeholder="">
                            </div>
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="{{url("/assets/img/upload-img.png")}}"
                                         class="licenseshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="license_image">
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
                                               class="form-control form-control-sm form-control form-control-sm-sm license_image"
                                               id="license_image" name="license_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label>Driveing Licence Image</label>

                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Shadow Details</h6>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Team Id</label>
                                <input type="text" class="form-control form-control-sm" name="team_id" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Fleet ID</label>
                                <input type="text" class="form-control form-control-sm" name="fleet_id" placeholder="">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Login Id</label>
                                <input type="text" class="form-control form-control-sm" name="login_id" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Password</label>
                                <input type="password" class="form-control form-control-sm" name="password"
                                       placeholder="">
                            </div>
                        </div>

                        <div class="form-group col-12 d-flex align-items-center" style="gap: 8px">
                            <input style="height: 1rem; width: 1rem" type="checkbox" class="" name="enableAppAccess"
                                   id="enableAppAccess"/>
                            <label for="enableAppAccess">Enable App Access to Driver</label>
                        </div>

                        <div id="enabledAppAccess" class="form-row" style="display: none">

                            <div class="col-12 p-2">
                                <div class="form-group mb-0">
                                    <label>Tagged Branch</label>
                                    <select id="branch_id" class="form-control tagging" multiple="multiple" name="branch_id[]">
                                        <option disabled>Select</option>
                                        <option>None</option>
                                        <option value="AL">Alabama</option>
                                        <option value="dWY">Wyoming</option>
                                        <option value="fY">Wyoming</option>
                                        <option value="WdfY">Wyoming</option>
                                    </select>
                                </div>
                            </div>

                            <div class="appLoginDetailsBlock">
                                @if(true)
                                    <div class="d-flex flex-wrap justify-content-between align-items-center"
                                         style="gap: 1rem">
                                        <p id="createUsernameInfo"
                                           style="font-size: 18px;font-weight: 600; margin-bottom: 0;">
                                            No username & password created yet.
                                        </p>
                                        <span id="toggleCreateUsernameView">
                                            <span id="toggleCreateUsernameLabel">Create</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-chevron-down">
                                                <polyline points="6 9 12 15 18 9"></polyline>
                                            </svg>
                                        </span>
                                    </div>
                                @endif

                                <div id="createUsernameView"
                                     class="mx-auto mt-2 row align-items-center justify-content-between"
                                     style="width: 100%; @if(true)display: none;@endif">
                                    <div class="form-group col-md-4">
                                        <label for="nickName">Nick Name</label>
                                        <input class="form-control form-control-sm" name="nickName" id="nickName"
                                               placeholder="Driver Nick Name"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="loginId">App Login ID</label>
                                        <input class="form-control form-control-sm" name="loginId" id="loginId"
                                               placeholder="Username"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="appPassword">Password</label>
                                        <input class="form-control form-control-sm" name="appPassword" id="appPassword"
                                               placeholder="password"/>
                                    </div>
                                </div>
                            </div>

                        </div>


                        {{--                        <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>--}}
                        {{--                        <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>--}}


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

    {{--    view Driver Modal--}}
    <div class="modal fade" id="driverDetailsModal" tabindex="-1" role="dialog"
         aria-labelledby="driverDetailsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="driverDetailsModalLabel">Driver Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4" style="min-height: 320px">

                    <div class="driverPersonalDetails mb-5 d-flex align-items-center justify-content-between flex-wrap"
                         style="width: 100%;">
                        <!-- <img src="s" alt="driver photo"/> -->
                        <img id="view_src_pc" src="#" alt="sample image" style="width: 100%; max-height: 300px; border-radius: 12px;">

                        <div class="flex-grow-1">
                            <p>
                                <span class="textValue" style="font-size: 1rem">Driver Name:</span>
                                <span id="DriverName"></span>
                            </p>
                            <p>
                                <span class="textValue">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-phone">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                    <span id="DriverPhone"></span>
                                </span>
                            </p>
                            <p>
                                <span class="textHeading">Licence No.:</span>
                                <span class="textValue" id="LicenseNum"></span>
                                <!-- <span class="licenceViewLink">View</span> -->
                            </p>
                        </div>

                    </div>

                    <label class="ml-2">Bank Details</label>
                    <div class="driverBankDetails">
                        <p style="width: 100%">
                            <span class="textHeading">Account Holder Name:</span>
                            <span class="textValue" id="acc_holder_name"></span>
                        </p>
                        <p>
                            <span class="textHeading">Account No.:</span>
                            <span class="textValue" id="acc_num"></span>
                        </p>
                        <p>
                            <span class="textHeading">IFSC:</span>
                            <span class="textValue" id="acc_ifsc"></span>
                        </p>
                        <p>
                            <span class="textHeading">Bank Name:</span>
                            <span class="textValue" id="bank_name"></span>
                        </p>
                        <p style="width: 100%">
                            <span class="textHeading">Branch:</span>
                            <span class="textValue" id="bank_branch"></span>
                        </p>


                    </div>

                </div>
                <div class="modal-footer col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                    <button type="button" style="width: 80px" class="btn btn-outline-primary"
                            onclick="clickEditDriverModal()">
                        Edit
                    </button>
                    <button type="button" style="width: 80px" class="btn btn-outline-primary" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    {{--    Edit Driver Modal--}}
    <div class="modal fade" id="DriverDetailsEditModal" tabindex="-1" role="dialog"
         aria-labelledby="editDriverModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDriverModalLabel">Edit Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">

                    <form class="general_form" method="POST" action="{{url($prefix.'/drivers/update-driver')}}"
                          id="updatedriver">

                        @csrf
                        {{--                        <input type="hidden" name="driver_id" value="{{$getdriver->id}}">--}}
                        <input type="hidden" name="driver_id" id="edit_driver_id" value="">
                        <div class="form-row align-items-end" style="box-shadow: none">

                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="edit_driver_name" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver Phone<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                       placeholder="Phone" id="edit_driver_phone"
                                       maxlength="10">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleFormControlInput2">Driver License Number<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="license_number"
                                     id="edit_license_number"  placeholder="">
                            </div>
                            <div class="d-flex flex-wrap justify-content-center col-md-3">
                                <div class="image_upload" style="position:relative;">
                                    <img src="#"
                                    id="edit_src_pc"
                                         class="licenseshow image-fluid" id="img-tag" width="140"
                                         height="100" style="border-radius: 8px; object-fit: contain;">

                                    <div class="imageUploadInput">
                                        <label class="d-flex justify-content-center align-items-center" style="height: 100%;
                                    width: 100%;" for="license_image">
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
                                               class="form-control form-control-sm form-control form-control-sm-sm license_image"
                                               id="license_image" name="license_image" hidden accept="image/*">
                                    </div>
                                </div>
                                <label>Driveing Licence Image</label>

                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Bank Details</h6>

                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Account Holder Name</label>
                                <input type="text" class="form-control form-control-sm" name="account_holdername" id="edit_acc_holder_name"
                                       placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">Account No</label>
                                <input type="text" class="form-control form-control-sm" name="account_number" id="edit_acc_no"
                                       placeholder="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleFormControlInput2">IFSC</label>
                                <input type="text" class="form-control form-control-sm" name="ifsc" placeholder="" id="edit_acc_ifsc">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Bank Name</label>
                                <input type="text" class="form-control form-control-sm" name="bank_name" id="edit_bank_name" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Branch Name</label>
                                <input type="text" class="form-control form-control-sm" name="branch_name" id="edit_branch_name"
                                       placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Shadow Details</h6>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Team Id</label>
                                <input type="text" class="form-control form-control-sm" id="edit_team_id" name="team_id" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Fleet ID</label>
                                <input type="text" class="form-control form-control-sm" id="edit_fleet_id" name="fleet_id" placeholder="">
                            </div>
                        </div>

                        <div class="form-row">
                            <h6 class="col-12">Login Details</h6>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Login Id</label>
                                <input type="text" class="form-control form-control-sm" name="login_id" id="edit_login_id" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleFormControlInput2">Password</label>
                                <input type="password" class="form-control form-control-sm" name="password" id="edit_password"
                                       placeholder="">
                            </div>
                        </div>


                        {{--                        <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>--}}
                        {{--                        <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>--}}


                        <div class="col-md-12 d-flex justify-content-end align-items-center"
                             style="gap: 1rem; margin-top: 3rem;">
                            <button type="button" style="width: 80px" class="btn btn-outline-primary"
                                    data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" style="width: 80px" class="btn btn-primary">
                                Update
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{--    DL view modal--}}
    <div class="modal fade" id="dlViewModal" tabindex="-1" role="dialog"
         aria-labelledby="dlViewModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 500px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dlViewModalLabel">Driving Licence</h5>
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