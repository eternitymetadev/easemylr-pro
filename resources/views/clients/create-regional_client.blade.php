@extends('layouts.main')
@section('content')
<style>
.row.layout-top-spacing {
    width: 80%;
    margin: auto;

}
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create
                                Regional Client</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/create-regional')}}" id="createclient">
                            <!-- <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Client Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="client_name" id="client_name" placeholder="">
                            </div> -->
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Client Name<span
                                            class="text-danger">*</span></label>
                                            <select class="form-control  my-select2" id="base_client_id" name="base_client_id"
                                        tabindex="-1">
                                        <option selected disabled>select..</option>
                                        @foreach($base_clients as $base_client)
                                        <option value="{{ $base_client->id }}">{{ucwords($base_client->client_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Branch Location</label>
                                    <select class="form-control  my-select2" id="branch_id" name="branch_id"
                                        tabindex="-1">
                                        <option selected disabled>select..</option>
                                        @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ucwords($location->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Regional Client Name</label>
                                    <input type="text" class="form-control" id="regional_client_name" name="name" placeholder="" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Regional Client Nick Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="regional_client_nick_name" placeholder="">
                                </div>
                            </div> 
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Email</label>
                                    <input type="email" class="form-control" id="" name="email" placeholder="" >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Phone<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="phone" placeholder="">
                                </div>
                            </div> 
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">GST Number<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="gst_no" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">PAN<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="pan" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Upload GST RC<span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="upload_gst" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Upload PAN<span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="upload_pan" placeholder="">
                                </div>
                            </div>
                      

                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/clients') }}"> Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script>
// $("a").click(function(){
function addrow() {
    var i = $('.rowcls').length;
    i = i + 1;
    var rows = '';

    rows += '<tr class="rowcls">';
    rows += '<td>';
    rows += '<input type="text" class="form-control name" name="data[' + i + '][name]" placeholder="">';
    rows += '</td>';
    rows += '<td>';
    rows += '<input type="email" class="form-control name" name="data[' + i + '][email]" placeholder="">';
    rows += '</td>';
    rows += '<td>';
    rows += '<select class="form-control location_id" name="data[' + i + '][location_id]">';
    rows += '<option value="">Select</option>';
    <?php if (count($locations) > 0) {
    foreach ($locations as $key => $location) {
        ?>
    rows += '<option value="{{ $key }}">{{ucwords($location)}}</option>';
    <?php
    }
    }
    ?>
    rows += '</select>';
    rows += '</td>';
    rows += '<td>';
    rows += '<select class="form-control is_multiple_invoice" name="data[' + i +
        '][is_multiple_invoice]"> <option value="">Select..</option> <option value="1">Per invoice-Item wise</option> <option value="2">Multiple Invoice-Item wise</option> <option value="3">per invoice-Without Item</option> <option value="4">LR Multiple invoice-Without item</option> </select>';
    rows += '</td>';
    rows +=
        '<td><div class="check-box d-flex"><div class="checkbox radio"><label class="check-label">Yes<input type="radio"  value="1" name="data[' +
        i + '][is_prs_pickup]"><span class="checkmark"></span></label></div>';
    rows += '<div class="checkbox radio"><label class="check-label">No<input type="radio" name="data[' + i +
        '][is_prs_pickup]" value="0" checked><span class="checkmark"></span></label></div></div></td>';
    rows +=
        '<td><div class="check-box d-flex"><div class="checkbox radio"><label class="check-label">Yes<input type="radio"  value="1" name="data[' +
        i + '][is_email_sent]"><span class="checkmark"></span></label></div>';
    rows += '<div class="checkbox radio"><label class="check-label">No<input type="radio" name="data[' + i +
        '][is_email_sent]" value="0" checked><span class="checkmark"></span></label></div></div></td>';
    rows += '<td>';
    rows += '<button type="button" class="btn btn-danger removeRow"><i class="fa fa-minus-circle"></i></button>';
    rows += '</td>';
    rows += '</tr>';

    $('#myTable tbody').append(rows);

}

$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
});

$("#branch_id").change(function(){
    var base_client = $('#base_client_id').val();
    var branch_id = $(this).val();
    $.ajax({
        url: "generate-regional",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: { base_client: base_client, branch_id:branch_id },
     
        beforeSend: function () {
            
        },
        success: (data) => {
            // console.log(data.generate_regional);
            $('#regional_client_name').val(data.generate_regional);
       
        },
    });
   
});

</script>
@endsection