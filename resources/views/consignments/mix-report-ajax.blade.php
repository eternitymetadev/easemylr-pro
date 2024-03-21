<p class="totalcount">Total Count: <span class="reportcount">{{$drswiseReports->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Type</th>
                <th>Transaction Date</th>
                <th>Transaction Id</th>
                <th>Drs / Prs / Hrs</th>
                <th>No of Drs/Prs/Hrs</th>
                <th>No of Lrs</th>
                <th>Box Count</th>
                <th>Gross Wt</th>
                <th>Net Weight</th>
                <th>Consignee Distt</th>
                <th>Vehicle Type</th>
                <th>Vehicle No</th>
                <th>Vendor Name</th>
                <th>Branch</th>
                <th>Advance</th>
                <th>Balance</th>
                <th>Total Amount</th>
                <th>Status</th>

            </tr>
        </thead>
        <tbody>

            @foreach($drswiseReports as $drswiseReport)

            <tr>
                <td>{{ @$drswiseReport->type }}</td>
                <td>{{ Helper::ShowDayMonthYear(@$drswiseReport->transaction_date )}}</td>
                <td>{{ @$drswiseReport->transaction_id }}</td>
                <td>{{ @$drswiseReport->drs_no }}</td> 
                <td>{{ @$drswiseReport->no_of_drs }}</td>
                <td>{{ @$drswiseReport->no_of_lrs }}</td>
                <td>{{ @$drswiseReport->box_count }}</td>
                <td>{{ @$drswiseReport->gross_wt }}</td>
                <td>{{ @$drswiseReport->net_wt }}</td>
                <td>{{ @$drswiseReport->consignee_distt }}</td>
                <td>{{ @$drswiseReport->vehicle_type }}</td>
                <td>{{ @$drswiseReport->vehicle_no }}</td>

                @php
                    $paymentRequest = null;
                    $vendorName = '';
                    $branchName = '';
                    $advanceAmount = 0;
                    $balanceAmount = 0;
                    $totalAmount = 0;
                    $paymentStatus = '';

                    if ($drswiseReport->type == 'DRS') {
                        $paymentRequest = $drswiseReport->PaymentRequest;
                    } elseif ($drswiseReport->type == 'PRS') {
                        $paymentRequest = $drswiseReport->PrsPaymentRequest;
                    } elseif ($drswiseReport->type == 'HRS') {
                        $paymentRequest = $drswiseReport->HrsPaymentRequest;
                    }

                    if ($paymentRequest) {
                        $vendorName = $paymentRequest->VendorDetails->name ?? '';
                        $branchName = $paymentRequest->Branch->name ?? '';
                        $advanceAmount = $paymentRequest->advanced ?? 0;
                        $balanceAmount = $paymentRequest->balance ?? 0;
                        $totalAmount = $paymentRequest->total_amount ?? 0;

                        switch ($paymentRequest->payment_status) {
                            case 0: $paymentStatus = '<label class="badge badge-dark">Failed</label>'; break;
                            case 1: $paymentStatus = '<label class="badge badge-success">Paid</label>'; break;
                            case 2: $paymentStatus = '<label class="badge badge-dark">Sent to Account</label>'; break;
                            case 3: $paymentStatus = '<label class="badge badge-primary">Partial Paid</label>'; break;
                            default: $paymentStatus = '<button type="button" class="btn btn-danger" style="margin-right:4px;">Unknown</button>'; break;
                        }
                    }
                @endphp

                <td>{{ $vendorName }}</td>
                <td>{{ $branchName }}</td>
                <td>{{ $advanceAmount }}</td>
                <td>{{ $balanceAmount }}</td>    
                <td>{{ $totalAmount }}</td>
                <!-- Payment Status -->
                <td>{!! $paymentStatus !!}</td>
                <!-- end payment -->
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