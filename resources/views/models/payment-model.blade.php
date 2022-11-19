
<!-- /////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="edit_amt" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">update Purchase Price</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form id="update_purchase_amt_form">
                    <input type="hidden" class="form-control" id="drs_num_edit" name="drs_no" value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Purchase Price</label>
                            <input type="text" class="form-control" id="purchse_edit" name="purchase_price" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle Type</label>
                            <select class="form-control my-select2" id="vehicle_type_edit" name="vehicle_type" >
                                @foreach($vehicletype as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="btn-section w-100 P-0">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a type="" class="btn btn-modal" data-dismiss="modal">Cancel</a>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- ----------------------------------------------------------------------  -->
<!-- /////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="show_drs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Confirm</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
           
            <table id="show_drs_table" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;"> 
                            <thead>
                                <tr>
                                    <th>Drs No</th> 
                                 
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table> 
        


            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- =========================================================================== -->
<!-- ----------------------------------------------------------------------  -->
<div class="modal fade bd-example-modal-xl" id="pymt_request_modal" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Create Payment</h5>
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
                <form id="payment_form">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-4">
                            <input type="hidden" class="form-control" id="drs_no_request" name="drs_no" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="hidden" class="form-control" id="vendor_no_request" name="vendor_no" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="hidden" class="form-control" id="transaction_id_2" name="transaction_id"
                                value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                    <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Branch Location</label>
                                    <select class="form-control" id="branch_id" name="branch_id"
                                        tabindex="-1">
                                        <?php $countbranch = count($branchs); 
                                        if($countbranch > 1){?>
                                          <option selected disabled>select location </option>
                                       <?php } ?>
                                        @foreach($branchs as $branch)
                                        <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vendor Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="" readonly>
                        </div>
                        <div class="form-group col-md-6"> 
                            <label for="exampleFormControlInput2">Purchase Amount</label>
                            <input type="text" class="form-control" id="total_clam_amt" name="claimed_amount" value=""
                                readonly>
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">

                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Beneficiary Name</label>
                            <input type="text" class="form-control" id="beneficiary_name" name="beneficiary_name"
                                value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Branch Name</label>
                            <input type="text" class="form-control" id="branch_name" name="branch_name" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">
                        <div class="form-group col-md-6">
                            <label for="location_name">Account No</label>
                            <input type="text" class="form-control" id="bank_acc" name="acc_no" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Ifsc Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">
                        <div class="form-group col-md-6">
                            <label for="location_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">
                    <div class="form-group col-md-6">
                            <label for="location_name">Pan</label>
                            <input type="text" class="form-control" id="pan" name="pan" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Type</label>
                            <select class="form-control" id="p_type" name="p_type" tabindex="-1"> 

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Amount</label>
                            <input type="text" class="form-control" id="amt" name="payable_amount" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Tds rate</label>
                            <input type="text" class="form-control" id="tds_rate" name="tds_rate" value="" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">tds deduct amount</label>
                            <input type="text" class="form-control" id="tds_dedut" name="final_payable_amount" value="" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                            Discard</button>
                        <!-- <button type="submit" id="crt_pytm" class="btn btn-warning">Create Payment</button> -->
                        <button type="submit" class="btn btn-primary" id="crt_pytm"><span class="indicator-label">Create Payment</span>
                        <span class="indicator-progress" style="display: none;">Please wait...
            	        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button> 
                </form>
            </div>
        </div>
    </div>
</div>

</div>
<!-- ================================================================================================== -->
<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="view_drs_lrmodel" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View LR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table id="view_drs_lrtable" class="table"
                        style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                        <thead>
                            <tr>
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
                                <td id="totallr"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td id="total_boxes"></td>
                                <td id="totalweights"></td>
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
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- =====================================================================================-->

<!-- /////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="add_amt" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Add Purchase Price</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form id="purchase_amt_form">
                    <input type="hidden" class="form-control" id="drs_num" name="drs_no" value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Purchase Price</label>
                            <input type="text" class="form-control" id="purchse" name="purchase_price" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle Type</label>
                            <select class="form-control my-select2" id="vehicle_type" name="vehicle_type" tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach($vehicletype as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="btn-section w-100 P-0">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a type="" class="btn btn-modal" data-dismiss="modal">Cancel</a>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- ----------------------------------------------------------------------  -->
<div class="modal fade bd-example-modal-xl" id="pymt_modal" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Create Payment</h5>
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
            <form id="create_request_form">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-3">
                            <input type="hidden" class="form-control" id="drs_no_1" name="drs_no" value="">
                        </div>
                        <div class="form-group col-md-3">
                            <input type="hidden" class="form-control" id="vendor_no_1" name="vendor_no" value="">
                        </div>
                        <div class="form-group col-md-3">
                            <input type="hidden" class="form-control" id="transaction_id_1" name="transaction_id"
                                value="">
                        </div>
                        <div class="form-group col-md-3">
                            <input type="hidden" class="form-control" id="name_1" name="v_name"
                                value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                    <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Branch Location</label>
                                    <select class="form-control" id="branch_id_1" name="branch_id"
                                        tabindex="-1">
                                        <?php $countbranch = count($branchs); 
                                        if($countbranch > 1){?>
                                          <option selected disabled>select location </option>
                                       <?php } ?>
                                        @foreach($branchs as $branch)
                                        <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                    </div>
                    <div class="form-row mb-0">
                    <div class="form-group col-md-6">
                            <label for="location_name">Vendor</label>
                            <select class="form-control my-select2" id="vendor_id_1" name="vendor_name" tabindex="-1">
                                <option value="" selected disabled>Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <?php
                                $bank_details = json_decode($vendor->bank_details, true);
                                
                                ?>
                                <option value="{{$vendor->id}}">{{$vendor->name}}-{{$bank_details['account_no']}}-{{$vendor->Branch->name ?? '-'}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Purchase Amount</label>
                            <input type="text" class="form-control" id="total_clam_amt_1" name="claimed_amount" value=""
                                readonly>
                        </div>
                    </div>
                    <div class="form-row mb-0"  style="display: none">

                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Beneficiary Name</label>
                            <input type="text" class="form-control" id="beneficiary_name_1" name="beneficiary_name"
                                value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Branch Name</label>
                            <input type="text" class="form-control" id="branch_name_1" name="branch_name" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">
                        <div class="form-group col-md-6">
                            <label for="location_name">Account No</label>
                            <input type="text" class="form-control" id="bank_acc_1" name="acc_no" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Ifsc Code</label>
                            <input type="text" class="form-control" id="ifsc_code_1" name="ifsc" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">
                        <div class="form-group col-md-6">
                            <label for="location_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name_1" name="bank_name" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Email</label>
                            <input type="text" class="form-control" id="email_1" name="email" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none">
                    <div class="form-group col-md-6">
                            <label for="location_name">Pan</label>
                            <input type="text" class="form-control" id="pan_1" name="pan" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Type</label>
                            <select class="form-control" id="p_type_1" name="p_type" tabindex="-1">

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Amount</label>
                            <input type="text" class="form-control" id="amt_1" name="pay_amt" value="">
                        </div>
                        
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Tds rate</label>
                            <input type="text" class="form-control" id="tds_rate_1" name="tds_rate" value="" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">tds deduct amount</label>
                            <input type="text" class="form-control" id="tds_dedut_1" name="final_payable_amount" value="" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                            Discard</button>
                        <!-- <button type="submit" id="crt_pytm" class="btn btn-warning">Create Payment</button> -->
                        <button type="submit" id="crt_pytm" class="btn btn-primary"><span class="indicator-label">Create Payment</span>
                        <span class="indicator-progress" style="display: none;">Please wait...
            	        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button> 
                </form>
            </div>
        </div>
    </div>
</div>

</div>