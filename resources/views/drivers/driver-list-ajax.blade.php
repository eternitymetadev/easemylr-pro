<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Driver Name</th>
                <th>Driver Phone</th>
                <th>Driver License Number</th>
                <th>Image</th>
                <th>App Access</th>
                <th>Password</th>
                {{-- <th>Tagged Branch</th> --}}
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">

            @if(count($drivers)>0)
            @foreach($drivers as $value)
            <tr>
                <td>{{ $value->name ?? "-" }}</td>
                <td>{{ $value->phone ?? "-" }}</td>
                <!-- <td>{{ Helper::ShowDayMonthYear($value->regndate) ?? "-" }}</td> -->
                <td>{{ $value->license_number ?? "-" }}</td>
                {{-- //////////// --}}
                <td>
                    @php
                    $licenseImage = $value->license_image;
                    $awsUrl = env('AWS_S3_URL');

                    if ($licenseImage) {
                        $imgUrlSegments = explode("/", $licenseImage);
                        $imgPath = count($imgUrlSegments) >= 4 ? implode('/', array_slice($imgUrlSegments, 0, 3)) : '';

                        $licence = '<a href="' . ($awsUrl == $imgPath ? $licenseImage : $awsUrl . '/driverlicense_images/' . $licenseImage) . '" target="_blank">view</a>';
                    } else {
                        $licence = '-';
                    }
                    @endphp
                
                    {!! $licence !!}
                </td>
                
                {{-- ///////////// --}}
                <?php //if($value->license_image){
                    ?>
                {{-- <td><a href={{$value->license_image}} target="_blank">view</a></td> --}}
               

                {{-- <td><a href="{{url("/storage/images/driverlicense_images/$value->license_image")}}" target="_blank">view</a></td> --}}

                <?php //}else{?>
                    {{-- <td>-</td> --}}
                <?php// } ?>
                

                <?php if($value->access_status == 0){
                        $access_status = 'Not Enabled';
                    }else{
                        $access_status = 'Enabled';
                    } ?>
                <td>{{ $access_status ?? "-" }}</td>
                <td>{{ $value->driver_password ?? "-" }}</td>
                {{-- <?php 
                // if($value->branch_id){
                //     $branch_id = explode(',',$value->branch_id);
                //     $branch_ids = array();
                //     foreach($branch_id as $branch){
                //         $location = App\Models\Location::where('id',$branch)->first();
                //         // echo "<pre>"; print_r($location->name);die; 
                //         $branch_ids[] = @$location->name;
                //     }
                //     if($branch_ids){
                //         $branch_name = implode('/', $branch_ids);
                //     }else{
                //     $branch_name = '-';
                //     }
                // }else{
                //     $branch_name = '-';
                // }
                ?>
                <td>{{ $branch_name ?? "-" }}</td> --}}
                
                <td>
                    <div class="d-flex" style="gap: 4px">
                        <a href="<?php echo URL::to($prefix.'/drivers/'.Crypt::encrypt($value->id).'/edit')?>"
                            class="edit btn btn-primary btn-sm"><span><i class="fa fa-edit"></i></span></a>
                        <a href="<?php echo URL::to($prefix.'/drivers/'.Crypt::encrypt($value->id))?>"
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
            {{$drivers->appends(request()->query())->links()}}
        </nav>
    </div>
</div>