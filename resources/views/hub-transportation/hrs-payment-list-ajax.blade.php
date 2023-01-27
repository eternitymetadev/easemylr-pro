<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <?php $authuser = Auth::user();
            if ($authuser->role_id == 2) {?>
                <th>
                    <input type="checkbox" name="" id="ckbCheckAll" style="width: 16px; height:16px;" />
                </th>
                <?php }?>
                <th>Purchase Amt</th>
                <th>HRS</th>
                <th>HRS Status</th>
                <th>Vehicle
                    <a href="javascript:void();" class="vehicle-a" data-toggle="modal"
                        data-target="#search-paymentvehicle">
                        <i class="fa fa-caret-down"></i>
                    </a>
                </th>
                <th>Total Lr</th>
                <th>Gross Wt.</th>
                <th>Total Wt.</th>
                <th>Qty</th>
                <th>Driver Name</th>

            </tr>
        </thead>
        <tbody>
            @if(count($hrssheets)>0)
            @foreach($hrssheets as $hrssheet)
            <?php 
                $date = new DateTime($hrssheet->created_at, new DateTimeZone('GMT-7'));
                $date->setTimezone(new DateTimeZone('IST'));
                ?>

            <tr>
                @if ($authuser->role_id == 2)
                @if ($hrssheet->status != 0)
                @if (!empty($hrssheet->purchase_price))
                <td><input type="checkbox" name="checked_drs[]" class="chkBoxClass" value="{{$hrssheet->hrs_no}}"
                        data-price="{{$hrssheet->purchase_price}}" style="width: 16px; height:16px;">
                </td>
                @else
                <td><input type="checkbox" style="width: 16px; height:16px;" disabled /></td>
                @endif
                @else
                <td><input type="checkbox" style="width: 16px; height:16px;" disabled /></td>
                @endif
                @endif

                @if (!empty($hrssheet->purchase_price))
                <td>
                    <p class="d-flex justify-content-center align-items-center mb-0" style="gap: 6px">
                        ₹{{$hrssheet->purchase_price ?? '-'}}
                        <span class="swan-tooltip-right update_purchase_price" drs-no="{{$hrssheet->hrs_no}}"
                            data-tooltip="Change purchase amount">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </span>
                    </p>
                </td>
                @else
                <td style="text-align: center">
                    <button type="button" class="btn btn-sm add_purchase_price" value="{{$hrssheet->hrs_no}}"
                        style="font-size: 10px; background: #0b8fcb; color: #fff; font-weight: 600">
                        Add Amount
                    </button>
                </td>
                @endif

                <td>
                    <p class="mb-0">
                        <span style="color: #000">HRS-{{$hrssheet->hrs_no}}</span><br />
                        Dated: {{$date->format('d-m-Y')}}
                    </p>
                </td>

                <td>
                    <?php $drs_status = Helper::getdeleveryStatus($hrssheet->drs_no) ?>
                    <a class="drs_cancel hrs_lr" hrs-no="{{$hrssheet->hrs_no}}" data-text="consignment" data-status="0">
                        <p class="swan-tooltip-right drsStatus pointer orange" data-tooltip="View LR's">
                            <span>Outgoing</span>
                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                        </p>
                    </a>
                </td>


                <td>
                    <p class="mb-0 textWrap" style="max-width: 150px">
                        <span style="font-size: 14px; font-weight: 700">
                            {{$hrssheet->VehicleDetail->regn_no ?? '-'}}
                        </span><br />
                        {{$hrssheet->vehicletype->name ?? '-'}}
                    </p>
                </td>

                <td class="text-center">{{ Helper::counthrslr($hrssheet->hrs_no) ?? "-" }}</td>
                <td>{{ Helper::totalGrossWeightHrs($hrssheet->hrs_no) ?? "-"}}</td>
                <td>{{ Helper::totalWeightHrs($hrssheet->hrs_no) ?? "-"}}</td>
                <td class="text-center">{{ Helper::totalQuantityHrs($hrssheet->hrs_no) ?? "-"}}</td>
                <td>
                    <p class="textWrap pb-0" style="max-width: 200px">{{$hrssheet->DriverDetail->name ?? '-'}}</p>
                </td>

            </tr>

            @endforeach
            @else
            <tr>
                <td colspan="9" class="text-center">No Record Found</td>
            </tr>
            @endif
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
            {{$hrssheets->appends(request()->query())->links()}}
        </nav>
    </div>
</div>