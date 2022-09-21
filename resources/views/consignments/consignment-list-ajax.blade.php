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
                <td class=" dt-control"></td>
                <td>
                    <div class="">
                        <div class=""><span style="color:#4361ee;">LR No: </span>{{ $consignment->id ?? "-" }}<span class="badge bg-cust">{{ $consignment->VehicleDetail->regn_no ?? " " }}<span></span></span></div>
                        
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
                <td></td>
                <td></td>
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