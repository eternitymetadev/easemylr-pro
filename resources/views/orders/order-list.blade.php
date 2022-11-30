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

        .driverBankDetails {
            border-radius: 12px;
            border: 1px solid #83838360;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .driverBankDetails p, .driverPersonalDetails p {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            font-size: 14px;
            gap: 8px;
            margin-bottom: 4px;
        }

        .driverPersonalDetails p svg {
            height: 14px;
            width: 14px;
        }

        .driverBankDetails p .textHeading, .driverPersonalDetails p .textHeading {
            font-weight: 500;
        }

        .driverBankDetails p .textValue, .driverPersonalDetails p .textValue {
            font-weight: 700;
        }

        .driverPersonalDetails img {
            height: 90px;
            width: 90px;
            border-radius: 50vh;
            background: rgba(248, 183, 9, 0.16);
            margin-right: 2rem;
            margin-left: 1rem;
        }

        .licenceViewLink {
            color: #f8b709;
            cursor: pointer;
            transition: all 150ms ease-in-out;
        }

        .licenceViewLink:hover {
            color: #c69200;
            font-weight: bold;
        }

        /*for image upload*/
        .image_upload:hover .imageUploadInput {
            visibility: visible;

        }

        .image_upload img {
            border: 1px dashed;
            object-fit: cover !important;
        }

        .imageUploadInput {
            position: absolute;
            height: 100%;
            width: 100%;
            background-image: radial-gradient(black, transparent);
            color: #fff;
            top: 0;
            border-radius: 8px;
            visibility: hidden;
        }

        .imageUploadInput label {
            cursor: pointer;
        }

        /*.imageUploadInput:hover label {*/
        /*    visibility: visible;*/
        /*}*/
        .imageUploadInput label svg {
            color: #fff;
        }

        .form-row {
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 0 3px #83838360;
            margin-bottom: 1rem;
        }

        .form-row h6 {
            margin-bottom: 1rem;
            font-weight: 700;
        }
    </style>


    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Order List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <a href="{{'orders/create'}}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Order
                </a>
            </div>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="mb-4 mt-4">
                @csrf
                <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                    <thead>
                    <tr>
                        <!-- <th> </th> -->
                        <th>LR No</th>
                        <th>Consigner Name</th>
                        <th>Consignee Name</th>
                        <th>City</th>
                        <!-- <th>Pin Code</th>
                        <th>Boxes</th>
                        <th>Net Weight</th>
                        <th>EDD</th> -->
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($consignments as $key => $consignment)
                        <tr>
                            <!-- <td class="dt-control">+</td> -->
                            <td>{{ $consignment->id ?? "-" }}</td>
                            <td>{{ $consignment->consigner_id}}</td>
                            <td>{{ $consignment->consignee_id}}</td>
                            <td>{{ $consignment->city ?? "-" }}</td>
                        <!-- <td>{{ $consignment->pincode ?? "-" }}</td>
                                    <td>{{ $consignment->total_quantity ?? "-" }}</td>
                                    <td>{{ $consignment->total_weight ?? "-" }}</td>
                                    <td>{{ $consignment->edd ?? "-" }}</td> -->

                            <td>
                                <div class="d-flex align-content-center justify-content-center" style="gap: 6px">
                                    <a href="{{url($prefix.'/orders/'.Crypt::encrypt($consignment->id).'/edit')}}"
                                       class="edit editIcon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                    <a title="Cancel Order" href="#" data-id="{{$consignment->id}}"
                                       data-action="<?php echo URL::current();?>"
                                       class="orderstatus delete deleteIcon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="feather feather-x-circle">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                            <line x1="9" y1="9" x2="15" y2="15"></line>
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
    @include('models.delete-user')
    @include('models.common-confirm')
@endsection
@section('js')
    <script>
        // Order list status change onchange
        jQuery(document).on('click', '.orderstatus', function (event) {
            event.stopPropagation();

            let order_id = jQuery(this).attr('data-id');
            var dataaction = jQuery(this).attr('data-action');
            var updatestatus = 'updatestatus';
            var status = 0;


            jQuery('#commonconfirm').modal('show');
            jQuery(".commonconfirmclick").one("click", function () {

                var reason_to_cancel = jQuery('#reason_to_cancel').val();
                var data = {
                    id: order_id,
                    updatestatus: updatestatus,
                    status: status,
                    reason_to_cancel: reason_to_cancel
                };

                jQuery.ajax({
                    url: dataaction,
                    type: 'get',
                    cache: false,
                    data: data,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="_token"]').attr('content')
                    },
                    processData: true,
                    beforeSend: function () {
                        // jQuery("input[type=submit]").attr("disabled", "disabled");
                    },
                    complete: function () {
                        //jQuery("#loader-section").css('display','none');
                    },

                    success: function (response) {
                        if (response.success) {
                            jQuery('#commonconfirm').modal('hide');
                            if (response.page == 'order-statusupdate') {
                                setTimeout(() => {
                                    window.location.href = response.redirect_url
                                }, 10);
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
