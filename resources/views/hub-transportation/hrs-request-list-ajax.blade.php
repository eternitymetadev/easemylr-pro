<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Requesting Branch</th>
                <th>BM Name</th>
                <th>Total Hrs</th>
                <th>Vendor Name</th>
                <th>Purchase Amount</th>
                <th>Transaction ID</th>
                <th>Payment Type</th>
                <th>Advanced</th>
                <th>Balance</th>
                <th>Create Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hrsRequests as $hrsRequest)
            <?php 
            $authuser = Auth::user();
            
            ?>
            <tr>
                <td>{{$hrsRequest->Branch->name}}</td>
                <td>{{$hrsRequest->User->name}}</td>
                <td class="show-hrs" data-id="{{$hrsRequest->transaction_id}}">
                            {{ Helper::countHrsInTransaction($hrsRequest->transaction_id) ?? "" }}</td>
                <td>{{$hrsRequest->VendorDetails->name}}</td>
                <td>{{$hrsRequest->total_amount}}</td>
                <td>{{$hrsRequest->transaction_id}}</td>
                <td>{{$hrsRequest->payment_type}}</td>
                <td>{{$hrsRequest->advanced}}</td>
                <td>{{$hrsRequest->balance}}</td>
                <!--------- Payment Request Status --------->
                <?php if($hrsRequest->payment_status == 0){?>
                <td><button class="btn btn-warning" value="{{$hrsRequest->transaction_id}}"> Unpaid </button></td>
                <?php } else if($hrsRequest->payment_status == 1) { ?>
                <td><button class="btn btn-warning" value="{{$hrsRequest->transaction_id}}"> Paid </button></td>
                <?php } else if($hrsRequest->payment_status == 2 ){
                    if($hrsRequest->is_approve == 0){
                        if($authuser->role_id == 3){?>
                <!-- approver check -->
                <td valign="middle">
                    <a class="approve" data-text="consignment" data-status="0"
                        data-id="{{$hrsRequest->transaction_id}}">
                        <p class=" drsStatus pointer" style="background:#008000; margin-bottom: 0">
                            <span>Approve</span>
                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                        </p>
                    </a>
                </td>
                <?php } else { ?>
                    <td valign="middle">
                    <a class="drs_cancel hrs_lr" data-text="consignment" data-status="0">
                        <p class=" drsStatus pointer" style="background:#008000; margin-bottom: 0">
                            <span>Wait for approve</span>

                        </p>
                    </a>
                </td>
                <?php } ?>
                <?php } else {?>
                    <td valign="middle">
                    <a class="drs_cancel hrs_lr" data-text="consignment" data-status="0">
                        <p class="drsStatus pointer" style="background:#ffa500; margin-bottom: 0">
                            <span>Sent</span>
                        </p>
                    </a>
                </td>
                <!-- approver check end -->
                <?php } ?>
                <?php } else if($hrsRequest->payment_status == 3) {?>
                <td><button class="btn btn-warning second_payment" value="{{$hrsRequest->transaction_id}}"> Partial Paid </button></td>
                <?php } elseif($hrsRequest->payment_status == 4 ){ ?>
                    <td valign="middle">
                    <a class="drs_cancel hrs_lr" data-text="consignment" data-status="0">
                        <p class="drsStatus pointer" style="background:#e7515a; margin-bottom: 0">
                            <span>Rejected</span>
                        </p>
                    </a>
                </td>
               <?php } else{ ?>
                <td><button class="btn btn-warning" value="{{$hrsRequest->transaction_id}}"> Unknown </button></td>
                <?php } ?>
                <!-- ------- End --------- -->
                <!------ Payment Status ------->
                <?php if($hrsRequest->payment_status == 0){ ?>
                <td> <label class="badge badge-dark">Faild</label>
                </td>
                <?php } elseif($hrsRequest->payment_status == 1) { ?>
                    <td valign="middle">
                    <a class="drs_cancel hrs_lr" data-text="consignment" data-status="0">
                        <p class=" drsStatus pointer" style="background:#32cd32; margin-bottom: 0">
                            <span>Paid</span>
                        </p>
                    </a>
                </td>
                <?php } elseif($hrsRequest->payment_status == 2) { 
                    if($hrsRequest->is_approve == 0){?>
                 <td valign="middle">
                    <a class="drs_cancel hrs_lr" data-text="consignment" data-status="0">
                        <p class=" drsStatus pointer" style="background:#800080; margin-bottom: 0">
                            <span>Approve Pending</span>
                        </p>
                    </a>
                </td>
                <?php } else {?>
                    <td valign="middle">
                    <a class="drs_cancel hrs_lr" data-text="consignment" data-status="0">
                        <p class="drsStatus pointer" style="background:#6b8e23; margin-bottom: 0">
                            <span>Sent to Account</span>
                        </p>
                    </a>
                </td>
                <?php } ?>
                <?php } elseif($hrsRequest->payment_status == 3) { ?>
                <td><label class="badge badge-primary">Partial Paid</label></td>
                <?php }elseif($hrsRequest->payment_status == 4){ ?>
                    <td valign="middle">
                    <a class="drs_cancel hrs_lr"  data-text="consignment" data-status="0">
                        <p class="swan-tooltip drsStatus pointer" style="background:#e7515a; margin-bottom: 0" data-tooltip="{{$hrsRequest->rejected_remarks}}">
                            <span>Rejected</span>
                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                        </p>
                    </a>
                </td>
             <?php   } else {?>
                <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Unknown</button>
                </td>
                <?php } ?>

            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="perpage container-fluid">
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
            {{$hrsRequests->appends(request()->query())->links()}}
        </nav>
    </div>
</div>