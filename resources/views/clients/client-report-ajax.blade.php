<p class="totalcount">Total Count: <span class = "reportcount">{{$consignments->total()}}</span></p>
<div class="custom-table">
    <table class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>LR No</th>
                <th>LR Date</th>
                <th>Order No</th>
                <th>Base Client</th>
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
                <th>Boxes</th>
                <th>Net Weight</th>
                <th>Gross Weight</th>
                <th>LR Status</th>
                <th>Dispatch Date</th>
                <th>Delivery Date</th>
                <th>Delivery Status</th>
                <th>TAT</th>
                <th>Average Weight Per Carton</th>
                <th>Check 1 - CFT 5 KGs</th>
                <th>Check 2 - Per Shipment 25 Kgs MOQ</th>
                <th>Final Chargeable Weight Check2</th>
                <th>Final Chargeable Weight Check1</th>
                <th>Final </th>
                <th>Per Kg Rate</th>
                <th>Per Kg Rate - 3.80</th>
                <th>Open Delivery Charges Intra & Inter State</th>
                <th>Docket Charges</th>
                <th>Final Freight Amount</th>                
            </tr>
        </thead>

        <tbody>
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <?php
                $start_date = strtotime($consignment->consignment_date);
                $end_date = strtotime($consignment->delivery_date);
                $tat = ($end_date - $start_date)/60/60/24;
                $cnr_state = @$consignment->ConsignerDetail->Zone->state;
                $cnee_state = @$consignment->ConsigneeDetail->Zone->state;
                //echo $cstate;die;
            ?>
            <tr>
                <td>{{ $consignment->id ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}</td>
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
                
                        $order_item['orders'] = implode(',', $order);
                        $order_item['invoices'] = implode(',', $invoices);
                        $invoice['date'] = implode(',', $inv_date);
                        $invoice['amt'] = implode(',', $inv_amt); ?>

                        <td>{{ $orders->order_id ?? "-" }}</td>

                    <?php }else{ ?>
                        <td>-</td>
                    <?php } 
                }else{ ?>
                    <td>{{ $consignment->order_id ?? "-" }}</td>
                <?php  } ?>
                    <td>{{ $consignment->ConsignerDetail->GetRegClient->BaseClient->client_name ?? "-" }}</td>
                    <td>{{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                    <td>{{ $consignment->ConsignerDetail->nick_name ?? "-" }}</td>
                    <td>{{ $consignment->ConsignerDetail->city ?? "-" }}</td>
                    <td>{{ $consignment->ConsigneeDetail->nick_name ?? "-" }}</td>
                    <td>{{ $consignment->ConsigneeDetail->city ?? "-" }}</td>
                    <td>{{ $consignment->ConsigneeDetail->postal_code ?? "-" }}</td>
                    <td>{{ @$consignment->ConsigneeDetail->Zone->district ?? "-" }}</td>
                    <td>{{ @$consignment->ConsigneeDetail->Zone->state ?? "-" }}</td>
                    
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
                        <?php }else{?>
                            <td>Unknown</td>
                        <?php }?>
                        <?php if($consignment->delivery_date == ''){?>
                            <td> - </td>
                        <?php }else{?>
                            <td>{{ $tat }}</td>
                        <?php } ?>

                        <?php 
                        if($consignment->total_quantity>0){
                            $avg_wt_per_carton = $consignment->total_gross_weight/$consignment->total_quantity;
                        }else{
                            $avg_wt_per_carton = 0;
                        } ?>

                        <td>{{ number_format($avg_wt_per_carton,2)}}</td>
                        <?php
                        if($avg_wt_per_carton > 5){
                            $check_cft_kgs = $avg_wt_per_carton;
                        } else{
                            $check_cft_kgs = 5;
                        } ?>

                        <td>{{ number_format($check_cft_kgs,2) }} </td>
                        <?php
                        if($consignment->total_gross_weight > 25){
                            $check_per_shipment_kgs_moq = $consignment->total_gross_weight;
                        } else{
                            $check_per_shipment_kgs_moq = 25;
                        } ?>

                        <td>{{ $check_per_shipment_kgs_moq }} </td>

                        <?php
                        if($check_per_shipment_kgs_moq > $consignment->total_gross_weight){
                            $final_chargeable_weight_check2 = $check_per_shipment_kgs_moq;
                        } else{
                            $final_chargeable_weight_check2 = $consignment->total_gross_weight;
                        } ?>
                        <td>{{ $final_chargeable_weight_check2 }} </td>

                        <?php
                        if($check_per_shipment_kgs_moq > $check_cft_kgs){
                            $final_chargeable_weight_check1 = $check_per_shipment_kgs_moq;
                        } else{
                            $final_chargeable_weight_check1 = $check_cft_kgs;
                        } ?>
                        <td> {{ $final_chargeable_weight_check1 }} </td>

                        <?php
                        if($final_chargeable_weight_check1 > $final_chargeable_weight_check2){
                            $final = $final_chargeable_weight_check1;
                        } else{
                            $final = $final_chargeable_weight_check2;
                        } ?>
                        <td>{{ $final}} </td>

                        <?php
                        $data = DB::table('client_price_details')->select('from_state','to_state','price_per_kg','open_delivery_price')->where('from_state',$cnr_state)->where('to_state',$cnee_state)->first();
                        if(isset($data->price_per_kg)){
                            $price_per_kg = $data->price_per_kg;
                        }else{
                            $price_per_kg = 0;
                        }
                        ?>

                        <td>{{ $price_per_kg }} </td>
                        <?php
                        $perkg_rate3 = (int)$final_chargeable_weight_check2 * (int)$price_per_kg;
                        if(isset($perkg_rate3)){
                            $perkg_rate3 = $perkg_rate3;
                        } else{
                            $perkg_rate3 = 0;
                        }?>
                        <td>{{ $perkg_rate3 ?? '0'}} </td>

                        <?php 
                        if(isset($data->open_delivery_price)){
                            $open_del_charge = $data->open_delivery_price;
                        }else{
                            $open_del_charge = 0; 
                        }
                        ?>
                        <td>{{  $open_del_charge }} </td>

                        <?php
                        if(isset($consignment->RegClientdetail)){
                            $docket_price = (int)$consignment->RegClientdetail->docket_price;
                        }else{
                            $docket_price = 0;
                        }
                        ?>
                        <td>{{$docket_price}} </td>

                        <?php 
                            $final_freight_amt = $perkg_rate3+$open_del_charge+$docket_price;
                        ?>
                        <td>{{$final_freight_amt}} </td>
                    </tr>
                @endforeach
            @else
            <tr>
                <td colspan="15" class="text-center">No Record Found </td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="container-fluid perpage">
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