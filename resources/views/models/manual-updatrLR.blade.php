<div class="modal fade bd-example-modal-xl" id="manualLR" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Update Delivery Status</h4>
                <span id="close_get_delivery_dateLR" data-dismiss="modal" style="cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-x-circle">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </span>
            </div>
            <!-- Modal body -->
            <div class="modal-body">

                <div class="Delt-content text-center">
                </div>
                <div class="" id="lrid">
                    <table id="get-delvery-dateLR" class="table table-hover"
                           style="width:100%; text-align:left; border-radius: 12px; box-shadow: #8383834a -1px 4px 16px -4px; overflow: hidden;">
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
        </div>
    </div>
</div>
