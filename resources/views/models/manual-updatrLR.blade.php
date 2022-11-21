<div class="modal fade bd-example-modal-xl" id="manualLR" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-xl">
     <div class="modal-content">
       <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
       <!-- Modal Header -->
       <div class="modal-header text-center">
        	<h4 class="modal-title">Update Delivery Status</h4>
       </div>
       <!-- Modal body -->
       <div class="modal-body">
       <!-- <div class="form-row mb-0">
                        <div class="form-group col-md-12">
                            <label for="location_name">Status</label>

                            <select class="form-control" id="lr_status" name="lr_status" onchange="lrCheck(this);" tabindex="-1" >


                            </select>
                        </div>
                        </div> -->
          <div class="Delt-content text-center">
            <!-- <img src="/assets/images/sucess.png" class="img-fluid mb-2">  -->
             <!-- <p class="confirmtext">Are You Sure You Want To Cancel It ?</p> -->
          </div>
          <div class="" id="lrid">
                        <table id="get-delvery-dateLR" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>LR No</th>
                                    <th>Consignee</th>
                                    <th>City</th>
                                    <th>Delivery Date</th>
                                    <th>Image</th>
                             <?php $authuser = Auth::user(); 
                                if($authuser->role_id != 7){?>
                                    <th>update</th>
                                    <?php } ?>
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
               <!-- <a class="btn-cstm btn-danger btn btn-modal delete-btn-modal commonconfirmclick">Ok</a>  -->
               <a id="close_get_delivery_dateLR" type="" class="btn btn-modal" data-dismiss="modal">Close</a>
           </div>
       </div>
     </div>
   </div>
</div>
