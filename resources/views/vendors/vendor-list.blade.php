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

.btn-group>.btn,
.btn-group .btn {
    padding: 0px 0px;
    padding: 10px;
}

.btn {

    font-size: 10px;
}

.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
}

.move {
    cursor: move;
}
</style>
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Vendor List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
            <a href="{{'vendor/create'}}" class="btn btn-primary " style="font-size: 13px; padding: 6px 0px;">Add Vendor</a>
                <div class="table-responsive mb-4 mt-4">
                    @csrf
                    <table id="" class="table table-hover get-datatable" style="width:100%">
                       
                        <thead>
                            <tr>
                                <th>Vendor Name</th>
                                <th>Transporter Name</th>
                                <th>Driver Name <th>
                                <th>Contact Email</th>
                                <th>Contact Number</th>
                                <th>Acc Holder Name</th>
                                <th>Account No.</th>
                                <th>IFSC </th>
                                <th>Bank Name</th>
                                <th>Branch Name</th>
                                <th>Cancel Cheaque</th>
                                <th>Pan</th>
                                <th>Upload Pan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                            <?php $bank_details = json_decode($vendor->bank_details);
                                  $other_details = json_decode($vendor->other_details);
                            ?>
                            <tr>
                                <td>{{$vendor->name ?? '-'}}</td>
                                <td>{{$other_details->transporter_name ?? '-'}}</td>
                                <td>{{$other_details->contact_person_name ?? '-'}}</td>
                                <td>{{$other_details->contact_person_number ?? '-'}}</td>
                                <td>{{$vendor->email ?? '-'}}</td>
                                <td>{{$bank_details->account_no ?? '-'}}</td>
                                <td>{{$vendor->pan ?? '-'}}</td>
                                <td>{{$vendor->upload_pan ?? '-'}}</td>
                                <td>{{$vendor->email ?? '-'}}</td>
                                <td>{{$bank_details->account_no ?? '-'}}</td>
                                <td>{{$vendor->pan ?? '-'}}</td>
                                <td>{{$vendor->upload_pan ?? '-'}}</td>
                                <td>{{$vendor->upload_pan ?? '-'}}</td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
</script>
@endsection