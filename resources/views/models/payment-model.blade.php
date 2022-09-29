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
        <input type="text" class="form-control" id="drs_num" name="drs_no" value="">
       <div class="form-row mb-0">
                        <div class="form-group col-md-8">
                            <label for="location_name">Purchase Price</label>
                            <input type="text" class="form-control" id="purchse" name="purchase_price" value="">
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
                <form id="payment_form">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <input type="hidden" class="form-control" id="drs_no" name="drs_no" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="hidden" class="form-control" id="vendor_no" name="vendor_no" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vendor</label>
                            <select class="form-control my-select2" id="vendor" name="vendor_name" tabindex="-1">
                                <option value="" selected disabled>Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Purchase Amount</label>
                            <input type="text" class="form-control" id="purchase_amount" name="claimed_amount" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none;">
                        <div class="form-group col-md-6">
                            <label for="location_name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Beneficiary Name</label>
                            <input type="text" class="form-control" id="beneficiary_name" name="beneficiary_name"
                                value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none;">
                        <div class="form-group col-md-6">
                            <label for="location_name">Account No</label>
                            <input type="text" class="form-control" id="bank_acc" name="acc_no" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Ifsc Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none;">
                        <div class="form-group col-md-6">
                            <label for="location_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0" style="display: none;">
                        <div class="form-group col-md-6">
                            <label for="location_name">Branch Name</label>
                            <input type="text" class="form-control" id="branch_name" name="branch_name" value="">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Type</label>
                            <select class="form-control my-select2" id="p_type" name="p_type" tabindex="-1">
                                <option selected disabled>Select</option>
                                <option value="Balance">Balance</option>
                                <option value="Advance">Advance</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Amount</label>
                            <input type="text" class="form-control" id="amt" name="payable_amount" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                            Discard</button>
                        <button type="submit" id="crt_pytm" class="btn btn-warning">Create Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

</div>