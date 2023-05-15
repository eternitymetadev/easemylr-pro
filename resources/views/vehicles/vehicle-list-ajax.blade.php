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
                <td>{{ $value->state_id ?? "-" }}</td>
                <td>{{ $value->body_type ?? "-" }}</td>
                <td>{{ $value->make ?? "-" }}</td>
                <td>{{ $value->tonnage_capacity ?? "-" }}</td>
                <td>{{ $value->mfg ?? "-" }}</td>
                <?php
                if($value->rc_image == null){
                    $rc_image = '-';
                }else{
                    $chk_url = "https://easemylr.s3.us-east-2.amazonaws.com/vehicle_rc_images";
                    $img_url = $value->rc_image;
                    if($img_url != '' || $img_url != null){
                        
                        $explode_url = explode("/",$img_url);
                        // echo "<pre>"; print_r($explode_url);die;
                        if(isset($explode_url[0]) && isset($explode_url[1]) && isset($explode_url[2]) && isset($explode_url[3])){
                            $img_url = $explode_url[0].'/'.$explode_url[1].'/'.$explode_url[2].'/'.$explode_url[3];

                            // echo "<pre>"; print_r($img_url);die;
                        }else{
                            $img_url = '';
                        }
                        
                        if($chk_url == $img_url){
                            $rc_image = `<a href="$value->rc_image" target="_blank">view</a>`;
                        }else{
                            $rc_image = `<a href="$chk_url.'/'.$value->rc_image" target="_blank">view</a>`;
                        }
                    }else{
                        $rc_image = '';
                    }
                }?>
                <td>{{ $rc_image ?? "-" }}</td>
                <?php
                if($value->second_rc_image == null){
                    $second_rc_image = '-';
                }else{
                    $chk_url = "https://easemylr.s3.us-east-2.amazonaws.com/vehicle_rc_images";
                    $img_url = $value->second_rc_image;
                    if($img_url != '' || $img_url != null){
                        $explode_url = explode("/",$img_url);
                        if(isset($explode_url[0]) && isset($explode_url[1]) && isset($explode_url[2]) && isset($explode_url[3])){
                            $img_url = $explode_url[0].'/'.$explode_url[1].'/'.$explode_url[2].'/'.$explode_url[3];
                        }else{
                            $img_url = '';
                        }
                        
                        if($chk_url == $img_url){
                            $second_rc_image = `<a href="$value->second_rc_image" target="_blank">view</a>`;
                        }else{
                            $second_rc_image = `<a href="$chk_url.'/'.$value->second_rc_image" target="_blank">view</a>`;
                        }
                    }else{
                        $second_rc_image = '';
                    }
                } ?>
                <td>{{ $second_rc_image ?? "-" }}</td>
                <td>
                    <a href="<?php echo URL::to($prefix.'/vehicles/'.Crypt::encrypt($value->id).'/edit')?>" class="edit btn btn-primary btn-sm"><span><i class="fa fa-edit"></i></span></a>
                    <a href="<?php echo URL::to($prefix.'/vehicles/'.Crypt::encrypt($value->id))?>" class="view btn btn-info btn-sm"><span><i class="fa fa-eye"></i></span></a>
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