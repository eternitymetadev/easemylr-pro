<p class="totalcount">Total Count: <span class="reportcount">{{$drswiseReports->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>

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
                <th>Status</th>

            </tr>
        </thead>
        <tbody>
            <?php $i = 0;?>
            @foreach($drswiseReports as $drswiseReport)
            <?php
              
                $trans_id = $lrdata = DB::table('payment_histories')->where('transaction_id', $drswiseReport->transaction_id)->get();
                $histrycount = count($trans_id);

                if ($histrycount > 1) {
                    @$paid_amt = @$trans_id[0]->tds_deduct_balance + @$trans_id[1]->tds_deduct_balance;
                } else {
                    @$paid_amt = @$trans_id[0]->tds_deduct_balance;
                }

                if($drswiseReport->DrsDetails->status == '0' ){
                        $drs_status = 'Cancelled';
                }else{
                    $drs_status = 'Active';
                }
            ?>

            <tr>
                <td>DRS-{{$drswiseReport->drs_no}}</td>
                <td>{{ Helper::ShowDayMonthYear($drswiseReport->date )}}</td>
                <td>{{$drswiseReport->vehicle_no}}</td>
                <td>{{$drswiseReport->vehicle_type}}</td>
                <td>{{$drswiseReport->purchase_amount}}</td>
                <td>{{$drswiseReport->transaction_id}}</td>
                <td>{{$drswiseReport->transaction_id_amt}}</td>
                <td>{{@$paid_amt}}</td>
                <td>{{$drswiseReport->client}}</td>
                <td>{{@$drswiseReport->location}}</td>
                <td>{{$drswiseReport->lr_no}}</td>
                <td>{{$drswiseReport->no_of_cases}}</td>
                <td>{{$drswiseReport->net_wt}}</td>
                <td>{{$drswiseReport->gross_wt}}</td>
                <td>
                    {{ $drs_status }}
                </td>
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