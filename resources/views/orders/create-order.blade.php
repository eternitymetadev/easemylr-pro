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
    /* / background-color: white; / */
    border-radius: 55%;
    /* / vertical-align: middle;  / */
    border: 1px solid #ddd;
    /* / appearance: none; /
    / -webkit-appearance: none; /
    / outline: none;  /
    / cursor: pointer; / */
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
    /* / margin: 20px auto; / */
    background: #fff;
}

    .wizard .nav-tabs {
        position: relative;
        margin: 40px auto;
        margin-bottom: 0;
        /* / border-bottom-color: #e0e0e0; / */
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
    /* / border-bottom-color: transparent; / */
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

/* .wizard li:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 0;
    margin: 0 auto;
    bottom: 0px;
    border: 5px solid transparent;
    border-bottom-color: #5bc0de;
    transition: 0.1s ease-in-out;
} */

/* .wizard li.active:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 1;
    margin: 0 auto;
    bottom: 0px;
    border: 10px solid transparent;
    border-bottom-color: #5bc0de;
} */

.wizard .nav-tabs > li a {
    width: 48px;
    height: 70px;
    /* / margin: 20px auto; / */
    border-radius: 100%;
    padding: 0;
}

    /* .wizard .nav-tabs > li a:hover {
        background: transparent;
    } */

/* .wizard .tab-pane {
    position: relative;
    padding-top: 50px;
}

.wizard h3 {
    margin-top: 0;
} */

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
            <!-- <div class="row">
                            <div class="col-sm-8">
                                        
                                            <div class="wizard" style="width: 762px; MARGIN-TOP:-25PX;">
                                                <div class="wizard-inner">
                                                    <div class="connecting-line"></div>
                                                    <ul class="nav nav-tabs" role="tablist">

                                                        <li role="presentation" class="aa">
                                                            <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
                                                                <span class="round-tab">
                                                                <i class="fa-solid fa-truck-fast"></i>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li role="presentation" class="bb" >
                                                            <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
                                                                <span class="round-tab">
                                                                <i class="fa-solid fa-truck-fast"></i>
                                                                </span>
                                                            </a>
                                                        </li>
                                                        <li role="presentation" class="cc">
                                                            <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3">
                                                                <span class="round-tab">
                                                                <i class="fa-solid fa-truck-fast"></i>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li role="presentation" class="dd">
                                                            <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
                                                                <span class="round-tab">
                                                                    <i class="glyphicon glyphicon-ok"></i>
                                                                </span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                           
                                        </div> -->

                <!-- ---------------------------- -->
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
                            <select class="form-control form-small my-select2" style="width: 328px;"  type="text" name="consignee_id"
                                        id="select_consignee">
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

            </div>
            <div class="col-lg-12 layout-spacing">

            </div>
            <div class="col-lg-12 layout-spacing">

            </div>
            <div class="col-lg-12 layout-spacing">
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

 
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ6x_bU2BIZPPsjS8Y8Zs-yM2g2Bs2mnM&callback=myMap"></script> 
@endsection