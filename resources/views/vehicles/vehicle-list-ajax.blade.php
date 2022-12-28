<p class="totalcount">Total Count: <span class="reportcount">{{$vehicles->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
            <th>Vehicle Number</th>
                        <th>Reg. Date</th>
                        <th>Manufacture</th>
                        <th>Make</th>
                        <th>Body Type</th>
                        <th>Loading Cap.</th>
                        <th>RC Image</th>
                        <th style="text-align: center">Action</th>
            </tr>
        </thead>
        <tbody> 
            @foreach($vehicles as $vehicle)
            <tr>
                <td valign="middle" style="max-width: 350px">
                    <p class="consigner">
                        <span class="legalName" title="{{@$consigner->RegClient->name}}">
                            {{@$vehicle->regn_no ?? '-'}}
                        </span>
                    </p>
                </td>
                <td>{{$vehicle->regndate}}</td>
                <td>{{$vehicle->mfg}}</td>
                <td>{{$vehicle->make}}</td>
                <td>{{$vehicle->body_type}}</td>
                <td>{{$vehicle->tonnage_capacity}}</td>
                @if($vehicle->rc_image == null)
                <td>-</td>
                @else
                <td><a href="<?php echo URL::to('/storage/images/vehicle_rc_images/' . $vehicle->rc_image) ?>" target="_blank" style="text-align: center">view</a></td>
               @endif
                <td>
                    <div class="d-flex align-content-center justify-content-center" style="gap: 6px"><a
                            id="editConsignerIcon" href="#"
                            class="edit editIcon editVehicleBtn" data-id="{{$vehicle->id}}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg></a>
                        <a href="#" class="view viewIcon VehicleView" data-id="{{$vehicle->id}}"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-eye">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg></a>
                            <a class="delete deleteIcon delete_vehicle" data-id="{{$vehicle->id}}" data-action="<?php echo URL::to($prefix . '/' . $segment . '/delete-vehicle'); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"                                                  viewBox="0 0 24 24"                                                  fill="none" stroke="currentColor" stroke-width="2"                                                  stroke-linecap="round"                                                  stroke-linejoin="round" class="feather feather-trash-2">                                                 <polyline points="3 6 5 6 21 6"></polyline>                                                 <path                                                     d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>                                                 <line x1="10" y1="11" x2="10" y2="17"></line>                                                 <line x1="14" y1="11" x2="14" y2="17"></line>                                             </svg></a>
                    </div>
                </td>

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
            {{$vehicles->appends(request()->query())->links()}}
        </nav>
    </div>
</div>

