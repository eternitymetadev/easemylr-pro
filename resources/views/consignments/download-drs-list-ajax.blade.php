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
                <th>Total LR</th>
                <th>Delivery Status</th>
                <th class="text-center">Payment Status</th>
                
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($transaction)>0)
            @foreach($transaction as $trns)
            @php
               
            @endphp 
            <tr>
                <td>DRS-{{$trns->drs_no}}</td>
                <td>{{Helper::ShowDayMonthYear($trns->created_at) ?? ''}}</td>
                <td>{{$trns->vehicle_no}}</td>
                <td>{{$trns->driver_name}}</td>
                <td>{{$trns->driver_no}}</td>
                <td>{{ Helper::getCountDrs($trns->drs_no) ?? "" }}</td>
                
                <?php if($authuser->role_id == 3){
                    $disable = 'disable_n' ;
                }else{
                    $disable = '';
                } ?>
                
                {{-- delivery status --}}
                <td>
                    <?php
                            $new = Helper::oldnewLr($trns->drs_no) ?? "";

                            $status = Helper::getdeleveryStatus($trns->drs_no);
                            if($status == 'Started' && $trns->status == 0) $statusColor = '#187fb6';
                            else if($status == 'Partial') $statusColor = '#b69d18';
                            else if($status == 'Successful') $statusColor = '#18b69b';
                            else if($status == 'Cancel') $statusColor = '#f40404';
                            else $statusColor = '#18b69b';

                            if($status == 'Unassigned'){
                            ?>
                            <button class="delBtn statusBtn btn view-sheet {{$disable}}" value="{{$trns->drs_no}}" style="--statusColor: #9118b6;">Unassigned</button>
                            <?php }else{ ?>
                                <a class="delBtn statusBtn drs_cancel btn {{$disable}}" style="--statusColor: {{$statusColor}};" drs-no="{{$trns->drs_no}}" data-text="consignment" data-status="0" data-action="<?php echo URL::current(); ?>" data-textstatus="{{$status}}"><span>{{$status}}</span></a>
                            <?php }
                            if($trns->is_started == 1 && $status != 'Cancel'){
                                if (!empty($new)) { ?>
                                    <a class="btn btn-primary" href="{{url($prefix.'/print-transactionold/'.$trns->drs_no)}}"
                                role="button">Print</a>
                            <?php } else {?>
                                <a class="btn btn-primary" href="{{url($prefix.'/print-transaction/'.$trns->drs_no)}}" role="button">Print</a>
                            <?php }
                            }
                        // }
                    ?>
                </td>
                {{-- End delivery status --}}
                <!-- payment Status ---- -->
                <?php 
                    $status = Helper::drsPaymentStatus($trns->payment_status );
                    if($status == 'Unpaid' && $trns->status == 0) $statusColor = '#187fb6';
                    else if($status == 'Partial') $statusColor = '#18b69b';
                    else if($status == 'Paid') $statusColor = '#18b69b';
                    else if($status == 'Processing') $statusColor = '#b69d18';
                    else $statusColor = '#18b69b';
                    ?>
                <td class="text-center"><a style="color: {{$statusColor}}; text-align: center">{{$status}}</a></td>
                <!-- END payment Status  -------------  -->
 
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