{{--modal for create consigner--}}
        <div class="modal fade" id="createConsigner" tabindex="-1" role="dialog" aria-labelledby="createConsignerLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createConsignerLabel">Create Consigner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="general_form" method="POST" action="{{url($prefix.'/consigners')}}"
                              id="createconsigner">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlSelect1">Regional Client<span
                                            class="text-danger">*</span></label>
                                    <?php $authuser = Auth::user();
                                    if($authuser->role_id == 4){
                                    ?>
                                    <select class="form-control form-control-sm" id="regionalclient_id"
                                            name="regionalclient_id">
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
                                    <select class="form-control form-control-sm" id="regionalclient_id"
                                            name="regionalclient_id">
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
                                    <input type="text" class="form-control form-control-sm" name="nick_name"
                                           placeholder="" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Consigner Legal Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="legal_name"
                                           placeholder="" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">GST No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="gst_number"
                                           name="gst_number"
                                           placeholder="" maxlength="15" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Contact Person Name</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_name"
                                           placeholder="Contact Name">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Mobile No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                           placeholder="Enter 10 digit mobile no" maxlength="10" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Email ID</label>
                                    <input type="email" class="form-control form-control-sm" name="email"
                                           placeholder="">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Pincode<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="postal_code"
                                           name="postal_code"
                                           placeholder="" maxlength="6" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">City<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="city" name="city"
                                           placeholder=""
                                           required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">District<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="district"
                                           name="district" placeholder=""
                                           required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlSelect1">State<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="state" name="state_id"
                                           placeholder=""
                                           readonly required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 1</label>
                                    <input type="text" class="form-control form-control-sm" name="address_line1"
                                           placeholder="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 2</label>
                                    <input type="text" class="form-control form-control-sm" name="address_line2"
                                           placeholder="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 3</label>
                                    <input type="text" class="form-control form-control-sm" name="address_line3"
                                           placeholder="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 4</label>
                                    <input type="text" class="form-control form-control-sm" name="address_line4"
                                           placeholder="">
                                </div>

                                <div class="col-md-12 d-flex justify-content-end align-items-center" style="gap: 1rem;">
                                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close
                                    </button>
                                    <button type="submit" style="min-width: 120px" class="btn btn-primary">Submit
                                    </button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>


        {{--modal for view consigner--}}
        <div class="modal fade" id="consignerDetailsModal" tabindex="-1" role="dialog"
             aria-labelledby="consignerDetailsModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="consignerDetailsModalLabel">Consigner Details</h5>
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
                                    <span class="detailValue text-uppercase" id="legal_name">FRONTIER AGROTECH PRIVATE LIMITED</span>
                                </p>
                                <p>
                                    <span class="detailKey">Nick Name: </span>
                                    <span class="detailValue text-capitalize" id="nick_name">Agrotech DD GZB</span>
                                </p>
                                <p>
                                    <span class="detailKey">Regional Client: </span>
                                    <span class="detailValue text-capitalize" id="regional_client">Agrotech SD-1 GZB</span>
                                </p>
                                <p>
                                    <span class="detailKey">Contact Person: </span>
                                    <span class="detailValue text-uppercase" id="contact_person">ABHISHEK SHARMA</span>
                                </p>
                            </div>
                            <div class="d-flex flex-wrap align-items-center detailsBlock contactDetails">
                                <p>
                                    <span class="detailKey">EMAIL: </span>
                                    <span class="detailValue" id="cnr_email">ghaiabadstc@frontierag.com</span>
                                </p>
                                <p>
                                    <span class="detailKey">PHONE: </span>
                                    <span class="detailValue text-uppercase" id="cnr_phone">9115115604</span>
                                </p>
                                <p>
                                    <span class="detailKey">GSTIN: </span>
                                    <span class="detailValue text-uppercase" id="cnr_gst">09AACCF3772B1ZU</span>
                                </p>
                            </div>
                            <div class="d-flex flex-wrap col-md-12 detailsBlock addressBlock py-4">
                                <p style="flex: 1;">
                                    <span class="detailKey">Pincode: </span>
                                    <span class="detailValue text-uppercase" id="cnr_pin">201003</span>
                                </p>
                                <p style="flex: 1;">
                                    <span class="detailKey">City: </span>
                                    <span class="detailValue text-uppercase" id="cnr_city">GHAZIABAD</span>
                                </p>
                                <p style="flex: 1;">
                                    <span class="detailKey">District: </span>
                                    <span class="detailValue text-uppercase" id="cnr_district">GHAZIABAD</span>
                                </p>
                                <p style="flex: 1;">
                                    <span class="detailKey">State: </span>
                                    <span class="detailValue text-uppercase" id="cnr_state">Uttar Pradesh</span>
                                </p>
                                <p class="mt-2" style="width:  100%;">
                                    <span class="detailKey">Address: </span>
                                    <span class="detailValue text-capitalize" id="cnr_address">
                                        KHASRA NO. 938, MORTA, MEERUT ROAD, Ghaziabad - 201003, Uttar Pradesh
                                    </span>
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer d-flex justify-content-end align-items-center mt-3 pt-3"
                         style="gap: 1rem;">
                        <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                                onclick="closeConsignerDetaislModal()">
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
        <div class="modal fade" id="consignerDetailsEditModal" tabindex="-1" role="dialog"
             aria-labelledby="consignerDetailsEditModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(95%, 1100px)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="consignerDetailsEditModalLabel">Edit Consigner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form class="general_form" method="POST" action="{{url($prefix.'/consigners/update-consigner')}}" id="updateconsigner">
                    @csrf
                    <input type="hidden" name="consigner_id" id="edit_cnr_id" value="">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlSelect1">Regional Client<span
                                            class="text-danger">*</span></label>
                                    <?php $authuser = Auth::user();
                                    if($authuser->role_id == 4){
                                    ?>
                                    <select class="form-control form-control-sm edit_regin" id="regionalclient_id"
                                            name="regionalclient_id">
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
                                       
                                    <select class="form-control form-control-sm edit_regin" id="regionalclient_id"
                                            name="regionalclient_id">
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
                                    <input type="text" class="form-control form-control-sm" name="nick_name" id="edit_nick_name"
                                           placeholder="" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Consigner Legal Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="legal_name" id="edit_legal_name"
                                           placeholder="" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">GST No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm edit_gst" id="gst_number"
                                           name="gst_number"
                                           placeholder="" maxlength="15" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Contact Person Name</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_name" id="edit_contact_person"
                                           placeholder="Contact Name">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Mobile No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm mbCheckNm" name="phone"
                                           placeholder="Enter 10 digit mobile no" id="edit_phone" maxlength="10" required>
                                </div> 
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Email ID</label>
                                    <input type="email" class="form-control form-control-sm" name="email" id="edit_email"
                                           placeholder="">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">Pincode<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm edit_pin" id="postal_code"
                                           name="postal_code"
                                           placeholder="" maxlength="6" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">City<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm edit_city" id="city" name="city"
                                           placeholder=""
                                           required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlInput2">District<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm edit_district" id="district"
                                           name="district" placeholder=""
                                           required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleFormControlSelect1">State<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm edit_state" id="state" name="state_id"
                                           placeholder=""
                                           readonly required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 1</label>
                                    <input type="text" class="form-control form-control-sm" name="address_line1"
                                           id="edit_address1" placeholder="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 2</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_address2" name="address_line2"
                                           placeholder="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 3</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_address3" name="address_line3"
                                           placeholder="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Address Line 4</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_address4" name="address_line4"
                                           placeholder="">
                                </div>

                            </div>

                            <div class="modal-footer d-flex justify-content-end align-items-center mt-3 pt-3"
                                 style="gap: 1rem;">
                                <button type="button" style="min-width: 80px" class="btn btn-outline-primary"
                                        data-dismiss="modal">
                                    Close
                                </button>
                                <!-- <button type="submit" style="min-width: 80px" class="btn btn-primary"
                                        data-dismiss="modal">
                                    Update
                                </button> -->
                                <input type="submit" class="mt-4 mb-4 btn btn-primary">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>