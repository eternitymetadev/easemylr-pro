<p class="totalcount">Total Count: <span class="reportcount">{{$consignments->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>LR Date</th>
                <th>LR Number</th>
                <th>Regional Client</th>
                <th>Consigner</th>
                <th>Consigner District</th>
                <th>Consigner Pincode</th>
                <th>Consignee</th>
                <th>Consignee District</th>
                <th>Consignee Pincode</th>
           
                <th>Number of Box</th>
                <th>Net Weight</th>
                <th>Gross Weight</th>
                
                <th>Expected TAT</th>
                <th>Payment Type</th>
                <th>Dispatch Date</th>
                <th>Shipment Status</th>
                <th>Ageing</th>
                <th>Delivery Date</th>

                <th>Issue</th>
                
                
            </tr>
        </thead>
        <tbody>
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <?php
      
            $start_date = strtotime($consignment->consignment_date);
            $end_date = strtotime($consignment->edd);
            $tat_diff = ($end_date - $start_date)/60/60/24;
            if($tat_diff < 0){
                $tat = '-';
            }else{
                $tat = $tat_diff;
            }

            $start_date = strtotime($consignment->consignment_date);
            $end_date = strtotime($consignment->delivery_date);
            $age_diff = ($end_date - $start_date)/60/60/24;

            $prspickup_date = strtotime(@$consignment->PrsDetail->PrsDriverTask->pickup_date);
            $pickup_diff = ($end_date - $prspickup_date)/60/60/24;
            
            if(!empty($consignment->prs_id)){
                if($pickup_diff > 0){
                    $ageing_day = $pickup_diff;
                }else{
                    if($age_diff < 0){
                        $ageing_day = '-';
                    }else{
                        $ageing_day = $age_diff;
                    }
                }
            }else{
                if($age_diff < 0){
                    $ageing_day = '-';
                }else{
                    $ageing_day = $age_diff;
                }
            }
        ?>
            <tr>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}</td>
                <td>{{ $consignment->id ?? "-" }}</td>
                                    
                <td>{{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->district ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->postal_code ?? "-" }}</td>

                <td>{{ $consignment->ConsigneeDetail->nick_name ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->district ?? "-" }}</td>
                <td>{{ $consignment->ConsigneeDetail->postal_code ?? "-" }}</td>

                <td>{{ $consignment->total_quantity ?? "-" }}</td>
                <td>{{ $consignment->total_weight ?? "-" }}</td>
                <td>{{ $consignment->total_gross_weight ?? "-" }}</td>
                
                <td>{{$tat ?? "-"}}</td>
                <td>{{$consignment->payment_type ?? "-"}}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}</td>

                <td>{{@$consignment->delivery_status ?? 'Unknown'}}</td>
                <td>{{$ageing_day ?? "-"}}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->delivery_date )}}</td>
                <td>-</td>
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