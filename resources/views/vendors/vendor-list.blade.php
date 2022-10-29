@extends('layouts.main')
@section('content')

<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Payments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Vendor
                                List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <a href="{{'vendor/create'}}" class="btn btn-primary mt-3"
                    style="margin-left:4px; font-size: 13px; padding: 6px 0px;">Add Vendor</a><?php $authuser = Auth::user(); 
                                if($authuser->role_id == 5){?> <button type="button"
                    class="btn btn-primary ml-2 mt-3 dsd">Import Vendor</button>
                <?php } ?><a class="btn btn-success ml-2 mt-3" href="{{ url($prefix.'/export-vendor') }}">Export
                    data</a>
                <div class="mb-4 mt-4">

                    @csrf
                    <table id="vendor_list" class="table table-hover">

                        <thead>
                            <tr>
                                <th>Vendor Code</th>
                                <th>Location</th>
                                <th>Vendor Name </th>
                                <th>Transporter Name</th>
                                <th>Pan No</th>
                                <th>Vendor Type</th>
                                <th>TDS Rate</th>
                                <th>Pan View</th>
                                <th>Declaration View</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                            <?php $bank_details = json_decode($vendor->bank_details);
                                  $other_details = json_decode($vendor->other_details);
                                  $img = URL::to($prefix.'/drs/uploadpan/'.$vendor->upload_pan);
                                  $decl = URL::to('/drs/declaration/'.$vendor->declaration_file.'');
                            ?>
                            <tr>
                                <td>{{$vendor->vendor_no ?? '-'}}</td>
                                <td>{{$vendor->Branch->name ?? '-'}}</td>
                                <td>{{$vendor->name}}</td>
                                <td>{{$other_details->transporter_name ?? '-'}}</td>
                                <td>{{$vendor->pan ?? '-'}}</td>
                                <td>{{$vendor->vendor_type ?? '-'}}</td>
                                <td>{{$vendor->tds_rate ?? '-'}}</td>
                                <?php if(!empty($vendor->upload_pan)){?>
                                <td><a class="btn btn-sm btn-warning" target='_blank' href="{{$img}}"
                                        role="button">Pan</a></td>
                                <?php }else{ ?>
                                <td>-</td>
                                <?php } ?>
                                <?php if(!empty($vendor->declaration_file)){?>
                                <td><a class="btn btn-sm btn-warning" target='_blank' href="{{$decl}}"
                                        role="button">Declaration</a></td>
                                <?php }else{ ?>
                                <td>-</td>
                                <?php } ?>
                                <td><button type="button" class="btn btn-sm btn-primary view"
                                        value="{{$vendor->id}}">View</button> <a
                                        href="{{ url($prefix.'/edit-vendor/'.$vendor->id) }}"
                                        class="edit btn btn-sm btn-primary ml-2"><i class="fa fa-edit"></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.view-vendor')
@endsection
@section('js')
<script>
$(document).on('click', '.view', function() {

    var vendor_id = $(this).val();
    $('#view_vendor').modal('show');
    $.ajax({
        type: "GET",
        url: "view-vendor-details",
        data: {
            vendor_id: vendor_id
        },
        beforeSend: //reinitialize Datatables
            function() {
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
        success: function(data) {

            var other_details = jQuery.parseJSON(data.view_details.other_details);
            var bank_details = jQuery.parseJSON(data.view_details.bank_details);

            $('#name').html(data.view_details.name)
            $('#trans_name').html(other_details.transporter_name)
            if(data.view_details.driver_detail == '' || data.view_details.driver_detail == null){
            $('#driver_nm').html('-')
            }else{
            $('#driver_nm').html(data.view_details.driver_detail.name)
            }
            $('#cont_num').html(other_details.contact_person_number)
            $('#cont_email').html(data.view_details.email)
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
            $('#gst_no').html(data.view_details.gst_no)
        }

    });

});

$(document).on('click', '.dsd', function() {

    $('#imp_vendor_modal').modal('show');
});


$('#vendor_list').DataTable({
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