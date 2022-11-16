<?php  $authuser = Auth::user(); ?>
<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th> </th>
                <th>LR Details</th>
                <th>Route</th>
                <th>Dates</th>
                <?php if($authuser->role_id !=6 && $authuser->role_id !=7){ ?>
                <th>Printing options</th>
                <?php }else {?>
                <th></th>
                <?php }?>
                <th>Dlvry Status</th>
                <th>LR Status</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <tr>
                <td class="card-header collapsed" id="{{$consignment->id}}" data-toggle="collapse" href="#collapse-{{$consignment->id}}" data-jobid="{{$consignment->job_id}}" data-action="<?php echo URL::to($prefix.'/get-jobs'); ?>" onClick="row_click(this.id,this.getAttribute('data-jobid'),this.getAttribute('data-action'))">

                </td>
                <td>
                    <div class="">
                        <div class=""><span style="color:#4361ee;">LR No: </span>{{$consignment->id}}<span
                                class="badge bg-cust">{{ $consignment->VehicleDetail->regn_no ?? " " }}<span></span></span>
                        </div>

                        <?php
                            if(empty($consignment->order_id)){ 
                                if(!empty($consignment->ConsignmentItems)){
                                    $order = array();
                                    $invoices = array();
                                    foreach($consignment->ConsignmentItems as $orders){ 
                                        $order[] = $orders->order_id;
                                        $invoices[] = $orders->invoice_no;
                                    }
                                    $order_item['orders'] = implode(',', $order);
                                    $order_item['invoices'] = implode(',', $invoices);
                                ?>

                        <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Order No:
                            </span>{{ $order_item['orders'] ?? "-" }}</div>
                        <?php }}else{ ?>
                        <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Order No:
                            </span>{{ $consignment->order_id ?? "-" }}</div>
                        <?php } ?>
                        <?php if(empty($consignment->invoice_no)){ ?>
                        <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Invoice No:
                            </span>{{ $order_item['invoices'] ?? "-" }}</div>
                        <?php }else{ ?>
                        <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Invoice No:
                            </span>{{ $consignment->invoice_no ?? "-" }}</div>
                        <?php } ?>

                    </div>
                </td>
                <td>

                <!-- relocate cnr cnee check for sale to return case -->
                <?php 
                if($consignment->is_salereturn == 1){
                    $cnr_nickname = $consignment->ConsigneeDetail->nick_name;
                    $cne_nickname = $consignment->ConsignerDetail->nick_name;
                }else{
                    $cnr_nickname = $consignment->ConsignerDetail->nick_name;
                    $cne_nickname = $consignment->ConsigneeDetail->nick_name;
                } ?>
                    <ul class="ant-timeline">
                        <li class="ant-timeline-item  css-b03s4t">
                            <div class="ant-timeline-item-tail"></div>
                            <div class="ant-timeline-item-head ant-timeline-item-head-green"></div>
                            <div class="ant-timeline-item-content">
                                <div class="css-16pld72 ellipse">
                                    {{ $cnr_nickname ?? "-" }}
                                </div>
                            </div>
                        </li>
                        <li class="ant-timeline-item ant-timeline-item-last css-phvyqn">
                            <div class="ant-timeline-item-tail"></div>
                            <div class="ant-timeline-item-head ant-timeline-item-head-red"></div>
                            <div class="ant-timeline-item-content">
                                <div class="css-16pld72 ellipse">
                                    {{ $cne_nickname ?? "-" }}
                                </div>
                                <div class="css-16pld72 ellipse" style="font-size: 12px; color: rgb(102, 102, 102);">
                                <?php if($consignment->is_salereturn == '1'){ ?>
                                    <span>{{ $consignment->ConsignerDetail->postal_code ?? "" }},
                                        {{ $consignment->ConsignerDetail->city ?? "" }},
                                        {{ $consignment->ConsignerDetail->district ?? "" }} </span>
                                        <?php }else{ ?>
                                            <span>{{ $consignment->ConsigneeDetail->postal_code ?? "" }},
                                            {{ $consignment->ConsigneeDetail->city ?? "" }},
                                            {{ $consignment->ConsigneeDetail->district ?? "" }} </span>
                                            <?php } ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </td>
                <td>
                    <div class="">
                        <div class=""><span style="color:#4361ee;">LRD:
                            </span>{{ Helper::ShowDayMonthYear($consignment->consignment_date) ?? '-' }}</div>
                        <div class=""><span style="color:#4361ee;">EDD:
                            </span>{{ Helper::ShowDayMonthYear($consignment->edd) ?? '-' }}</div>
                        <div class=""><span style="color:#4361ee;">ADD:
                            </span>{{ Helper::ShowDayMonthYear($consignment->delivery_date) ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <?php if($authuser->role_id !=6 && $authuser->role_id !=7){
                    if($consignment->invoice_no != null || $consignment->invoice_no != ''){ ?>
                    <a href="{{url($prefix.'/print-sticker/'.$consignment->id)}}" target="_blank"
                        class="badge alert bg-cust shadow-sm">Print Sticker</a> | <a
                        href="{{url($prefix.'/consignments/'.$consignment->id.'/print-viewold/2')}}" target="_blank"
                        class="badge alert bg-cust shadow-sm">Print LR</a>
                    <?php }else{ ?>
                    <a href="{{url($prefix.'/print-sticker/'.$consignment->id)}}" target="_blank"
                        class="badge alert bg-cust shadow-sm">Print Sticker</a> | <a
                        href="{{url($prefix.'/consignments/'.$consignment->id.'/print-view/2')}}" target="_blank"
                        class="badge alert bg-cust shadow-sm">Print LR</a>
                    <?php }} ?>
                </td>

                <?php if($authuser->role_id == 7 || $authuser->role_id == 6 ) { 
                    $disable = 'disable_n'; 
                } elseif($authuser->role_id != 7 || $authuser->role_id != 6){
                    if($consignment->status == 0){ 
                        $disable = 'disable_n';
                    }else{
                        $disable = '';
                    }
                } ?>
                <td>
                    <?php if ($consignment->delivery_status == "Unassigned") { ?>
                    <span class="badge alert bg-primary shadow-sm manual_updateLR {{$disable}}"
                        lr-no="{{$consignment->id}}">{{ $consignment->delivery_status ?? ''}}</span>
                    <?php } elseif ($consignment->delivery_status == "Assigned") { ?>
                    <span class="badge alert bg-secondary shadow-sm manual_updateLR {{$disable}}"
                        lr-no="{{$consignment->id}}">{{ $consignment->delivery_status ?? '' }}</span>
                    <?php } elseif ($consignment->delivery_status == "Started") { ?>
                    <span class="badge alert bg-warning shadow-sm manual_updateLR {{$disable}}"
                        lr-no="{{$consignment->id}}">{{ $consignment->delivery_status ?? '' }}</span>
                    <?php } elseif ($consignment->delivery_status == "Successful") { ?>
                    <span class="badge alert bg-success shadow-sm manual_updateLR"
                        lr-no="{{$consignment->id}}">{{ $consignment->delivery_status ?? '' }}</span>
                    <?php } elseif ($consignment->delivery_status == "Accepted") { ?>
                    <span class="badge alert bg-info shadow-sm" lr-no="{{$consignment->id}}">Acknowledged</span>
                    <?php }elseif ($consignment->delivery_status == "Cancel") { ?>
                    <span class="badge alert bg-info shadow-sm" lr-no="{{$consignment->id}}">Cancel</span>
                    <?php } else{ ?>
                    <span class="badge alert bg-success shadow-sm" lr-no="{{$consignment->id}}">need to update</span>
                    <?php } ?>

                </td>

                <?php if($authuser->role_id == 7 || $authuser->role_id == 6) { 
                    $disable = 'disable_n'; 
                } else{
                    $disable = '';
                } ?>
                <td>
                    <?php if ($consignment->status == 0) { ?>
                    <span class="alert badge alert bg-secondary shadow-sm">Cancel</span>
                    <?php } elseif($consignment->status == 1){
                            if($consignment->delivery_status == 'Successful'){ ?>
                    <a class="alert activestatus btn btn-success disable_n" data-id="{{$consignment->id}}"
                        data-text="consignment" data-status="0"><span><i class="fa fa-check-circle-o"></i>
                            Active</span></a>
                    <?php }else{ ?>
                    <a class="alert activestatus btn btn-success {{$disable}}" data-id="{{$consignment->id}}"
                        data-text="consignment" data-status="0"><span><i class="fa fa-check-circle-o"></i>
                            Active</span></a>
                    <?php }
                        } elseif($consignment->status == 2){ ?>
                    <span class="badge alert bg-success activestatus {{$disable}}"
                        data-id="{{$consignment->id}}">Unverified</span>
                    <?php } elseif($consignment->status == 3){ ?>
                    <span class="badge alert bg-gradient-bloody text-white shadow-sm">Unknown</span>
                    <?php } ?>
                </td>

            </tr>
            <tr id="collapse-{{$consignment->id}}" class="card-body collapse" data-parent="#accordion">
                <td colspan="7">
                    <?php if(!empty($consignment->job_id)){
                        $jobid = $consignment->job_id;
                    }else{
                        $jobid = "Manual";
                    } ?>
                    <div id="tabsIcons" class="col-lg-12 col-12 layout-spacing">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-content widget-content-area icon-tab" style="padding: 0px;">

                                <ul class="nav nav-tabs  mb-3 mt-3" id="iconTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" id="icon-txndetail-tab" data-toggle="tab"
                                            href="#icon-txndetail-{{$consignment->id}}" role="tab"
                                            aria-controls="icon-txndetail" aria-selected="true"> TXN Details</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="icon-timeline-tab" data-toggle="tab"
                                            href="#icon-timeline-{{$consignment->id}}" role="tab"
                                            aria-controls="icon-timeline" aria-selected="false"> Timeline</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="icon-otherdetail-tab" data-toggle="tab"
                                            href="#icon-otherdetail-{{$consignment->id}}" role="tab"
                                            aria-controls="icon-otherdetail" aria-selected="false"> Other Details</a>
                                    </li>

                                </ul>
                                <div class="tab-content" id="iconTabContent-1">
                                    <div class="tab-pane fade show active" id="icon-txndetail-{{$consignment->id}}"
                                        role="tabpanel" aria-labelledby="icon-txndetail-tab">
                                        <div class="row">
                                            <div class="col-md-4">

                                                <table id="" class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td>Delivery Status</td>
                                                            <td><span
                                                                    class="badge bg-info">{{$consignment->delivery_status}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Shadow Job Id</td>
                                                            <td>{{$jobid}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Vehicle No</td>
                                                            <td>{{$consignment->regn_no ?? ''}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Driver Name</td>
                                                            <td>{{$consignment->driver_name ?? ''}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Driver Phone</td>
                                                            <td>{{$consignment->driver_phone ?? ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>No. of Boxes</td>
                                                            <td>{{$consignment->total_quantity ?? ''}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Net Weight</td>
                                                            <td>{{$consignment->total_weight ?? ''}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Gross Weight</td>
                                                            <td>{{$consignment->total_gross_weight ?? ''}}</td>
                                                        </tr>

                                                        <tr>
                                                        <?php if($consignment->is_salereturn ==1){
                                                                    $cnr_nickname_txn = $consignment->ConsigneeDetail->nick_name;
                                                                    $cne_nickname_txn = $consignment->ConsignerDetail->nick_name;
                                                                }else{
                                                                $cnr_nickname_txn = $consignment->ConsignerDetail->nick_name;
                                                                $cne_nickname_txn = $consignment->ConsigneeDetail->nick_name;
                                                                }
                                                                ?>
                                                            <td colspan="2">
                                                                <ul class="ant-timeline mt-3" style="">
                                                                    <li class="ant-timeline-item  css-b03s4t">
                                                                        <div class="ant-timeline-item-tail"></div>
                                                                        <div
                                                                            class="ant-timeline-item-head ant-timeline-item-head-green">
                                                                        </div>
                                                                        <div class="ant-timeline-item-content">
                                                                            <div class="css-16pld72">
                                                                                {{$cnr_nickname_txn ?? ''}}
                                                                            </div>
                                                                            
                                                                        </div>
                                                                    </li>
                                                                    <li
                                                                        class="ant-timeline-item ant-timeline-item-last css-phvyqn">
                                                                        <div class="ant-timeline-item-tail"></div>
                                                                        <div
                                                                            class="ant-timeline-item-head ant-timeline-item-head-red">
                                                                        </div>
                                                                        <div class="ant-timeline-item-content">
                                                                            <div class="css-16pld72">
                                                                                {{$cne_nickname_txn ?? ''}}
                                                                            </div>
                                                                            <?php if($consignment->is_salereturn == 1){ ?>
                                                                            <div class="css-16pld72"
                                                                                style="font-size: 12px; color: rgb(102, 102, 102);">
                                                                                <span>{{$consignment->ConsignerDetail->postal_code}},
                                                                                    {{$consignment->ConsignerDetail->city}},
                                                                                    {{$consignment->ConsignerDetail->district}}</span>
                                                                            </div>
                                                                            <?php }else{?>
                                                                            <div class="css-16pld72"
                                                                                style="font-size: 12px; color: rgb(102, 102, 102);">
                                                                                <span>{{$consignment->ConsigneeDetail->postal_code}},
                                                                                    {{$consignment->ConsigneeDetail->city}},
                                                                                    {{$consignment->ConsigneeDetail->district}}</span>
                                                                            </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-8" id="mapdiv-{{$consignment->id}}">
                                                                                       
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade append-modal" id="icon-timeline-{{$consignment->id}}" role="tabpanel" aria-labelledby="icon-timeline-tab">
                                        <!-- modal code here -->

                                    </div>
                                    <div class="tab-pane fade" id="icon-otherdetail-{{$consignment->id}}"
                                        role="tabpanel" aria-labelledby="icon-otherdetail-tab">
                                        <table class="table table-striped">
                                            <tbody>

                                            <?php
                                                if(empty($consignment->order_id)){ 
                                                    if(!empty($consignment->ConsignmentItems)){
                                                        $order = array();
                                                        $invoices = array();
                                                        foreach($consignment->ConsignmentItems as $orders){ 
                                                            $order[] = $orders->order_id;
                                                            $invoices[] = $orders->invoice_no;
                                                        }
                                                        $order_item['orders'] = implode(',', $order);
                                                        $order_item['invoices'] = implode(',', $invoices);
                                                    ?>
                                            <tr>
                                                <td>Order Number</td>
                                                <td><span class="badge bg-info mt-2">{{ $order_item['orders'] ?? "-" }}</span>
                                                </td>
                                            </tr>
                                            <?php }}else{ ?>
                                                <tr>
                                                    <td>Order Number</td>
                                                    <td><span class="badge bg-info mt-2">{{ $consignment->orders_id ?? "-" }}</span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if(empty($consignment->invoice_no)){ ?>
                                                <tr>
                                                    <td>Invoice Number</td>
                                                    <td>{{ $order_item['invoices'] ?? "-" }}</td>
                                                </tr>
                                            <?php }else{ ?>
                                                <tr>
                                                    <td>Invoice Number</td>
                                                    <td>{{ $consignment->invoice_no ?? "-" }}</td>
                                                </tr>
                                            <?php } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="6" class="text-center">No Record Found </td>
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
            {{$consignments->appends(request()->query())->links()}}
        </nav>
    </div>
</div>