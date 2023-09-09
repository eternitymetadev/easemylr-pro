<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Vehicle Number</th>
                <th>Registration Date</th>
                <th>State(Regd)</th>
                <th>Body Type</th>
                <th>Make</th>
                <th>Vehicle Capacity</th>
                <th>Manufacture</th>
                <th>First RC Image</th>
                <th>Second RC Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($vehicles)>0)
            @foreach($vehicles as $value)

            <tr>
                <td>{{ $value->regn_no ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYear($value->regndate) ?? "-" }}</td>
                <td>{{ $value->GetState->name ?? "-" }}</td>
                <td>{{ $value->body_type ?? "-" }}</td>
                <td>{{ $value->make ?? "-" }}</td>
                <td>{{ $value->tonnage_capacity ?? "-" }}</td>
                <td>{{ $value->mfg ?? "-" }}</td>
                <td>
                    @php
                    $rcImage = $value->rc_image;
                    $awsUrl = env('AWS_S3_URL');

                    if ($rcImage) {
                        $imgUrlSegments = explode("/", $rcImage);
                        $imgPath = count($imgUrlSegments) >= 4 ? implode('/', array_slice($imgUrlSegments, 0, 3)) : '';

                        $rcImageLink = '<a href="' . ($awsUrl == $imgPath ? $rcImage : $awsUrl . '/' . $rcImage) . '" target="_blank">view</a>';
                    } else {
                        $rcImageLink = '-';
                    }
                    @endphp

                    {!! $rcImageLink !!}
                </td>

                <td>
                    @php
                    $secondRcImage = $value->second_rc_image; // Replace with your actual field name
                    $awsUrl = env('AWS_S3_URL');

                    if ($secondRcImage) {
                        $imgUrlSegments = explode("/", $secondRcImage);
                        $imgPath = count($imgUrlSegments) >= 4 ? implode('/', array_slice($imgUrlSegments, 0, 3)) : '';

                        $secondRcImageLink = '<a href="' . ($awsUrl == $imgPath ? $secondRcImage : $awsUrl . '/' . $secondRcImage) . '" target="_blank">view</a>';
                    } else {
                        $secondRcImageLink = '-';
                    }
                    @endphp

                    {!! $secondRcImageLink !!}
                </td>
                
                <td>
                    <div class="d-flex" style="gap: 4px">
                        <a href="<?php echo URL::to($prefix.'/vehicles/'.Crypt::encrypt($value->id).'/edit')?>"
                            class="edit btn btn-primary btn-sm"><span><i class="fa fa-edit"></i></span></a>
                        <a href="<?php echo URL::to($prefix.'/vehicles/'.Crypt::encrypt($value->id))?>"
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
            {{$vehicles->appends(request()->query())->links()}}
        </nav>
    </div>
</div>