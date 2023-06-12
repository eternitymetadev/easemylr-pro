<?php $authuser = Auth::user();?>
<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Postal Code</th>
                <th>District</th>
                <th>State</th>
                <th>Service HUB Name </th>
                <th>Service Hub Code</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zones as $zone)
            <tr>
                <td>{{ $zone->postal_code ?? '-'}}</td>
                <td>{{ $zone->district ?? '-'}}</td>
                <td>{{ $zone->state ?? '-'}}</td>
                <td>{{ $zone->hub_transfer ?? '-'}}</td>
                <td>{{ $zone->Branch->hub_nickname ?? '-'}}</td>
                <td><button type="button" class="btn btn-warning edit_postal" value="{{$zone->id}}">edit</button></td>
            </tr>
            @endforeach
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
            {{$zones->appends(request()->query())->links()}}
        </nav>
    </div>
</div>