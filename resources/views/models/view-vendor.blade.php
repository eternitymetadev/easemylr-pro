<!-- -------------------Import Vendor Model---------------- -->
<div class="modal fade" id="imp_vendor_modal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
     <div class="modal-content">
       <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
       <!-- Modal Header -->
       <div class="modal-header text-center">
        	<h4 class="modal-title">Add Purchase Price</h4>
       </div>
       <!-- Modal body -->
       <div class="modal-body">
        <form id="vendor_import">
        <input type="hidden" class="form-control" id="drs_num" name="drs_no" value="">
       <div class="form-row mb-0">
                        <div class="form-group col-md-8">
                            <label for="location_name">Upload File</label>
                            <input type="file" class="form-control" id="vendor_file" name="vendor_file" value="">
                        </div>
                    </div>

                    <div class="ignored" style="display:none;">
            <h4>Ignored Vendor, These Vendor Ifsc code is less than 11 digit</h4>
             </div>
          
       </div>
     
       <!-- Modal footer -->
       <div class="modal-footer">
           <div class="btn-section w-100 P-0">
            <button type="submit" class="btn btn-warning">Import</button>
               <a type="" class="btn btn-modal" data-dismiss="modal">Cancel</a>
           </div>
       </div>
</form>
     </div>
   </div>
</div>
<!---------- View Vendor Modal -------------------------->
<div class="modal fade bd-example-modal-xl" id="view_vendor" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </div>
            <div class="modal-body">
                <div class="statbox widget box box-shadow">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row">Vendor Name</th>
                                <td id="name">
                                </td>
                                <th scope="row">Transporter Name</th>
                                <td id="trans_name">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Driver Name</th>
                                <td id="driver_nm">

                                </td>
                                <th scope="row">Contact Number</th>
                                <td id="cont_num">

                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Contact Email</th>
                                <td id="cont_email">
                                </td>
                                <th scope="row">Account Holder Name</th>
                                <td id="acc_holder">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Account No</th>
                                <td id="acc_no">
                                </td>
                                <th scope="row">Ifsc Code</th>
                                <td id="ifsc_code">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Bank Name</th>
                                <td id="bank_name">
                                </td>
                                <th scope="row">Branch Name</th>
                                <td id="branch_name">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Pan</th>
                                <td id="pan">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">Vendor Type</th>
                                <td id="vendor_type">
                                </td>
                                <th scope="row">Declaration Available</th>
                                <td id="decl_avl">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">TDS Rate applicacle</th>
                                <td id="tds_rate">
                                </td>
                                <th scope="row">Branch Location</th>
                                <td id="branch_id">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">GST</th>
                                <td id="gst">
                                </td>
                                <th scope="row">Gst No</th>
                                <td id="gst_no">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


            </div>

            <div class="modal-footer">
                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>

            </div>
        </div>
    </div>
</div>