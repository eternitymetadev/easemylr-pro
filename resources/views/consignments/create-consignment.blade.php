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
h4{
    font-size: 18px;

}
.form-control {
    height: 33px;
    padding: 0px;
    
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


th,td {
  text-align: left;
  padding: 8px;
  color: black;
}
.cont{
  background:white;
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

    .wizard > div.wizard-inner {
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
    border-bottom:none;
}
.wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
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
span.round-tab i{
    color:#555555;
}
.wizard li.active span.round-tab {
    background: #fff;
    border: 2px solid #5bc0de;
    
}
.wizard li.active span.round-tab i{
    color: #5bc0de;
}

span.round-tab:hover {
    color: #333;
    border: 2px solid #333;
}

.wizard .nav-tabs > li {
    width: 25%;
}

.wizard .nav-tabs > li a {
    width: 48px;
    height: 70px;
    border-radius: 100%;
    padding: 0;
}

@media( max-width : 585px ) {

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

    .wizard .nav-tabs > li a {
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
</style>
<div class="layout-px-spacing">
    <form class="general_form" method="POST" action="{{url($prefix.'/consignments')}}" id="createconsignment" style="margin: auto; ">
        <div class="row">
            <div class="col-lg-12 layout-spacing">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <h4><b>Bill To Information</b></h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="row">
                        <div class=" col-sm-4 ">
                            <p>Select Bill to Client</p>
                            <select class="form-control form-small my-select2" id="select_regclient" name="regclient_id">
                                <option selected="selected" disabled>select client..</option>
                                @foreach($regionalclient as $client)
                                <option value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" col-sm-2 ">
                            <p>Payment Term</p>
                            <select class="form-control form-small my-select2" style="width: 160px;" name="payment_type">
                                <option value="To be Billed" selected="selected">To be Billed</option>
                                <option value="To Pay">To Pay</option>
                                <option value="Paid">Paid</option> 
                            </select>
                        </div>
                        <div class=" col-sm-2 ">
                            <p>Freight</p>
                            <Input type="number" class="form-control form-small" style="width: 160px; height: 43px;" name="freight">
                        </div>

                        <div class="form-group col-sm-4">
                            <p>Sale Return</p>
                            <div class="check-box d-flex">
                                <div class="checkbox radio">
                                    <label class="check-label">Yes
                                        <input type="radio" name="is_salereturn" value="1" class="" >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="checkbox radio ml-3">
                                    <label class="check-label">No
                                        <input type="radio" name="is_salereturn" value="0" checked="">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>                             
                        </div>

                        
                    </div>
                </div>
              <!-- </div> --> 
                <input type="hidden" class="form-seteing date-picker" id="consignDate" name="consignment_date" placeholder="" value="<?php echo date('d-m-Y'); ?>">
                <!-- <div class="col-sm-4">
                        <div id="googleMap"  style="width:98%; height:201px; background:#e1e1e8; margin-top: 19px;"></div>
                    </div>
                </div> -->
            </div>

            <div class="col-lg-12 layout-spacing">

                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <h4><b>Pickup and Drop Information</b></h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="row">
                        <div class="col-sm-4 ">
                            <p>Select Pickup Location (Consigner)</p>
                            <select class="form-control form-small my-select2" style="width: 328px;" id="select_consigner"  type="text"
                                        name="consigner_id">
                            <option value="">Select Consignor</option>
                                        <!-- @foreach($consigners as $consigner)
                                        <option value="{{$consigner->id}}">{{$consigner->nick_name}}
                                        </option>
                                        @endforeach -->
                            </select>
                            <div id="consigner_address">
                                    </div>

                        </div>
                        
                        <div class="col-sm-4 ">
                            <p>Select Drop location (Bill To Consignee)</p>
                            <select class="form-control form-small my-select2" style="width: 328px;"  type="text" name="consignee_id" id="select_consignee">
                                <option value="">Select Consignee</option>
                            </select>
                            <div id="consignee_address">

                                    </div>
                        </div>
                        <!-- <div class="col-sm-3 ">
                                <p style="margin-left: 60px;">Different Ship To Location </p>
                                <input type="checkbox" class="checkbox-round" />

                                </div> -->
                        <div class="col-sm-4 ">
                            <p>Select Drop Location (Ship To Consignee)</p>
                            <select class="form-control form-small my-select2" style="width: 328px;"  type="text" name="ship_to_id"  id="select_ship_to">
                            <option value="">Select Ship To</option>
                            </select>
                            <div id="ship_to_address">

                                    </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-12 layout-spacing">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <h4><b>Order Information</b></h4>
                        </div>
                    </div>
                </div>
                <table border="1" width="100%">
                    <div class="row">
                        <tr>
                            <th>Item Description</th>
                            <th>Mode of packing</th>
                            <th>Total Quantity</th>
                            <th>Total Net Weight</th>
                            <th>Total Gross Weight</th>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-small" value="Pesticide" name="description" list="json-datalist" onkeyup="showResult(this.value)"><datalist id="json-datalist"></datalist></td>
                            <td><input type="text" class="form-control form-small" value="Case/s" name="packing_type"></td>
                            <td align="center"><span id="tot_qty">
                                    <?php echo "0";?>
                                </span></td>
                            <td align="center"><span id="tot_nt_wt">
                                    <?php echo "0";?>
                                </span> Kgs.</td>
                            <td align="center"><span id="tot_gt_wt">
                                    <?php echo "0";?>
                                </span> Kgs.</td>

                            <input type="hidden" name="total_quantity" id="total_quantity" value="">
                            <input type="hidden" name="total_weight" id="total_weight" value="">
                            <input type="hidden" name="total_gross_weight" id="total_gross_weight" value="">
                            <input type="hidden" name="total_freight" id="total_freight" value="">
                            <!-- <td><input type="number" class="form-control form-small" name="total_quantity"></td>
                            <td><input type="number" class="form-control form-small" name="total_weight"></td>
                            <td><input type="number" class="form-control form-small" name="total_gross_weight"></td> -->
                        </tr>
                    </div>
                </table>

            </div>
            <div class="col-lg-12 layout-spacing">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <div style="overflow-x:auto;">
                                <table>
                                    <tr>
                                        <th style="width: 160px">Order ID</th>
                                        <th style="width: 180px">Invoice Number</th>
                                        <th style="width: 160px">Invoice Date</th>
                                        <th style="width: 180px">Invoice Amount</th>
                                        <th style="width: 210px">E-way Bill Number</th>
                                        <th style="width: 200px">E-Way Bill Date</th>
                                        <th style="width: 160px">Quantity</th>
                                        <th style="width: 160px">Net Weight</th>
                                        <th style="width: 160px">Gross Weight</th>

                                    </tr>
                                </table>
                                <table style=" border-collapse: collapse;" border='1' id="items_table" >
                                    <tbody>
                                <tr></tr>
                                        <tr>
                                            <td><input type="text" class="form-control form-small orderid" name="data[1][order_id]"></td>
                                            <td><input type="text" class="form-control form-small invc_no" id="1" name="data[1][invoice_no]"></td>
                                            <td><input type="date" class="form-control form-small invc_date" name="data[1][invoice_date]"></td>
                                            <td><input type="number" class="form-control form-small invc_amt" name="data[1][invoice_amount]"></td>
                                            <td><input type="number" class="form-control form-small ew_bill" name="data[1][e_way_bill]"></td>
                                            <td><input type="date" class="form-control form-small ewb_date" name="data[1][e_way_bill_date]"></td>
                                            <td><input type="number" class="form-control form-small qnt" name="data[1][quantity]"></td>
                                            <td><input type="number" class="form-control form-small net" name="data[1][weight]"></td>
                                            <td><input type="number" class="form-control form-small gross" name="data[1][gross_weight]"></td>
                                            <td> <button type="button" class="btn btn-default btn-rounded insert-more"> + </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-12 layout-spacing">

                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <h4><b>vehicle Information </b></h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="row">
                        <div class=" col-sm-4 ">
                            <p>vehicle Number</p>
                            <select class="form-control form-small my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                            <option value="">Select vehicle no</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                            </option>
                            @endforeach
                            </select>
                        </div>
                        <div class=" col-sm-3 ">
                            <p>Driver Name</p>
                            <select class="form-control form-small my-select2" id="driver_id" name="driver_id" tabindex="-1">
                            <option value="">Select driver</option>
                            @foreach($drivers as $driver)
                            <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                '-'}}
                            </option>
                            @endforeach
                            </select>
                        </div>
                        <div class=" col-sm-3 ">
                            <p>EDD</p>
                            <Input type="date" class="form-control form-small" style="width: 160px; height: 43px;" name="edd">
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-12 layout-spacing">

                <div class="widget-header">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <input type="checkbox" id="chek" style="margin-left:19px;">
                            <label for="vehicle1">Vehical Purchase Information</label><br>
                        </div>
                    </div>
                </div>  
                <div class="widget-content" style="display:none;" id="veh">
                    <div class="row">
                        <div class=" col-sm-4 ">
                            <p>Vendor Name</p>
                            <Input type="text" class="form-control form-small" name="transporter_name" style="height: 43px;">
                        </div>
                        <div class=" col-sm-3 ">
                            <p>Vehicle Type</p>
                            
                            <select class="my-select2 sete" id="vehicle_type" name="vehicle_type" tabindex="-1">
                            <option value="">Select vehicle type</option>
                                @foreach($vehicletypes as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                </option>
                                @endforeach
                                </select>
                        </div>
                        <div class=" col-sm-2 ">
                            <p>Purchase Price</p>
                            <Input type="number" class="form-control form-small" style="width: 160px; height: 43px;" name="purchase_price">
                        </div>
                    </div>
                    
                </div>
                <div class=" col-sm-3">
                    <button type="submit" class="mt-2 btn btn-primary disableme">Submit</button>

                    <a class="mt-2 btn btn-primary" href="{{url($prefix.'/consignments') }}"> Back</a>
                </div>
            </div>
        </div>
    </form>
    <!-- widget-content-area -->

@endsection
@section('js')
<script>
    // $(function() {
    //     $('.basic').selectpicker();
    // });
    $(document).ready(function() {
        $('.insert-more').attr('disabled',true);
    });

    jQuery(function () {
        $('.my-select2').each(function () {
            $(this).select2({
                theme: "bootstrap-5",
                dropdownParent: $(this).parent(), // fix select2 search input focus bug
            })
        })

        // fix select2 bootstrap modal scroll bug
        $(document).on('select2:close', '.my-select2', function (e) {
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

    $('#chek').click(function () {
        $('#veh').toggle();
    });

    function myMap() {
var mapProp= {
  center:new google.maps.LatLng(51.508742,-0.120850),
  zoom:5,
};
var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
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
            if(response.success == true){
                swal('error', response.errors, 'error');
                $("#"+row_id).val('');
            }
        }
    })
});

 
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ6x_bU2BIZPPsjS8Y8Zs-yM2g2Bs2mnM&callback=myMap"></script> 
@endsection