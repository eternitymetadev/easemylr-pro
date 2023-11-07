@php
    $authuser = Auth::user();
@endphp
<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>DRS NO</th>
                <th>DRS Date</th>
                <th>Vehicle No</th>
                <th>Driver Name</th>
                <th>Driver Number</th>
                <th>Action</th>
                <th>Delivery Status</th>
                
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($transaction)>0)
            @foreach($transaction as $trns)
            @php
               $new = Helper::oldnewLr($trns->drs_no) ?? "";
                
            @endphp 
            <tr>
                <td>DRS-{{$trns->drs_no}}</td>
                <td>{{Helper::ShowDayMonthYear($trns->created_at) ?? ''}}</td>
                <td>{{$trns->vehicle_no}}</td>
                <td>{{$trns->driver_name}}</td>
                <td>{{$trns->driver_no}}</td>
                <!-- action button -->
                <?php 
                if ($trns->status == 0) {?>
                <td>
                    <label class="badge badge-dark">Cancelled</label>
                </td>
                <?php } else {?>
                <td>
                    <?php //if (empty($trns->vehicle_no) || empty($trns->driver_name) || empty($trns->driver_no)) {
                        if($trns->is_started != 1){
                            if(!empty($trns->vehicle_no) && !empty($trns->driver_name)){
                                $color_class = 'btn-danger';
                            }else{
                                $color_class = 'bg-secondary!important';
                            }
                        ?>
                    <button type="button" class="btn btn-warning view-sheet" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Draft</button>
                        <button type="button" class="btn start-sheet {{$color_class}}" data-drsid="{{$trns->drs_no}}"
                        style="margin-right:4px;">Start</button>
                    <?php } if($trns->is_started == 1){
                    if (!empty($new)) {
                    ?>
                    <a class="btn btn-primary" href="{{url($prefix.'/print-transactionold/'.$trns->drs_no)}}"
                        role="button">Print</a>
                    <?php } else {?>
                    <a class="btn btn-primary" href="{{url($prefix.'/print-transaction/'.$trns->drs_no)}}"
                        role="button">Print</a>
                    <?php }}?>
                    <?php
                    //if ($trns->delivery_status == 'Unassigned') {?>
                    {{-- <button type="button" class="btn btn-danger" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Unassigned</button> --}}
                    <?php// } elseif ($lr == 0) {?>
                    {{-- <button type="button" class="btn btn-warning" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Assigned</button> --}}
                    <?php //}
                    if ($trns->is_started == 1) {?>
                    <button type="button" class="btn btn-warning" value="{{$trns->drs_no}}"
                        style="margin-right:4px;">Assigned</button>
                        <?php } ?>
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