<?php  $authuser = Auth::user(); ?>
<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Vehicle No</th>
                <th>Drver Name</th>
                <th>Vehicle Type</th>
                <th>Quantity (in Pieces) </th>
                <th>Status </th>
                <th>Action </th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
        
            @if(count($vehiclereceives)>0)
            @foreach($vehiclereceives as $value)
            
                @if(count($value->PrsDriverTasks)>0)
                @foreach($value->PrsDriverTasks as $item)
                
                @if(count($item->PrsTaskItems)>0)
                <?php $qty_value = [];  ?>
                @foreach($item->PrsTaskItems as $taskitem)
                <?php $qty_value[] = $taskitem->quantity; 
                $task_status = $taskitem->status;
                $taskitem_id = $taskitem->id;
                $drivertask_id = $taskitem->drivertask_id;
                ?>
                
                @endforeach
                <?php $total_qty = array_sum($qty_value);
                
                ?>
                @endif
                <tr>
                <td>{{ $value->VehicleDetail->regn_no ?? "-" }}</td>
                <td>{{ $value->DriverDetail->name ?? "-" }}</td>
                <td>{{ $value->VehicleType->name ?? "-" }}</td>
                <td>{{$total_qty}}</td>
                <td>{{ Helper::VehicleReceiveGateStatus($task_status) ? Helper::VehicleReceiveGateStatus($task_status) : "-"}}</td>
                
                <td><a class="alert taskitemstatus btn btn-success" data-toggle="modal" href="#taskitem-status" data-id="{{$taskitem_id}}" data-drivertaskid="{{$drivertask_id}}"><span><i class="fa fa-check-circle-o"></i> Receive Vehicle</span></a>
            </td>
                @endforeach
                @endif
                
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