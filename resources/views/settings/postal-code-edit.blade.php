@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<style>
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

.table>tbody>tr>td {
    vertical-align: middle;
    color: #515365;
    padding: 3px 21px;
    font-size: 13px;
    letter-spacing: normal;
    font-weight: 600;
}

.btn {
    font-size: 10px;
}

.select2-container--open {
    z-index: 99999;
}

.select2-dropdown {
    /* margin-top: 3rem;
    margin-left: 2rem; */
}
</style>
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Postal Code</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    <!-- <a class="btn btn-success ml-2 mt-3" href="{{ url($prefix.'/export-drs-table') }}">Export
                    data</a> -->

                    <div class="container-fluid">
                        <div class="row winery_row_n spaceing_2n mb-3">
                            <div class="col d-flex pr-0">
                                <div class="search-inp w-100">
                                    <form class="navbar-form" role="search">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search" id="search"
                                                data-action="<?php echo url()->current(); ?>">
                                            <!-- <div class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </div> -->
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg lead_bladebtop1_n pl-0">
                                <div class="winery_btn_n btn-section px-0 text-right">
                                    <!-- <a class="btn-primary btn-cstm btn ml-2"
                                        style="font-size: 15px; padding: 9px; width: 130px"
                                        href="{{'consignments/create'}}"><span><i class="fa fa-plus"></i> Add
                                            New</span></a> -->
                                    <?php $authuser = Auth::user();
                                    if($authuser->role_id ==1 ){ ?>
                                    <div class="btn-group relat">
                                        <a style="font-size: 15px; padding: 9px;"
                                            href="<?php echo URL::to($prefix.'/'.$segment.'/export/excel'); ?>"
                                            class="downloadEx btn btn-primary pull-right" download><span><i
                                                    class="fa fa-download"></i> Export</span></a>
                                    </div>
                                    <?php } ?>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                                        style="font-size: 15px; padding: 9px;"
                                        data-action="<?php echo url()->current(); ?>"><span>
                                            <i class="fa fa-refresh"></i> Reset Filters</span></a>
                                    <button type="button" class="btn btn-warning" data-toggle="modal"
                                        data-target="#exampleModalCenter" style="font-size: 15px; padding: 9px;">
                                        Apply Hub
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('settings.postal-code-editAjax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- edit modle -->
<!-- Modal -->
<div class="modal fade" id="postal_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="update_postal_code">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Update Zone Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-row">
                    <div class="col-12">
                        <input type="hidden" id="zone_id" name="zone_id" />
                        <label for="x">Postal Code</label>
                        <input class="form-control form-control-sm" id="postal_code" name="postal_code" placeholder="" readonly />
                    </div>
                    <div class="col-12">
                        <label for="x">State</label>
                        <input class="form-control form-control-sm" id="state" name="state" placeholder="" />
                    </div>
                    <div class="col-12">
                        <label for="x">District</label>
                        <input class="form-control form-control-sm" id="district" name="district" placeholder="" />
                    </div>
                    <div class="col-12">
                        <label for="x">Hub Transfer</label>
                        <input class="form-control form-control-sm" id="hub_transfer" name="hub_transfer"
                            placeholder="" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Update Hub</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update_hub">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Select State</label>
                            <select class="form-control my-select2" id="state_id" name="state_id" tabindex="-1">
                                <option value="">--Select--</option>
                                @foreach($all_states as $all_state)
                                <option value="{{$all_state->state}}">{{$all_state->state}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Select District</label>
                            <select class="form-control tagging" id="state_district" name="district[]"
                                multiple="multiple">
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Select Pickup Hub</label>
                            <select class="form-control my-select2" id="pickup_hub" name="pickup_hub" tabindex="-1"
                                required>
                                <option value="">--Select--</option>
                                @foreach($branchs as $branch)
                                <option value="{{$branch->id}}">{{$branch->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Select Delivery Hub</label>
                            <select class="form-control my-select2" id="hub_assign" name="branch_id" tabindex="-1"
                                required>
                                <option value="">--Select--</option>
                                @foreach($branchs as $branch)
                                <option value="{{$branch->id}}">{{$branch->name}}</option>
                                @endforeach
                            </select>
                        </div>
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
@endsection
@section('js')
<script>
$(document).ready(function() {

    jQuery(function() {
        $('.my-select2').each(function() {
            $(this).select2({
                theme: "bootstrap-5",
                dropdownParent: $(this).parent(), // fix select2 search input focus bug
            })
        })

        // fix select2 bootstrap modal scroll bug
        $(document).on('select2:close', '.my-select2', function(e) {
            var evt = "scroll.select2"
            $(e.target).parents().off(evt)
            $(window).off(evt)
        })
    })

    $('#sheet').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'print'
        ]
    });
});
$(document).on('click', '.edit_postal', function() {
    var postal_id = $(this).val();
    $('#postal_edit').modal('show');

    $.ajax({
        type: "GET",
        url: "edit-postal-code/" + postal_id,
        data: {
            postal_id: postal_id
        },
        beforeSend: //reinitialize Datatables
            function() {

            },
        success: function(data) {
            console.log(data.zone_data);
            $('#primary_zone').val(data.zone_data.primary_zone);
            $('#district').val(data.zone_data.district);
            $('#state').val(data.zone_data.state);
            $('#zone_id').val(data.zone_data.id);
            $('#hub_transfer').val(data.zone_data.hub_transfer);
            $('#postal_code').val(data.zone_data.postal_code);
            
        }

    });
});

$('#update_postal_code').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "update-postal-code",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {

        },
        success: (data) => {
            if (data.success == true) {
                swal('success', data.success_message, 'success');
                window.location.reload();
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});

$('#update_hub').submit(function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    $.ajax({
        url: "update-hub",
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
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});

$('#state_id').change(function() {
    $("#state_district").empty();
    // $("#hub_assign").empty();
    var state_name = $(this).val();

    $.ajax({
        type: 'get',
        url: 'get-district',
        data: {
            state_name: state_name
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        beforeSend: function() {

        },
        success: function(res) {

            $("#state_district").append(
                '<option value="">--select--</option>'
            );
            $.each(res.all_district, function(key, value) {
                $("#state_district").append(
                    '<option value="' +
                    value +
                    '">' +
                    value +
                    "</option>"
                );
            });

        }
    });

});
</script>
@endsection