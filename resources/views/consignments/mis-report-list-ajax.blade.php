<p class="totalcount">Total Count: <span class="reportcount">{{$consignments->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <!-- <th> </th> -->
                <th>LR No</th>
                <th>LR Date</th>
                <th>Order No</th>
                <th>Regional Client</th>
                <th>Consigner</th>
                <th>Consigner City</th>
                <th>Consignee Name</th>
                <th>City</th>
                <th>Pin Code</th>
                <th>District</th>
                <th>State</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Invoice Amount</th>
                <th>Vehicle No</th>
                <th>Vehicle Type</th>
                <th>Boxes</th>
                <th>Net Weight</th>
                <th>Gross Weight</th>
                <th>LR Status</th>
                <th>Dispatch Date</th>
                <th>Delivery Date</th>
                <th>Delivery Status</th>
                <th>TAT</th>
                <th>Delivery Type</th>
                <th>POD</th>
            </tr>
        </thead>
        <tbody>
        @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <?php 
                $start_date = strtotime($consignment['consignment_date']);
                $end_date = strtotime($consignment['delivery_date']);
                $tat = ($end_date - $start_date) / 60 / 60 / 24;
                ?>
            <tr>
                <td>{{ $consignment['id'] ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment['consignment_date'] ?? "-" )}}</td>
                <?php if(empty($consignment->order_id)){ 
                    if(!empty($consignment->consignment_items)){
                    $order = array();
                    $invoices = array();
                    $inv_date = array();
                    $inv_amt = array();

                    foreach($consignment->ConsignmentItems as $orders){ 
                        
                        $order[] = $orders->order_id;
                        $invoices[] = $orders->invoice_no;
                        $inv_date[] = Helper::ShowDayMonthYearslash($orders->invoice_date);
                        $inv_amt[] = $orders->invoice_amount;
                    }
                    //echo'<pre>'; print_r($order); die;
                    $order_item['orders'] = implode(',', $order);
                    $order_item['invoices'] = implode(',', $invoices);
                    $invoice['date'] = implode(',', $inv_date);
                    $invoice['amt'] = implode(',', $inv_amt);?>

                <td>{{ $orders->order_id ?? "-" }}</td>

                <?php }else{ ?>
                <td>-</td>
                <?php } }else{ ?>
                <td>{{ $consignment->order_id ?? "-" }}</td>
                <?php  } ?>
                <td>{{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->city ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->city ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->postal_code ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->district ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->zone->state ?? "-" }}</td>
                
                <!-- <?php if(empty($consignment['invoice_no'])){ 
                    if(!empty( $order_item['invoices'])){?>
                <td>{{ $order_item['invoices'] ?? "-" }}</td>
                <?php }else{ ?>
                <td>-</td>
                <?php } 
                if(!empty($invoice['date'])){?>
                <td>{{ $invoice['date'] ?? '-'}}</td>
                <?php }else{ ?>
                <td>-
                <td>
                    <?php } 
                if(!empty($invoice['amt'] )){?>
                <td>{{ $invoice['amt'] ?? '-'}}</td>
                <?php }else{?>
                <td>-</td>
                <?php }  } else{ ?>
                <td>{{ $consignment['invoice_no'] ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment['invoice_date'] ?? "-" )}}</td>
                <td>{{ $consignment['invoice_amount'] ?? "-" }}</td>
                <?php  } ?> -->

                <?php if(empty($consignment->invoice_no)){ ?>
                <td>{{ $order_item['invoices'] ?? "-" }}</td>
                <td>{{ $invoice['date'] ?? '-'}}</td>
                <td>{{ $invoice['amt'] ?? '-' }}</td>
            <?php  } else{ ?>
                <td>{{ $consignment->invoice_no ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->invoice_date ?? "-" )}}</td>
                <td>{{ $consignment->invoice_amount ?? "-" }}</td>
            <?php  } ?>
            
                <td>{{ $consignment->VehicleDetail->regn_no ?? "Pending" }}</td>
                <td>{{ $consignment->vehicletype->name ?? "-" }}</td>
                <td>{{ $consignment->total_quantity ?? "-" }}</td>
                <td>{{ $consignment->total_weight ?? "-" }}</td>
                <td>{{ $consignment->total_gross_weight ?? "-" }}</td>
                <?php 
                if($consignment->status == 0){ ?>
                    <td>Cancel</td>
                <?php }elseif($consignment->status == 1){ ?>
                    <td>Active</td>
                    <?php }elseif($consignment->status == 2){ ?>
                    <td>Unverified</td>
                    <?php } ?>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->consignment_date )}}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->delivery_date )}}</td>
                <?php 
                if($consignment->delivery_status == 'Assigned'){ ?>
                    <td>Assigned</td>
                    <?php }elseif($consignment->delivery_status == 'Unassigned'){ ?>
                    <td>Unassigned</td>
                    <?php }elseif($consignment->delivery_status == 'Started'){ ?>
                    <td>Started</td>
                <?php }elseif($consignment->delivery_status == 'Successful'){ ?>
                    <td>Successful</td>
                <?php }elseif($consignment->delivery_status == 'Cancel'){ ?>
                    <td>Cancel</td>
                <?php }else{?>
                    <td>Unknown</td>
                <?php }?>
                <?php if($consignment->delivery_date == ''){?>
                    <td> - </td>
                <?php }else{?>
                <td>{{ $tat }}</td>
                <?php } if($consignment->job_id== ''){?>
                    <td>Manual</td>
                    <?php }else{?>
                        <td>Shadow</td>
                    <?php } ?>

                <?php if(empty($consignment->job_id)){
            if(empty($consignment->signed_drs)){
            ?>
                <td>Not Available</td>
                <?php } else { ?>
                <td>Avliable</td>
                <?php } ?>
                <?php } else { 
                    $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id','desc')->first();

            if(!empty($job->response_data)){
            $trail_decorator = json_decode($job->response_data);
            $img_group = array();
            foreach($trail_decorator->task_history as $task_img){
                if($task_img->type == 'image_added'){
                    $img_group[] = $task_img->description;
                }
            }
            if(empty($img_group)){?>
                <td>Not Available</td>
                <?php } else{?>
                <td>Available</td>
                <?php }
            }
            ?>
                <?php } ?>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="15" class="text-center">No Record Found </td>
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
            {{$consignments->appends(request()->query())->links()}}
        </nav>
    </div>
</div>