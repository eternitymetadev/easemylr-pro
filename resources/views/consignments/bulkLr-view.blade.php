@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <style>
        input[type=checkbox] {
            accent-color: #ffb200;
            cursor: pointer;
        }

        input[type=checkbox] + label {
            cursor: pointer;
            user-select: none;
        }

        .pdfDownloadBlock {
            gap: 1rem;
            border-radius: 8px;
            padding: 2px 2px 2px 16px;
            box-shadow: 0 0 7px #83838370 inset;
        }

        .pdfDownloadButton {
            border-radius: 8px;
            background-color: #f9b600;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .pdfDownloadButton[disabled] {
            background-color: #ffb200 !important;
            cursor: not-allowed;
            color: #000;
        }

        .searchIcon {
            position: absolute;
            left: 0.5rem;
            height: 1rem;
            width: 1rem;
        }

        .dataTables_paginate {
            width: 100%;
            margin-top: 1rem !important;
            padding-inline: 1rem;
        }

    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Bulk LR Download</h2>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="mb-4 mt-4">
                <form method="post" action="{{url($prefix.'/download-bulklr')}}">

                    <div class=" pb-3 px-3 d-flex flex-wrap justify-content-between align-items-center"
                         style="gap: 1rem;">
                        <div class="d-flex justify-content-center align-items-center"
                             style="flex: 1; max-width: 240px; position: relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-search searchIcon">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input class="form-control form-control-sm"
                                   style="width: 100%; height: 38px; padding-left: 2rem"
                                   id="myInput" type="text" placeholder="search..."/>
                        </div>
                        <div
                            class="pdfDownloadBlock d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 8px">
                                <input type="checkbox" id="vehicle1" name="type[]" value="1"/>
                                <label for="vehicle1">ORIGINAL</label>
                            </div>
                            <div class="d-flex justify-content-center align-items-center" style="gap: 8px">
                                <input type="checkbox" id="vehicle2" name="type[]" value="2"/>
                                <label for="vehicle2"> DUPLICATE</label>
                            </div>
                            <div class="d-flex justify-content-center align-items-center" style="gap: 8px">
                                <input type="checkbox" id="vehicle3" name="type[]" value="3" checked/>
                                <label for="vehicle3"> TRIPLICATE</label>
                            </div>
                            <div class="d-flex justify-content-center align-items-center" style="gap: 8px">
                                <input type="checkbox" id="vehicle4" name="type[]" value="4"/>
                                <label for="vehicle4"> QUADRUPLE</label>
                            </div>

                            <div>
                                <input class="btn btn-primary pdfDownloadButton bulk_loder" type="submit"
                                       value="Download Bulk Pdf"
                                       id="bulk" disabled/>
                                <div class="spinner-border text-primary  align-self-center" id="pageloader"
                                     style="display: none;"></div>
                            </div>

                        </div>
                    </div>

                    @csrf
                    <table id="bulk-table" class="table table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" name="" id="checkAll_Lr"
                                       style="width: 18px; height:18px; margin: 2px 10px;"/>
                            </th>
                            <th>LR No.</th>
                            <th>LR Date</th>
                            <th>Consigner</th>
                            <th>Consignee</th>
                            <th>City</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($consignments as $value)
                            <tr>
                                <td><input type="checkbox" name="checked_lr[]" class="checkLr"
                                           value="{{$value->id}}" data-trp="" data-vehno="" data-vctype=""
                                           style="width: 16px; height:16px; margin: 10px"></td>
                                <td>{{$value->id}}</td>
                                <td>{{$value->consignment_date}}</td>
                                <?php
                                if ($value->is_salereturn == 1) {
                                    $cnr_nickname = @$value->ConsigneeDetail->nick_name;
                                    $cne_nickname = @$value->ConsignerDetail->nick_name;
                                    $cne_city = @$value->ConsignerDetail->city;
                                } else {
                                    $cnr_nickname = $value->ConsignerDetail->nick_name;
                                    $cne_nickname = @$value->ConsigneeDetail->nick_name;
                                    $cne_city = @$value->ConsigneeDetail->city;
                                } ?>
                                <td>{{$cnr_nickname ?? '-'}}</td>
                                <td>{{$cne_nickname ?? '-'}}</td>
                                <td>{{$cne_city ?? '-'}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        jQuery(document).on('click', '#checkAll_Lr', function () {
            if (this.checked) {
                jQuery('#bulk').prop('disabled', false);
                jQuery('.checkLr').each(function () {
                    this.checked = true;
                });
            } else {
                jQuery('.checkLr').each(function () {
                    this.checked = false;
                });
                jQuery('#bulk').prop('disabled', true);
            }
        });

        jQuery(document).on('click', '.checkLr', function () {
            if ($('.chkBoxClass:checked').length == $('.checkLr').length) {
                $('#checkAll_Lr').prop('checked', true);
            } else {
                var checklength = $('.checkLr:checked').length;
                if (checklength < 1) {
                    jQuery('#bulk').prop('disabled', true);
                } else {
                    jQuery('#bulk').prop('disabled', false);
                }

                $('#checkAll_Lr').prop('checked', false);
            }
        });
        $(function () {
            $('#download_bulkLr').click(function () {
                // alert('hi'); return false;

                var consignmentID = [];
                $(':checkbox[name="checked_lr[]"]:checked').each(function () {
                    consignmentID.push(this.value);
                });
                //alert(consignmentID); return false;

                $.ajax({
                    url: "download-bulklr",
                    method: "POST",
                    data: {consignmentID: consignmentID},
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

                            alert('Data Updated Successfully');
                            location.reload();
                        } else {
                            alert('something wrong');
                        }

                    }
                })


            });
        });


        //////////////////////////////////////////
        $('#bulk-table').DataTable({
            dom: 'tp',
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
            },

            "ordering": false,
            "paging": true,
            "pageLength": 120,

        });

        $('#myInput').on('keyup', function () {
            $('#bulk-table').DataTable().search(this.value).draw();
        });

    </script>

@endsection
