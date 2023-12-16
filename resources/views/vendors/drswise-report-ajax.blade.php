<p class="totalcount">Total Count: <span class="reportcount">{{$drswiseReports->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
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
                        $trans_id = DB::table('payment_histories')->where('transaction_id', $drswiseReport->transaction_id)->get();

                        $histrycount = count($trans_id);
                        if($histrycount){
                            if($histrycount > 1){
                            $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance + $drswiseReport->PaymentHistory[1]->tds_deduct_balance;
                            }else{
                                $paid_amt = $drswiseReport->PaymentHistory[0]->tds_deduct_balance;
                            }
                        }else{
                            $paid_amt=0;
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
            {{$drswiseReports->appends(request()->query())->links()}}
        </nav>
    </div>
</div>