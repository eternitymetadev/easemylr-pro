<p class="totalcount">Total Count: <span class="reportcount">{{$drswiseReports->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                 <th>Transaction Date</th>
                 <th>Transaction Id</th>
                <th>Drs No</th>
                <th>No of Drs</th>
                <th>No of Lrs</th>
                <th>Box Count</th>
                <th>Gross Wt</th>
                <th>Net Weight</th>
                <th>Consignee Distt</th>
                <th>Vehicle Type</th>

            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            @foreach($drswiseReports as $drswiseReport)

            <?php 
             $lr_count = Helper::LrCountMix($drswiseReport->transaction_id);
           
            $i++; 
                     $date = date('d-m-Y',strtotime($drswiseReport->created_at));
                     $result = Helper::totalQuantityMixReport($drswiseReport->transaction_id);
                     $consignee = Helper::mixReportConsignee($drswiseReport->transaction_id);
                    
                    ?>
            <tr>
                <td>{{$date}}</td>
                <td>{{$drswiseReport->transaction_id}}</td>
                <td>DRS-{{$drswiseReport->drs_no_list}}</td>
                <td>{{$drswiseReport->drs_no_count}}</td>
                <td>{{$lr_count}}</td>
                <td>{{$result->total_quantity}}</td>
                <td>{{$result->total_gross}}</td>
                <td>{{$result->total_weight}}</td>
                <td>{{$consignee->district_consignee}}</td>
                <td>{{$consignee->vehicle_type}}</td>
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