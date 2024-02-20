<div class="d-flex justify-content-between">
    <p class="totalcount px-3">
        <strong>Total Count: <span class="reportcount">{{$consignments->total()}}</span></strong>
    </p>
</div>

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
                <th>User Id</th>
                <th class="text-center">POD</th>
            </tr> 
        </thead>
        <tbody>
            <?php $authuser = Auth::user();  ?>
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
                        <label class="lrStatus" style="background: #f40404">Cancel</label>
                        @elseif ($consignment->status == 1)
                        <label class="lrStatus" style="background: #087408">Active</label>                        
                        @endif
                        <br />
                        Dated: {{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}
                    </p>
                </td>

                <td>
                    <p class="textWrap" style="max-width: 240px; font-size: 11px; text-transform: capitalize">
                        <span style="color: #000; font-weight: 700; font-size: 13px">
                            {{ $consignment->ConsignerDetail->GetRegClient->BaseClient->client_name ?? "-" }}
                        </span>
                        <br />
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
                    <?php $dlStatus = $consignment->delivery_status?>
                    <p style="font-size: 11px; text-align: center">
                        Mode:
                        @if ($consignment->lr_mode == 0)
                        <a class="swan-tooltip-right dlMode notAllowed" data-tooltip="Already in manual mode"
                            style="color: #009a10">Manual</a>
                        @elseif($consignment->lr_mode == 1)
                        <a class="dlMode change_mode swan-tooltip-right pointer" data-tooltip="Click to change mode"
                            style="color: #005892" data-id="{{$consignment->id}}">Shadow</a>
                            @else
                            <a class="dlMode change_mode swan-tooltip-right pointer" data-tooltip="Click to change mode"
                            style="color: #ff3c07" data-id="{{$consignment->id}}">ShipRider</a>
                        @endif
                        </span>
                        <br />
                        @if ($dlStatus == 'Assigned')
                        <label class="statusLabel mt-1" style="background: #f9b808">Assigned</label>
                        @elseif ($dlStatus == 'Unassigned')
                        <label class="statusLabel mt-1" style="background: #e2a03f">Unassigned</label>
                        @elseif ($dlStatus == 'Started')
                        <label class="statusLabel mt-1" style="background: #1abc9c">Started</label>
                        @elseif ($dlStatus == 'Successful')
                        <label class="statusLabel mt-1" style="background: #087408">Successful</label>
                        @elseif ($dlStatus == 'Cancel')
                        <label class="statusLabel mt-1" style="background: #f40404">Cancel</label>
                        @else
                        <label class="statusLabel mt-1" style="background: #805dca">Unknown</label>
                        @endif
                    </p>
                </td>

                <td style="text-align: center; width: 120px;">
                    {{ Helper::ShowDayMonthYearslash($consignment->delivery_date )}}</td>
                <td>{{@$consignment->User->login_id}}</td>

                <?php if ($consignment->lr_mode == 1) {
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

                <?php if ($consignment->lr_mode == 0) {
                    // old path ('/drs/Image/' . $consignment->signed_drs);

                    $awsUrl = env('AWS_S3_URL');
                    $img = $awsUrl.'/pod_images/' . $consignment->signed_drs;

                    $pdfcheck = explode('.',$consignment->signed_drs);
                ?>

                <td style="max-width: 260px; vertical-align: middle">
                    @if (!empty($consignment->signed_drs))
                    <div class="d-flex align-items-center">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 4px; width: 220px; background: #f1f1f1; border-radius: 6px; padding: 5px">
                        @if(@$pdfcheck[1] == 'pdf')
                            <img src="{{asset('assets/img/unnamed.png')}}" pdf-nm="{{$img}}" class="viewpdfInNewTab" data-toggle="modal"
                                data-target="#exampleModalPdf"
                                style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;" />
                        @else
                        <img src="{{$img}}"  class="viewImageInNewTab" data-toggle="modal"
                                data-target="#exampleModal"
                                style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;" />  
                        @endif
                        </div>
                        {{-- delete image of pods //its working delte pod commented now--}}
                        <?php if($authuser->role_id == 8){ ?>
                            <a class="edit @if($consignment->status == 1 && ($consignment->delivery_status == 'Started' || $consignment->delivery_status == 'Successful')) editButtonimg @endif editIcon swan-tooltip-left" 
                                data-tooltip="@if($consignment->status == 1 && ($consignment->delivery_status == 'Started' || $consignment->delivery_status == 'Successful')) Add Images @else Need to update status @endif" 
                                data-id="{{$consignment->id}}" lr-date="{{$consignment->consignment_date}}" data-deliverydate="{{$consignment->delivery_date}}" data-roleId={{$authuser->role_id}} >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-edit-2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                </a>
                            <a class="delete deleteIcon deletePod swan-tooltip-left" data-tooltip="Delete Images"
                                data-id="{{$consignment->id}}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </a>
                        <?php } ?>
                    </div>
                    @else
                    <div style="min-height: 50px" class="d-flex align-items-center">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 4px; width: 220px; background: #f1f1f1; border-radius: 6px; padding: 5px">
                            Not Available
                        </div> 
                        <a class="edit @if($consignment->status == 1 && ($consignment->delivery_status == 'Started' || $consignment->delivery_status == 'Successful')) editButtonimg @endif editIcon swan-tooltip-left" 
                        data-tooltip="@if($consignment->status == 1 && ($consignment->delivery_status == 'Started' || $consignment->delivery_status == 'Successful')) Add Images @else Need to update status @endif" 
                        data-id="{{$consignment->id}}" lr-date="{{$consignment->consignment_date}}" data-deliverydate="{{$consignment->delivery_date}}" data-roleId={{$authuser->role_id}}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit-2">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                        </a>
                    </div>
                    @endif
                </td>
                <?php } else if($consignment->lr_mode == 1){ ?>
                    <td style="max-width: 260px">
                    <?php if (!empty($img_group)) {?>
                    <div class="d-flex align-items-center" style="gap: 4px; width: 250px;">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 4px; width: 220px; background: #f1f1f1; border-radius: 6px; padding: 5px">
                            @foreach($img_group as $img)
                            <img src="{{$img}}" class="viewImageInNewTab" data-toggle="modal"
                                data-target="#exampleModal"
                                style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;" />
                            @endforeach
                        </div>
                    </div>
                    <?php } else {?>
                    <div style="min-height: 50px" class="d-flex align-items-center">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 4px; width: 220px; background: #f1f1f1; border-radius: 6px; padding: 5px">
                            Not Available 
                        </div>

                        <a class="edit editIcon notAllowed swan-tooltip-left" data-tooltip="First change mode to manual">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit-2">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                        </a>
                    </div>
                    <?php }?>
                </td>
               <?php }else {?>
                <td style="max-width: 260px">
                 <?php
                 $getjobimg = DB::table('app_media')->where('consignment_no', $consignment->id)->get();
                 $count_arra = count($getjobimg);
                    ?>
                    <?php if ($count_arra > 1) { ?>
                    <div class="d-flex align-items-center" style="gap: 4px; width: 250px;">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 4px; width: 220px; background: #f1f1f1; border-radius: 6px; padding: 5px">
                            @foreach($getjobimg as $img)
                            <img src="{{$img->pod_img}}" class="viewImageInNewTab" data-toggle="modal"
                                data-target="#exampleModal"
                                style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;" />
                            @endforeach
                        </div>
                    </div>
                    <?php } else {?>
                    <div style="min-height: 50px" class="d-flex align-items-center">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 4px; width: 220px; background: #f1f1f1; border-radius: 6px; padding: 5px">
                            Not Available 
                        </div>

                        <a class="edit editIcon notAllowed swan-tooltip-left" data-tooltip="First change mode to manual">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit-2">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                        </a>
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