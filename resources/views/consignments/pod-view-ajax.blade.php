<p class="totalcount px-3"><strong>Total Count: <span class="reportcount">{{$consignments->total()}}</span></strong></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
        <tr>
            <th>Branch</th>
            <th>LR</th>
            <th>Client</th>
            <th>Order No</th>
            <th>Invoice No</th>
            <th style="text-align: center; width: 120px;">Delivery Status</th>
            <th style="text-align: center; width: 120px;">Delivery Date</th>
            <th>POD</th>
            {{--            <th>Images</th>--}}
        </tr>
        </thead>
        <tbody>
        @if(count($consignments)>0)
            @foreach($consignments as $consignment)
                <?php

                $start_date = strtotime($consignment->consignment_date);
                $end_date = strtotime($consignment->delivery_date);
                $tat = ($end_date - $start_date) / 60 / 60 / 24;
                ?>
                <tr>
                    <td>{{$consignment->Branch->name ?? '-'}}</td>

                    <td>
                        <p>
                            <span style="font-weight: 700; font-size: 14px; color: #000">
                                {{ $consignment->id ?? "-" }}
                            </span>
                            @if ($consignment->status == 0)
                                <label class="lrStatus" style="background: #bb2727">Cancel</label>
                            @elseif ($consignment->status == 1)
                                <label class="lrStatus" style="background: #087408">Active</label>
                            @elseif ($consignment->status == 2)
                                <label class="lrStatus" style="background: #e2a03f">Unverified</label>
                            @endif
                            <br/>
                            Dated: {{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}
                        </p>
                    </td>

                    <td>
                        <p class="textWrap" style="max-width: 240px; font-size: 11px; text-transform: capitalize">
                            <span style="color: #000; font-weight: 700; font-size: 13px">
                                {{ $consignment->ConsignerDetail->GetRegClient->BaseClient->client_name ?? "-" }}
                            </span>
                            <br/>
                            Regional:
                            <span style="color: #000; font-weight: 700; font-size: 13px">
                                {{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}
                            </span>
                        </p>
                    </td>

                    @if (empty($consignment->order_id))
                        @if (!empty($consignment->ConsignmentItems))
                            <?php
                            $order = array();
                            $invoices = array();
                            $inv_date = array();
                            $inv_amt = array();
                            foreach ($consignment->ConsignmentItems as $orders) {
                                $order[] = $orders->order_id;
                                $invoices[] = $orders->invoice_no;
                                $inv_date[] = Helper::ShowDayMonthYearslash($orders->invoice_date);
                                $inv_amt[] = $orders->invoice_amount;
                            }
                            //echo'<pre>'; print_r($order); die;
                            $order_item['orders'] = implode(',', $order);
                            $order_item['invoices'] = implode(', ', $invoices);
                            $invoice['date'] = implode(',', $inv_date);
                            $invoice['amt'] = implode(',', $inv_amt);
                            ?>
                            <td>{{ $orders->order_id ?? "-" }}</td>
                        @else
                            <td>-</td>
                        @endif
                    @else
                        <td>{{ $consignment->order_id ?? "-" }}</td>
                    @endif

                    <td>
                        <p class="textWrap" style="max-width: 140px">
                            @if (empty($consignment->invoice_no))
                                {{ $order_item['invoices'] ?? "-" }}
                            @else
                                {{ $consignment->invoice_no ?? "-" }}
                            @endif
                        </p>

                        @if(count($invoices) > 1)
                            <span class="viewAllInvoices">
                                more
                                <span class="moreInvoicesView">
                                  <ul style="padding: 0; margin-bottom: 0;">
                                      @foreach($invoices as $invoiceId)
                                          <li style="margin-bottom: 8px">{{$invoiceId}}</li>
                                      @endforeach
                                  </ul>
                                </span>
                            </span>
                        @endif
                    </td>

                    <td style="width: 120px;">
                        <?php $dlStatus = $consignment->delivery_status ?>
                        <p style="font-size: 11px; text-align: center">
                            Mode:
                            @if ($consignment->job_id == '')
                                <span class="dlMode" style="color: #009a10">Manual</span>
                            @else
                                <span class="dlMode" style="color: #005892">Shadow</span>
                                @endif
                                </span>
                                <br/>
                                @if ($dlStatus == 'Assigned')
                                    <label class="statusLabel mt-1" style="background: #f9b808">Assigned</label>
                                @elseif ($dlStatus == 'Unassigned')
                                    <label class="statusLabel mt-1" style="background: #e2a03f">Unassigned</label>
                                @elseif ($dlStatus == 'Started')
                                    <label class="statusLabel mt-1" style="background: #1abc9c">Started</label>
                                @elseif ($dlStatus == 'Successful')
                                    <label class="statusLabel mt-1" style="background: #087408">Successful</label>
                                @elseif ($dlStatus == 'Cancel')
                                    <label class="statusLabel mt-1" style="background: #bb2727">Cancel</label>
                                @else
                                    <label class="statusLabel mt-1" style="background: #805dca">Unknown</label>
                                @endif
                        </p>
                    </td>

                    <td style="text-align: center; width: 120px;">{{ Helper::ShowDayMonthYearslash($consignment->delivery_date )}}</td>


                    <?php if (!empty($consignment->job_id)) {
                        $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id', 'desc')->first();
                        if (!empty($job->response_data)) {
                            $trail_decorator = json_decode($job->response_data);
                            $img_group = array();
                            foreach ($trail_decorator->task_history as $task_img) {
                                if ($task_img->type == 'image_added') {
                                    $img_group[] = $task_img->description;
                                }
                            }
                        }
                    }?>

                    <?php if (empty($consignment->job_id)) {
                    $img = URL::to('/drs/Image/' . $consignment->signed_drs)?>

                    <td style="max-width: 260px; vertical-align: middle">
                        <?php if (!empty($consignment->signed_drs)) {?>
                        <div class="d-flex flex-wrap" style="gap: 4px; width: 220px;">
                            <img src="{{$img}}" class="viewImageInNewTab"
                                 style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;"/>
                        </div>
                        <?php } else {?>
                        <div style="min-height: 50px" class="d-flex align-items-center">
                            Not Available
                        </div>
                        <?php }?>
                    </td>
                    <?php } else {?>
                    <td style="max-width: 260px">
                        <?php if(!empty($img_group)){ ?>
                        <div class="d-flex flex-wrap" style="gap: 4px; width: 220px;">
                            @foreach($img_group as $img)
                                <img src="{{$img}}" class="viewImageInNewTab" data-toggle="modal"
                                     data-target="#exampleModal"
                                     style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;"/>
                            @endforeach
                        </div>
                        <?php }else{?>
                        <div style="min-height: 50px" class="d-flex align-items-center">
                            Not Available
                        </div>
                        <?php }?>
                    </td>
                    <?php }?>

                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12" class="text-center">No Record Found</td>
            </tr>
        @endif
        </tbody>
    </table>


    <div class="px-3 pt-4 d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <label class=" mb-0">items per page</label>
            <select style="width: 100px; height: 38px; padding: 0 1.5rem 0 0.5rem" class="form-control report_perpage"
                    data-action="<?php echo url()->current(); ?>">
                <option value="10" {{$peritem == '10' ? 'selected' : ''}}>10</option>
                <option value="50" {{$peritem == '50' ? 'selected' : ''}}>50</option>
                <option value="100" {{$peritem == '100'? 'selected' : ''}}>100</option>
            </select>
        </div>

        <div>
            <nav class="navigation2 text-center" aria-label="Page navigation">
                {{$consignments->appends(request()->query())->links()}}
            </nav>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: max-content">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="#" id="toggledImageView" style="max-height: 90vh; max-width: 90vw"/>
                </div>
            </div>
        </div>
    </div>
</div>
