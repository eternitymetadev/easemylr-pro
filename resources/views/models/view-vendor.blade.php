<!-- -------------------Import Vendor Model---------------- -->
<div class="modal fade" id="imp_vendor_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="vendor_import">

                <div class="modal-header text-center">
                    <h4 class="modal-title">Import Vendors</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" class="form-control" id="drs_num" name="drs_no" value=""/>
                    <div class="form-row mb-0 flex-grow-1">
                        <div class="form-group">
                            <label for="location_name">Upload File</label>
                            <input type="file" style="width: 100%; margin: 1rem 0.5rem 0.5rem;" id="vendor_file"
                                   name="vendor_file" value=""/>
                        </div>
                    </div>

                    <div class="ignored" style="display:none;">
                        <h4 style="font-size: 0.825rem;">
                            Ignored Vendor <span class="text-danger">(Invalid IFSC)</span>
                        </h4>
                    </div>

                </div>

                <div class="p-3 d-flex justify-content-end align-items-center" style="gap: 1rem">
                    <a type="" class="btn btn-outline-primary btn-modal" data-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!---------- View Vendor Modal -------------------------->
<div class="modal fade bd-example-modal-xl" id="view_vendor" tabindex="-1" role="dialog"
     aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="name" style="font-weight: 700">Vendor Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">

                <div class="d-flex flex-wrap" style="gap: 1rem">
                    <div class="p-3 d-flex justify-content-between align-items-center flex-wrap"
                         style="flex: 1; min-width: 300px">

                        <div class="d-flex flex-wrap justify-content-between align-items-center" style="width: 100%">
                            <p style="flex: 1; min-width: 43%">ID: <span id="vendor_id"></span></p>
                            <p style="min-width: max-content">Type: <span id="vendor_type"></span></p>
                        </div>

                        <p style="min-width: 43%">Transporter Name: <span id="trans_name"></span></p>
                        <p style="min-width: 43%">Driver Name: <span id="driver_nm"></span></p>

                        <p style="width: 100%">Declaration Available: <span id="decl_avl"></span></p>

                        <h5 class="vdTitle mt-3">Tax Details:</h5>
                        <p style="flex: 1; min-width: 27%">PAN: <span id="pan"></span></p>
                        <p style="flex: 1; min-width: 27%">GST: <span id="gst_no"></span></p>
                        <p style="min-width: max-content">TDS Rate: <span id="tds_rate"></span></p>

                        <h5 class="vdTitle mt-3">Contact Details:</h5>
                        <p style="min-width: 43%">Email: <span id="cont_email"></span></p>
                        <p style="min-width: 43%">Mobile: <span id="cont_num"></span></p>
                    </div>

                    <div class="vendorBankDetails col-md-4 p-3">
                        <h5 class="vdTitle">Bank Details</h5>
                        <p class="between">Account Holder: <span id="acc_holder"></span></p>
                        <p class="between">Bank Name: <span id="bank_name"></span></p>
                        <p class="between">Account No: <span id="acc_no"></span></p>
                        <p class="between">IFSC code: <span id="ifsc_code"></span></p>
                        <p class="between">Branch Name: <span id="branch_name"></span></p>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard
                </button>

            </div>
        </div>
    </div>
</div>
