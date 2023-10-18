<!-- ================================================================================================== -->
<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="save_hrs_details_model" tabindex="-1" role="dialog"
     aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Save Draft</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="updt_hrs_details" method="post">

                    <input type="hidden" class="form-control" id="transaction_id" name="lr_id" value="">
                    <input type="hidden" class="form-control" id="hrs_id" name="hrs_id" value="">
                    <div class="form-row mb-4">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle No.</label>
                            <select class="form-control form-control-sm my-select2" id="vehicle_no" name="vehicle_id"
                                    tabindex="-1">
                                <option value="">Select vehicle no</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Driver Name</label>
                            <select class="form-control form-control-sm my-select2" id="driver_id" name="driver_id"
                                    tabindex="-1">
                                <option value="">Select driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                    '-'}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Vehicle Type</label>
                            <select class="form-control form-control-sm my-select2" id="vehicle_type"
                                    name="vehicle_type" tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach($vehicletypes as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Transporter Name</label>
                            <input type="text" class="form-control form-control-sm" id="Transporter"
                                   name="transporter_name" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Purchase Price</label>
                            <input type="text" class="form-control form-control-sm" name="purchase_price" value="">
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table id="save-HrsDraftSheet" class="table table-hover"
                               style="width: 100%;text-align: left; border-radius: 12px; overflow: hidden; box-shadow: 0 0 2px #83838370 inset;">
                            <thead>
                            <tr>
                                <th>LR</th>
                                <th>Consignee Name</th>
                                <th>Location</th>
                                <th>Boxes</th>
                                <th>Net Weight</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <p style="font-size: 1rem; margin-top: 1rem;font-weight: 800; border-bottom: 1px solid; text-align: right">Total</p>
                    <div class="d-flex align-items-center justify-content-end" style="gap: 1rem;">
                        <p style="font-weight: 800" id="totallr"></p>
                        |
                        <p style="font-weight: 800" id="total_boxes"></p>
                        |
                        <p style="font-weight: 800" id="totalweights"></p>
                    </div>


                    <div class="d-flex justify-content-end align-items-center py-3 mt-4" style="gap: 1rem">
                        <button style="font-size: 14px; width: 80px" class="btn btn-outline-primary" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                        <button style="font-size: 14px; width: 80px" type="submit" class="btn btn-primary"><span class="indicator-label">Save</span>
                            <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"/>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- =====================================================================================-->

<div class="modal fade bd-example-modal-xl" id="draft_hrs" tabindex="-1" role="dialog"
     aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Hub Run Sheet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="hrs_details_table" class="table hrs_details_table"
                                       style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                    <thead>
                                    <tr>
                                        <th>LR No</th>
                                        <th>Consignee Name</th>
                                        <th>city</th>
                                        <th>Pin Code</th>
                                        <th style='text-align: right'>Boxes</th>
                                        <th style='text-align: right'>Net Weight</th>
                                        <th style='text-align: center'>Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                                <button type="button" class="btn btn-primary" id="addlr_in_hrs"
                                        style=" float: right; font-size: 12px;">
                                    Add LR
                                </button>

                                <div style="display: none; width: 100%;" id="unverifiedlist">
                                    <input type="hidden" class="form-control" id="current_hrs" name="" value="">
                                    <button type="button" class="mb-2 btn btn-warning disableDrs" id="add_unverified_lr_hrs"
                                            style="font-size: 11px;">
                                        Add Hrs
                                    </button>
                                    <table id="unverifiedlrlist_hrs" class="table table-hover"
                                           style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                        <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" name="" id="ckbCheckAll"
                                                       style="width: 16px; height:16px;">
                                            </th>
                                            <th>LR No</th>
                                            <th>Consignor Name</th>
                                            <th>Consignee</th>
                                            <th>Location</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                
            </div>

        </div>
    </div>
</div>