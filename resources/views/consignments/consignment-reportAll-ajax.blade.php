<p class="totalcount">Total Count: <span class="reportcount">{{$consignments->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>LR No</th>
                <th>LR Date</th>
                <th>Delivered Drs No</th>
                <th>Drs Nos</th>
                <th>Drs Date</th>
                <th>Order No</th>
                <th>Booking Branch</th>
                <th>Delivery Branch</th>
                <th>Base Client</th>
                <th>Regional Client</th>
                <th>Consignor</th>
                <th>Consignor City</th>
                <th>Consignee Name</th>
                <th>Contact Person Name</th>
                <th>Consignee Phone</th>
                <th>City</th>
                <th>Pin Code</th>
                <th>District</th>
                <th>State</th>
                <th>Ship To Name</th>
                <th>Ship To City</th>
                <th>Ship To pin code</th>
                <th>Ship To District</th>
                <th>Ship To State</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Invoice Amount</th>
                <th>Vehicle No</th>
                <th>Vehicle Type</th>
                <th>Transporter Name</th>
                <th>Boxes</th>
                <th>Net Weight</th>
                <th>Gross Weight</th>
                <th>Driver Name</th>
                <th>Driver Number</th>
                <th>Driver Fleet</th>
                <th>LR Status</th>
                <th>Dispatch Date</th>
                <th>Delivery Date</th>
                <th>Delivery Status</th>
                <th>TAT</th>
                <th>Delivery Mode</th>
                <th>POD</th>
                <th>Payment Type</th>
                <th>Freignt on Delivery</th>
                <th>Cash on Delivery</th>
                <th>LR Type</th>
                <th>No of Reattempt</th>
            </tr>
        </thead>
        <tbody>
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <?php
      
            $start_date = strtotime($consignment->consignment_date);
            $end_date = strtotime($consignment->delivery_date);
            $tat = ($end_date - $start_date)/60/60/24;
            
            if(!empty($consignment->DrsDetail->created_at)){
            //  $date = new DateTime(@$consignment->DrsDetail->created_at, new DateTimeZone('GMT-7'));
            //  $date->setTimezone(new DateTimeZone('IST'));
             $drsdate = $consignment->DrsDetail->created_at;
             $drs_date = $drsdate->format('d-m-Y');
            }else{
            $drs_date = '-';
            }

            // reatempted drs nos
            if(!empty($consignment->DrsDetailReattempted)){
                $drs_nos = array();
                foreach($consignment->DrsDetailReattempted as $reattemptDrs){ 
                    $drs_nos[] = $reattemptDrs->drs_no;
                }
                $reattempt_drs['drs_nos'] = implode(',', $drs_nos);
            }
        ?>
            <tr>
                <td>{{ $consignment->id ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}</td>
                <td>DRS-{{ @$consignment->DrsDetail->drs_no ?? "-" }}</td>
                <td>DRS-{{ @$reattempt_drs['drs_nos'] ?? "-" }}</td>
                <td>{{$drs_date}}</td>
                <?php if(empty($consignment->order_id)){ 
                    if(!empty($consignment->ConsignmentItems)){
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
                <td>{{ $consignment->Branch->name ?? "-" }}</td>
                <?php 
                if($consignment->lr_type == 0){
                    $delivery_branch = @$consignment->Branch->name;
                }else{
                    $delivery_branch = @$consignment->ToBranch->name;
                }
                ?>
                <td>{{ @$delivery_branch ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->GetRegClient->BaseClient->client_name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->city ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->contact_name ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->phone ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->city ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->postal_code ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->district ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->Zone->state ?? "-" }}</td>
                <td>{{ $consignment->ShiptoDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ShiptoDetail->city ?? "-" }}</td>
                <td>{{ $consignment->ShiptoDetail->postal_code ?? "-" }}</td>
                <td>{{ $consignment->ShiptoDetail->district ?? "-" }}</td>
                <td>{{ $consignment->ShiptoDetail->Zone->state ?? "-" }}</td>
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
                <td>{{ $consignment->transporter_name ?? "-" }}</td>
                <td>{{ $consignment->total_quantity ?? "-" }}</td>
                <td>{{ $consignment->total_weight ?? "-" }}</td>
                <td>{{ $consignment->total_gross_weight ?? "-" }}</td>
                <td>{{ $consignment->DriverDetail->name ?? "-" }}</td>
                <td>{{ $consignment->DriverDetail->phone ?? "-" }}</td>
                <td>{{ $consignment->DriverDetail->fleet_id ?? "-" }}</td>

                <?php 
                if($consignment->status == 0){ ?>
                <td>Cancel</td>
                <?php }elseif($consignment->status == 1){ ?>
                <td>Active</td>
                <?php }elseif($consignment->status == 2 || $consignment->status == 6){ ?>
                <td>Unverified</td>
                <?php }else{?>
                <td>Unknown</td>
                <?php   } ?>
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
                <?php } if($consignment->lr_mode == 0){?>
                <td>Manual</td>
                <?php }else if($consignment->lr_mode == 1){ ?>
                <td>Shadow</td>
                <?php  }else{?>
                <td>Shiprider</td>
                <?php } ?>

                <?php if($consignment->lr_mode == 0){
            if(empty($consignment->signed_drs)){
            ?>
                <td>Not Available</td>
                <?php } else { ?>
                <td>Avliable</td>
                <?php } ?>
                <?php } else if($consignment->lr_mode == 1) { 
                    $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id','desc')->first();

            if(!empty($job->response_data)){
            $trail_decorator = json_decode($job->response_data);
            $img_group = array();
            if(!empty($trail_decorator)){
                foreach($trail_decorator->task_history as $task_img){
                    if($task_img->type == 'image_added'){
                        $img_group[] = $task_img->description;
                    }
                }
            }
            if(empty($img_group)){?>
                <td>Not Available</td>
                <?php } else{?>
                <td>Available</td>
                <?php }
            }
            ?>
                <?php } else{ 
                    $getjobimg = DB::table('app_media')->where('consignment_no', $consignment->id)->get();
                    $count_arra = count($getjobimg);
                    if ($count_arra > 1) { ?>
                <td>Available</td>
                <?php   }else{ ?>
                <td>Not Available</td>
                <?php   }  ?>
                <?php  } ?>
                <td>{{$consignment->payment_type}}</td>
                <td>{{$consignment->freight_on_delivery}}</td>
                <td>{{$consignment->cod}}</td>
                <?php if($consignment->lr_type == 0){ 
                    $lr_type = "FTL";
                     }elseif($consignment->lr_type == 1 || $consignment->lr_type ==2){ 
                        $lr_type = "PTL";
                         }else{ 
                            $lr_type = "-";
                             } ?>
                <td>{{$lr_type}}</td>
                <?php // No of reattempt
                if($consignment->reattempt_reason != null){
                    $no_reattempt = count(json_decode($consignment->reattempt_reason,true));
                }else{
                    $no_reattempt = '';
                }?>
                <td>{{$no_reattempt}}</td>

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