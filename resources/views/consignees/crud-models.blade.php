{{--modal for create consigner--}}
    <div class="modal fade" id="createConsignee" tabindex="-1" role="dialog" aria-labelledby="createConsigneeLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createConsigneeLabel">Create Consignee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="general_form" method="POST" action="{{url($prefix.'/consignees')}}"
                          id="createconsignee">
                        <div class="form-row mb-2">

                            <div class="form-group form-group-sm col-md-6">
                                <label for="exampleFormControlSelect1">Consigner<span
                                        class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="consigner_id">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($consigners) > 0) {
                                    foreach ($consigners as $key => $consigner) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($consigner)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-group-sm col-md-6">
                                <label for="exampleFormControlSelect1">Type Of Dealer</label>
                                <select class="form-control form-control-sm" id="dealer_type" name="dealer_type">
                                    <option value="">Select</option>
                                    <option value="1">Registered</option>
                                    <option value="0">Unregistered</option>
                                </select>
                            </div>

                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Consignee Nick Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="nick_name" placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Consignee Legal Name</label>
                                <input type="text" class="form-control form-control-sm" name="legal_name"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Contact Person Name</label>
                                <input type="text" class="form-control form-control-sm" name="contact_name"/>
                            </div>

                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Email ID</label>
                                <input type="email" class="form-control form-control-sm" name="email" placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Mobile No.<span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control form-control-sm mbCheckNm" name="phone"
                                       placeholder="Enter 10 digit mobile no" maxlength="10">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">GST No.<span
                                        style="display: none;"
                                        class="gstno_error text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="gst_number"
                                       name="gst_number" disabled
                                       placeholder="" maxlength="15">
                                {{--                                <p class="gstno_error text-danger"--}}
                                {{--                                   style="display: none; color: #ff0000; font-weight: 500;">Please enter GST no.</p>--}}
                            </div>

                            <div class="form-group form-group-sm col-md-2">
                                <label for="exampleFormControlInput2">Pincode</label>
                                <input type="text" class="form-control form-control-sm" id="postal_code"
                                       name="postal_code"
                                       placeholder="Pincode" maxlength="6">
                            </div>
                            <div class="form-group form-group-sm col-md-3">
                                <label for="exampleFormControlInput2">Village/City</label>
                                <input type="text" class="form-control form-control-sm" id="city" name="city"
                                       placeholder="City">
                            </div>

                            <div class="form-group form-group-sm col-md-3">
                                <label for="exampleFormControlInput2">District</label>
                                <input type="text" class="form-control form-control-sm" id="district" name="district"
                                       placeholder="District">
                            </div>
                            <div class="form-group form-group-sm col-md-2">
                                <label for="exampleFormControlSelect1">Select State</label>
                                <input type="text" class="form-control form-control-sm" id="state" name="state_id"
                                       placeholder=""
                                       readonly>
                            </div>

                            <div class="form-group form-group-sm col-md-2">
                                <label for="exampleFormControlInput2">Primary Zone</label>
                                <input type="text" class="form-control form-control-sm" id="zone_name" name="zone_name"
                                       disabled
                                       placeholder="">
                            </div>
                            <input type="hidden" id="zone_id" name="zone_id" value="">

                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 1</label>
                                <input type="text" class="form-control form-control-sm" name="address_line1"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 2</label>
                                <input type="text" class="form-control form-control-sm" name="address_line2"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 3</label>
                                <input type="text" class="form-control form-control-sm" name="address_line3"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 4</label>
                                <input type="text" class="form-control form-control-sm" name="address_line4"
                                       placeholder="">
                            </div>
                        </div>

                        <div class="col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" style="min-width: 120px" class="btn btn-primary">
                                Submit
                            </button>
                        </div>

                        {{--                        <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>--}}
                        {{--                        <a class="btn btn-primary" href="{{url($prefix.'/consignees') }}"> Back</a>--}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--modal for view consigner--}}
    <div class="modal fade" id="consigneeDetailsModal" tabindex="-1" role="dialog"
         aria-labelledby="consigneeDetailsModalLabel" 
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consigneeDetailsModalLabel">Consignee Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="d-flex flex-wrap justify-content-between px-4 pt-4 pb-1"
                         style="gap: 1rem; row-gap: 1.5rem">
                        <div class="d-flex flex-wrap col-md-7 detailsBlock">
                            <p>
                                <span class="detailKey">Legal Name: </span>
                                <span class="detailValue text-uppercase" id="legal_name"></span>
                            </p>
                            <p>
                                <span class="detailKey">Nick Name: </span>
                                <span class="detailValue text-capitalize" id="nick_name"></span>
                            </p>
                            <p>
                                <span class="detailKey">Contact Person: </span>
                                <span class="detailValue text-uppercase" id="contact_person"></span>
                            </p>

                            <p>
                                <span class="detailKey">Consigner Name: </span>
                                <span class="detailValue text-capitalize" id="consignee_name"></span>
                            </p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center detailsBlock contactDetails">
                            <p>
                                <span class="detailKey">EMAIL: </span>
                                <span class="detailValue" id="cne_email"></span>
                            </p>
                            <p>
                                <span class="detailKey">PHONE: </span>
                                <span class="detailValue text-uppercase" id="cne_phone"></span>
                            </p>
                            <p>
                                <span class="detailKey">Dealer Type: </span>
                                <span class="detailValue text-uppercase" id="dealer_type"></span>
                            </p>
                            <p>
                                <span class="detailKey">GSTIN: </span>
                                <span class="detailValue text-uppercase" id="cne_gst"></span>
                            </p>
                        </div>
                        <div class="d-flex flex-wrap col-md-12 detailsBlock addressBlock py-4">
                            <p class="mb-2" style="width:  100%;">
                                <span class="detailKey">Zone: </span>
                                <span class="detailValue text-capitalize" id="testdt">
                                        Zone - 4
                                    </span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">Pincode: </span>
                                <span class="detailValue text-uppercase" id="cne_pin"></span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">City: </span>
                                <span class="detailValue text-uppercase" id="cne_city"></span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">District: </span>
                                <span class="detailValue text-uppercase" id="cne_district"></span>
                            </p>
                            <p style="flex: 1;">
                                <span class="detailKey">State: </span>
                                <span class="detailValue text-uppercase" id="cne_state"></span>
                            </p>
                            <p class="mt-2" style="width:  100%;">
                                <span class="detailKey">Address: </span>
                                <span class="detailValue text-capitalize" id="cne_address">
                                    </span>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center mt-3 pt-3"
                     style="gap: 1rem;">
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                            onclick="closeConsigneeDetaislModal()" >
                        Edit
                    </button>
                    <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                            data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{--modal for Edit consigner--}}
    <div class="modal fade" id="consigneeDetailsEditModal"  tabindex="-1" role="dialog"
         aria-labelledby="consigneeDetailsEditModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consigneeDetailsEditModalLabel">Edit Consignee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form class="general_form" method="POST" action="{{url($prefix.'/consignees/update-consignee')}}" id="updateconsignee">
                            @csrf
                          <input type="hidden" name="consignee_id" id="edit_cne_id" value="">
                        <div class="form-row mb-2">

                            <div class="form-group form-group-sm col-md-6">
                                <label for="exampleFormControlSelect1">Consignee<span
                                        class="text-danger">*</span></label>
                                <select class="form-control form-control-sm edit_cnr" name="consigner_id">
                                    <option value="">Select</option>
                                    <?php
                                    if(count($consigners) > 0) {
                                    foreach ($consigners as $key => $consigner) {
                                    ?>
                                    <option value="{{ $key }}">{{ucwords($consigner)}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-group-sm col-md-6">
                                <label for="exampleFormControlSelect1">Type Of Dealer</label>
                                <select class="form-control form-control-sm" id="edit_dealer_type" name="dealer_type">
                                    <option value="">Select</option>
                                    <option value="1">Registered</option>
                                    <option value="0">Unregistered</option>
                                </select>
                            </div>

                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Consignee Nick Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="edit_nick_name" name="nick_name" placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Consignee Legal Name</label>
                                <input type="text" class="form-control form-control-sm" name="legal_name" id="edit_legal_name"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Contact Person Name</label>
                                <input type="text" class="form-control form-control-sm" name="contact_name" id="edit_contact_person"/>
                            </div>

                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Email ID</label>
                                <input type="email" class="form-control form-control-sm" name="email" placeholder="" id="edit_email">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Mobile No.<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mbCheckNm" name="phone" id="add_phone"
                                       placeholder="Enter 10 digit mobile no" maxlength="10">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">GST No.<span
                                        style="display: none;"
                                        class="gstno_error text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm gst_number" id="gst_edit"
                                       name="gst_number" disabled
                                       placeholder="" maxlength="15">
                                {{--                                <p class="gstno_error text-danger"--}}
                                {{--                                   style="display: none; color: #ff0000; font-weight: 500;">Please enter GST no.</p>--}}
                            </div>

                            <div class="form-group form-group-sm col-md-2">
                                <label for="exampleFormControlInput2">Pincode</label>
                                <input type="text" class="form-control form-control-sm" name="postal_code" id="pin_code" placeholder="Pincode" maxlength="6">
                            </div>
                            <div class="form-group form-group-sm col-md-3">
                                <label for="exampleFormControlInput2">Village/City</label>
                                <input type="text" class="form-control form-control-sm edit_city" id="city" name="city" 
                                       placeholder="City">
                            </div>

                            <div class="form-group form-group-sm col-md-3">
                                <label for="exampleFormControlInput2">District</label>
                                <input type="text" class="form-control form-control-sm edit_district" id="district" name="district" 
                                       placeholder="District">
                            </div>
                            <div class="form-group form-group-sm col-md-2">
                                <label for="exampleFormControlSelect1">Select State</label>
                                <input type="text" class="form-control form-control-sm edit_state" id="state" name="state_id"
                                       placeholder=""
                                       readonly>
                            </div>

                            <div class="form-group form-group-sm col-md-2">
                                <label for="exampleFormControlInput2">Primary Zone</label>
                                <input type="text" class="form-control form-control-sm" id="zone_name" name="zone_name"
                                       disabled
                                       placeholder="">
                            </div>
                            <input type="hidden" id="zone_id" name="zone_id" value="">

                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 1</label>
                                <input type="text" class="form-control form-control-sm" name="address_line1" id="edit_address1"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 2</label>
                                <input type="text" class="form-control form-control-sm" name="address_line2" id="edit_address2"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 3</label>
                                <input type="text" class="form-control form-control-sm" name="address_line3" id="edit_address3"
                                       placeholder="">
                            </div>
                            <div class="form-group form-group-sm col-md-4">
                                <label for="exampleFormControlInput2">Address Line 4</label>
                                <input type="text" class="form-control form-control-sm" name="address_line4" id="edit_address4"
                                       placeholder="">
                            </div>
                        </div>

                        <div class="col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                                Close
                            </button>
                            <input type="submit" class="mt-4 mb-4 btn btn-primary" value="Update">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>