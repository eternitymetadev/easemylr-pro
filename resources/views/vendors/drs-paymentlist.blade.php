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
.has-details {
  position: relative;
}

.details {
  position: absolute;
  top: 0;
  transform: translateY(70%) scale(0);
  transition: transform 0.1s ease-in;
  transform-origin: left;
  display: inline;
  background: white;
  z-index: 20;
  min-width: 100%;
  padding: 1rem;
  border: 1px solid black;
}

.has-details:hover span {
  transform: translateY(70%) scale(1);
}

.vehicleField .sumo_vehicle{
    width: 100% !important;
}

.vehicleField .text-right.close-c{
    display: none;
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
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Payment
                                Sheet</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
               
                <div class="form-row mb-0">
                <div class="form-group col-md-4">
                <?php $authuser = Auth::user();
                if ($authuser->role_id == 2 || $authuser->role_id == 3) {?>
                <button type="button" class="btn btn-warning mt-4 ml-4 payment" style="font-size: 12px;">Create Payment</button>
                <?php }?>
                </div>
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" placeholder="Vehicle Number Search" id="search" data-action="<?php echo url()->current(); ?>">
                    </div>
                    <div class="form-group col-md-4">
                    <div class="winery_btn_n btn-section px-0 text-right">
                        <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                            style="font-size: 15px; padding: 9px; width: 130px"
                            data-action="<?php echo url()->current(); ?>"><span><i class="fa fa-refresh"></i> Reset
                                Filters</span></a>
                    </div>
                </div>
                </div>

                @csrf
                <div class="main-table table-responsive">
                    @include('vendors.drs-paymentlist-ajax')
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.search-paymentvehicle')
@include('models.payment-model')
@endsection
@section('js')
<script> 
function testFunction(){
    alert('test')
}
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
$(document).on('click', '.payment', function() {
    $('#create_request_form')[0].reset();
    $('#p_type_1').empty();
    $('#pymt_modal').modal('show');
    var drs_no = [];
    var tdval = [];
    $(':checkbox[name="checked_drs[]"]:checked').each(function() {
        drs_no.push(this.value);
        var cc = $(this).attr('data-price');
        tdval.push(cc);
    });

    $('#drs_no_1').val(drs_no);
  
    var toNumbers = tdval.map(Number);
    var sum = toNumbers.reduce((x, y) => x + y);
    $('#total_clam_amt_1').val(sum);

    $.ajax({
        type: "GET",
        url: "get-drs-details",
        data: {
            drs_no: drs_no
        },
        beforeSend: //reinitialize Datatables
            function() {

            },
        success: function(data) {

            if (data.status == 'Successful') {
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
///////////// view drs lr model///////////////////
$(document).on('click', '.drs_lr', function() {

    var drs_lr = $(this).attr('drs-no');
    $('#view_drs_lrmodel').modal('show');
    $.ajax({
        type: "GET",
        url: "view-drslr/" + drs_lr,
        data: {
            drs_lr: drs_lr
        },
        beforeSend: function() {
            $('#view_drs_lrtable').dataTable().fnClearTable();
            $('#view_drs_lrtable').dataTable().fnDestroy();
            $("#total_boxes").empty();
            $("#totalweights").empty();
            $("#totallr").empty();

        },
        success: function(data) {
            var re = jQuery.parseJSON(data)
            console.log(re);
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


                $('#view_drs_lrtable tbody').append("<tr id=" + value.id +
                    "><td>" + value.consignment_no +
                    "</td><td>" + value.consignment_date + "</td><td>" + value
                    .consignee_id + "</td><td>" + value.city + "</td><td>" + value
                    .pincode + "</td><td>" + value.total_quantity + "</td><td>" + value
                    .total_weight + "</td></tr>");
            });
            var rowCount = $("#view_drs_lrtable tbody tr").length;

            $("#total_boxes").append("No Of Boxes: " + totalBoxes);
            $("#totalweights").append("Net Weight: " + totalweights);
            $("#totallr").append(rowCount);

        }
    });
});

/////////////
///// check box checked unverified lr page
jQuery(document).on('click', '#ckbCheckAll', function() {
    if (this.checked) {
        jQuery('.payment').prop('disabled', false);
        jQuery('.chkBoxClass').each(function() {
            this.checked = true;
        });
    } else {
        jQuery('.chkBoxClass').each(function() {
            this.checked = false;
        });
        jQuery('.payment').prop('disabled', true);
    }
});

jQuery(document).on('click', '.chkBoxClass', function() {
    if ($('.chkBoxClass:checked').length == $('.chkBoxClass').length) {
        $('#ckbCheckAll').prop('checked', true);
    } else {
        var checklength = $('.chkBoxClass:checked').length;
        if (checklength < 1) {
            jQuery('.payment').prop('disabled', true);
        } else {
            jQuery('.payment').prop('disabled', false);
        }

        $('#ckbCheckAll').prop('checked', false);
    }
});
// ====================Add Purchase Price================================== //
$(document).on('click', '.add_purchase_price', function() {
    var drs_no = $(this).val();
    $('#add_amt').modal('show');
    $('#drs_num').val(drs_no);
});
// ====================update Purchase Price================================== //
$(document).on('click', '.update_purchase_price', function() {
    var drs_no = $(this).attr('drs-no');
    $('#edit_amt').modal('show');
    $.ajax({
        type: 'get',
        url: 'edit-purchase-price',
        data: {
            drs_no: drs_no
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        beforeSend: function() {

        },
        success: function(response) {
            $('#drs_num_edit').val(response.drs_price.drs_no);
            $('#purchse_edit').val(response.drs_price.consignment_detail.purchase_price);
            
             $("#vehicle_type_edit").val(response.drs_price.consignment_detail.vehicle_type).change();

            
        }
    });

    
});
// ==================Vehicle search ================
$('#v_id').change(function() {
    var vehicle_no = $(this).val();

    $.ajax({
        type: 'get',
        url: 'drs-paymentlist',
        data: {
            vehicle_no: vehicle_no
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        beforeSend: function() {

        },
        success: function(response) {
            if (response.html) {
                jQuery('.main-table').html(response.html);
            }
        }
    });

});
// =============================
$('#p_type_1').change(function() {
    var p_type = $(this).val();
    var total_amt = $("#total_clam_amt_1").val();
     if(p_type == 'Fully'){
        $('#amt_1').val(total_amt);

        var amt = $('#amt_1').val();
        var tds_rate = $('#tds_rate_1').val();
        var cal = (tds_rate / 100) * amt;
        var final_amt = amt - cal;
        $('#tds_dedut_1').val(final_amt);
        $('#amt_1').attr('readonly', true);

     }else{
        $('#amt_1').val('');
        $('#tds_dedut_1').val('');

     }

});
// =============================
$("#amt_1").keyup(function() {
//  alert('k');
var firstInput = document.getElementById("total_clam_amt_1").value;
var secondInput = document.getElementById("amt_1").value;

if (parseInt(firstInput) < parseInt(secondInput)) {
    $('#amt_1').val('');
    $('#tds_dedut_1').val('');
    swal('error', 'amount must be greater than purchase price', 'error')
} else if (parseInt(firstInput) == '') {
    $('#amt_1').val('');
    jQuery('#amt_1').prop('disabled', true);
}
// Calculate tds
var tds_rate = $('#tds_rate_1').val();

var cal = (tds_rate / 100) * secondInput;
var final_amt = secondInput - cal;
$('#tds_dedut_1').val(final_amt);

});

</script>
@endsection