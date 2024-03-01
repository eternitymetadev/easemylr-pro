<div class="modal fade" id="location-modal" tabindex="-1" role="dialog" aria-labelledby="location-modal-title"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: min(70%, 700px)">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="location-modal-title">Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="general_form" method="POST" action="{{url($prefix.'/locations')}}" id="createlocation">
                    @csrf
                    <input type="hidden" class="locationid" value="" name="id">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="location_name">Location Name</label>
                                <input class="form-control form-control-sm" id="name" name="name" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location_name">Location City</label>
                                <input class="form-control form-control-sm" id="nick_name" name="nick_name"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="location_name">Email ID</label>
                                <input class="form-control form-control-sm" id="email" name="email" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location_name">Mobile No.</label>
                                <input class="form-control form-control-sm" id="phone" name="phone" placeholder=""
                                    maxlength="10">
                            </div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                <label for="location_name">Team ID</label>
                                <input class="form-control form-control-sm" id="team_id" name="team_id" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location_name">&nbsp;</label>
                                <div class="check-box d-flex align-content-center align-items-center ml-2"
                                    style="gap: 8px; height: 42px">
                                    <!-- <span style="color: #000"><strong>Is HUB?</strong></span> -->
                                    <div class="checkbox radio">
                                        <label class="check-label">
                                            <input type="radio" value='1' name="isHub">
                                            <span class="checkmark"></span>
                                            HUB
                                        </label>
                                    </div>
                                    <div class="checkbox radio">
                                        <label class="check-label">
                                            <input type="radio" name="isHub" value='0' checked>
                                            <span class="checkmark"></span>
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
                                        <input type="radio" value='1' name="with_vehicle_no" checked>
                                        <span class="checkmark"></span>
                                        Yes
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" name="with_vehicle_no" value='0'>
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
                                        <input type="radio" value='Eternity' name="app_use" checked>
                                        <span class="checkmark"></span>
                                        Eternity
                                    </label>
                                </div>
                                <div class="checkbox radio">
                                    <label class="check-label">
                                        <input type="radio" name="app_use" value='Shadow'>
                                        <span class="checkmark"></span>
                                        Shadow
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="form-row mb-0">
                            <span style="color: #000"><strong>Stationary:</strong></span>
                            <div class="check-box d-flex align-content-center ml-2" style="gap: 8px">
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
                        <button type="submit" id="location_savebtn" class="btn btn-primary btn-modal">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>