<p class="totalcount">Total Count: <span class="reportcount">{{$hrssheets->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Hrs No</th>
                <th>Hrs Date</th>
                <th>Vehicle No</th>
                <th>Driver Name</th>
                <th>Driver Phone</th>
                <th style="text-align: center">Action</th>
            </tr>
        </thead>
        <tbody> 
            @foreach($hrssheets as $hrssheet)
            <tr>
                <td>HRS-{{$hrssheet->hrs_no}}</td>
                <td>{{$hrssheet->created_at}}</td>
                <td>{{$hrssheet->VehicleDetail->regn_no ?? '-'}}</td>
                <td>{{$hrssheet->DriverDetail->name ?? '-'}}</td>
                <td>{{$hrssheet->DriverDetail->phone ?? '-'}}</td>
                <td><button class="flex1 btn btn-success save_hrs" value="{{$hrssheet->hrs_no}}"
                                            style="margin-right:4px;">Save
                                    </button></td>
           </tr>
            @endforeach
        </tbody>
    </table>
    <div class="perpage container-fluid">
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
                            <select class="form-control report_perpage" data-action="<?php echo url()->current(); ?>">
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
            {{$hrssheets->appends(request()->query())->links()}}
        </nav>
    </div>
</div>