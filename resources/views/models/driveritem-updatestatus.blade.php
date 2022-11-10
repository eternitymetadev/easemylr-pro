<style>
.dialogInput {
    border-radius: 6px;
    border: 1px solid #83838370;
}
.dialogInput:focus {
    border-radius: 6px;
    border: 1px solid #838383;
}
</style>

<div class="modal fade" id="taskitem-status" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="max-width: min(90%, 1180px);">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Receive Vehicle</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div>
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 4px;">
                        <thead>
                            <tr>
                                <th width="200px">Consignor name</th>
                                <th width="150px">Number of Invoices</th>
                                <th width="150px">Quantity (in Pieces)</th>
                                <th width="150px">Received quantity</th>
                                <th width="150px">Less Received (Auto)</th>
                                <th style="flex: 1;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input class="dialogInput" style="width: 170px;" type="text" name=""></td>
                                <td><input class="dialogInput" style="width: 120px;" type="text" name=""></td>
                                <td><input class="dialogInput" style="width: 120px;" type="text" name=""></td>
                                <td><input class="dialogInput" style="width: 120px;" type="text" name=""></td>
                                <td><input class="dialogInput" style="width: 120px;" type="text" name=""></td>
                                <td><input class="dialogInput" style="width: 100%;" type="text" name=""></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="btn-section w-100 P-0 d-flex justify-content-end align-items-center" style="gap:8px">
                    <a type="" class="btn btn-outline-primary btn-modal" data-dismiss="modal">Close</a>
                    <a class="btn-cstm btn-primary btn btn-modal delete-btn-modal statusconfirmclick">Save</a>
                </div>
            </div>
        </div>
    </div>
</div>