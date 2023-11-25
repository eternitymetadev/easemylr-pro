<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
            <tr>
                <th>Pickup ID</th>
                <th>Date</th>
                <th>Regional Client</th>
                <th>Pickup Points</th>
                <!-- <th>PRS Type </th> -->
                <th>Vehicle No.</th>
                <th>Driver Name </th>
                <th>Status </th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="accordion" class="accordion">
            @if(count($prsdata)>0)
            @foreach($prsdata as $value)
            <?php 
            // echo'<pre>'; print_r(json_decode($value)); die;
            $regclients = $value->regclient_id;
            $regclient_ids  = explode(',',$regclients);
            $regclient_count = count($regclient_ids);

            $consigners = $value->consigner_id;
            $consinger_ids  = explode(',',$consigners);
            $consigner_count = count($consinger_ids);
            ?>
            <tr>
                <td>{{ $value->pickup_id ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYear($value->prs_date) ?? "-" }}</td>
                <td>

                    @if(count($value->PrsRegClients)>0)
                    <span class="viewAllInvoices">
                        <span class="moreInvoicesView">
                            <ul style="padding: 0; margin-bottom: 0;">
                                @foreach($value->PrsRegClients as $regclients)
                                <li style="margin-bottom: 8px" title="{{$regclients->RegClient->name ?? '-'}}">{{$regclients->RegClient->name ?? "-"}}</li>
                                @endforeach
                            </ul>
                        </span>
                    </span>
                    @endif
                </td>
                <td>
                    @if(count($value->PrsRegClients)>0)
                    <span class="viewAllInvoices">
                        <span class="moreInvoicesView">
                        <a href="{{url($prefix.'/driver-tasks')}}" target="">
                                <ul style="padding: 0; margin-bottom: 0;">
                                    @foreach($value->PrsRegClients as $regcnrs)
                                    @foreach($regcnrs->RegConsigner as $regcnr)

                                    <li style="margin-bottom: 8px" title="{{ $regcnr->Consigner->nick_name ?? '-' }}">{{$regcnr->Consigner->nick_name ?? "-"}}</li>
                                    @endforeach
                                    @endforeach
                                </ul>
                            </a>
                        </span>
                    </span>
                    @endif
                </td>
                <td>{{ isset($value->VehicleDetail->regn_no) ? $value->VehicleDetail->regn_no : "-"}}</td>
                <td>{{ isset($value->DriverDetail->name) ? ucfirst($value->DriverDetail->name) : "-" }}</td>
                <td style="font-weight: 600;">{{ Helper::PrsStatus($value->status) ? Helper::PrsStatus($value->status) : "-"}}</td>
                <td>
                    <?php $authuser = Auth::user();
                    if($authuser->role_id == 3){
                        $disable = 'disable_n' ;
                    }else{
                        $disable = '';
                    } 
                    if($value->status == 1){
                    ?>
                    <a href="{{url($prefix.'/'.$segment.'/'.Crypt::encrypt($value->id).'/edit')}}" class="btn btn-white btn-cstm {{$disable}}"><span><i class="fa fa-edit"></i> Edit</span></a> 
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
            {{$prsdata->appends(request()->query())->links()}}
        </nav>
    </div>
</div>