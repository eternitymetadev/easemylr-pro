@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <style>
        td p {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        p.consigner {
            border-radius: 6px;
            padding: 2px 6px;
            background: #f5f5f5;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        td p.consigner span {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td p.consigner span.legalName {
            font-weight: 700;
            font-size: 14px;
        }

        .textOverflow {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            white-space: nowrap;
        }

        .detailsBlock {
            padding: 1rem;
            gap: 0.5rem;
        }

        .detailsBlock p {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 14px;
            line-height: 17px;
            font-weight: 600;
            margin-bottom: 0;
        }

        .detailsBlock p .detailKey {
            font-size: 14px;
            line-height: 17px;
            font-weight: 400;
        }

        .contactDetails {
            min-width: 250px;
            flex: 1;
            border-radius: 12px;
            border: 1px solid;
        }

        .contactDetails p .detailKey {
            min-width: 50px;
        }

        .addressBlock {
            border-radius: 12px;
            background: #f9b80820;
        }


    </style>
    <div class="layout-px-spacing">

        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Consigner List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <a href="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>"
                   class="downloadEx btn btn-primary pull-right"
                   data-action="<?php echo URL::to($prefix . '/' . $segment . '/export/excel'); ?>"
                   download>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-download-cloud">
                        <polyline points="8 17 12 21 16 17"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                    </svg>
                    Excel
                </a>
                <button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createConsigner">
                    {{--                <a class="btn btn-primary" id="add_role" href="{{'consigners/create'}}">--}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Consigner
                </button>
            </div>
        </div>
        <div class="widget-content widget-content-area br-6 px-3">

            <div class="mb-4 mt-4" style="min-height: 350px">
                @csrf
                <table id="consignerstable" class="table table-hover" style="width:100%;">
                    <thead>
                    <tr>
                        <th>Cnr ID</th>
                        <th>Client Name</th>
                        <th>Location</th>
                        <th>Contact Person</th>
                        <th>Mobile No.</th>
                        {{--                        <th>PIN Code</th>--}}
                        {{--                        <th>City</th>--}}
                        {{--                        <th>District</th>--}}
                        {{--                        <th>State</th>--}}
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@include('models.delete-consigner')
@include('consigners.crud-models')
@endsection
@section('js')
    <script>
        $(".consignerView").click(function () {
            alert('sfds');

        }); 
     
        var table = $('#consignerstable').DataTable({
            processing: true,
            serverSide: true,

            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
                "sLengthMenu": "Results :  _MENU_",
            },


            "stripeClasses": [],
            "pageLength": 30,
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered');
            },

            columns: [
                {data: 'id', name: 'id', defaultContent: '-'},
                {data: 'regclient', name: 'regclient', defaultContent: '-'},
                {data: 'location', name: 'location', defaultContent: '-'},
                {data: 'contact_name', name: 'contact_name', defaultContent: '-'},
                {data: 'phone', name: 'phone', defaultContent: '-'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]

            // columns: [
            //     {data: 'id', name: 'id', defaultContent: '-'},
            //     {data: 'regclient', name: 'regclient', defaultContent: '-'},
            //     {data: 'nick_name', name: 'nick_name', defaultContent: '-'},
            //     {data: 'contact_name', name: 'contact_name', defaultContent: '-'},
            //     {data: 'phone', name: 'phone', defaultContent: '-'},
            //     {data: 'postal_code', name: 'postal_code', defaultContent: '-'},
            //     {data: 'city', name: 'city', defaultContent: '-'},
            //     {data: 'district', name: 'district', defaultContent: '-'},
            //     {data: 'state', name: 'state', defaultContent: '-'},
            //     {data: 'action', name: 'action', orderable: false, searchable: false}
            // ]

        });


        function closeConsignerDetaislModal() {
            $('#consignerDetailsModal').modal('hide');
            setTimeout(() => {
                $('#editConsignerIcon').click();
            }, 400)
        }
// 

    </script>
@endsection
