<style>
    .flex1 {
        flex: 1
    }

    p {
        margin-bottom: 0;
    }

    .boldText {
        font-weight: 700;
        font-size: 14px;
    }

    .drsStatusCancelled {
        position: relative;
        background-color: rgb(154, 1, 1);
        border-radius: 8px;
        padding: 0 8px;
        cursor: not-allowed;
        color: #ffffff;
    }

    .drsStatusActive {
        margin-left: 0.5rem;
        position: relative;
        background-color: rgba(68, 154, 1, 0.24);
        border-radius: 8px;
        padding: 0 2px 0 8px;
        cursor: pointer;
        color: #00970c;
    }

    .drsStatusActive span {
        position: absolute;
        top: -24px;
        left: calc(100% + 2px);
        background-color: #ffffff;
        color: #494949;
        box-shadow: 0 0 8px rgba(68, 154, 1, 0.58);
        padding: 5px 10px;
        border-radius: 12px 12px 12px 0;
        display: none;
        transition: all 200ms ease-in-out;
    }

    .drsStatusActive:hover span {
        display: flex;
    }

    .drsStatusActive:hover {
        background-color: rgb(9, 123, 1);
        color: #fff;
        border-radius: 8px;
    }

    .textWrap {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .green {
        background-color: #34a94e;
        color: #fff;
    }

    .red {
        background-color: #d63232;
        color: #fff;
    }

    .orange {
        background-color: #e2a03f;
        color: #fff;
    }

    .extra {
        background-color: #7a6eff;
        color: #fff;
    }

    .extra2 {
        background-color: #1abc9c;
        color: #fff;
    }

    .pointer {
        cursor: pointer !important;
    }

    .disabledCursor {
        cursor: not-allowed !important;
        pointer-events: auto !important;
    }

    .deliveryStatus {
        user-select: none;
        cursor: default;
        text-align: center;
        width: 110px;
        border-radius: 50vh;
        padding: 6px 8px;
        font-size: 11px;
        line-height: 11px;
    }

    .paymentStatus {
        user-select: none;
        cursor: default;
        text-align: center;
        width: 110px;
        border-radius: 50vh;
        padding: 6px 8px;
        font-size: 11px;
        line-height: 11px;
    }

    .actionDiv {
        width: 190px;
        row-gap: 2px;
        column-gap: 8px;
        border-radius: 6px;
        box-shadow: 0 0 7px #83838360 inset;
        padding: 2px 8px 6px;
        margin-inline: auto;
    }

    .blackButton {
        font-weight: 600;
        color: #fff !important;
        background-color: #0c9b95 !important;
        border-color: #0c9b95;
        box-shadow: 0 10px 20px -10px #0c9b95;
    }

    .blackButton:hover {
        color: #fff !important;
    }

    .deliveryStatus span {
        position: absolute;
        top: -24px;
        left: calc(100% + 2px);
        background-color: #ffffff;
        color: #494949;
        box-shadow: 0 0 8px rgba(68, 154, 1, 0.58);
        padding: 5px 10px;
        border-radius: 12px 12px 12px 0;
        display: none;
        transition: all 200ms ease-in-out;
    }

    .deliveryStatus:hover span {
        display: flex;
    }

</style>


<?php $authuser = Auth::user();?>
<div class="custom-table">
    <table class="table mb-3" style="width:100%">
        <thead>
        <tr>
            <th>DRS</th>
            <th>Vehicle No</th>
            <th>Driver</th>
            <th style="text-align: center">Total LR</th>
            <th>Delivery Status</th>
            <th>Payment Status</th>
            <th style="text-align: center">Action</th>
        </tr>
        </thead>
        <tbody id="accordion" class="accordion">
        @if(count($transaction)>0)
            @foreach($transaction as $trns)
                <?php

                $date = new DateTime($trns->created_at, new DateTimeZone('GMT-7'));
                $date->setTimezone(new DateTimeZone('IST'));
                $getdeldate = Helper::getdeleveryStatus($trns->drs_no) ?? "";
                $new = Helper::oldnewLr($trns->drs_no) ?? "";
                $lr = Helper::deliveryDate($trns->drs_no);

                ?>
                <tr>
                    <td>
                        <p>
                            <span class="boldText">DRS-{{$trns->drs_no}}</span> @if($trns->status == 0)<span
                                class="drsStatusCancelled">Cancelled</span> @else
                                <span class="drsStatusActive active_drs" drs-no="{{$trns->drs_no}}">Active <i
                                        class="fa fa-check-circle-o"></i><span>Click to cancel</span></span>@endif
                            <br/>
                            Dated: {{$date->format('Y-m-d')}}
                        </p>
                    </td>
                    <td>{{$trns->vehicle_no ?? '-NA-'}}</td>
                    <td>
                        @if($trns->driver_name)
                            <p class="textWrap" style="max-width: 170px">{{$trns->driver_name}}<br/>
                                Mob: {{$trns->driver_no}}</p>
                        @else -NA-
                        @endif
                    </td>
                    <td style="text-align: center">{{ Helper::getCountDrs($trns->drs_no) ?? "" }}</td>

                    {{--delivery Status--}}
                    <td>
                        <?php $del_status = Helper::getdeleveryStatus($trns->drs_no) ?>
                        @if ($trns->status == 0)
                            <p class="red deliveryStatus">Cancelled</p>
                        @else
                            @if (empty($trns->vehicle_no) || empty($trns->driver_name) || empty($trns->driver_no))
                                <p class="extra deliveryStatus">No Status</p>
                            @else
                                <a class="drs_cancel pointer" drs-no="{{$trns->drs_no}}" data-text="consignment"
                                   data-status="0" data-action="<?php echo URL::current(); ?>">
                                    <p style="position:relative;"
                                       class="deliveryStatus pointer @if($del_status == 'Successful')
                                           green @elseif($del_status == 'Partial Delivered')
                                           orange @elseif($del_status == 'Started')
                                           extra2 @endif">
                                        {{ $del_status }}<i class="fa fa-caret-down ml-1" aria-hidden="true"></i>
                                        <span style="font-size: 13px; line-height: 1rem">Click to update status</span>
                                    </p>

                                </a>
                            @endif
                        @endif
                    </td>

                    {{--payment status--}}
                    @if ($trns->payment_status == 0)
                        <td><p class="paymentStatus red">Unpaid</p></td>
                    @elseif ($trns->payment_status == 1)
                        <td><p class="paymentStatus green">Paid</p></td>
                    @elseif ($trns->payment_status == 2)
                        <td><p class="paymentStatus extra">Sent to Account</p></td>
                    @elseif ($trns->payment_status == 3)
                        <td><p class="paymentStatus orange">Partial Paid</p></td>
                    @else
                        <td><p class="paymentStatus warning">unknown</p></td>
                    @endif


                    {{--action button--}}
                    @if ($trns->status == 0)
                        <td>
                            <div class="actionDiv d-flex flex-wrap justify-content-center align-items-center">
                                <p style="text-align: center; color: darkred; font-size: 16px; line-height: 42px">
                                    Cancelled</p>
                            </div>
                        </td>
                    @else
                        <td>
                            <div class="actionDiv d-flex flex-wrap justify-content-center align-items-center">
                                @if($trns->delivery_status == 'Unassigned')
                                    <p style="width: 100%; text-align: center; color: #c90000">Unassigned</p>
                                @elseif($lr == 0)
                                    <p style="width: 100%; text-align: center; color: green">Assigned</p>
                                @else
                                    <p style="width: 100%; text-align: center; color: green">&nbsp;</p>
                                @endif


                                @if (empty($trns->vehicle_no) || empty($trns->driver_name) || empty($trns->driver_no))
                                    <button class="flex1 btn btn-warning view-sheet" value="{{$trns->drs_no}}"
                                            style="margin-right:4px;">Draft
                                    </button>
                                    <button class="flex1 btn btn-success draft-sheet" value="{{$trns->drs_no}}"
                                            style="margin-right:4px;">Save
                                    </button>
                                    {{--                                @else--}}
                                    {{--                                    <a class="flex1 btn btn-warning disabled disabledCursor" disabled--}}
                                    {{--                                       style="margin-right:4px;">Draft--}}
                                    {{--                                    </a>--}}
                                    {{--                                    <a class="flex1 btn btn-success disabled disabledCursor" disabled--}}
                                    {{--                                       style="margin-right:4px;">Save--}}
                                    {{--                                    </a>--}}
                                @endif
                                @if (!empty($trns->vehicle_no))
                                    <a class="flex1 btn blackButton" target="_blank"
                                       @if (!empty($new))
                                       href="{{url($prefix.'/print-transactionold/'.$trns->drs_no)}}"
                                       @else
                                       href="{{url($prefix.'/print-transaction/'.$trns->drs_no)}}"
                                       @endif
                                       role="button">Print</a>
                                    {{--                                @else--}}
                                    {{--                                    <a class="flex1 btn blackButton disabled disabledCursor" href="#" disabled role="button">Print</a>--}}
                                @endif
                            </div>

                        </td>
                    @endif

                </tr>

            @endforeach
        @else
            <tr>
                <td colspan="7" style="padding: 8rem 1rem;" class="text-center">No Record Found</td>
            </tr>
        @endif
        </tbody>
    </table>

    <div class="px-3 mt-5 d-flex flex-wrap justify-content-between align-items-center" style="gap: 1rem;">
        <div class="d-flex align-items-center">
            <label class=" mb-0 mr-1">Items per page</label>
            <select style="width: 90px" class="form-control form-control-sm perpage"
                    data-action="<?php echo url()->current(); ?>">
                <option value="10" {{$peritem == '10' ? 'selected' : ''}}>10</option>
                <option value="50" {{$peritem == '50' ? 'selected' : ''}}>50</option>
                <option value="100" {{$peritem == '100'? 'selected' : ''}}>100</option>
            </select>
        </div>

        <nav class="navigation2 text-center" aria-label="Page navigation">
            {{$transaction->appends(request()->query())->links()}}
        </nav>
    </div>

</div>
