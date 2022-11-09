<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Vehicle No</th>
                <th>Drver Name</th>
                <th>Vehicle Type</th>
                <th>Quantity (in Pieces) </th>
                <th>Status </th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
        
            @if(count($vehiclereceives)>0)
            @foreach($vehiclereceives as $value)
            <?php //dd($value->PrsDriverTasks); ?>
            <tr>
                <td>{{ $value->VehicleDetail->regn_no ?? "-" }}</td>
                <td>{{ $value->DriverDetail->name ?? "-" }}</td>
                <td>{{ $value->VehicleType->name ?? "-" }}</td>
                @if(count($value->PrsDriverTasks)>0)
                @foreach($value->PrsDriverTasks as $item)
                <?php $qty_value = []; ?>
                @if(count($item->PrsTaskItems)>0)
                @foreach($item->PrsTaskItems as $taskitem)
                <?php $qty_value[] = $taskitem->quantity;
                $total_qty = array_sum($qty_value); ?>

                <td>{{$total_qty}}</td>
                
                @endforeach
                @endif
                @endforeach
                @endif
                
                <td>{{ Helper::PrsDriverTaskStatus($value->status) ? Helper::PrsDriverTaskStatus($value->status) : "-"}}</td>
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
            {{$vehiclereceives->appends(request()->query())->links()}}
        </nav>
    </div>
</div>