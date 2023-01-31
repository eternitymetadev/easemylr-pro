<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Requesting Branch</th>
                <th>BM Name</th>
                <th>Total Prs</th>
                <th>Vendor Name</th>
                <th>Purchase Amount</th>
                <th>Transaction ID</th>
                <th>Payment Type.</th>
                <th>Advanced</th>
                <th>Balance</th>
                <th>Create Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prsRequests as $prsRequest)
            <?php 
            $authuser = Auth::user();
            
            ?>
            <tr>
                <td>{{$prsRequest->Branch->name}}</td>
                <td>{{$prsRequest->User->name}}</td>
                <td class="show-prs" data-id="{{$prsRequest->transaction_id}}">
                    {{ Helper::countPrsInTransaction($prsRequest->transaction_id) ?? "" }}</td>
                <td>{{$prsRequest->VendorDetails->name}}</td>
                <td>{{$prsRequest->total_amount}}</td>
                <td>{{$prsRequest->transaction_id}}</td>
                <td>{{$prsRequest->payment_type}}</td>
                <td>{{$prsRequest->advanced}}</td>
                <td>{{$prsRequest->balance}}</td>
                <!--------- Payment Request Status --------->
                <?php if($prsRequest->payment_status == 0){?>
                <td><button class="btn btn-warning" value="{{$prsRequest->transaction_id}}"> Unpaid </button></td>
                <?php } else if($prsRequest->payment_status == 1) { ?>
                <td><button class="btn btn-warning" value="{{$prsRequest->transaction_id}}"> Paid </button></td>
                <?php } else if($prsRequest->payment_status == 2 ){
                    if($prsRequest->is_approve == 0){
                        if($authuser->role_id == 3){?>
                <!-- approver check -->
                <td><button class="btn btn-warning approve" value="{{$prsRequest->transaction_id}}"> Approve </button>
                </td>
                <?php } else { ?>
                <td><button class="btn btn-warning" value="{{$prsRequest->transaction_id}}"> waiting for approver
                    </button></td>
                <?php } ?>
                <?php } else {?>
                <td><button class="btn btn-warning" value="{{$prsRequest->transaction_id}}"> Sent </button></td>
                <!-- approver check end -->
                <?php } ?>
                <?php } else if($prsRequest->payment_status == 3) {?>
                <td><button class="btn btn-warning second_payment_prs" value="{{$prsRequest->transaction_id}}"> Partial Paid
                    </button></td>
                <?php } else{ ?>
                <td><button class="btn btn-warning" value="{{$prsRequest->transaction_id}}"> Unknown </button></td>
                <?php } ?>
                <!-- ------- End --------- -->
                <!------ Payment Status ------->
                <?php if($prsRequest->payment_status == 0){ ?>
                <td> <label class="badge badge-dark">Faild</label>
                </td>
                <?php } elseif($prsRequest->payment_status == 1) { ?>
                <td> <label class="badge badge-success">Paid</label> </td>
                <?php } elseif($prsRequest->payment_status == 2) { 
                    if($prsRequest->is_approve == 0){?>
                <td> <label class="badge badge-dark">Approve Pending</label> </td>
                <?php } else {?>
                <td> <label class="badge badge-dark">Sent to Account</label> </td>
                <?php } ?>
                <?php } elseif($prsRequest->payment_status == 3) { ?>
                <td><label class="badge badge-primary">Partial Paid</label></td>
                <?php } else{?>
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
            {{$prsRequests->appends(request()->query())->links()}}
        </nav>
    </div>
</div>