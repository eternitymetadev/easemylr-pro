<?php  $authuser = Auth::user(); ?>
<p class="totalcount">Total Count: <span class="reportcount">{{$consignments->total()}}</span></p>

<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Pickup Branch </th>
                <th>Booking Branch</th>
                <th>LR No</th>
                <th>LR Date</th>
                <th>Client</th>
                <th>Consigner</th>
                <th>PIN</th>
                <th>City</th>
                <th>Quantity</th>
                <th>Net Weight</th>
                <!-- <th>Status</th> -->
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <tr>
                <td>{{ $consignment->ConsignerDetail->GetBranch->name ?? "" }}</td>
                <td>{{ $consignment->Branch->name ?? "-" }}</td>
                <td>{{ $consignment->id ?? "" }}</td>
                <td>{{ $consignment->consignment_date ?? "" }}</td>
                <td>{{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->nick_name ?? "" }}</td>
                <td>{{ $consignment->ConsignerDetail->postal_code}}</td>
                <td>{{ $consignment->ConsignerDetail->city}}</td>
                <td>{{ $consignment->total_quantity ?? "-" }}</td>
                <td>{{ $consignment->total_weight ?? "-" }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="10" class="text-center">No Record Found </td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="container-fluid">
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
            {{$consignments->appends(request()->query())->links()}}
        </nav>
    </div>
</div>