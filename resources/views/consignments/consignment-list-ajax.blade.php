<?php  $authuser = Auth::user(); ?>

@foreach($consignments as $consignment)

    <div id="accordion" class="consignmentItem">

        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 1.5rem">
            <div class="lrDetailBlock">
                <p class="mb-0">
                    <span style="font-size: 12px">
                        LR No: <sapn style="font-size: 15px">{{$consignment->id}}</sapn>
                    </span>
                    <?php
                    $lrStatus = $consignment->status;
                    if ($authuser->role_id == 7 || $authuser->role_id == 6)
                        $disable = 'disable_n';
                    else  $disable = '';
                    ?>

                    @if ($lrStatus == 0)
                        <span class="statusBadge swan-tooltip red notAllowed"
                              data-tooltip="LR cancelled ">Cancelled</span>
                    @elseif($lrStatus == 3)
                        <span class="statusBadge swan-tooltip extra notAllowed"
                              data-tooltip="Unkown status ">Unknown</span>

                    @elseif($lrStatus == 1)
                        @if($consignment->delivery_status == 'Successful')
                            <span class="statusBadge swan-tooltip green notAllowed"
                                  data-tooltip="Already delivered successfully">
                                Active
                            </span>
                        @else
                            <span class="statusBadge activestatus green swan-tooltip {{$disable}}"
                                  data-tooltip="Click to cancel LR" data-id="{{$consignment->id}}"
                                  data-text="consignment" data-status="0">
                            Active
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                        @endif
                    @elseif($lrStatus == 2)
                        <span class="statusBadge activestatus orange swan-tooltip {{$disable}}"
                              data-tooltip="Click to cancel LR"
                              data-id="{{$consignment->id}}">
                            Unverified
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    @endif
                </p>

                <p class="mb-0">Delivery Status:
                    <?php
                    $deliveryStatus = $consignment->delivery_status;
                    if ($authuser->role_id == 7 || $authuser->role_id == 6) {
                        $disable = 'disable_n';
                    } elseif ($authuser->role_id != 7 || $authuser->role_id != 6) {
                        if ($consignment->status == 0) {
                            $disable = 'disable_n';
                        } else {
                            $disable = '';
                        }
                    } ?>

                    @if ($deliveryStatus == "Unassigned")
                        <span class="statusBadge extra shadow-sm manual_updateLR swan-tooltip {{$disable}}"
                              lr-no="{{$consignment->id}}" data-tooltip="Click to cancel LR">
                            {{ $deliveryStatus ?? ''}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    @elseif ($deliveryStatus == "Assigned")
                        <span class="statusBadge orange manual_updateLR swan-tooltip {{$disable}}"
                              lr-no="{{$consignment->id}}" data-tooltip="Click to cancel LR">
                            {{ $deliveryStatus ?? '' }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    @elseif ($deliveryStatus == "Started")
                        <span class="statusBadge extra2 manual_updateLR swan-tooltip {{$disable}}"
                              lr-no="{{$consignment->id}}" data-tooltip="Click to cancel LR">
                            {{ $deliveryStatus ?? '' }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                    @elseif ($deliveryStatus == "Successful")
                        <span class="statusBadge green manual_updateLR swan-tooltip" lr-no="{{$consignment->id}}"
                              data-tooltip="Click to cancel LR">
                            {{ $deliveryStatus ?? '' }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    @elseif ($deliveryStatus == "Accepted")
                        <span class="statusBadge extra3 swan-tooltip"
                              data-tooltip="Click to cancel LR">Acknowledged</span>
                    @elseif ($deliveryStatus == "Cancel")
                        <span class="statusBadge red notAllowed swan-tooltip"
                              data-tooltip="Click to cancel LR">Cancelled</span>
                    @else
                        <span class="statusBadge extra4 swan-tooltip"
                              data-tooltip="Click to cancel LR">Update needed</span>
                    @endif
                </p>

                <p class="mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                         viewBox="0 0 24 24" fill="none" stroke="#34a6f1" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-truck">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    {{$consignment->VehicleDetail->regn_no ?? 'Not Assigned'}}
                </p>

            </div>

            <div class="d-flex orderAndInvoice">
                <div class="d-flex flex-column align-items-center justify-content-center" style="flex: 1">
                    Order No.
                    <span>
                        @if(empty($consignment->order_id))
                            @if(!empty($consignment->ConsignmentItems))
                                <?php
                                $order = array();
                                $invoices = array();
                                foreach ($consignment->ConsignmentItems as $orders) {
                                    $order[] = $orders->order_id;
                                    $invoices[] = $orders->invoice_no;
                                }
                                ?>
                                @foreach($order as $orderItem)
                                    {{ $orderItem ?? "-" }}<br/>
                                @endforeach
                            @endif
                        @else
                            {{ $consignment->order_id ?? "-" }}
                        @endif
                </span>
                </div>

                <div class="d-flex flex-column align-items-center justify-content-center" style="flex: 1">
                    Invoice No.
                    <span>
                        @if(empty($consignment->invoice_no))
                            @foreach($invoices as $invoiceItem)
                                {{ $invoiceItem ?? "-" }}<br/>
                            @endforeach
                        @else
                            {{ $consignment->invoice_no ?? "-" }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="datesBlock">
                <p class="dateItem">
                    <span>LRD: </span>{{ Helper::ShowDayMonthYear($consignment->consignment_date) ?? '-' }}</p>
                <p class="dateItem"><span>EDD: </span>{{ Helper::ShowDayMonthYear($consignment->edd) ?? '-' }}</p>
                <p class="dateItem"><span>ADD: </span>{{ Helper::ShowDayMonthYear($consignment->delivery_date) ?? '-' }}
                </p>
            </div>

            <?php
            if ($consignment->is_salereturn == 1) {
                $cnr_nickname = $consignment->ConsigneeDetail->nick_name;
                $cne_nickname = $consignment->ConsignerDetail->nick_name;
            } else {
                $cnr_nickname = $consignment->ConsignerDetail->nick_name;
                $cne_nickname = $consignment->ConsigneeDetail->nick_name;
            } ?>
            <ul class="ant-timeline" style="flex: 1">
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
                        <div class="css-16pld72 ellipse"
                             style="font-size: 11px; color: rgb(102, 102, 102);">
                            @if($consignment->is_salereturn == '1')
                                <span>{{ $consignment->ConsignerDetail->postal_code ?? "" }},
                                        {{ $consignment->ConsignerDetail->city ?? "" }},
                                        {{ $consignment->ConsignerDetail->district ?? "" }} </span>
                            @else
                                <span>{{ $consignment->ConsigneeDetail->postal_code ?? "" }},
                                            {{ $consignment->ConsigneeDetail->city ?? "" }},
                                            {{ $consignment->ConsigneeDetail->district ?? "" }} </span>
                            @endif
                        </div>
                    </div>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                @if($authuser->role_id != 6 && $authuser->role_id != 7)
                    <a href="{{url($prefix.'/print-sticker/'.$consignment->id)}}"
                       target="_blank" class="actionIcon edit editIcon swan-tooltip" data-tooltip="Print Stickers">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-printer">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Sticker</span>
                    </a>

                    <a target="_blank" class="actionIcon edit editIcon swan-tooltip" data-tooltip="Print LR"
                       @if($consignment->invoice_no != null || $consignment->invoice_no != '')
                       href="{{url($prefix.'/consignments/'.$consignment->id.'/print-viewold/2')}}"
                       @else href="{{url($prefix.'/consignments/'.$consignment->id.'/print-view/2')}}" @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-printer">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path
                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>LR</span>
                    </a>
                @endif

                <a role="menu" data-target="#collapse-{{$consignment->id}}"
                   class="collapsed" id="{{$consignment->id}}" data-toggle="collapse"
                   href="#collapse-{{$consignment->id}}" data-jobid="{{$consignment->job_id}}"
                   data-action="<?php echo URL::to($prefix . '/get-jobs'); ?>"
                   onClick="row_click(this.id,this.getAttribute('data-jobid'),this.getAttribute('data-action'))"
                   aria-expanded="true" aria-controls="defaultAccordionOne">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
            </div>

        </div>


        <div id="collapse-{{$consignment->id}}" class="collapse" aria-labelledby="..." data-parent="#accordion">
            <div class="consignmentDetailBlock">
                <?php if (!empty($consignment->job_id)) {
                    $jobid = $consignment->job_id;
                } else {
                    $jobid = "Manual";

                } ?>

                <ul class="tabContainer nav nav-pills" id="driverList" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="transactionDetailTab-{{$consignment->id}}" data-toggle="pill"
                           href="#transactionDetailsTab-{{$consignment->id}}" role="tab"
                           aria-controls="transactionDetailsTab-{{$consignment->id}}"
                           aria-selected="true">
                            Transaction Detail
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="timelineTab-{{$consignment->id}}" data-toggle="pill"
                           href="#consignmentTimelineTab-{{$consignment->id}}" role="tab"
                           aria-controls="consignmentTimelineTab-{{$consignment->id}}"
                           aria-selected="true">
                            Timeline
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="otherDetailTab-{{$consignment->id}}" data-toggle="pill"
                           href="#otherDetailsTab-{{$consignment->id}}" role="tab"
                           aria-controls="otherDetailsTab-{{$consignment->id}}"
                           aria-selected="true">
                            Other Detail
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="driverListContent">
                    <div class="tab-pane fade show active" id="transactionDetailsTab-{{$consignment->id}}"
                         role="tabpanel"
                         aria-labelledby="transactionDetailTab-{{$consignment->id}}">
                        <div class="row" style="width: 100%; margin: 0">
                            <div class="col-md-4 p-0">
                                <div class="taskContainer taskDetailContainer" style="height: calc(100% - 2rem)">
                                    <p><span class="dHeading">Shadow Job Id:</span><span
                                            class="dDescription">{{$jobid}}</span></p>
                                    <p><span class="dHeading">Driver Name:</span><span
                                            class="dDescription">{{$consignment->driver_name ?? '-'}}</span></p>
                                    <p><span class="dHeading">Driver Phone:</span><span
                                            class="dDescription">{{$consignment->driver_phone ?? '-'}}</span></p>
                                    <p><span class="dHeading">No. of Boxes:</span><span
                                            class="dDescription">{{$consignment->total_quantity ?? '-'}}</span></p>
                                    <p><span class="dHeading">Net Weight:</span><span
                                            class="dDescription">{{$consignment->total_weight ?? '-'}}</span></p>
                                    <p><span class="dHeading">Gross Weight:</span><span
                                            class="dDescription">{{$consignment->total_gross_weight ?? '-'}}</span></p>
                                </div>
                            </div>
                            <div class="col-md-8 p-0">
                                <div class="taskContainer taskDetailContainer p-0" id="mapdiv-{{$consignment->id}}"
                                     style="height: 350px; overflow: hidden">

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="consignmentTimelineTab-{{$consignment->id}}" role="tabpanel"
                         aria-labelledby="timelineTab-{{$consignment->id}}">
                        <div class="p-3" style="width: 100%">
                            <div id="icon-timeline-{{$consignment->id}}" class="append-modal">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade " id="otherDetailsTab-{{$consignment->id}}" role="tabpanel"
                         aria-labelledby="otherDetailTab-{{$consignment->id}}">

                        @if(!empty($consignment->ConsignmentItems))
                            <?php
                            $order = array();
                            $invoices = array();
                            foreach ($consignment->ConsignmentItems as $orders) {
                                $order[] = $orders->order_id;
                                $invoices[] = $orders->invoice_no;
                            }
                            ?>
                        @endif

                        <div class="otherDetailsBlock d-flex align-items-center">
                            <div class="detailsCol d-flex flex-column align-items-center justify-content-center">
                                <span class="heading">Order No</span>
                                @if(empty($consignment->order_id))
                                    @foreach($order as $orderItem)
                                        <span>{{ $orderItem ?? "-" }}</span>
                                    @endforeach
                                @else
                                    {{ $consignment->order_id ?? "-" }}
                                @endif
                            </div>
                            <div class="detailsCol d-flex flex-column align-items-center justify-content-center"
                                 style="border-left: 1px solid">
                                <span class="heading">Invoice No</span>
                                @if(empty($consignment->invoice_no))
                                    @foreach($invoices as $invoiceItem)
                                        <span>{{ $invoiceItem ?? "-" }}</span>
                                    @endforeach
                                @else
                                    {{ $consignment->invoice_no ?? "-" }}
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endforeach

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 d-flex align-items-center" style="gap: 10px">
            <label class="mb-0" style="width: 120px">items per page</label>
            <select style="width: 100px" class="form-control perpage" data-action="<?php echo url()->current(); ?>">
                <option value="10" {{$peritem == '10' ? 'selected' : ''}}>10</option>
                <option value="50" {{$peritem == '50' ? 'selected' : ''}}>50</option>
                <option value="100" {{$peritem == '100'? 'selected' : ''}}>100</option>
            </select>
        </div>

        <div class="col-md-8 d-flex align-items-center justify-content-end" style="gap: 10px">
            <nav class="navigation2 text-center" aria-label="Page navigation">
                {{$consignments->appends(request()->query())->links()}}
            </nav>
        </div>
    </div>
</div>


