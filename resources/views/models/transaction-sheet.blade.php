<!-- /////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="drs_commonconfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Confirm</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="Delt-content text-center">
                    <p class="confirmtext">Are You Sure You Want To Cancel It ?</p>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="btn-section w-100 P-0">
                    <a class="btn-cstm btn-danger btn btn-modal delete-btn-modal commonconfirmclick">Yes</a>
                    <a type="" class="btn btn-modal" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- =========================================================================== -->
<div class="modal fade bd-example-modal-xl" id="commonconfirm" tabindex="-1" role="dialog" aria-hidden="true"
    aria-labelledby="myExtraLargeModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Update DRS Status</h4>
            </div>
            <!-- Modal body -->
            <form id="allsave">
                <div class="modal-body">
                    <div class="Delt-content text-center">
                    </div>
                    <div class="table-responsive" id="opi">
                        <table id="get-delvery-date" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>LR No</th>
                                    <th>Consignee</th>
                                    <th>City</th>
                                    <th>EDD</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Upload Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <div class="btn-section w-100 P-0">
                        <button type="submit" class="btn-danger btn btn-modal delete-btn-modal allsave">
                            <span class="indicator-label">Update</span>
                            <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <a type="" class="btn btn-modal btn-warning" data-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="delivery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Delivery Status</h5>
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
                <form id="update_delivery_status">
                    <input type="text" name="consignment_no" id="drs_status">
                    <div class="table-responsive">
                        <table id="delivery_status" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>LR No</th>
                                    <th>Delivery Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>

            </div>
            <div class="modal-footer">
                <button class="btn discardButton" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                    Discard</button>
                <button type="submit" class="btn btn-primary">Update Delivery Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>
<!-- xtra Large modal -->
<!-- ================================================================================================== -->

<!-- ===============================Delivery Run Sheet======================================================-->

<div class="modal fade bd-example-modal-xl" id="opm" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <form id="updt_vehicle" method="post" class="modal-content" style="min-height: calc(100vh - 3.5rem)">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Delivery Run Sheet</h5>
            </div>
            <div class="modal-body">
                <div id="www">
                    <div class="row">
                        <div class="col-sm-12">

                            <div id="taskAppendDiv">
                                <input type="hidden" class="form-control" id="transaction_id" name="transaction_id"
                                    value="" />
                                <div class="form-row mb-0">
                                    <div class="form-group col-md-6">
                                        <label for="location_name">Vehicle Nos.</label>

                                        <select class="form-control my-select2" id="vehicle_no" name="vehicle_id"
                                            tabindex="-1">
                                            <option value="">Select vehicle</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->regn_no }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="exampleFormControlInput2">Driver Name</label>
                                        <select class="form-control my-select2" id="driver_id" name="driver_id"
                                            tabindex="-1">
                                            <option value="">Select driver</option>
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}">
                                                    {{ ucfirst($driver->name) ?? '-' }}-{{ $driver->phone ?? '-' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="form-row mb-0">
                                    <div class="form-group col-md-4">
                                        <label for="exampleFormControlInput2">Vehicle Type</label>
                                        <select class="form-control my-select2" id="vehicle_type" name="vehicle_type"
                                            tabindex="-1">
                                            <option value="">Select vehicle type</option>
                                            @foreach ($vehicletypes as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="exampleFormControlInput2">Transporter Name</label>
                                        <input type="text" class="form-control" id="Transporter"
                                            name="transporter_name" value="">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="exampleFormControlInput2">Purchase Price</label>
                                        <input type="text" class="form-control" id="draft_purchase"
                                            name="purchase_price" value="">
                                    </div>
                                </div>

                                <div class="table-responsive tableContainer">
                                    <table id="sheet" class="table table-hover"
                                        style="width:100%; text-align:left;">
                                        <thead>
                                            <tr>
                                                <th>E-Way</th>
                                                <th>EDD</th>
                                                <th>LR No</th>
                                                <th>Consignment Date</th>
                                                <th>Consignee Name</th>
                                                <th>city</th>
                                                <th>Pin Code</th>
                                                <th>Number Of Boxes</th>
                                                <th>Net Weight</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody id="suffle">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <div class="d-flex align-items-center" style="gap: 1rem">
                                                        <span style="font-weight: bold">
                                                            Total: <span id="total"></span>
                                                        </span>
                                                        |
                                                        <span id="total_box" style="font-weight: bold"></span>
                                                        |
                                                        <span id="totalweight" style="font-weight: bold"></span>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tfoot>

                                    </table>
                                </div>

                                <div class="d-flex align-items-center justify-content-start mt-3" style="gap: 1rem">
                                    <a id="addlr" class="btn btn-outline-primary ">
                                        Add LR
                                    </a>
                                </div>

                                <div id="addLrDiv" class="table-responsive tableContainer my-3"
                                    style="max-height: 400px; overflow: auto;"></div>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <div class="col-12 d-flex align-items-center justify-content-between" style="gap: 1rem">
                    <button type="button" class="btn submitButton delete-btn-modal" id="discarddraftForm" style="color: #333 !important; border: 1px solid; font-weight: 600;">
                        <span class="indicator-label">Discard & Close</span>
                    </button>

                    <div class="d-flex align-items-center justify-content-end" style="gap: 1rem; flex: 1;">

                        <button type="button" onclick="drsSubmit(0)"
                            class="btn btn-outline-primary submitButton  delete-btn-modal allsave">
                            <span class="indicator-label">Save as Draft</span>
                            <span class="indicator-progress" style="display: none;">Submitting...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>

                        <button type="button" onclick="drsSubmit(1)"
                            class="btn btn-primary submitButton delete-btn-modal allsave">
                            <span class="indicator-label">Start</span>
                            <span class="indicator-progress" style="display: none;">Submitting...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>

                    </div>


                </div>
            </div>
        </form>
    </div>
</div>
</div>
<!--===========================Delevery Status ========================================== -->

<!--Draft Modal -->
<div class="modal fade bd-example-modal-xl" id="save-draft" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="min-height: calc(100vh - 3.5rem)">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Save Draft</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </div>
            <div class="modal-body">
                <form id="updt_vehicle" method="post">
                    <input type="hidden" class="form-control" id="transaction_id" name="transaction_id"
                        value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle No.</label>

                            <select class="form-control my-select2" id="vehicle_no" name="vehicle_id"
                                tabindex="-1">
                                <option value="">Select vehicle no</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->regn_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Driver Name</label>
                            <select class="form-control my-select2" id="driver_id" name="driver_id" tabindex="-1">
                                <option value="">Select driver</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">
                                        {{ ucfirst($driver->name) ?? '-' }}-{{ $driver->phone ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="form-row mb-0">
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Vehicle Type</label>
                            <select class="form-control my-select2" id="vehicle_type" name="vehicle_type"
                                tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach ($vehicletypes as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Transporter Name</label>
                            <input type="text" class="form-control" id="Transporter" name="transporter_name"
                                value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Purchase Price</label>
                            <input type="text" class="form-control" id="draft_purchase" name="purchase_price"
                                value="">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="save-DraftSheet" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>E-Way</th>
                                    <th>EDD</th>
                                    <th>LR No</th>
                                    <th>Consignment Date</th>
                                    <th>Consignee Name</th>
                                    <th>city</th>
                                    <th>Pin Code</th>
                                    <th>Number Of Boxes</th>
                                    <th>Net Weight</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9">
                                        <div class="d-flex align-items-center" style="gap: 1rem">
                                            <span style="font-weight: bold">
                                                Total: <span id="total"></span>
                                            </span>
                                            |
                                            <span id="total_box" style="font-weight: bold"></span>
                                            |
                                            <span id="totalweight" style="font-weight: bold"></span>
                                        </div>
                                    </td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>

            </div>

            <div class="modal-footer">
                <button class="btn bg-danger discardButton" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                    Discard</button>
                <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                <button type="submit" class="btn btn-primary"><span class="indicator-label">Update</span>
                    <span class="indicator-progress" style="display: none;">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Start Modal -->
<div class="modal fade bd-example-modal-xl" id="start-draft" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="min-height: calc(100vh - 3.5rem)">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Draft</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </div>
            <div class="modal-body" style="pointer-events:none;">
                <form id="startupdt_vehicle" method="post">

                    <input type="hidden" class="form-control" id="starttrn_id" name="transaction_id"
                        value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle No.</label>

                            <select class="form-control my-select2" id="start-vehicle" name="vehicle_id"
                                tabindex="-1">
                                <option value="">Select vehicle no</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->regn_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Driver Name</label>
                            <select class="form-control my-select2" id="start-driver" name="driver_id"
                                tabindex="-1">
                                <option value="">Select driver</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">
                                        {{ ucfirst($driver->name) ?? '-' }}-{{ $driver->phone ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="form-row mb-0">
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Vehicle Type</label>
                            <select class="form-control my-select2" id="start-vehicletype" name="vehicle_type"
                                tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach ($vehicletypes as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Transporter Name</label>
                            <input type="text" class="form-control" id="start-transporter"
                                name="transporter_name" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Purchase Price</label>
                            <input type="text" class="form-control" id="start-purchase" name="purchase_price"
                                value="">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="start-DraftSheet" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>E-Way</th>
                                    <th>EDD</th>
                                    <th>LR No</th>
                                    <th>Consignment Date</th>
                                    <th>Consignee Name</th>
                                    <th>city</th>
                                    <th>Pin Code</th>
                                    <th>Number Of Boxes</th>
                                    <th>Net Weight</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td id="start-totallr"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td id="start-totalboxes"></td>
                                    <td id="start-totalweights"></td>
                                </tr>

                            </tfoot>
                        </table>
                        <div class="row">
                            <div class="col-sm-12">

                            </div>
                        </div>

                    </div>

            </div>

            <div class="modal-footer">
                <button class="btn bg-danger discardButton" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                    Discard</button>
                <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                <button type="submit" class="btn btn-primary"><span class="indicator-label">Start</span>
                    <span class="indicator-progress" style="display: none;">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- model for re-attempt -->
<div class="modal fade" id="reAttemptModel" tabindex="-1" role="dialog" aria-labelledby="reAttemptModelLabel"
    data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background: #fdecbe;border: 2px solid #e2a03f;margin-top: 5rem;">
            <div class="modal-header">
                <h5 class="modal-title" id="reAttemptModelLabel">Re-Attempt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="reattempt-reason" class="d-flex flex-column align-items-center" style="min-height: 150px;"
                    method="POST" action="{{ url($prefix . '/transaction-sheet/create-reattempt') }}">
                    @csrf
                    <input type="hidden" name="lr_id" id="reattempt_lrid" value="" />
                    <div class="form-group" style="width: 90%">
                        <label for="reason">Reason Select</label>
                        <select class="form-control form-control-sm" id="reason" name="reattempt_reason"
                            style="border-radius: 12px;" required>
                            <option value="">--Select Reason--</option>
                            <option value="Customer shop closed">Customer shop closed</option>
                            <option value="Material refused by customer">Material refused by customer</option>
                            <option value="Material Damaged">Material Damaged</option>
                            <option value="Material Loss">Material Loss</option>
                            <option value="Material Short">Material Short</option>
                            <option value="Wrong Invoice Address">Wrong Invoice Address</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div id="otherInput" style="display: none; width: 90%">
                        <input class="form-control form-control-sm" style="border-radius: 12px;" type="text"
                            id="otherText" placeholder="Enter other option">
                    </div>
                    <button type="submit" class="btn btn-primary"
                        style="font-size: 14px;max-width: 180px;margin-top: 1rem;">Confirm
                        Re-Attempt</button>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- #modal 2 -->
<div class="modal fade bd-example-modal-xl" id="modal-2" tabindex="-1" role="dialog"
    style="backdrop-filter: blur(2px); background: #00000030;" aria-labelledby="myExtraLargeModalLabel"
    data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 1080px;">
        <form id="all_inv_save" class="modal-content"
            style="background: #fdecbe;border: 2px solid #e2a03f;margin-top: 5rem;">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->
                <h4 class="modal-title">Update Invoices</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="cn_no" id="cn_no" value="">
                <table id="view_invoices" class="table table-hover"
                    style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                    <thead>
                        <tr>
                            <th>LR No</th>
                            <th>Invoice no</th>
                            <th>e-way bill no</th>
                            <th>e-way bill Date</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- start confirm modal --}}
<div class="modal fade" id="start-commonconfirm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #fdecbe;border: 2px solid #e2a03f;margin-top: 5rem;">
            <!-- Modal Header -->
            <!-- Modal body -->
            <div class="modal-body d-flex align-items-end justify-content-center" style="min-height: 150px;">
                <div class="Delt-content text-center">
                    <h4 style="margin-bottom: 22px; font-size: 22px; font-weight: 600; ">Are you sure to start this task?</h4>
                    <p style="color: #080808; max-width: 350px;" >
                        Please note: Once you start it, you will not be able to make any changes in this DRS.
                    </p>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="col-12 d-flex align-items-center justify-content-end" style="gap: 1rem">
                    <button type="button" class="btn btn-outline-primary submitButton" data-dismiss="modal">
                        <span class="indicator-label">Cancel</span>
                    </button>
                    <button type="button" class="btn btn-primary submitButton confirmStartClick">
                        <span class="indicator-label">Yes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- end start confirm modal --}}
