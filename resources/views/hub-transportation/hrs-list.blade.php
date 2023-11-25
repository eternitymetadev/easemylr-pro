@extends('layouts.main')
@section('content')

    <style>
        .dt--top-section {
            margin: none;
        }

        div.relative {
            position: absolute;
            left: 110px;
            top: 24px;
            z-index: 1;
            width: 145px;
            height: 38px;
        }

        /* .table > tbody > tr > td {
            color: #4361ee;
        } */
        .dt-buttons .dt-button {
            width: 83px;
            height: 38px;
            font-size: 13px;
        }

        .btn-group > .btn, .btn-group .btn {
            padding: 0px 0px;
            padding: 10px;
        }

        .select2-results__options {
            list-style: none;
            margin: 0;
            padding: 0;
            height: 160px;
            /* scroll-margin: 38px; */
            overflow: auto;
        }

        .textWrap {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 270px;
            margin-bottom: 0;
            color: #000;
        }

        .boldText {
            font-weight: 800;
            color: #000;
        }

        .searchIcon {
            position: absolute;
            left: 0.5rem;
            height: 1rem;
            width: 1rem;

        }

    </style>
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="page-header layout-spacing">
                    <h2 class="pageHeading">Create Hrs</h2>
                </div>


                <div class="widget-content widget-content-area br-6">
                    <div class=" mb-4 mt-4">
                        <div class="d-flex justify-content-between align-items-center px-3 pb-3" style="gap: 1rem;">
                        <?php $authuser = Auth::user();
                         if($authuser->role_id != 3){
                             ?>    
                        <button disabled="true" type="button" class="btn btn-warning disableDrs" id="create_hrs"
                                    style="align-self: stretch;">
                                Create HRS
                            </button>
                            <?php } ?>
                            <div class="d-flex justify-content-center align-items-center" style="flex: 1; max-width: 240px; position: relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-search searchIcon">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input class="form-control form-control-sm" style="width: 100%; height: 38px; padding-left: 2rem"
                                       id="myInput" type="text" placeholder="search..."/>
                            </div>
                        </div>
                        @csrf
                        <table id="unverified-table" class="table table-hover" style="width:100%">
                            <thead>
                            <tr>
                                <th style="text-align: center">
                                    <input type="checkbox" name="" id="ckbCheckAll" style="width: 20px; height:20px;">
                                </th>
                                <th>LR No</th>
                                <th>CN Date</th>
                                <th>Hub Transfer</th>
                                <th>Consignor Name</th>
                                <th>Consignee Name</th>
                                <th>Location</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            foreach ($consignments as $key => $consignment) {
                                $branch_id = $consignment->branch_id;
                                $to_branch_id = $consignment->to_branch_id;
                                $fall_in = $consignment->fall_in;
                                if($branch_id == $to_branch_id && $branch_id == $fall_in){
                                }else{
                            ?>
                            <tr>
                                <td style="text-align: center">
                                    <input type="checkbox" name="checked_consign[]" class="chkBoxClass ddd"
                                           value="{{$consignment->id}}" data-trp="" data-vehno="" data-vctype=""
                                           style="width: 18px; height:18px;">
                                </td>
                                <td>{{ $consignment->id ?? "-" }}</td>
                                <td>{{ $consignment->consignment_date}}</td>
                                <td>{{ $consignment->ToBranch->name ?? "-"}}</td>
                                <td title="{{ $consignment->ConsignerDetail->nick_name}}"><p
                                        class="textWrap">{{ $consignment->ConsignerDetail->nick_name}}</p></td>
                                <td title="{{ $consignment->ConsigneeDetail->nick_name}}"><p
                                        class="textWrap">{{ $consignment->ConsigneeDetail->nick_name}}</p></td>
                                <td style="text-transform: uppercase">
                                    {{ $consignment->ConsigneeDetail->city ?? "-" }} - {{ $consignment->ConsigneeDetail->postal_code ?? "-" }}<br/>
                                    Dist: {{ $consignment->ConsigneeDetail->district ?? "-" }}
                                </td>
                                <td>
                                    <p style="margin-bottom: 0">
                                        No.of Boxes: <span
                                            class="boldText">{{ $consignment->total_quantity ?? "-" }}</span><br/>
                                        <span>
                                            Total Weight: <span
                                                class="boldText">{{ $consignment->total_weight ?? "-" }}</span>
                                        </span>
                                    </p>

                                </td>
                            </tr>

                            <?php } $i++; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {

            ///// check box checked unverified lr page
            jQuery(document).on('click', '#ckbCheckAll', function () {
                if (this.checked) {
                    jQuery('#create_hrs').prop('disabled', false);
                    jQuery('.chkBoxClass').each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery('.chkBoxClass').each(function () {
                        this.checked = false;
                    });
                    jQuery('#create_hrs').prop('disabled', true);
                }
            });

            jQuery(document).on('click', '.chkBoxClass', function () {
                if ($('.chkBoxClass:checked').length == $('.chkBoxClass').length) {
                    $('#ckbCheckAll').prop('checked', true);
                    jQuery('#launch_model').prop('disabled', false);
                } else {
                    var checklength = $('.chkBoxClass:checked').length;
                    if (checklength < 1) {
                        jQuery('#create_hrs').prop('disabled', true);
                    } else {
                        jQuery('#create_hrs').prop('disabled', false);
                    }

                    $('#ckbCheckAll').prop('checked', false);
                }
            });

        });
        /////////////////////////////////////////////////////////////////
        $('#create_hrs').click(function () {

                var consignmentID = [];
                $(':checkbox[name="checked_consign[]"]:checked').each(function () {
                    consignmentID.push(this.value);
                });

                $.ajax({
                    url: "create-hrs",
                    method: "POST",
                    data: { consignmentID: consignmentID },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('.disableDrs').prop('disabled', true);

                    },
                    complete: function (response) {
                        $('.disableDrs').prop('disabled', true);
                    },
                    success: function (data) {
                        if (data.success == true) {
                            swal('success','Hrs Created Successfully','success');
                            window.location.href = "hrs-sheet";
                        }
                        else {
                            swal('error','something wrong','error');
                        }

                    }
                })

});


        $('#unverified-table').DataTable(
            {
                dom: 'lrt',
                "lengthChange": false,
                orderable: false,
            },
        );


        $('#myInput').on('keyup', function () {
            $('#unverified-table').DataTable().search(this.value).draw();
        });
    </script>
@endsection
