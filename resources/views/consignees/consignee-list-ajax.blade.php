<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Cnee ID</th>
                <th>Consignee Nick Name</th>
                <th>Consigner</th>
                {{-- <th>Base Client</th>  --}}
                <th>Contact Person Name</th>
                <th>Mobile No</th>
                <th>PIN Code</th>
                <th>City</th>
                <th>District</th>
                <th>State</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($consignees)>0)
            @foreach($consignees as $value)

            <tr>
                <td>{{ $value->id ?? "-" }}</td>
                <td>{{ $value->nick_name ?? "-" }}</td>
                <td>{{ $value->GetConsigner->nick_name ?? "-" }}</td>
                {{-- <td>{{ $value->RegClients->BaseClient->client_name ?? "-" }}</td> --}}
                <td>{{ $value->contact_name ?? "-" }}</td>
                <td>{{ $value->phone ?? "-" }}</td>
                <td>{{ $value->postal_code ?? "-" }}</td>
                <td>{{ $value->city ?? "-" }}</td>
                <td>{{ $value->district ?? "-" }}</td>
                <td>{{ $value->state_id ?? "-" }}</td>
                
                <td>
                    <div class="d-flex" style="gap: 4px">
                        <a href="<?php echo URL::to($prefix.'/consignees/'.Crypt::encrypt($value->id).'/edit')?>"
                            class="edit btn btn-primary btn-sm"><span><i class="fa fa-edit"></i></span></a>
                        <a href="<?php echo URL::to($prefix.'/consignees/'.Crypt::encrypt($value->id))?>"
                            class="view btn btn-info btn-sm"><span><i class="fa fa-eye"></i></span></a>
                    </div>
                </td>

            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="6" class="text-center">No Record Found </td>
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
            {{$consignees->appends(request()->query())->links()}}
        </nav>
    </div>
</div>