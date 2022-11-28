<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Pickup ID</th>
                <th>Date</th>
                <th>Consigner </th>
                <th>City</th>
                <th>Status </th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($drivertasks)>0)
            @foreach($drivertasks as $value)
            
            <tr>
                <td>{{ $value->task_id ?? "-" }}</td>
                <td>{{ $value->PickupId->pickup_id ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYear($value->prs_date) ?? "-" }}</td>
                <td>{{ $value->ConsignerDetail->nick_name ?? "-" }}</td>
                <td>{{ $value->ConsignerDetail->city ?? "-" }}</td>
                <td>{{ Helper::PrsDriverTaskStatus($value->status) ? Helper::PrsDriverTaskStatus($value->status) : "-"}}</td>

                <?php if($value->status == 1 ) { 
                    $disable = ''; 
                } else{
                    $disable = 'disable_n';
                } ?>

                <td>
                <?php if($value->status == 1 ) { ?>
                    <a href="javascript:void();" class="add-taskbtn {{$disable}}" data-prsid="{{$value->prs_id}}" data-drivertaskid="{{$value->id}}" data-toggle="modal" data-target="#add-task">Add Task</a> 
                    <?php } else if($value->status == 2){ ?>
                    <a href="javascript:void();" class="taskstatus_change " data-prsid="{{$value->prs_id}}" data-drivertaskid="{{$value->id}}" data-toggle="modal" data-target="#prs-commonconfirm">Status Change</a>
                    <?php }else{ ?>
                        <span></span>
                    <?php } ?>
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
            {{$drivertasks->appends(request()->query())->links()}}
        </nav>
    </div>
</div>