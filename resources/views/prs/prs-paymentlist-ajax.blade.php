<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
            <?php $authuser = Auth::user();
            if ($authuser->role_id == 2) {?>
                <th>
                    <input type="checkbox" name="" id="ckbCheckAll" style="width: 30px; height:30px;">
                </th>
                <?php } ?>
                <th>Purchase Amount</th> 
                <th>Pickup ID</th>
                <th>Date</th>
                <th>Vehicle No</th>
                <th>Driver Name </th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($prsdata)>0)
            @foreach($prsdata as $value)
            <tr>
            @if ($authuser->role_id == 2)
                <td>
                    
                    @if(!empty($value->purchase_amount))
                    <input type="checkbox" name="checked_drs[]" class="chkBoxClass" value="{{$value->pickup_id}}"
                        data-price="{{$value->purchase_amount}}" style="width: 30px; height:30px;">
                    @else
                    -
                    @endif
                </td>
                @endif
                @if(!empty($value->purchase_amount))
                <td class="update_purchase_price" drs-no="{{$value->id}}">{{$value->purchase_amount ?? '-'}}</td>
                @else
                <td> 
                    <button type="button" class="btn btn-warning add-prs-purchase-price" value="{{$value->pickup_id}}"
                        style="margin-right:4px;">Add amount</button>
                </td>
                @endif
                <td>{{$value->pickup_id ?? "-"}}</td>
                <td>{{Helper::ShowDayMonthYear($value->prs_date) ?? "-"}}</td>
                <td>{{ isset($value->VehicleDetail->regn_no) ? $value->VehicleDetail->regn_no : "-"}}</td>
                <td>{{ isset($value->DriverDetail->name) ? ucfirst($value->DriverDetail->name) : "-" }}</td>
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
            {{$prsdata->appends(request()->query())->links()}}
        </nav>
    </div>
</div>