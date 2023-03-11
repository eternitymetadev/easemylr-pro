<div class="d-flex justify-content-between">
    <p class="totalcount px-3">
        <strong>Total Count: <span class="reportcount">{{$consignments->total()}}</span></strong>
    </p>
</div>

<div class="custom-table">

    <table id="unverified-table" class="table table-hover" style="width:100%">

</div>
<thead>
    <tr>

        <th>Sr No</th>
        <th>Drs No</th>
        <th>Date</th>
        <th>Vehicle No</th>
        <th>Vehicle Type</th>
        <th>Purchase Amount</th>
        <th>Transaction Id</th>
        <th>Transaction Id Amount</th>
        <th>Paid Amount</th>
        <th>Clients</th>
        <th>Locations</th>
        <th>LRs No</th>
        <th>No. of cases</th>
        <th>Net Weight</th>
        <th>Gross Wt</th>

    </tr>
</thead>
<tbody>
    <?php $i = 0; ?>
    @foreach($drswiseReports as $drswiseReport)

    <?php $i++; 
     $date = date('d-m-Y',strtotime($drswiseReport->created_at));
     $no_ofcases = Helper::totalQuantity($drswiseReport->drs_no);
     $totlwt = Helper::totalWeight($drswiseReport->drs_no);
     $grosswt = Helper::totalGrossWeight($drswiseReport->drs_no);
    $lrgr = array();
    $regnclt = array();
    $vel_type = array();
        foreach($drswiseReport->TransactionDetails as $lrgroup){
               $lrgr[] =  $lrgroup->ConsignmentNote->id;
               $regnclt[] = @$lrgroup->ConsignmentNote->RegClient->name;
               $vel_type[] = @$lrgroup->ConsignmentNote->vehicletype->name;
               $purchase = @$lrgroup->ConsignmentDetail->purchase_price;
        }
        $lr = implode('/', $lrgr);
        $unique_regn = array_unique($regnclt);
        $regn = implode('/', $unique_regn);

        $unique_veltype = array_unique($vel_type);
        $vehicle_type = implode('/', $unique_veltype);
        $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $drswiseReport->transaction_id)->get();
        $histrycount = count($trans_id);
        
        if($histrycount > 1){
           $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance + $drswiseReport->PaymentHistory[1]->tds_deduct_balance;
        }else{
            $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance;
        }

    ?>
    <tr>
        <td>{{$i}}</td>
        <td>DRS-{{$drswiseReport->drs_no}}</td>
        <td>{{$date}}</td>
        <td>{{$drswiseReport->vehicle_no}}</td>
        <td>{{$vehicle_type}}</td>
        <td>{{$purchase}}</td>
        <td>{{$drswiseReport->transaction_id}}</td>
        <td>{{$drswiseReport->total_amount}}</td>
        <td>{{$paid_amt}}</td>
        <td>{{$regn}}</td>
        <td>{{@$drswiseReport->Branch->name}}</td>
        <td>{{$lr}}</td>
        <td>{{$no_ofcases}}</td>
        <td>{{$totlwt}}</td>
        <td>{{$grosswt}}</td>
    </tr>
    @endforeach
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