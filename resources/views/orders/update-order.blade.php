@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->

<style>
@media only screen and (max-width: 600px) {
    .checkbox-round {
        margin-left: 1px;
    }

}

h4 {
    font-size: 18px;

}


.checkbox-round {
    width: 2.3em;
    height: 2.3em;
    border-radius: 55%;
    border: 1px solid #ddd;
    margin-left: 103px;
}

p {
    font-size: 11px;
    font-weight: 500;
}


th,
td {
    text-align: left;
    padding: 8px;
    color: black;
}

.cont {
    background: white;
    height: 240px;
    border-style: ridge;
    width: 390px;
    border-radius: 17px;
}

.mini_container {
    margin-top: 8px;
}

.wizard {
    background: #fff;
}

.wizard .nav-tabs {
    position: relative;
    margin: 40px auto;
    margin-bottom: 0;
}

.wizard>div.wizard-inner {
    position: relative;
}

.connecting-line {
    height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 80%;

    top: 42%;

}

.nav-tabs {
    border-bottom: none;
}

.wizard .nav-tabs>li.active>a,
.wizard .nav-tabs>li.active>a:hover,
.wizard .nav-tabs>li.active>a:focus {
    color: #555555;
    cursor: default;
    border: none;
}

span.round-tab {
    width: 50px;
    height: 50px;
    line-height: 51px;
    display: inline-block;
    border-radius: 100px;
    background: #fff;
    border: 2px solid #e0e0e0;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 25px;
}

span.round-tab i {
    color: #555555;
}

.wizard li.active span.round-tab {
    background: #fff;
    border: 2px solid #5bc0de;

}

.wizard li.active span.round-tab i {
    color: #5bc0de;
}

span.round-tab:hover {
    color: #333;
    border: 2px solid #333;
}

.wizard .nav-tabs>li {
    width: 25%;
}

.wizard .nav-tabs>li a {
    width: 48px;
    height: 70px;
    border-radius: 100%;
    padding: 0;
}

@media (max-width: 585px) {

    .wizard {
        width: 90%;
        height: auto !important;
    }

    span.round-tab {
        font-size: 16px;
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard .nav-tabs>li a {
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard li.active:after {
        content: " ";
        position: absolute;
        left: 35%;
    }
}

/* / ////////////////////////////////////////////////////////////////////end wizard / */
.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 160px;
    /* scroll-margin: 38px; */
    overflow: auto;
}

/*.form-group {*/
/*    margin-bottom: 0;*/
/*}*/

.form-row>.col,
.form-row>[class*=col-] {
    padding-inline: 10px !important;
}

span.select2.select2-container.mb-4 {
    margin-bottom: 0 !important;
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

.mainTr {
    outline: 1px solid #838383;
    background: #f4f4f4;
    border-radius: 12px;
    width: 100%;
}

.childTable {
    background: #F9B60030;
    border-radius: 12px;
}

.addRowButton {
    text-align: right;
    width: 100%;
    font-weight: 800;
    color: #f9b600;
    margin-right: 10px;
    cursor: pointer;
}

.addItem {
    float: right;
    font-weight: 800;
    color: #f9b600;
    margin-right: 10px;
    cursor: pointer;
}

.items_table_body tr {
    position: relative;
}


.main_table_body td {
    min-width: 150px;
}

.main_table_body td:has(div.removeIcon) {
    min-width: 50px;
    width: 50px;
}

.main_table_body td div.removeIcon:has(span) {
    cursor: pointer;
    height: 20px;
    width: 20px;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50vh;
    color: darkred;
    font-weight: 800;
    border: 1px solid darkred;
    transition: all 200ms ease-in-out;
}

.main_table_body td div.removeIcon:has(span):hover {
    background: darkred;
    color: white;
}

.appendedAddress:has(br) {
    padding: 1rem;
    border-radius: 12px;
    margin-top: 1rem;
    background: #f9b60024;
    color: #000;
}

select[readonly] {
    pointer-events: none;
}
</style>
<?php 
if(!empty($getconsignments->prs_id) || ($getconsignments->prs_id != NULL)){
    $disable = ''; 
} else{
    $disable = 'readonly';
}
?>

<div class="layout-px-spacing">
    {{-- page title--}}
    <div class="page-header layout-spacing">
        <h2 class="pageHeading">Update Order</h2>
    </div>

    <form class="general_form" method="POST" action="{{url($prefix.'/orders/update-order')}}" id="updateorder"
        style="margin: auto; ">
        <input type="hidden" name="consignment_id" value="{{$getconsignments->id}}">
        <input type="hidden" name="booked_drs" value="{{$getconsignments->booked_drs}}">
        <?php if(!empty($getconsignments->prs_id) || ($getconsignments->prs_id != NULL)){ ?>
        <input type="hidden" name="lr_type" value="{{$getconsignments->lr_type}}">
        <?php }else{ ?>
        <input type="hidden" name="lr_type" value="{{$getconsignments->lr_type}}">
        <?php } ?>

        {{--Branch Location--}}

        {{--bill to info--}}
        <div class="form-row">
            <h6 class="col-12">Bill To Information</h6>

            <div class="form-group col-md-4">
                <label for="exampleFormControlSelect1">
                    Select Bill to Client<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" id="select_regclient" name="regclient_id" disabled>
                    <option value="">Select</option>
                    @if(count($regionalclient) > 0)
                    @foreach($regionalclient as $client)
                    <option value="{{ $client->id }}"
                        {{ $client->id == $getconsignments->regclient_id ? 'selected' : ''}}>{{ucwords($client->name)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <?php $invc_data = DB::table('regional_clients')->select('id','name','is_multiple_invoice')->where('id',$getconsignments->regclient_id)->first(); ?>

                <input type="hidden" name="invoice_check" id="inv_check" value="{{$invc_data->is_multiple_invoice}}">
                <input type="hidden" name="regclient_id" value="{{$getconsignments->regclient_id}}" />
            </div>

            <div class="form-group col-md-2">
                <label for="exampleFormControlSelect1">
                    Payment Term<span class="text-danger">*</span>
                </label>
                <select class="form-control" style="width: 160px;" name="payment_type" id="payment_type"
                    onchange="togglePaymentAction()" {{$disable}}>
                    <option value="To be Billed" {{$getconsignments->payment_type == 'To be Billed' ? 'selected' : ''}}>
                        TBB</option>
                    <option value="UPI/Wallet" {{$getconsignments->payment_type == 'UPI/Wallet' ? 'selected' : ''}}>
                        TUPI/Wallet
                    </option>
                    <option value="Cash" {{$getconsignments->payment_type == 'Cash' ? 'selected' : ''}}>
                        Cash
                    </option>
                    <option value="Card" {{$getconsignments->payment_type == 'Card' ? 'selected' : ''}}>
                        Card
                    </option>
                    <option value="Net Banking" {{$getconsignments->payment_type == 'Net Banking' ? 'selected' : ''}}>
                        Net Banking
                    </option>
                </select>
            </div>
        </div>

        <input type="hidden" class="form-seteing date-picker" id="consignDate" name="consignment_date" placeholder=""
            value="<?php echo date('d-m-Y'); ?>">

        {{--pickup & drop info--}}
        <div class="form-row">
            <h6 class="col-12">Farmer Address Details Section:</h6>
            <div class="form-group col-md-4">
                <label>
                Farmer Name and contact search option<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" style="width: 328px;" type="text" name="consignee_id"
                    id="select_farmer" disabled>
                    <option value="">Select Consignee</option>
                    @if(count($consignees) > 0)
                    @foreach($consignees as $k => $consignee)
                    <option value="{{$consignee->id}}"
                        {{ $consignee->id == $getconsignments->consignee_id ? 'selected' : ''}}>
                        {{ucwords($consignee->nick_name)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <?php 
                if(empty($getconsignments->prs_id)){ ?>
                <input type="hidden" name="consignee_id" value="{{$getconsignments->consignee_id}}" />
                <?php } ?>
                <div class="" id="consigner_address"></div>
            </div>
            <div class="form-group col-md-4">
                <label>
                Select Farm Location<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" style="width: 328px;" type="text" name="ship_to_id"
                    id="select_farmer_add" disabled>
                    <option value="">Select Farm</option>
                    @if(count($farms) > 0)
                    @foreach($farms as $k => $consignee)
                    <option value="{{$consignee->id}}"
                        {{ $consignee->id == $getconsignments->ship_to_id ? 'selected' : ''}}>
                        {{ucwords($consignee->field_area)}}
                    </option>
                    @endforeach
                    @endif
                </select>
                <?php if(empty($getconsignments->prs_id)){ ?>
                <input type="hidden" name="ship_to_id" value="{{$getconsignments->ship_to_id}}" />
                <?php } ?>
                <div id="farm_address">

                </div>
            </div>

            <div class="form-row" style="width: 100%">
                <h6 class="col-12">Spray Details Section</h6>
                <div class="form-group col-md-4">
                <label>
                    Crop<span class="text-danger">*</span>
                </label>
                <select class="form-control form-small my-select2" id="crop" name="crop" tabindex="-1">
                    <option value="">Select Crop</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>
                 Acreage<span class="text-danger">*</span>
                </label>
                <Input type="number" class="form-control" id="" name="acreage" value="{{$getconsignments->acreage}}">
            </div>

            </div>
            {{--vehicle info--}}
            <div class="form-row" style="width: 100%">
                <h6 class="col-12">Drone Information</h6>

                <div class="form-group col-md-4">
                    <label>
                        Drone Number<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                        <option value="">Select Drone no</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>
                        Rider<span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-small my-select2" id="driver_id" name="driver_id" tabindex="-1">
                        <option value="">Select Rider</option>
                        @foreach($drivers as $driver)
                        <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                '-'}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>
                        EDD<span class="text-danger">*</span>
                    </label>
                    <Input type="date" class="form-control form-small" name="edd" />
                </div>

            </div>
            <div class=" col-12 d-flex justify-content-end align-items-center" style="gap: 1rem; margin-top: 3rem;">
                {{-- <a class="mt-2 btn btn-outline-primary" href="{{url($prefix.'/consignments') }}"> Back</a>--}}
                <button type="submit" class="mt-2 btn btn-primary disableme"
                    style="height: 40px; width: 200px">Submit</button>
            </div>

    </form>
</div>
<!-- widget-content-area -->

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

    // fix select2 bootstrap modal scroll bug
    $(document).on('select2:close', '.my-select2', function(e) {
        var evt = "scroll.select2"
        $(e.target).parents().off(evt)
        $(window).off(evt)
    })
})

// add consignment date
$('#consignDate, #date').val(new Date().toJSON().slice(0, 10));

function showResult(str) {
    if (str.length == 0) {
        $(".search-suggestions").empty();
        $(".search-suggestions").css('border', '0px');
    } else if (str.length > 0) {
        $(".search-suggestions").css('border', 'solid 1px #f6f6f6');
        var options = '';
        options = "<option value='Seeds'>";
        options += "<option value='Chemicals'>";
        options += "<option value='PGR'>";
        options += "<option value='Fertilizer'>";
        options += "<option value='Pesticides'>";
        $('#json-datalist').html(options);
    }
}

$('#chek').click(function() {
    $('#veh').toggleClass('d-flex');
});

function myMap() {
    var mapProp = {
        center: new google.maps.LatLng(51.508742, -0.120850),
        zoom: 5,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
}

// invoice no duplicate validate
$('.invc_no').blur(function() {
    var invc_no = $(this).val();
    var row_id = jQuery(this).attr('id');
    $.ajax({
        url: "/invoice-check",
        method: "get",
        data: {
            invc_no: invc_no
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (response.success == true) {
                swal('error', response.errors, 'error');
                $("#" + row_id).val('');
            }
        }
    })
});

// select item onchange
function getItem(item) {
    var item_val = item.value;
    $.ajax({
        url: '/get-items',
        method: "get",
        data: {
            item_val: item_val
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            console.log(res);
            if (res.success == true) {
                ($(item).closest('tr').find('td').eq(1).find('input').prop('disabled', false));
                ($(item).closest('tr').find('td').eq(1).find('input').val(''));

                ($(item).closest('tr').find('td').eq(2).find('input').val(res.item.net_weight));
                ($(item).closest('tr').find('td').eq(3).find('input').val(res.item.gross_weight));
                ($(item).closest('tr').find('td').eq(4).find('input').val(res.item.chargable_weight));
            }
        }
    });
}

// append address
$(document).ready(function() {
    var regclient_id = $('#select_regclient').val();
    var consigner_id = $('#select_farmer').val();
    var consignee_id = $('#select_consignee').val();
    var shipto_id = $('#select_farmer_add').val();
    $.ajax({
        type: 'get',
        url: APP_URL + '/get_consigners',
        data: {
            consigner_id: consigner_id,
            regclient_id: regclient_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            // $('#consigner_address').empty();
            // $('#consignee_address').empty();
            // $('#ship_to_address').empty();

            $('#select_farmer').append('<option value="">Select Farmer</option>');
            $('#select_ship_to').append('<option value="">Select Ship To</option>');
            $.each(res.consignee, function(key, value) {
                $('#select_farmer').append('<option value="' + value
                    .id + '">' + value.nick_name + '</option>');
            });
            if (res.data) {
                console.log(res.data);
                if (res.data.address_line1 == null) {
                    var address_line1 = '';
                } else {
                    var address_line1 = res.data.address_line1 + '<br>';
                }
                if (res.data.address_line2 == null) {
                    var address_line2 = '';
                } else {
                    var address_line2 = res.data.address_line2 + '<br>';
                }
                if (res.data.address_line3 == null) {
                    var address_line3 = '';
                } else {
                    var address_line3 = res.data.address_line3 + '<br>';
                }
                if (res.data.address_line4 == null) {
                    var address_line4 = '';
                } else {
                    var address_line4 = res.data.address_line4 + '<br>';
                }
                if (res.data.gst_number == null) {
                    var gst_number = '';
                } else {
                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                }
                if (res.data.phone == null) {
                    var phone = '';
                } else {
                    var phone = 'Phone: ' + res.data.phone;
                }
                $('#consigner_address').append(address_line1 + ' ' + address_line2 + '' +
                    address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
                // $("#dispatch").val(res.data.city);
                // if (res.data.get_reg_client.name == null) {
                //     var regclient = '';
                // } else {
                //     var regclient = res.data.get_reg_client.name;
                // }
                // $("#regclient").val(regclient);
                // // multiple invoice chaeck on regclicent //
                // if (res.regclient == null) {
                //     var multiple_invoice = '';
                // } else {
                //     if (res.regclient.is_multiple_invoice == null || res.regclient
                //         .is_multiple_invoice == '') {
                //         var multiple_invoice = '';
                //     } else {
                //         var multiple_invoice = res.regclient.is_multiple_invoice;
                //     }
                // }
                // if (multiple_invoice == 4) {
                //     $('.insert-more').attr('disabled', false);
                // } else {
                //     $('.insert-more').attr('disabled', true);
                // }
            }
        }
    });
    ///////get consinee address//
    $.ajax({
        type: 'get',
        url: APP_URL + '/get_consignees',
        data: {
            consignee_id: consignee_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            // $('#consignee_address').empty();
            if (res.data) {
                console.log(res.data);
                if (res.data.address_line1 == null) {
                    var address_line1 = '';
                } else {
                    var address_line1 = res.data.address_line1 + '<br>';
                }
                if (res.data.address_line2 == null) {
                    var address_line2 = '';
                } else {
                    var address_line2 = res.data.address_line2 + '<br>';
                }
                if (res.data.address_line3 == null) {
                    var address_line3 = '';
                } else {
                    var address_line3 = res.data.address_line3 + '<br>';
                }
                if (res.data.address_line4 == null) {
                    var address_line4 = '';
                } else {
                    var address_line4 = res.data.address_line4 + '<br>';
                }
                if (res.data.gst_number == null) {
                    var gst_number = '';
                } else {
                    var gst_number = 'GST No: ' + res.data.gst_number + '<br>';
                }
                if (res.data.phone == null) {
                    var phone = '';
                } else {
                    var phone = 'Phone: ' + res.data.phone;
                }
                $('#consignee_address').append(address_line1 + ' ' + address_line2 + '' +
                    address_line3 + ' ' + address_line4 + ' ' + gst_number + ' ' + phone + '');
            }
        }
    });
    //////////get ship to address///
    $.ajax({
        type: 'get',
        url: APP_URL + '/get_consignees',
        data: {
            consignee_id: shipto_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            // $('#ship_to_address').empty();
            console.log(res.data.address);
            if (res.data) {
                if (res.data.address == null) {
                    var address = '';
                } else {
                    var address = res.data.address + '<br>';
                }

                $('#farm_address').append(address);
            }
        }
    });
});

function togglePaymentAction() {
    if ($('#payment_type').val() == 'To Pay') {
        $('#freight_on_delivery').attr('readonly', false);
        $('#cod').attr('readonly', false);
    } else if ($('#payment_type').val() == 'Paid') {
        $('#cod').attr('readonly', true);
        $('#freight_on_delivery').attr('readonly', true);
    } else {
        $('#freight_on_delivery').attr('readonly', true);
        $('#cod').attr('readonly', false);
        $('#freight_on_delivery').val('');
    }
}

// function togglePaymentAction() {

// if ($('#paymentType').val() == 'To Pay') {
//     $('#freight_on_delivery').attr('readonly', false);
//     $('#cod').attr('readonly', false);
// } else if($('#paymentType').val() == 'Paid'){
//     $('#cod').attr('readonly', true);
//     $('#freight_on_delivery').attr('readonly', true);
// } else {
//     $('#freight_on_delivery').attr('readonly', true);
//     $('#cod').attr('readonly', false);
//     $('#freight_on_delivery').val('');
// }
// }
</script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ6x_bU2BIZPPsjS8Y8Zs-yM2g2Bs2mnM&callback=myMap"> -->
</script>
@endsection