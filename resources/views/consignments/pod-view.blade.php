@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->

<style>
td p {
    margin-bottom: 0;
}

.lrStatus {
    border-radius: 4px; 
    border: 1px solid;
    padding: 2px 5px;
    font-size: 10px !important;
    font-weight: 400;
    line-height: 10px;
    color: white !important;
    margin-bottom: 0 !important;
}

.dlMode {
    border-radius: 4px;
    border: 1px solid;
    padding: 0 5px;
    user-select: none;
}

.pointer {
    cursor: pointer;
}

.notAllowed {
    cursor: not-allowed;
}

label.statusLabel {
    font-size: 12px !important;
    color: #fff !important;
    letter-spacing: 0px;
    font-weight: 500;
    padding: 1px 6px;
    border-radius: 30px;
    width: 90px;
    text-align: center;
    margin-bottom: 0 !important;
}

.viewAllInvoices {
    position: relative;
    cursor: pointer;
    color: #f9b808;
    float: right;
}

.viewAllInvoices:hover {
    color: #715200;
}

.moreInvoicesView {
    padding: 1rem 1rem 1rem 2rem;
    width: 200px;
    position: absolute;
    top: calc(100%);
    right: calc(100% + 5px);
    background-color: #ffffff;
    color: #494949;
    box-shadow: 0 0 8px rgb(68 154 1 / 58%);
    display: none;
    flex-wrap: wrap;
    font-weight: 500;
    font-size: 14px;
    line-height: 1rem;
    transition: all 200ms ease-in-out;
    border-radius: 12px 0 12px 12px;
}

.viewAllInvoices:hover .moreInvoicesView {
    display: flex;
}

#exampleModal img {
    max-height: 100% !important;
    max-width: 100% !important;
}
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">

                    <h5 class="limitmessage text-danger" style="display: none;">
                        You cannot download more than 30,000 records. Please select Filters.
                    </h5>
                    <div class="row mt-4" style="margin-inline: auto; margin-bottom:15px;">
                        <div class="page-header flex-wrap" style="width: 100%">
                            <h2>POD View</h2>
                            <input type="text" class="form-control" placeholder="Search" id="search"
                                data-action="<?php echo url()->current(); ?>" style="width: min(100%, 250px);" />
                        </div>

                        <div class="row justify-content-center" style="width: 100%">
                            <div class="col-sm-3">
                                <label>from</label>
                                <input type="date" id="startdate" class="form-control" name="startdate">
                            </div>
                            <div class="col-sm-3">
                                <label>To</label>
                                <input type="date" id="enddate" class="form-control" name="enddate">
                            </div>
                            <div class="col-4 d-flex align-items-end">
                                <button type="button" id="filter_reportall" class="btn btn-primary"
                                    style=" font-size: 15px; padding: 9px; width: 130px">
                                    <span class="indicator-label">Filter Data</span>
                                </button>
                                <a href="<?php echo URL::to($prefix.'/pod-export'); ?>"
                                    data-url="<?php echo URL::to($prefix.'/consignment-report2'); ?>"
                                    class="consignmentReportEx btn btn-white btn-cstm"
                                    style=" font-size: 15px; padding: 9px; width: 130px"
                                    data-action="<?php echo URL::to($prefix.'/pod-export'); ?>" download><span><i
                                            class="fa fa-download"></i> Export</span></a>
                                <a href="javascript:void();" style=" font-size: 15px; padding: 9px;"
                                    class="btn btn-primary btn-cstm ml-2 reset_filter"
                                    data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i>
                                        Reset
                                        Filters</span></a>
                            </div>
                        </div>

                    </div>
                    @csrf
                    <div class="main-table table-responsive">
                        @include('consignments.pod-view-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="#" id="toggledImageView" style="max-height: 90vh; max-width: 90vw" />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalPdf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center">
                    <iframe src="#" id="toggledPdfView"
                        style="height: 90vh; width: 90vw; max-height: 90vh; max-width: 90vw"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- update image Modal -->
<div class="modal fade" id="updateImageModal" tabindex="-1" role="dialog" aria-labelledby="updateImageModalLabel"
    aria-hidden="true">
    <form class="modal-dialog modal-dialog-centered" role="document" id="update_image_pod">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Update Image</h4>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-center align-items-center">
                    <input type="hidden" name="lr_no" id="lr_no" value="" />
                    <input type="hidden" name="dispatch_date" id="dispatch_date" value="" />
                    <div class="form-group col-12">
                        <label class="control-label">Image</label>
                        <input type="file" class="form-control" id="image-url" accept="image/*" placeholder="Image URL"
                            name="pod" style="border: none;" />
                    </div>
                    <div class="form-group col-12">
                        <label class="control-label">Delivery Date</label>
                        <input type="date" class="form-control" id="dlvery_date" name="delivery_date"
                            onkeydown="return false" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>

<!-- delete images modal -->
<div class="modal fade" id="deleteImages" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Delete</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="Delt-content text-center">
                    <img src="{{asset('assets/img/delte.png')}}" class="img-fluid mb-2">
                    <h5 class="my-2">Are you sure to delete all images?</h5>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="btn-section w-100 P-0">
                    <a class="btn-cstm btn-white btn btn-modal delete-btn-modal deleteclientconfirm">Yes</a>
                    <a type="" class="btn btn-modal" data-dismiss="modal">No</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- update delivery mode Modal -->
<div class="modal fade" id="changemodeConfirm" tabindex="-1" role="dialog" aria-labelledby="updateImageModalLabel"
    aria-hidden="true">
    <form class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <h4 class="p-3">Change Mode</h4>
            <div class="modal-body">
                <div class="form-group">
                    <label>Reason:</label>
                    <textarea class="form-control" name="reason_to_change_mode" id="reason_to_change_mode"
                        rows="2"></textarea>
                    <div class="form-group">

                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center p-3 pt-1" style="gap: 1rem">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary confirmclick">Update</button>
            </div>
        </div>
    </form>
</div>


@endsection
@section('js')
<script>
// $(function(){
//     var dtToday = new Date();

//     var month = dtToday.getMonth() + 1;
//     var day = dtToday.getDate();
//     var year = dtToday.getFullYear();
//     if(month < 10)
//         month = '0' + month.toString();
//     if(day < 10)
//      day = '0' + day.toString();
//     var maxDate = year + '-' + month + '-' + day;
//     $('#dlvery_date').attr('min', maxDate);
// });


$("#update_image_pod").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var files = $("#image-url")[0].files;
    var dd = $("#dlvery_date").val();
    var consignment_date = $("#dispatch_date").val();
    if (files.length == 0) {
        alert("Please choose a file");
        return false;
    }
    if (!dd) {
        alert("Please select Date");
        return false;
    }

    var c_date = new Date(consignment_date); //Year, Month, Date
    var d_date = new Date(dd); //Year, Month, Date
    if (c_date > d_date) {
        swal("Error", "delivery date can't be less than lr date", "error");
        return false;
    } 


    $.ajax({
        url: "update-poddetails",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {},
        success: (data) => {
            if (data.success == true) {
                swal('success', data.messages, 'success');
                location.reload();
            } else {
                swal('error', data.messages, 'error');
            }
        },
    });

});

jQuery(document).on('click', '#filter_reportall', function() {
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var search = jQuery('#search').val();

    jQuery.ajax({
        type: 'get',
        url: 'pod-view',
        data: {
            startdate: startdate,
            enddate: enddate,
            search: search
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.html) {
                jQuery('.main-table').html(response.html);
            }
        }
    });
    return false;
});

jQuery(document).on('change', '.report_perpage', function() {
    var startdate = jQuery('#startdate').val();
    var enddate = jQuery('#enddate').val();
    if (startdate == enddate) {
        startdate = "";
        enddate = "";
    }
    var url = jQuery(this).attr('data-action');
    var peritem = jQuery(this).val();
    var search = jQuery('#search').val();
    jQuery.ajax({
        type: 'get',
        url: url,
        data: {
            peritem: peritem,
            search: search,
            startdate: startdate,
            enddate: enddate
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.html) {
                if (response.page == 'lead_note') {
                    jQuery('#Note .main-table').html(response.html);
                } else {
                    jQuery('.main-table').html(response.html);
                }
            }
        }
    });
    return false;
});

jQuery(document).on('click', '.consignmentReportEx', function(event) {
    event.preventDefault();

    var totalcount = jQuery('.totalcount').text();
    if (totalcount > 30000) {
        jQuery('.limitmessage').show();
        setTimeout(function() {
            jQuery('.limitmessage').fadeOut();
        }, 5000);
        return false;
    }

    var geturl = jQuery(this).attr('data-action');
    var startdate = jQuery('#startdate').val();
    var enddate = jQuery('#enddate').val();

    var search = jQuery('#search').val();
    var url = jQuery('#search').attr('data-url');
    if (startdate)
        geturl = geturl + '?startdate=' + startdate + '&enddate=' + enddate;
    else if (search)
        geturl = geturl + '?search=' + search;

    jQuery.ajax({
        url: url,
        type: 'get',
        cache: false,
        data: {
            startdate: startdate,
            enddate: enddate
        },
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="_token"]').attr('content')
        },
        processData: true,
        beforeSend: function() {
            //jQuery(".load-main").show();
        },
        complete: function() {
            //jQuery(".load-main").hide();
        },
        success: function(response) {
            // jQuery(".load-main").hide();
            setTimeout(() => {
                window.location.href = geturl
            }, 10);
        }
    });
});

jQuery(document).on('click', '.viewImageInNewTab', function() {
    let toggledImage = $(this).attr('src');
    $('#toggledImageView').attr('src', toggledImage);
});

jQuery(document).on('click', '.viewpdfInNewTab', function() {
    let toggledImage = $(this).attr('pdf-nm');
    $('#toggledPdfView').attr('src', toggledImage);
});

jQuery(document).on('click', '.editButtonimg', function() {
    var li_no = $(this).attr('data-id');
    var date = $(this).attr('lr-date');
    $('#updateImageModal').modal('show');
    $('#lr_no').val(li_no);
    $('#dispatch_date').val(date);
});

// lr mode change
jQuery(document).on(
    "click",
    ".change_mode",
    function(event) {
        event.stopPropagation();
        let lr_id = jQuery(this).attr("data-id");

        jQuery("#changemodeConfirm").modal("show");
        jQuery(".confirmclick").one("click", function() {
            var reason_to_change_mode = jQuery("#reason_to_change_mode").val();
            if (!reason_to_change_mode) {
                alert('Please enter a reason to change mode');
                return false;
            }
            var data = {
                lr_id: lr_id,
                reason_to_change_mode: reason_to_change_mode,
            };

            jQuery.ajax({
                url: "change-pod-mode",
                type: "get",
                cache: false,
                data: data,
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                        "content"
                    ),
                },
                processData: true,
                beforeSend: function() {
                    // jQuery("input[type=submit]").attr("disabled", "disabled");
                },
                complete: function() {
                    //jQuery("#loader-section").css('display','none');
                },
                success: function(response) {
                    if (response.success == true) {
                        swal('success', response.messages, 'success')
                        location.reload();
                    } else {
                        swal('error', response.messages, 'error')
                    }
                },
            });
        });
    }
);
//mode alert
jQuery(document).on('click', '.modealert', function() {
    swal('Error', 'Mode Already in Manual', 'error');
});
///Delete Pod and status changed
jQuery(document).on(
    "click",
    ".deletePod",
    function(event) {
        event.stopPropagation();
        $("#deleteImages").trigger("reset");
        let lr_id = jQuery(this).attr("data-id");
        jQuery("#deleteImages").modal("show");
        jQuery(".deleteclientconfirm").one("click", function() {
            var data = {
                lr_id: lr_id,
            };

            jQuery.ajax({
                url: "delete-pod-status",
                type: "get",
                cache: false,
                data: data,
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                        "content"
                    ),
                },
                processData: true,
                beforeSend: function() {
                    // jQuery("input[type=submit]").attr("disabled", "disabled");
                },
                complete: function() {
                    //jQuery("#loader-section").css('display','none');
                },
                success: function(response) {
                    if (response.success == true) {
                        swal('success', response.messages, 'success')
                        location.reload();
                    } else {
                        swal('error', response.messages, 'error')
                    }
                },
            });
        });
    }
);
</script>
@endsection