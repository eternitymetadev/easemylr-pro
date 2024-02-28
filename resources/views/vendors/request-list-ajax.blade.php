<p class="totalcount">Total Count: <span class="reportcount">{{$requestlists->total()}}</span></p>
<div class="custom-table">
    <table id="unverified-table" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Transaction Id</th>
                <th>Created Date</th>
                <th>Vendor Name</th>
                <th>Branch </th>
                <th>State </th>
                <th>Total Drs</th>
                <th>Payment Type</th>

                <th>Adavanced</th>
                <th>Balance</th>
                <th>Total Amount</th>
                <th>Create Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if(count($requestlists)>0)
            @foreach($requestlists as $requestlist)
                <tr>
                    <td>{{ $requestlist->transaction_id ?? "-" }}</td>
                    <td>{{ Helper::ShowDayMonthYear($requestlist->created_at) ?? "" }}</td>
                    <td>{{ @$requestlist->VendorDetails->name ?? "-"}}</td>
                    <td>{{ @$requestlist->Branch->name ?? "-" }}</td>
                    <td>{{ @$requestlist->Branch->nick_name ?? "-" }}</td>
                    <td class="show-drs highlight-on-hover" data-id="{{$requestlist->transaction_id}}" >
                        {{ Helper::countDrsInTransaction($requestlist->transaction_id) ?? "" }}
                    </td>

                    <td>{{$requestlist->payment_type}}</td>

                    <td>{{ @$requestlist->advanced ?? "-"}}</td>
                    <td>{{ @$requestlist->balance ?? "-" }}</td>                    
                    <td>{{ @$requestlist->total_amount ?? "-"}}</td>

                    {{--  create payment status --}}
                    <?php if($requestlist->payment_status == 1){?>
                    <td><button class="btn btn-warning" value="{{$requestlist->transaction_id}}" disabled>Fully
                            Paid</button></td>
                    <?php }elseif($requestlist->payment_status == 2 || $requestlist->payment_status == 1){ ?>
                    <td><button class="btn btn-warning payment_button" value="{{$requestlist->transaction_id}}" disabled>Processing...</button></td>
                    <?php } else if($requestlist->payment_status == 0){
                        if(!empty($requestlist->current_paid_amt)){
                         ?>
                    <td><button class="btn btn-warning swan-tooltip" data-tooltip="{{$requestlist->remarks}}" value="{{$requestlist->transaction_id}}">Repay</button></td>
                    <?php } else{ ?>
                        <td><button class="btn btn-danger" value="{{$requestlist->transaction_id}}">Failed</button></td>
                        <?php } ?>
                    
                    <?php }else{
                        if($requestlist->balance < 1){ ?>
                            <td><button class="btn btn-warning" value="{{$requestlist->transaction_id}}" disabled>Fully
                                Paid</button></td>
                        <?php }else{ ?>
                        <td><button class="btn btn-warning payment_button"
                                value="{{$requestlist->transaction_id}}">Create Payment</button></td>
                        <?php  }
                        } ?>

                    <!-- payment Status -->
                    <?php if($requestlist->payment_status == 0){ ?>
                    <td> <label class="badge badge-dark">Failed</label>
                    </td>
                    <?php } elseif($requestlist->payment_status == 1) { ?>
                    <td> <label class="badge badge-success">Paid</label> </td>
                    <?php } elseif($requestlist->payment_status == 2) { ?>
                    <td> <label class="badge badge-dark">Sent to Account</label>
                    </td>
                    <?php } elseif($requestlist->payment_status == 3) { ?>
                    <td><label class="badge badge-primary">Partial Paid</label></td>
                    <?php } else{?>
                    <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Unknown</button>
                    </td>
                    <?php } ?>
                    <!-- end payment -->

                </tr>
                @endforeach
            @else
            <tr>
                <td colspan="10" class="text-center">No Record Found </td>
            </tr>
            @endif
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
                            <select class="form-control report_perpage" data-action="<?php echo url()->current(); ?>">
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
            {{$requestlists->appends(request()->query())->links()}}
        </nav>
    </div>
</div>