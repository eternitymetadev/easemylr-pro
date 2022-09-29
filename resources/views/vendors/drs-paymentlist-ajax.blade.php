<div class="custom-table">
<table id="" class="table table-hover" style="width:100%">
                        <div class="btn-group relative">
                            <!-- <a href="{{'consignments/create'}}" class="btn btn-primary pull-right" style="font-size: 13px; padding: 6px 0px;">Create Consignment</a> -->
                        </div>
                        <thead>
                            <tr>
                                <th>
                                     <input type="checkbox" name="" id="ckbCheckAll" style="width: 30px; height:30px;">
                                </th>
                                <th>DRS NO</th>
                                <th>Status</th>
                                <th>Vehicle No</th>
                                <th>Purchase Amount</th>
                                <!-- <th>Payment</th> -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($paymentlist)>0)
                            @foreach($paymentlist as $list)
                            <tr>
                            <td><input type="checkbox" name="checked_consign[]" class="chkBoxClass ddd" value="{{$list->drs}}" data-trp="" data-vehno="" data-vctype="" style="width: 30px; height:30px;"></td>
                                <td>DRS-{{$list->drs_no}}</td>
                                <!-- delivery Status ---- -->
                                <td>
                                    <?php if($list->status == 0){?>
                                    <label class="badge badge-dark">Cancelled</label>
                                    <?php } else { ?>
                                    <?php if(empty($list->vehicle_no) || empty($list->driver_name) || empty($list->driver_no)) { ?>
                                    <label class="badge badge-warning">No Status</label>
                                    <?php }else{ ?>
                                    <a class="drs_cancel btn btn-success" drs-no="{{$list->drs_no}}"
                                        data-text="consignment" data-status="0"
                                        data-action="<?php echo URL::current();?>"><span>{{ Helper::getdeleveryStatus($list->drs_no) }}</span></a>
                                    <?php } ?>
                                    <?php } ?>
                                </td>
                                <!-- END Delivery Status  --------------->

                                <td>{{$list->vehicle_no ?? '-'}}</td>
                                <?php if(!empty($list->ConsignmentDetail->purchase_price)){ ?>
                                <td>{{$list->ConsignmentDetail->purchase_price ?? '-'}}</td>
                                <?php } else { ?>
                                    <td><button type="button" class="btn btn-warning" value="{{$list->drs_no}}"
                                        style="margin-right:4px;">Add amount</button> </td>
                                        <?php } ?>
                                <!-- <td> <button type="button" class="btn btn-warning payment" value="{{$list->drs_no}}"
                                        style="margin-right:4px;">Create Payment</button> </td> -->
                                
                            </tr>
                            
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="text-center">No Record Found </td>
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