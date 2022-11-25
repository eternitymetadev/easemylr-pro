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
    <form class="general_form" method="POST" action="{{url($prefix.'/orders')}}" id="createorder" style="margin: auto; ">
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
                            <?php $authuser = Auth::user();
                                if($authuser->role_id ==3) { ?>
                                <input id="location_id" type="hidden" name="branch_id" value="">
                            <?php } ?> 
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
                            <select class="form-control form-small my-select2" style="width: 328px;" id="select_consigner"  type="text" name="consigner_id">
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
            <div class=" col-sm-3">
                <button type="submit" class="mt-2 btn btn-primary disableme">Submit</button>

                <a class="mt-2 btn btn-primary" href="{{url($prefix.'/consignments') }}"> Back</a>
            </div>
        </div>
    </form>
</div>

@endsection
@section('js')
<script>
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

</script>
@endsection