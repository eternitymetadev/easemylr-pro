<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
        <tr>
            <th>Requesting Branch</th>
            <th>BM Name</th>
            <th>Purchase Amount</th>
            <th>Transaction ID</th>
            <th>Vehicle Type</th>
            <th>Payment Type.</th>
            <th>Qty</th>
            <th>Driver Name</th>
        </tr>
        </thead>
        <tbody>
            @foreach($hrsRequests as $hrsRequest)
            <tr>
                <td>{{$hrsRequest->Branch->name}}</td>
                <td>{{$hrsRequest->User->name}}</td>
                <td>{{$hrsRequest->total_amount}}</td>
                <td>{{$hrsRequest->transaction_id}}</td>
                <td></td>
                <td>{{$hrsRequest->payment_type}}</td>
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
                            <select class="form-control perpage" data-action="<?php echo url()->current(); ?>">
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
            {{$hrsRequests->appends(request()->query())->links()}}
        </nav>
    </div>
</div>