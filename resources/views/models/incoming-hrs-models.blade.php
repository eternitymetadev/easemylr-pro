<!-- =====================================================================================-->

<div class="modal fade bd-example-modal-xl" id="receive_hrs_model" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Hub Run Sheet</h5>
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
                <form id="save_receving_details">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" class="form-control" name="lr_no" id="lr_no" />
                            <div class="table-responsive">
                                <table id="receving_hrs_details" class="table receving_hrs_details"
                                    style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                    <thead>
                                        <tr>
                                            <th>Hrs No</th>
                                            <th>No. of LRs</th>
                                            <th>Boxes</th>
                                            <th>Receive Box</th>
                                            <th style='text-align: right'>Remarks</th>
                                            <th style='text-align: right'>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<!-- =====================================================================================-->

<div class="modal fade bd-example-modal-xl" id="view_lr_in_hrs" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Hub Run Sheet</h5>
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="hrs_details_table" class="table hrs_details_table"
                                style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                <thead>
                                    <tr>
                                        <th>LR No</th>
                                        <th>Consignee Name</th>
                                        <th>city</th>
                                        <th>Pin Code</th>
                                        <th style='text-align: right'>Boxes</th>
                                        <th style='text-align: right'>Net Weight</th>

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