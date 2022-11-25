<div class="modal fade bd-example-modal-xl" id="add-task" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Create Task</h4>
            </div> 
            <!-- Modal body -->
            <form method="POST" action="{{url($prefix.'/driver-tasks/create-taskitem')}}" id="createprstaskitem">
                @csrf
                <input type="hidden" id="drivertask_id" value="" name="drivertask_id">
                <input type="hidden" id="prs_id" value="" name="prs_id">
                <div class="modal-body">
                
                    <div class="Delt-content text-center">
                        <!-- <img src="/assets/images/sucess.png" class="img-fluid mb-2">  -->
                        <!-- <p class="confirmtext">Are You Sure You Want To Cancel It ?</p> -->
                    </div>
                    <div class="" id="lrid">
                        <table id="create-driver-task" class="table table-hover" style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Quantity</th>
                                    <th>Net Weight</th>
                                    <th>Gross Weight</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control form-small orderid" name="data[1][order_id]"></td>
                                    <td><input type="text" class="form-control form-small invc_no" id="1" name="data[1][invoice_no]"></td>
                                    <td><input type="date" class="form-control form-small invc_date" name="data[1][invoice_date]"></td>
                                    <td><input type="text" class="form-control form-small qnt" name="data[1][quantity]"></td>
                                    <td><input type="number" class="form-control form-small net" name="data[1][net_weight]"></td>
                                    <td><input type="number" class="form-control form-small gross" name="data[1][gross_weight]"></td>
                                    <td> <button type="button" class="btn btn-default btn-rounded insert-moreprs"> + </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <div class="btn-section w-100 P-0">
                        <button type="submit" id="task_savebtn" class="btn-cstm btn-white btn btn-modal">Add</button>
                        <a type="" class="btn btn-modal" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>