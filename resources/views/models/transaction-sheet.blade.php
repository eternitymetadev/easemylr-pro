<!-- #modal 2 -->
<div class="modal fade bd-example-modal-xl" id="modal-2" tabindex="-1" role="dialog"
     aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Invoices</h4>
                <a href="#save-draft" style="font-weight: 800" data-toggle="modal" data-dismiss="modal">< Back</a>
            </div>
            <div class="modal-body">
                <form id="all_inv_save">
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

                    <div class="modal-footer">
                        <button style="font-size: 14px; width: 100px" type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button style="font-size: 14px; min-width: 100px" type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


{{--cancel DRS Dialog--}}
<div class="modal fade" id="drs_commonconfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-body">
                <div class="Delt-content text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-alert-circle deleteAlertIcon">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h5 class="my-2">Cancel DRS</h5>
                    <span>Are you sure to cancel this DRS?</span>
                </div>
            </div>

            <div class="d-flex justify-content-end align-content-center mt-4" style="gap: 1rem;">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                <button type="submit" id="location_savebtn"
                        class="btn btn-cstm btn-danger btn-modal delete-btn-modal commonconfirmclick">Yeah! Sure
                </button>
            </div>
        </div>
    </div>
</div>

{{--update DRS status--}}
<div class="modal fade bd-example-modal-xl" id="commonconfirm" tabindex="-1" role="dialog" aria-hidden="true"
     aria-labelledby="myExtraLargeModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Update DRS Status</h4>
            </div>
            <!-- Modal body -->
            <form id="allsave">
                <div class="modal-body">
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
                <div class="modal-footer col-md-12 d-flex justify-content-end align-items-center"
                     style="gap: 1rem; margin-top: 3rem;">
                    <button type="button" style="width: 80px; font-size: 14px" class="btn btn-outline-primary"
                            data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" style="width: 80px; font-size: 14px"
                            class="btn btn-primary btn-modal delete-btn-modal allsave">
                        <span class="indicator-label">Update</span>
                        <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                    </button>
                </div>

                {{--                <div class="modal-footer">--}}
                {{--                    <div class="btn-section w-100 P-0">--}}
                {{--                        <button type="submit" class="btn-danger btn btn-modal delete-btn-modal allsave">--}}
                {{--                            <span class="indicator-label">Update</span>--}}
                {{--                            <span class="indicator-progress" style="display: none;">Please wait...--}}
                {{--                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>--}}
                {{--                            </span>--}}
                {{--                        </button>--}}
                {{--                        <a type="" class="btn btn-modal btn-warning" data-dismiss="modal">Cancel</a>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
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

                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                        <button type="submit" class="btn btn-primary">Update Delivery Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- xtra Large modal -->
<!-- ================================================================================================== -->
<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="save-draft" tabindex="-1" role="dialog"
     aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Save Draft</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="updt_vehicle" method="post">

                    <input type="hidden" class="form-control" id="transaction_id" name="transaction_id" value="">
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
                        <table id="save-DraftSheet" class="table table-hover"
                               style="width: 100%;text-align: left; border-radius: 12px; overflow: hidden; box-shadow: 0 0 2px #83838370 inset;">
                            <thead>
                            <tr>
                                <th>LR</th>
                                <th>Consignee Name</th>
                                <th>Location</th>
                                <th>Boxes</th>
                                <th>Net Weight</th>
                                <th>EDD</th>
                                <th>E-Way</th>

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

<div class="modal fade bd-example-modal-xl" id="opm" tabindex="-1" role="dialog"
     aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Delivery Run Sheet</h5>
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
                <div id="www">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="sheet" class="table table-hover"
                                       style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                    <thead>
                                    <tr>
                                        <th>LR No</th>
                                        <th>Consignment Date</th>
                                        <th>Consignee Name</th>
                                        <th>city</th>
                                        <th>Pin Code</th>
                                        <th style='text-align: right'>Boxes</th>
                                        <th style='text-align: right'>Net Weight</th>
                                        <th style='text-align: center'>Action</th>

                                    </tr>
                                    </thead>
                                    <tbody id="suffle">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" style="font-weight: 800" id="total"></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" style="font-weight: 800; text-align: right" id="total_box"></td>
                                        <td colspan="2" style="font-weight: 800" id="totalweight"></td>
                                    </tr>

                                    </tfoot>

                                </table>

                                <button type="button" class="btn btn-primary" id="addlr"
                                        style=" float: right; font-size: 12px;">
                                    Add LR
                                </button>

                                <div style="display: none; width: 100%;" id="unverifiedlist">
                                    <input type="hidden" class="form-control" id="current_drs" name="" value="">
                                    <button type="button" class="mb-2 btn btn-warning disableDrs" id="add_unverified_lr"
                                            style="font-size: 11px;">
                                        Create DSR
                                    </button>
                                    <table id="unverifiedlrlist" class="table table-hover"
                                           style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                        <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" name="" id="ckbCheckAll"
                                                       style="width: 16px; height:16px;">
                                            </th>
                                            <th>LR No</th>
                                            <th>Consignment Date</th>
                                            <th>Consigner Name</th>
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
</div>
<!--===========================Delevery Status ========================================== -->
