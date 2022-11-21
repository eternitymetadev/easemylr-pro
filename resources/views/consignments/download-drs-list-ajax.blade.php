<?php $authuser = Auth::user();?>
<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>DRS NO</th>
                <th>DRS Date</th>
                <th>Vehicle No</th>
                <th>Driver Name</th>
                <th>Driver Number</th>
                <th>Total LR</th>
                <th>Action</th>
                <th>Delivery Status</th>
                <th>DRS Status</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($transaction)>0)
            @foreach($transaction as $trns)
            <?php

$date = new DateTime($trns->created_at, new DateTimeZone('GMT-7'));
$date->setTimezone(new DateTimeZone('IST'));
$getdeldate = Helper::getdeleveryStatus($trns->drs_no) ?? "";
$new = Helper::oldnewLr($trns->drs_no) ?? "";
$lr = Helper::deliveryDate($trns->drs_no);

?>
            <tr>
                <td>DRS-{{$trns->drs_no}}</td>
                <td>{{$date->format('Y-m-d')}}</td>
                <td>{{$trns->vehicle_no}}</td>
                <td>{{$trns->driver_name}}</td>
                <td>{{$trns->driver_no}}</td>
                <td>{{ Helper::getCountDrs($trns->drs_no) ?? "" }}</td>
                <!-- action button -->
                <?php 
if ($trns->status == 0) {?>
                <td>
                    <label class="badge badge-dark">Cancelled</label>
                </td>
                <?php } else {?>
                <td>
                    <?php if (empty($trns->vehicle_no) || empty($trns->driver_name) || empty($trns->driver_no)) {?>
                    <button type="button" class="btn btn-warning view-sheet" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Draft</button>
                    <button type="button" class="btn btn-danger draft-sheet" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Save</button>
                    <?php }?>
                    <?php if (!empty($trns->vehicle_no)) {
    if (!empty($new)) {
        ?>
                    <a class="btn btn-primary" href="{{url($prefix.'/print-transactionold/'.$trns->drs_no)}}"
                        role="button">Print</a>
                    <?php } else {?>
                    <a class="btn btn-primary" href="{{url($prefix.'/print-transaction/'.$trns->drs_no)}}"
                        role="button">Print</a>
                    <?php }}?>
                    <?php
if ($trns->delivery_status == 'Unassigned') {?>
                    <button type="button" class="btn btn-danger" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Unassigned</button>
                    <?php } elseif ($lr == 0) {?>
                    <button type="button" class="btn btn-warning" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Assigned</button>
                    <?php }?>
                </td>
                <?php }?>
                <!------- end Action ---- -->
                <!-- delivery Status ---- -->

                <td>
                    <?php if ($trns->status == 0) {?>
                    <label class="badge badge-dark">Cancelled</label>
                    <?php } else {?>
                    <?php if (empty($trns->vehicle_no) || empty($trns->driver_name) || empty($trns->driver_no)) {?>
                    <label class="badge badge-secondary">No Status</label>
                    <?php } else {?>
                    <a class="drs_cancel btn btn-success" drs-no="{{$trns->drs_no}}" data-text="consignment"
                        data-status="0"
                        data-action="<?php echo URL::current(); ?>"><span>{{ Helper::getdeleveryStatus($trns->drs_no) }}</span></a>
                    <?php }?>
                    <?php }?>
                </td>
                <!-- END Delivery Status  -------------  -->
                <!-- DRS STATUS --------------->
                <?php if ($trns->status == 0) {?>
                <td><label class="badge badge-dark">Cancelled</label></td>
                <?php } else {?>
                <td><a class="active_drs btn btn-success" drs-no="{{$trns->drs_no}}"><span><i
                                class="fa fa-check-circle-o"></i> Active</span></a></td>
                <?php }?>
                <!-- ------- payment status -->
                <?php if ($trns->payment_status == 0) {
              ?>
                <td><label class="badge badge-dark">Unpaid</label></td>
                <?php } else if ($trns->payment_status == 1) {?>
                <td><label class="badge badge-success">Paid</label></td>
                <?php } elseif ($trns->payment_status == 2) {?>
                <td><label class="badge badge-dark">Sent to Account</label></td>
                <?php } elseif ($trns->payment_status == 3) {?>
                <td><label class="badge badge-primary">Partial Paid</label></td>
                <?php } else {?>
                <td><label class="badge badge-dark">unknown</label></td>
                <?php }?>

                <!-- end payment status -->

            </tr>

            @endforeach
            @else
            <tr>
                <td colspan="15" class="text-center">No Record Found </td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-8 col-xl-9">
            </div>
            <div class="col-md-12 col-lg-4 col-xl-3">
                <div class="form-group mt-3 brown-select">
                    <div class="row">
                        <div class="col-md-6 pr-0">
                            <label class=" mb-0">items per page</label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control perpage" data-action="<?php echo url()->current(); ?>">
                                <option value="10" {{$peritem == '10' ? 'selected' : ''}}>10</option>
                                <option value="50" {{$peritem == '50' ? 'selected' : ''}}>50</option>
                                <option value="100" {{$peritem == '100'? 'selected' : ''}}>100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ml-auto mr-auto">
        <nav class="navigation2 text-center" aria-label="Page navigation">
            {{$transaction->appends(request()->query())->links()}}
        </nav>
    </div>
</div>