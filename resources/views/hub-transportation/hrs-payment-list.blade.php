@extends('layouts.main')
@section('content')
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
<!-- END PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css">

<style>
.update_purchase_price svg {
    height: 14px;
    width: 14px;
    margin-left: 8px;
    cursor: pointer;
}

.update_purchase_price svg:hover {
    color: #f9b600;
}

.pointer {
    cursor: pointer;
}

.drsStatus {
    user-select: none;
    cursor: default;
    text-align: center;
    width: 110px;
    border-radius: 50vh;
    padding: 6px 8px;
    font-size: 11px;
    line-height: 11px;
    font-size: 11px;
    color: #ffffff;
}

.green {
    background: #148b00;
}

.orange {
    background: #e2a03f;
}

.extra2 {
    background: #1abc9c;
}

#create_request_form span.select2 {
    margin-bottom: 0 !important;
}

input[readonly].styledInput {
    border: none;
    background-color: transparent !important;
    color: #000;
    font-weight: 700;
}
.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
}

</style>

<div class="layout-px-spacing">
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">HRS Payment Sheet</h2>
    </div>

    <div class="widget-content widget-content-area br-6" style="min-height: min(80vh, 600px)">

        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center" style="gap: 1rem;">
            <div>
                <?php $authuser = Auth::user();
                    if ($authuser->role_id == 2 || $authuser->role_id == 3) {?>
                <button type="button" class="btn btn-warning create_hrs_payment" style="font-size: 12px; height: 36px"
                    disabled>
                    Create Payment
                </button>
                <?php }?>

                <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                    data-action="<?php echo url()->current(); ?>"
                    style="font-size: 12px; width: 130px; height: 36px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa fa-refresh"></i> Reset Filters
                </a>
            </div>
            <div>
                <input class="form-control" placeholder="Vehicle Number Search" id="search"
                    data-action="<?php echo url()->current(); ?>"
                    style="height: 36px; max-width: 250px; width: 300px;" />
            </div>
        </div>

        @csrf
        <div class="main-table table-responsive">
            @include('hub-transportation.hrs-payment-list-ajax')
        </div>
    </div>
</div>
@include('models.hrs-payment-models')
@endsection
@section('js')
<script>
    
jQuery(function() {
    $('.my-select2').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            dropdownParent: $(this).parent(), // fix select2 search input focus bug
        })
    })

    $(document).ready(function() {
        $('.my-select3').select2();
    });

    // fix select2 bootstrap modal scroll bug
    $(document).on('select2:close', '.my-select2', function(e) {
        var evt = "scroll.select2"
        $(e.target).parents().off(evt)
        $(window).off(evt)
    })
})

/////////
$(document).on('click', '.hrs_lr', function() {
    var hrs_lr = $(this).attr('hrs-no');
    $('#view_hrs_lrmodel').modal('show');
    $.ajax({
        type: "GET",
        url: "view-hrslr/" + hrs_lr,
        data: {
            hrs_lr: hrs_lr
        },
        beforeSend: function() {
            $('#view_hrs_lrtable').dataTable().fnClearTable();
            $('#view_hrs_lrtable').dataTable().fnDestroy();
            $("#total_boxes").empty();
            $("#totalweights").empty();
            $("#totallr").empty();

        },
        success: function(data) {
            var re = jQuery.parseJSON(data)
            console.log(re.fetch);
            var consignmentID = [];
            var totalBoxes = 0;
            var totalweights = 0;

            var i = 0;
            $.each(re.fetch, function(index, value) {
                i++;
                var alldata = value;

                consignmentID.push(alldata.consignment_no);
                totalBoxes += parseInt(value.consignment_detail.total_quantity);
                totalweights += parseInt(value.consignment_detail.total_weight);


                $('#view_hrs_lrtable tbody').append("<tr id=" + value.id +
                    "><td>" + value.consignment_id +
                    "</td><td>" + value.consignment_detail.consignment_date +
                    "</td><td>" + value.consignment_detail
                    .consignee_detail.nick_name + "</td><td>" + value.consignment_detail
                    .consignee_detail.city + "</td><td>" + value.consignment_detail
                    .consignee_detail.postal_code + "</td><td>" + value
                    .consignment_detail.total_quantity + "</td><td>" + value
                    .consignment_detail.total_weight + "</td></tr>");
            });
            var rowCount = $("#view_hrs_lrtable tbody tr").length;

            $("#total_boxes").append("No Of Boxes: " + totalBoxes);
            $("#totalweights").append("Net Weight: " + totalweights);
            $("#totallr").append(rowCount);

        }
    });
});

///// check box checked unverified lr page
jQuery(document).on('click', '#ckbCheckAll', function() {
    if (this.checked) {
        jQuery('.create_hrs_payment').prop('disabled', false);
        jQuery('.chkBoxClass').each(function() {
            this.checked = true;
        });
    } else {
        jQuery('.chkBoxClass').each(function() {
            this.checked = false;
        });
        jQuery('.create_hrs_payment').prop('disabled', true);
    }
});

jQuery(document).on('click', '.chkBoxClass', function() {
    if ($('.chkBoxClass:checked').length == $('.chkBoxClass').length) {
        $('#ckbCheckAll').prop('checked', true);
    } else {
        var checklength = $('.chkBoxClass:checked').length;
        if (checklength < 1) {
            jQuery('.create_hrs_payment').prop('disabled', true);
        } else {
            jQuery('.create_hrs_payment').prop('disabled', false);
        }

        $('#ckbCheckAll').prop('checked', false);
    }
});
///
$(document).on('click', '.create_hrs_payment', function() {
    // $('#create_request_form')[0].reset();
    // $('#p_type_1').empty();
    $('#hrs_pymt_modal').modal('show');
    var hrs_no = [];
    var tdval = [];
    $(':checkbox[name="checked_drs[]"]:checked').each(function() {
        hrs_no.push(this.value);
        var cc = $(this).attr('data-price');
        tdval.push(cc);
    });

    $('#hrs_no_1').val(hrs_no);

    var toNumbers = tdval.map(Number);
    var sum = toNumbers.reduce((x, y) => x + y);
    $('#total_clam_amt_1').val(sum);

    $.ajax({
        type: "GET",
        url: "get-hrs-details",
        data: {
            hrs_no: hrs_no
        },
        beforeSend: //reinitialize Datatables
            function() {

            },
        success: function(data) {
            // console.log(data.get_data.receving_status);

            if (data.get_data.receving_status == 2) {
                $('#p_type_1').append('<option value="Fully">Fully Payment</option>');
                //check balance if null or delevery successful
                var total = $('#total_clam_amt_1').val();

                $('#amt_1').val(total);

                var amt = $('#amt_1').val();
                var tds_rate = $('#tds_rate_1').val();
                var cal = (tds_rate / 100) * amt;
                var final_amt = amt - cal;
                $('#tds_dedut_1').val(final_amt);
                $('#amt_1').attr('readonly', true);
            } else {
                $('#p_type_1').append(
                    '<option value="" selected disabled>Select</option><option value="Advance">Advance</option><option value="Fully">Fully Payment</option>'
                );
            }



        }

    });

});
// ============================================================== //
$('#vendor_id_1').change(function() {
    var vendor_id = $(this).val();

    $.ajax({
        type: 'get',
        url: 'vendor-details',
        data: {
            vendor_id: vendor_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        beforeSend: function() {

            $('#tds_dedut').val('');
        },
        success: function(res) {
            if (res.success === true) {
                jQuery('#crt_pytm').prop('disabled', false);
                var simp = jQuery.parseJSON(res.vendor_details.bank_details);
                $('#bank_acc_1').val(simp.account_no);
                $('#ifsc_code_1').val(simp.ifsc_code); 
                $('#bank_name_1').val(simp.bank_name);
                $('#branch_name_1').val(simp.branch_name);
                $('#vendor_no_1').val(res.vendor_details.vendor_no);
                $('#name_1').val(res.vendor_details.name);
                $('#beneficiary_name_1').val(simp.acc_holder_name);
                $('#email_1').val(res.vendor_details.email);
                $('#tds_rate_1').val(res.vendor_details.tds_rate);
                $('#pan_1').val(res.vendor_details.pan);

                //calculate
                var amt = $('#amt_1').val();

                var tds_rate = $('#tds_rate_1').val();
                var cal = (tds_rate / 100) * amt;
                var final_amt = amt - cal;
                $('#tds_dedut_1').val(final_amt);

            } else {
                $('#bank_acc_1').val('');
                $('#ifsc_code_1').val('');
                $('#bank_name_1').val('');
                $('#branch_name_1').val('');
                $('#vendor_no_1').val('');
                $('#name_1').val('');
                $('#beneficiary_name_1').val('');
                $('#email_1').val('');
                $('#tds_rate_1').val('');
                $('#pan_1').val('');
                jQuery('#crt_pytm_1').prop('disabled', true);
                swal('error', 'account not verified', 'error');
            }

        }
    });

});
// ===========================================================//
</script>
@endsection