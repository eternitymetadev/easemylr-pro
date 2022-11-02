<!-- The Modal Change status-->
<div class="modal" id="search-paymentvehicle">
   <div class="modal-dialog">
      <div class="modal-content">
         <button type="button" class="close" data-dismiss="modal">X</button>
         <!-- Modal Header -->

         <div class="modal-header text-center">
            <h4 class="modal-title">Search Assigned To</h4>
         </div>
         <!-- Modal body -->
         <div class="modal-body searchbody_n">
          <div class="form-group pt-2"> 
            <select name="vehicle[]" id="searchvehicle" class="form-control" multiple> 
              <option value="0">Select</option>
              <?php 

              foreach ($vehicles as $key => $vehicle) {
               ?>
                <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}</option>
              <?php } ?>
            </select>
            <div class="text-right close-c"><i class="fa fa-times"></i></div>
          </div>
         </div>
         <!-- Modal footer -->
         <div class="modal-footer">
    <!-- <div class="btn-section w-100 P-0">
               <a class="btn-cstm btn-white btn btn-modal searchassignvehicle" data-action="<?php echo url()->current(); ?>">search</a>
               <a class="btn btn-modal" data-dismiss="modal">Cancel</a>
            </div> -->
         </div>
      </div>
   </div>
</div>
<!--End The Modal Change status-->