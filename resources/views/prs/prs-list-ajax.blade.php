<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Pickup ID</th>
                <th>Regional Client</th>
                <th>Tasks</th>
                <th>Date</th>
                <!-- <th>PRS Type </th> -->
                <th>Vehicle No.</th>
                <th>Driver Name </th>
                <th>Status </th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($prsdata)>0)
            @foreach($prsdata as $value)
            <?php 
            $regclients = $value->regclient_id;
            $regclient_ids  = explode(',',$regclients);
            $regclient_count = count($regclient_ids);

            $consigners = $value->consigner_id;
            $consinger_ids  = explode(',',$consigners);
            $consigner_count = count($consinger_ids);
            ?>
            <tr>
                <td>{{ $value->pickup_id ?? "-" }}</td>
                <td><i data-toggle="tooltip" data-placement="top" title="{{ $regclient_count ?? '-' }}">{{ $regclient_count ?? "-" }}</i></td>
                <td><a href="{{url($prefix.'/driver-tasks')}}" target="_blank" title="{{ $consigner_count ?? '-' }}">{{ $consigner_count ?? "-" }}</a>
                </td>
                <td>{{ Helper::ShowDayMonthYear($value->prs_date) ?? "-" }}</td>
                <td>{{ isset($value->VehicleDetail->regn_no) ? $value->VehicleDetail->regn_no : "-"}}</td>
                <td>{{ isset($value->DriverDetail->name) ? ucfirst($value->DriverDetail->name) : "-" }}</td>
                <td>{{ Helper::PrsStatus($value->status) ? Helper::PrsStatus($value->status) : "-"}}</td>
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