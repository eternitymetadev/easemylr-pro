<?php  $authuser = Auth::user(); ?>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
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
        <tbody>
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <tr>
                <td class="">
                <span class="d-flex align-items-center"><i class="fa fa-plus orange-text mr-1 list-collapse collapsed" data-toggle="collapse" data-target="#{{$consignment->id}}"></i></span>
                </td>
                <td>
                    <div class="">
                        <div class=""><span style="color:#4361ee;">LR No: </span>{{$consignment->id}}<span class="badge bg-cust">{{ $consignment->VehicleDetail->regn_no ?? " " }}<span></span></span></div>
                        
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

                        <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Order No: </span>{{ $order_item['orders'] ?? "-" }}</div>
                        <?php }}else{ ?>
                            <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Order No: </span>{{ $consignment->order_id ?? "-" }}</div>
                        <?php } ?>
                        <?php if(empty($consignment->invoice_no)){ ?>
                        <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Invoice No: </span>{{ $order_item['invoices'] ?? "-" }}</div>
                        <?php }else{ ?>
                            <div class="css-16pld73 ellipse2"><span style="color:#4361ee;">Invoice No: </span>{{ $consignment->invoice_no ?? "-" }}</div>
                        <?php } ?>
                        
                    </div>
                </td>
                <td>
                    <ul class="ant-timeline">
                        <li class="ant-timeline-item  css-b03s4t">
                            <div class="ant-timeline-item-tail"></div>
                            <div class="ant-timeline-item-head ant-timeline-item-head-green"></div>
                            <div class="ant-timeline-item-content">
                                <div class="css-16pld72 ellipse">
                                    {{ $consignment->ConsignerDetail->nick_name ?? "-" }}
                                </div>
                            </div>
                        </li>
                        <li class="ant-timeline-item ant-timeline-item-last css-phvyqn">
                            <div class="ant-timeline-item-tail"></div>
                            <div class="ant-timeline-item-head ant-timeline-item-head-red"></div>
                            <div class="ant-timeline-item-content">
                                <div class="css-16pld72 ellipse">
                                    {{ $consignment->ConsigneeDetail->nick_name ?? "-" }} 
                                </div>
                                <div class="css-16pld72 ellipse" style="font-size: 12px; color: rgb(102, 102, 102);"><span>{{ $consignment->ConsigneeDetail->postal_code ?? "" }}, {{ $consignment->ConsigneeDetail->city ?? "" }}, {{ $consignment->ConsigneeDetail->district ?? "" }} </span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </td>
                <td>
                    <div class="">
                        <div class=""><span style="color:#4361ee;">LRD: </span>{{ Helper::ShowDayMonthYear($consignment->consignment_date) ?? '-' }}</div>
                        <div class=""><span style="color:#4361ee;">EDD: </span>{{ Helper::ShowDayMonthYear($consignment->edd) ?? '-' }}</div>
                        <div class=""><span style="color:#4361ee;">ADD: </span>{{ Helper::ShowDayMonthYear($consignment->delivery_date) ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <?php if($authuser->role_id !=6 && $authuser->role_id !=7){
                    if($consignment->invoice_no != null || $consignment->invoice_no != ''){ ?>
                        <a href="{{url($prefix.'/print-sticker/'.$consignment->id)}}" target="_blank" class="badge alert bg-cust shadow-sm">Print Sticker</a> | <a href="{{url($prefix.'/consignments/'.$consignment->id.'/print-viewold/2')}}" target="_blank" class="badge alert bg-cust shadow-sm">Print LR</a>
                    <?php }else{ ?>
                        <a href="{{url($prefix.'/print-sticker/'.$consignment->id)}}" target="_blank" class="badge alert bg-cust shadow-sm">Print Sticker</a> | <a href="{{url($prefix.'/consignments/'.$consignment->id.'/print-view/2')}}" target="_blank" class="badge alert bg-cust shadow-sm">Print LR</a>
                    <?php }} ?>
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
                                <a class="alert activestatus btn btn-success disable_n"  data-id = "'.$consignment->id.'" data-text="consignment" data-status = "0"><span><i class="fa fa-check-circle-o"></i> Active</span></a>
                            <?php }else{ ?>
                                <a class="alert activestatus btn btn-success '.$disable.'"  data-id = "'.$consignment->id.'" data-text="consignment" data-status = "0"><span><i class="fa fa-check-circle-o"></i> Active</span></a>
                            <?php }
                        } elseif($consignment->status == 2){ ?>
                                <span class="badge alert bg-success activestatus '.$disable.'" data-id = "'.$consignment->id.'">Unverified</span>
                        <?php } elseif($consignment->status == 3){ ?>
                            <span class="badge alert bg-gradient-bloody text-white shadow-sm">Unknown</span>
                        <?php } ?>
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
                        <span class="badge alert bg-primary shadow-sm manual_updateLR '.$disable.'" lr-no = "{{$consignment->id}}">{{ $consignment->delivery_status ?? ''}}</span>
                        <?php } elseif ($consignment->delivery_status == "Assigned") { ?>
                            <span class="badge alert bg-secondary shadow-sm manual_updateLR '.$disable.'" lr-no = "{{$consignment->id}}">{{ $consignment->delivery_status ?? '' }}</span>
                            <?php } elseif ($consignment->delivery_status == "Started") { ?>
                                <span class="badge alert bg-warning shadow-sm manual_updateLR '.$disable.'" lr-no = "{{$consignment->id}}">{{ $consignment->delivery_status ?? '' }}</span>
                                <?php } elseif ($consignment->delivery_status == "Successful") { ?>
                                    <span class="badge alert bg-success shadow-sm manual_updateLR" lr-no = "{{$consignment->id}}">{{ $consignment->delivery_status ?? '' }}</span>
                                    <?php } elseif ($consignment->delivery_status == "Accepted") { ?>
                                        <span class="badge alert bg-info shadow-sm" lr-no = "{{$consignment->id}}">Acknowledged</span>
                                        <?php } else{ ?>
                                            <span class="badge alert bg-success shadow-sm" lr-no = "{{$consignment->id}}">need to update</span>
                                            <?php } ?>
                    
                </td>
            </tr>
            <tr class="collapse_pdgremove">
                <td colspan="7">
                    <div id="{{$consignment->id}}" class="collapse reservationid_table" aria-expanded="false">
                        <table class="table w-100">
                            <tbody>
                                <tr>
                                    <th class="text-left" style="max-width:100px; width:130px;">Wine Name</th>
                                    <th class="text-center">Vintage</th>
                                    <th class="text-center">Bottle Size</th>
                                    <th class="text-center">Seller</th>
                                    <th class="text-center">Selling Broker</th>
                                    <th class="text-center">Buyer</th>
                                    <th class="text-center">Buying Broker</th>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td class="text-left">Errazuriz-Mondavi Sena</td>
                                    <td class="text-center">2016</td>
                                    <td class="text-center">750 ML</td>
                                    <td class="text-center">Marco Bianchi</td>

                                    <td class="text-center">Nicola Merighi</td>
                                    <td class="text-center">Uno Holding Company Limited</td>
                                    <td class="text-center">Jack Toghli</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
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