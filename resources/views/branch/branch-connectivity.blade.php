@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<style>
.dt--top-section {
    margin: 0;
}

div.relative {
    position: absolute;
    left: 110px;
    top: 24px;
    z-index: 1;
    width: 83px;
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

.select2-container {
    z-index: 99999;
}

.select2-dropdown {
    margin-top: 3rem;
    margin-left: 2rem;
}
</style>

<div class="layout-px-spacing">
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">Connectivity List</h2>
        <div class="d-flex align-content-center" style="gap: 1rem;">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter"
                style="font-size: 11px;">
                Add Branch Connectivity
            </button>
        </div>
    </div>

    <div class="widget-content widget-content-area br-6">
        <div class="table-responsive mb-4 mt-4 px-3">
            @csrf
            <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>EFPL Hub</th>
                        <th>Direct Connectivity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($locations) > 0)
                    @foreach($locations as $key => $value)
                    <tr>
                        <td>{{ $value->id ?? '-' }}</td>
                        <td>{{ ucwords($value->Location->name ?? '-') }}</td>
                        <?php $str = $value->direct_connectivity;
                        $connect_hubs = (explode(",",$str)); 
                        $getbranch = DB::table('locations')->whereIn('id', $connect_hubs)->get();
                       ?>
                       <?php $locarray = array(); ?>
                        @foreach($getbranch as $loc)
                        <?php $locarray[] = $loc->name; ?>
                        @endforeach
                        <?php 
                       
                        $hubs = implode(',',$locarray); ?>
                        <td>{{ $hubs }}</td>
                        <td>
                            <div class="d-flex align-content-center justify-content-center" style="gap: 6px">
                                <a class="btn btn-primary editIcon edit-connectivity" href="javascript:void(0)"
                                    data-action="<?php echo URL::to($prefix . '/branch-connectivity/edit-branch-connectivity'); ?>"
                                    data-id="{{ $value->id }}" data-toggle="modal"
                                    data-target="#connectivity-updatemodal">
                                    <span><i class="fa fa-edit"></i></span>
                                </a>
                                <a href="Javascript:void();" class="btn btn-danger delete_connectivity" data-id="{{ $value->id }}" data-action="<?php echo URL::to($prefix.'/branch-connectivity/delete-branch-connectivity'); ?>">
                                    <span><i class="fa fa-trash"></i></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!--Add connectivity Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Branch Connectivity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_connectivity">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Hub</label>
                            <select class="form-control" name="hub">
                                <option disabled selected>Select..</option>
                                @foreach($branchs as $branch)
                                <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="form-group col-md-12">
                        <label for="inputPassword4">Direct Connectivity</label>
                        <select class="form-control  tagging" name="direct_connectivity[]" multiple="multiple">
                            <option disabled>Select..</option>
                            @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                            @endforeach
                        </select>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="crt_pytm"><span class="indicator-label">Submit</span>
                    <span class="indicator-progress" style="display: none;">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
            </div>
            </form>
        </div>
    </div>
</div>

<!--Update connectivity Modal -->
<div class="modal fade" id="connectivity-updatemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Update Branch Connectivity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update_connectivity">
                    <input type="hidden" id="branchid_hidden" name="branchid_hidden" value="">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Hub</label>
                            <select class="form-control" id="branch_nameup" name="hub">
                                <option disabled selected>Select..</option>
                                @foreach($branchs as $branch)
                                <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="form-group col-md-12">
                        <label for="inputPassword4">Direct Connectivity</label>
                        <select class="form-control  tagging" id="direct_connectivityup" name="direct_connectivity[]" multiple="multiple">
                            <option disabled>Select..</option>
                            @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                            @endforeach
                        </select>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id=""><span class="indicator-label">Submit</span>
                    <span class="indicator-progress" style="display: none;">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
            </div>
            </form>
        </div>
    </div>
</div>


@include('models.delete-connectivity')
@endsection
@section('js')
<script>
$('#add_connectivity').submit(function(e) {
    alert('ll');
    e.preventDefault();

    var formData = new FormData(this);
    $.ajax({
        url: "add-branch-connectivity",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal('success', data.success_message, 'success');
                window.location.reload();
            } else if(data.validation == true) {
                swal('error', data.error_message, 'error');
            }else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});

/*===== get location on edit click =====*/   
    $(".edit-connectivity").click(function() {
    var branch_id = jQuery(this).attr("data-id");
    jQuery("#branchid_hidden").val(branch_id);
    var action = jQuery(this).attr("data-action");
    jQuery.ajax({
        type: "post",
        url: action,
        data: { branch_id:branch_id },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            console.log(response.branch_data);
            $('#branch_nameup').val(response.branch_data.efpl_hub).change();
            var myString = response.branch_data.direct_connectivity;
            var myArray = myString.split(",");
            var branchID = [];
            $.each(myArray, function(key, value) {
                console.log(key);
                branchID.push(value);
            });
            $('#direct_connectivityup').val(branchID).change();
        },
    });

});

// update conectivity

$('#update_connectivity').submit(function(e) {

    e.preventDefault();

    var formData = new FormData(this);
    $.ajax({
        url: "update-branch-connectivity",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal('success', data.success_message, 'success');
                window.location.reload();
            } else if(data.validation == true) {
                swal('error', data.error_message, 'error');
            }else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});

/*===== delete connectivity =====*/
jQuery(document).on("click", ".delete_connectivity", function () {
        jQuery("#deletelocation").modal("show");
        var location_id = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deletelocationconfirm")
            .on("click", ".deletelocationconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { location_id: location_id },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deletelocation").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
</script>

@endsection