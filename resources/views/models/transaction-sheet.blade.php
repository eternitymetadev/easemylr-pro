<div class="modal fade" id="drs_commonconfirm" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
     <div class="modal-content">
       <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
       <!-- Modal Header -->
       <div class="modal-header text-center">
        	<h4 class="modal-title">Confirm</h4>
       </div>
       <!-- Modal body -->
       <div class="modal-body">
          <div class="Delt-content text-center">
            <!-- <img src="/assets/images/sucess.png" class="img-fluid mb-2">  -->
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
<div class="modal fade bd-example-modal-xl" id="commonconfirm" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myExtraLargeModalLabel">
   <div class="modal-dialog modal-xl">
     <div class="modal-content">
       <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
       <!-- Modal Header -->
       <div class="modal-header text-center">
        	<h4 class="modal-title">Update DRS Status</h4>
       </div>
       <!-- Modal body -->
       <div class="modal-body">
       <div class="form-row mb-0">
                        <div class="form-group col-md-12">
                            <label for="location_name">Status</label>

                            <select class="form-control" id="drs_status" name="drs_status" onchange="catagoriesCheck(this);" tabindex="-1" >
                                <option value="">Select status</option>
                                <option value="Started">Started</option>
                                <option value="Successful">Successful</option>

                               
                            </select>
                        </div>
                        </div>
          <div class="Delt-content text-center">
            <!-- <img src="/assets/images/sucess.png" class="img-fluid mb-2">  -->
             <!-- <p class="confirmtext">Are You Sure You Want To Cancel It ?</p> -->
          </div>
          <div class="table-responsive" id="opi" style="display: none;">
                        <table id="get-delvery-date" class="table table-hover"
                            style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>LR No</th> 
                                    <th>Consignee</th>
                                    <th>City</th>
                                    <th>Delivery Date</th>
                                    <th>Action</th>
                                    <th>Image</th>
                                 
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
               <a class="btn-cstm btn-danger btn btn-modal delete-btn-modal commonconfirmclick">Ok</a> 
               <a type="" class="btn btn-modal" data-dismiss="modal">Cancel</a><span style="margin-left: 200px;">Click 'OK' to update delivery status</span>
           </div>
       </div>
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
                <form id="update_delivery_status" >
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
                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
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
<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="save-draft" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Save Draft</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </div>
            <div class="modal-body">
                <form id="updt_vehicle" method="post"> 

                    <input type="hidden" class="form-control" id="transaction_id" name="transaction_id" value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle No.</label>

                            <select class="form-control my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                                <option value="">Select vehicle no</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Vehicle Type</label>
                            <select class="form-control my-select2" id="vehicle_type" name="vehicle_type" tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach($vehicletypes as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Driver Name</label>


                            <select class="form-control my-select2" id="driver_id" name="driver_id" tabindex="-1">
                                <option value="">Select driver</option>
                                @foreach($drivers as $driver)
                                <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                    '-'}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Transporter Name</label>


                            <input type="text" class="form-control" id="Transporter" name="transporter_name" value="">

                        </div>

                    </div>
                    <div class="table-responsive">
                        <table id="save-DraftSheet" class="table table-hover"
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
                                    <th>EDD</th>
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
                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                <button type="submit" class="btn btn-primary"><span class="indicator-label">Save</span>
                 <span class="indicator-progress" style="display: none;">Please wait...
            	    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button> 
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
                <!-- <button type="button" id="print" class="btn btn-primary commonButton">
                    <i class="fas fa-save"></i>&nbsp;Print
                </button> -->
                <div id="www">
                    <div class="row">
                        <div class="col-sm-12">

                            <div>

                            </div>



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
                                            <th>Number Of Boxes</th>
                                            <th>Net Weight</th>

                                        </tr>
                                    </thead>
                                    <tbody id="suffle">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>Total</td>
                                            <td id="total"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td id="total_box"></td>
                                            <td id="totalweight"></td>
                                        </tr>

                                    </tfoot>

                                </table>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- <table>
                                        <tr>
                                            <td width: 131px;>
                                                <label>Total :</label>
                                            </td>
                                            <td width: 131px;>
                                                <label id="total"></label>
                                            </td>
                                        </tr>
                                    </table>  -->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--===========================Delevery Status ========================================== -->