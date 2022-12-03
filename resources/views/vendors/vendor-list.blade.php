@extends('layouts.main')
@section('content')

    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

    <style>
        #vendor_list p {
            margin-bottom: 0;
        }

        .vendorId {
            padding: 0px 6px 0 2px;
            border-radius: 4px;
            border: 0.5px solid #b6b6b6;
            color: #cc8839;
        }

        .vendorName {
            color: #363636;
            font-size: 14px;
            font-weight: 700
        }

        .vendorId span {
            color: #000;
        }

        .panView {
            height: 18px;
            width: 18px;
            background: #ffd35d;
            padding: 3px;
            border-radius: 48px;
            color: #0b0b0b;
        }

        .vdTitle {
            width: 100%;
            font-size: 1.1rem;
            font-weight: 800;
            color: #818181;
            border-bottom: 1px solid;
            margin-bottom: 16px;
        }

        .vendorBankDetails {
            border-radius: 12px;
            background: #0330730f;
        }

        #view_vendor p.between {
            justify-content: space-between;
        }

        #view_vendor p {
            font-weight: 700;
            display: flex;
            gap: 8px;
            color: #808080;
        }

        #view_vendor p span {
            color: #000;
            max-width: 65%;
        }

    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Vendor List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <a href="{{ url($prefix.'/export-vendor') }}" class="downloadEx btn btn-primary pull-right">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-download-cloud">
                        <polyline points="8 17 12 21 16 17"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                    </svg>
                    Export
                </a>

                <?php $authuser = Auth::user();
                if($authuser->role_id == 5){?>
                <button class="dsd btn btn-primary pull-right">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-download-cloud">
                        <polyline points="8 17 12 21 16 17"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                    </svg>
                    Import
                </button>
                <?php } ?>

                {{--<button class="btn btn-primary" id="add_role" data-toggle="modal" data-target="#createConsigner">--}}
                <a class="btn btn-primary" href="{{'vendor/create'}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Vendor
                </a>
            </div>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="mb-4 mt-4">
                @csrf
                <table id="vendor_list" class="table table-hover">
                    <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Location</th>
                        <th>Pan Number</th>
                        {{--                        <th>TDS Rate</th>--}}
                        <th>Transporter Name</th>
                        <th style="text-align: center;">Declaration</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($vendors as $vendor)
                        <?php $bank_details = json_decode($vendor->bank_details);
                        $other_details = json_decode($vendor->other_details);
                        $img = URL::to('/drs/uploadpan/' . $vendor->upload_pan);
                        $decl = URL::to('/drs/declaration/' . $vendor->declaration_file . '');
                        ?>
                        <tr>
                            <td class="py-1">
                                <p class="textWrap" style="max-width: 300px">
                                    <span class="vendorId">
                                        ID: <span>{{$vendor->vendor_no ?? '-'}}</span>
                                    </span>
                                    <span class="ml-1" style="color: #858501;">{{$vendor->vendor_type ?? '-'}}</span>
                                    <br/>
                                    <span class="vendorName" title="{{$vendor->name}}">
                                        {{$vendor->name}}
                                    </span>
                                </p>
                            </td>
                            <td><p>{{$vendor->Branch->name ?? '-'}}</p></td>
                            <td>
                                <p>
                                    {{$vendor->pan ?? '-'}}
                                    @if(!empty($vendor->upload_pan))
                                        <a target='_blank' href="{{$img}}" class="swan-tooltip-right"
                                           data-tooltip="View Pan card">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-eye panView">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    @endif
                                </p>
                            </td>
                            {{--                            <td><p>{{$vendor->tds_rate ?? '-'}}</p></td>--}}
                            <td><p class="textWrap" style="max-width: 200px"
                                   title="{{$other_details->transporter_name ?? '-'}}">{{$other_details->transporter_name ?? '-'}}</p>
                            </td>
                            <td style="text-align: center;">
                                @if(!empty($vendor->declaration_file))
                                    <a target='_blank' href="{{$decl}}">View</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="button" class="swan-tooltip-left view viewIcon"
                                            value="{{$vendor->id}}"
                                            style="border: none"
                                            data-tooltip="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="feather feather-eye">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                    <a href="{{ url($prefix.'/edit-vendor/'.$vendor->id) }}"
                                       class="swan-tooltip-left edit editIcon"
                                       data-tooltip="Edit Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('models.view-vendor')
@endsection
@section('js')
    <script>
        $(document).on('click', '.view', function () {

            var vendor_id = $(this).val();
            $('#view_vendor').modal('show');
            $.ajax({
                type: "GET",
                url: "view-vendor-details",
                data: {
                    vendor_id: vendor_id
                },
                beforeSend: //reinitialize Datatables
                    function () {
                        $('#name').empty()
                        $('#trans_name').empty()
                        $('#driver_nm').empty()
                        $('#cont_num').empty()
                        $('#cont_email').empty()
                        $('#acc_holder').empty()
                        $('#acc_no').empty()
                        $('#ifsc_code').empty()
                        $('#bank_name').empty()
                        $('#branch_name').empty()
                        $('#pan').empty()
                        $('#vendor_type').empty()
                        $('#decl_avl').empty()
                        $('#tds_rate').empty()
                        $('#branch_id').empty()
                        $('#gst').empty()
                        $('#gst_no').empty()
                    },
                success: function (data) {

                    var other_details = jQuery.parseJSON(data.view_details.other_details);
                    var bank_details = jQuery.parseJSON(data.view_details.bank_details);

                    $('#vendor_id').html(data.view_details.id ? data.view_details.id : '-NA-')
                    $('#name').html(data.view_details.name)
                    $('#trans_name').html(other_details.transporter_name)
                    if (data.view_details.driver_detail == '' || data.view_details.driver_detail == null) {
                        $('#driver_nm').html('-')
                    } else {
                        $('#driver_nm').html(data.view_details.driver_detail.name)
                    }
                    $('#cont_num').html(other_details.contact_person_number ? other_details.contact_person_number : '-NA-')
                    $('#cont_email').html(data.view_details.email ? data.view_details.email : '-NA-')
                    $('#acc_holder').html(bank_details.acc_holder_name)
                    $('#acc_no').html(bank_details.account_no)
                    $('#ifsc_code').html(bank_details.ifsc_code)
                    $('#bank_name').html(bank_details.bank_name)
                    $('#branch_name').html(bank_details.branch_name)
                    $('#pan').html(data.view_details.pan)
                    $('#vendor_type').html(data.view_details.vendor_type)
                    $('#decl_avl').html(data.view_details.declaration_available)
                    $('#tds_rate').html(data.view_details.tds_rate)
                    $('#branch_id').html(data.view_details.branch_id)
                    $('#gst').html(data.view_details.gst_register)
                    $('#gst_no').html(data.view_details.gst_no ? data.view_details.gst_no : 'Unregistered')
                }

            });

        });

        $(document).on('click', '.dsd', function () {

            $('#imp_vendor_modal').modal('show');
        });


        $('#vendor_list').DataTable({
            columnDefs: [
                {orderable: false, targets: [2, 4, 5]}
            ],
            "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [
                    // { extend: 'copy', className: 'btn btn-sm' },
                    // { extend: 'csv', className: 'btn btn-sm' },
                    // { extend: 'excel', className: 'btn btn-sm', title: '', },
                    // { extend: 'print', className: 'btn btn-sm' }
                ]
            },
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

            "ordering": true,
            "paging": true,
            "pageLength": 80,

        });
    </script>
@endsection
