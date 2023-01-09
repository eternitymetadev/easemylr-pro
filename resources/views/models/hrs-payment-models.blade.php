<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="view_hrs_lrmodel" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View LR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table id="view_hrs_lrtable" class="table"
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