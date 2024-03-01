<div class="modal fade" id="location-updatemodal" tabindex="-1" role="dialog"
    aria-labelledby="location-update-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(70%, 700px)">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="location-update-modal-title">Edit Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="general_form" method="POST" action="{{url($prefix.'/locations/update')}}"
                    id="updatelocation">
                    @csrf
                    <input type="hidden" class="locationid" value="" name="id">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="location_name">Location Name</label>
                                <input class="form-control" id="nameup" name="name" value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location_name">Location City</label>
                                <input class="form-control" id="nick_nameup" name="nick_name" value="">
                            </div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="location_name">Email</label>
                                <input class="form-control" id="emailup" name="email" value="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location_name">Mobile No.</label>
                                <input class="form-control" id="phoneup" name="phone" value="" maxlength="10">
                            </div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="location_name">Team ID</label>
                                <input class="form-control" id="team_idup" name="team_id" value="">
                            </div>
                            <!-- <div class="form-group col-md-6">
                                <label for="location_name">Consignment No.</label>
                                <input class="form-control" id="consignment_noup" name="consignment_no" value="" maxlength="4">
                            </div> -->

                            <div class="form-group col-md-6">
                                <label for="location_name">&nbsp;</label>
                                <div class="check-box d-flex align-content-center align-items-center ml-2"
                                    style="gap: 8px; height: 42px">
                                    <!-- <span style="color: #000"><strong>Is HUB?</strong></span> -->
                                    <div class="checkbox radio">
                                        <label class="check-label">
                                            <input type="radio" class="is_hub_yes" value='1' name="is_hub" />
                                            <!-- <span class="checkmark"></span> -->
                                            HUB
                                        </label>
                                    </div>
                                    <div class="checkbox radio">
                                        <label class="check-label">
                                            <input type="radio" name="is_hub" value='0' class="is_hub_no" />
                                            <!-- <span class="checkmark"></span> -->
                                            Not a HUB
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row mb-0">
                            <span style="color: #000"><strong>Allow LR without vehicle no. :</strong></span>
                            <div class="check-box d-flex align-content-center ml-2" style="gap: 8px">
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input class="radio_vehicleno_yes" type="radio" value='1'
                                            name="with_vehicle_no">
                                        <span class="checkmark"></span>
                                        Yes
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input class="radio_vehicleno_no" type="radio" name="with_vehicle_no" value='0'>
                                        <span class="checkmark"></span>
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-0">
                            <span style="color: #000"><strong>APP USE:</strong></span>
                            <div class="check-box d-flex align-content-center ml-2" style="gap: 8px">
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" class="app_use_eternity" value='Eternity' name="app_use">
                                        <span class="checkmark"></span>
                                        Eternity
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" class="app_use_shadow" name="app_use" value='Shadow'>
                                        <span class="checkmark"></span>
                                        Shadow
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="form-row mb-0">
                            <span style="color: #000"><strong>Stationary:</strong></span>
                            <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" name="stationary" value='2'>
                                        <span class="checkmark"></span>
                                        Plain
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" value='1' name="stationary" checked>
                                        <span class="checkmark"></span>
                                        Colored
                                    </label>
                                </div>
                                
                            </div>

                        </div>
                        <div class="form-row mb-0">
                            <span style="color: #000"><strong>Sticker:</strong></span>
                            <div class="check-box d-flex align-content-center ml-2" style="gap: 8px">
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" value='1' name="sticker" checked>
                                        <span class="checkmark"></span>
                                        Plain
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" name="sticker" value='2'>
                                        <span class="checkmark"></span>
                                        Colored
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="location_savebtn" class="btn btn-primary btn-modal">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>