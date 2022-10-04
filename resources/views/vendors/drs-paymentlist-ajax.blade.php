<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <div class="btn-group relative">
            <!-- <a href="{{'consignments/create'}}" class="btn btn-primary pull-right" style="font-size: 13px; padding: 6px 0px;">Create Consignment</a> -->
        </div>
        <thead>
            <tr>
                <?php $authuser = Auth::user(); 
                                if($authuser->role_id == 2){?>
                <th>
                    <input type="checkbox" name="" id="ckbCheckAll" style="width: 30px; height:30px;">
                </th>
                <?php } ?>
                <th>DRS NO</th>
                <th>Status</th>
                <th>Total Lr</th>
                <th>Vehicle No</th>
                <th>Purchase Amount</th>
                <th>Advance</th>
                <th>Balance</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @if(count($paymentlist)>0)
            @foreach($paymentlist as $list)
            <tr>
                <?php if($authuser->role_id == 2){?>
                <td><input type="checkbox" name="checked_drs[]" class="chkBoxClass" value="{{$list->drs_no}}"
                        data-price="{{$list->ConsignmentDetail->purchase_price}}" style="width: 30px; height:30px;">
                </td>
                <?php } ?>
                <td>DRS-{{$list->drs_no}}</td>
                <!-- delivery Status ---- -->
                <td>
                    <?php if($list->status == 0){?>
                    <label class="badge badge-dark">Cancelled</label>
                    <?php } else { ?>
                    <?php if(empty($list->vehicle_no) || empty($list->driver_name) || empty($list->driver_no)) { ?>
                    <label class="badge badge-warning">No Status</label>
                    <?php }else{ ?>
                    <a class="drs_cancel btn btn-success drs_lr" drs-no="{{$list->drs_no}}" data-text="consignment"
                        data-status="0"
                        data-action="<?php echo URL::current();?>"><span>{{ Helper::getdeleveryStatus($list->drs_no) }}</span></a>
                    <?php } ?>
                    <?php } ?>
                </td>
                <!-- END Delivery Status  --------------->

                <td>{{ Helper::countdrslr($list->drs_no) ?? "-" }}</td>
                <td>{{$list->vehicle_no ?? '-'}}</td>
                <!------- Purchase Price --------------->
                <?php if(!empty($list->ConsignmentDetail->purchase_price)){ ?>
                <td>{{$list->ConsignmentDetail->purchase_price ?? '-'}}</td>
                <?php } else { ?>
                <td><button type="button" class="btn btn-warning add_purchase_price" value="{{$list->drs_no}}"
                        style="margin-right:4px;">Add amount</button> </td>
                <?php } ?>
                <!-- end purchase price -->
                <!-- ----Advanced Amount -->
                <?php if (!empty($list->advanced)) { ?>
                <td class='has-details'>
                    club
                    <span class="details">{{$list->advanced}}</span>
                </td>
                <?php } else { ?>
                <td></td>
                <?php } ?>
                <!-- ----end advanced -->
                <!-- Balance amount -->
                <td>{{$list->balance}}</td>
                <!--  end balance -->
                <!-- --------- payment status------- -->
                <?php if($list->payment_status == 0){ ?>
                <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Unpaid</button> </td>
                <?php } elseif($list->payment_status == 1) { ?>
                <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Paid</button> </td>
                <?php } elseif($list->payment_status == 2) { ?>
                <td> <button type="button" class="btn btn-warning " style="margin-right:4px;">Sent</button> </td>
                <?php } elseif($list->payment_status == 3) { ?>
                <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Partial Paid</button> </td>
                <?php } else{?>
                <td> <button type="button" class="btn btn-danger " style="margin-right:4px;">Unknown</button> </td>
                <?php } ?>
                <!-- --------- End Payment Status---- -->

            </tr>

            @endforeach
            @else
            <tr>
                <td colspan="9" class="text-center">No Record Found </td>
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
            {{$paymentlist->appends(request()->query())->links()}}
        </nav>
    </div>
</div>