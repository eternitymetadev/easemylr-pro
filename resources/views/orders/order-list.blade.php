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

.myButtonExtra {
    border-radius: 8px !important;
    width: 120px;
    font-size: 13px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
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
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Order Bookings</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Order
                                List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    @csrf
                    <table id="usertable" class="table table-hover get-datatable" style="width:100%">
                        <div class="btn-group relative" style="width: auto">
                            <a href="{{'order-book-ptl'}}" class="btn btn-primary myButtonExtra"
                                style="font-size: 13px; padding: 6px 0px;">Create Order</a>
                            <button type="button" class="btn btn-primary myButtonExtra ml-2" data-toggle="modal"
                                data-target="#exampleModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-upload-cloud mr-1">
                                    <polyline points="16 16 12 12 8 16"></polyline>
                                    <line x1="12" y1="12" x2="12" y2="21"></line>
                                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                                    <polyline points="16 16 12 12 8 16"></polyline>
                                </svg>
                                upload
                            </button>
                        </div>
                        <thead>
                            <tr>
                                <!-- <th> </th> -->
                                <th>LR No</th>
                                <th>Pickup ID</th>
                                <th>LR Date</th>
                                <th>Booking Branch</th>
                                <th>Pickup Zone</th>
                                <th>Delivery Zone</th>
                                <th>Billing Client</th>
                                <th>Consignor Name</th>
                                <th>Consignor Pin Code</th>
                                <th>Consignee Name</th>
                                <th>Pin Code</th>
                                <th>Delivery City</th>
                                <th>Invoice no</th>
                                <th>Order No</th>
                                <th>Quantity</th>
                                <th>Net Weight</th>
                                <th>Booking Mode</th>
                                <th>Pickup Status</th>
                                <!-- <th>Action</th> -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($consignments as $key => $consignment) {
                                ?>
                            <tr>
                                <!-- <td class="dt-control">+</td> -->
                                <td>{{ @$consignment->id ?? "-" }}</td>
                                <td>{{ @$consignment->PrsDetail->pickup_id ?? "NA" }}</td>
                                <td>{{ @$consignment->consignment_date ?? "-" }}</td>
                                <td>{{ @$consignment->Branch->name ?? "-" }}</td>
                                <td>{{ @$consignment->fallIn->name ?? "-" }}</td>
                                <td>{{ @$consignment->ToBranch->name ?? "-" }}</td>
                                <td>{{ @$consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                                <td>{{ @$consignment->ConsignerDetail->nick_name ?? "-"}}</td>
                                <td>{{ @$consignment->ConsignerDetail->postal_code  ?? "-"}}</td>
                                <td>{{ @@$consignment->ConsigneeDetail->nick_name  ?? "-"}}</td>
                                <td>{{ @$consignment->ConsigneeDetail->postal_code  ?? "-"}}</td>
                                <td>{{ @$consignment->ConsigneeDetail->city ?? "-" }}</td>
                                <td>{{ @$consignment->ConsignmentItem->invoice_no ?? "-" }}</td>
                                <td>{{ @$consignment->ConsignmentItem->order_id ?? "-" }}</td>
                                <td>{{ @$consignment->total_quantity ?? "-" }}</td>
                                <td>{{ @$consignment->total_weight ?? "-" }}</td>
                                <?php 
                                $prs_pickup_status = DB::table('prs_drivertasks')->select('id','status')->where(['prsconsigner_id'=>$consignment->ConsignerDetail->id, 'prs_id'=>$consignment->prs_id])->first();

                                if(!empty($consignment->prs_id)){
                                    $booking_mode = "Driver";
                                    $pickup_status = Helper::PrsDriverTaskStatus($prs_pickup_status->status);
                                }else{
                                    $booking_mode = "Manual";
                                    $pickup_status = "N/A";
                                }
                                ?>
                                <td>{{ $booking_mode }}</td>
                                <td>{{ $pickup_status }}</td>


                                <!-- ---Action Button ----->
                                <!-- <?php if(!empty($consignment->fall_in)){ 
                                $authuser = Auth::user();
                               if($authuser->branch_id == $consignment->fall_in){ ?>
                                <td>
                                    <a class="orderstatus btn btn-danger" data-id="{{$consignment->id}}"
                                        data-action="<?php echo URL::current(); ?>"><span><i class="fa fa-ban"></i>
                                            Cancel</span></a>

                                </td>
                                <?php }else{ ?>
                                <td>
                                    <a class="orderstatus btn btn-danger" data-id="{{$consignment->id}}"
                                        data-action="<?php echo URL::current(); ?>"><span><i class="fa fa-ban"></i>
                                            Cancel</span></a>
                                </td>
                                <?php } 
                            } else { ?>

                                <td>
                                    <a class="orderstatus btn btn-danger" data-id="{{$consignment->id}}"
                                        data-action="<?php echo URL::current(); ?>"><span><i class="fa fa-ban"></i>
                                            Cancel</span></a>
                                        <?php if(!empty($consignment->prs_id)){ ?>
                                    <a class="btn btn-primary"
                                        href="{{url($prefix.'/orders/'.Crypt::encrypt($consignment->id).'/edit')}}"><span><i class="fa fa-edit"></i></span></a>
                                        <?php } ?>
                                </td>
                                <?php } ?> -->
                                <!-- -------- Status Button ------------>
                                <?php if(!empty($consignment->fall_in)){ 
                                $authuser = Auth::user();
                                   if($authuser->branch_id == $consignment->fall_in){ 
                                 if($consignment->prsitem_status == 1 || $consignment->prsitem_status == 2){ ?>
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{url($prefix.'/orders/'.Crypt::encrypt($consignment->id).'/edit')}}"><span>Complete
                                            Lr</span></a>
                                </td>
                                <?php }else{
                                    ?>
                                <td>
                                    <a class="btn btn-primary" href="#"><span>Pending Pickup</span></a>
                                    <button type="button" class="btn btn-success prs_not_require"
                                        value="{{$consignment->id}}">Receved Material</button>
                                </td>
                                <?php }
                                }else{ 
                                    if($consignment->prsitem_status == 0){ ?>
                                <td> <a class="btn btn-primary" href="#"><span>Pending Pickup</span></a></td>
                                <?php }else{ ?>
                                <td> <a class="btn btn-primary" href="#"><span>Picked up</span></a></td>
                                <?php } } 
                                }else{?>
                                <td>-</td>
                                <?php } ?>
                               
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="upload_order" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Order Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <h6 class="col-12">Branch</h6>

                    <?php $authuser = Auth::user();
            if($authuser->role_id == 2 || $authuser->role_id == 4)
            {
            ?>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlSelect1">
                            Select Branch <span class="text-danger">*</span>
                        </label>
                        <select class="form-control  my-select2" id="branch_id" name="branch_id" tabindex="-1">
                            @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <?php } else { ?>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlSelect1">
                            Select Branch <span class="text-danger">*</span>
                        </label>
                        <select class="form-control  my-select2" id="branch_id" name="branch_id" tabindex="-1">
                            <option value="">Select..</option>
                            @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                            @endforeach
                        </select>
                    </div>

                    <?php } ?>

                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput">Excel File*</label>
                    <input required type="file" class="form-control form-control-sm" id="formGroupExampleInput"
                        name="order_file" placeholder="Example input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><span class="indicator-label">Upload</span>
                    <span class="indicator-progress" style="display: none;">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="receve_material" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="prs_check_form">
                <!-- <button type="button" class="close" data-dismiss="modal"><img src="{{asset('assets/img/close-bottle.png')}}" class="img-fluid"></button> -->
                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title">Prs Not Required</h4>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="lr_id" id="lr_id" />

                    <input type="text" class="form-control" name="prs_remarks" placeholder="Remarks" Required/>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <div class="btn-section w-100 P-0">
                        <button type="submit" class="btn btn-warnimg">Save</button>
                        <a type="" class="btn btn-modal" data-dismiss="modal">No</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('models.delete-user')
@include('models.common-confirm')
@endsection
@section('js')
<script>
// Order list status change onchange
jQuery(document).on('click', '.orderstatus', function(event) {
    event.stopPropagation();

    let order_id = jQuery(this).attr('data-id');
    var dataaction = jQuery(this).attr('data-action');
    var updatestatus = 'updatestatus';
    var status = 0;


    jQuery('#commonconfirm').modal('show');
    jQuery(".commonconfirmclick").one("click", function() {

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
            beforeSend: function() {
                // jQuery("input[type=submit]").attr("disabled", "disabled");
            },
            complete: function() {
                //jQuery("#loader-section").css('display','none');
            },

            success: function(response) {
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

$('#upload_order').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "import-ordre-booking",
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
                swal("success!", data.success_message, "success");
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});
///
$(document).on('click', '.prs_not_require', function() {
    var lr_no = $(this).val();
    $('#receve_material').modal('show');
    $('#lr_id').val(lr_no);
});
//
$('#prs_check_form').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "prs-receive-material",
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
                swal("success!", data.success_message, "success");
                window.location.reload();
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});
</script>
@endsection