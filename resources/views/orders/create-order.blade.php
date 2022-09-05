@extends('layouts.main')
@section('content')
<style>
    .form-seteing {
        width: 100%;
        margin-top: 10px;
        height: 40px;
        background-color: #f5f6f7;
        border: none;
        padding: 10px;
        border-radius: 5px;
    }

    .form-group label,
    label {
        font-size: 12px;
        color: black;
        letter-spacing: 1px;
        font-weight: bold;
    }


    .table>thead>tr>th {

        background: white;

    }

    .table>thead>tr>th {

        padding: 10px 7px 6px 8px;

    }

    label.error {
        color: red;
        font-weight: bold;
    }

    .seteing {
        width: 130px;
        background: #4361ee;
        height: 40px;
        border: none;
        border-radius: 5px;
        padding: 0 10px;
        color: #fff;
    }

    .sete {
        width: 170px;
        height: 40px;
        border: none;
        background: #f1f2f3 !important;
        border-radius: 5px;
    }

    .row.cuss {
        box-shadow: 1px 0px 9px 1px #3b3f5c;
        width: 100%;
        margin: 0 auto;
    }

    .cusright {
        min-height: 238px;
        box-shadow: -7px 0px 6px -8px #000;
    }

    .cuss span {
        margin-bottom: 0px !important;
    }

    .row.cuss {
        box-shadow: 1px 0px 7px 1px #3b3f5c4f;
        width: 100%;
        margin: 0 auto;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        border: none;
        color: #3b3f5c !important;
        font-size: 15px;
        padding: 8px 10px;
        letter-spacing: 1px;
        background-color: #f1f2f3 !important;
        height: calc(1.4em + 1.4rem + 2px);
        / padding: 0.75rem 1.25rem;/ border-radius: 6px;
        box-shadow: none;
        height: 40px;
    }

    .row.cuss.fuss {
        padding: 15px 0;
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
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{$prefix.'/orders'}}">Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create
                                Order</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <form class="general_form" method="POST" action="{{url($prefix.'/orders')}}" id="createorder"
                    style="margin: auto; ">
                    <div class="row cuss">
                        <div class="col-sm-4">
                            <div class="panel info-box panel-white">
                                <div class="panel-body" style="padding: 10px;">
                                    <div class="row con1" style="background: white; padding: 0px;">
                                        <div class=" col-sm-3" style="margin-top:3px;">
                                            <label class=" control-label" style="font-weight: bold;">Select
                                                Consignor<span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-9" style="margin-top:3px;">
                                            <select id="select_consigner" class="my-select2 form-seteing" type="text"
                                                name="consigner_id">
                                                <option value="">Select Consignor</option>
                                                @foreach($consigners as $consigner)
                                                <option value="{{$consigner->id}}">{{$consigner->nick_name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="container" style="padding-top:10px">
                                            <div id="consigner_address">
                                                <!-- <strong>FRONTIER AGROTECH PRIVATE LIMITED </strong><br/>KHASRA NO-390, RELIANCE ROAD <br/>GRAM BHOVAPUR <br/>HAPUR-245304 <br/><strong>GST No. : </strong>09AACCF3772B1ZU<br/><strong>Phone No. : </strong>9115115612 -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class=" col-sm-4">
                            <div class="panel info-box panel-white">
                                <div class="panel-body" style="padding: 10px;">
                                    <div class="row con1 form-group" style="background: white; ">
                                        <div class=" col-sm-3">
                                            <label class=" control-label" style="font-weight: bold;">Select
                                                Consignee<span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="my-select2 form-seteing" type="text" name="consignee_id"
                                                id="select_consignee">
                                                <option value="">Select Consignee</option>

                                            </select>
                                        </div>
                                        <div class="container" style="padding-top:10px">
                                            <div id="consignee_address">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class=" col-sm-4">
                            <div class="panel info-box panel-white">
                                <div class="panel-body" style="padding: 10px;">
                                    <div class="row con1 form-group" style="background: white; ">
                                        <div class=" col-sm-3" style="margin-top:2px;">
                                            <label class=" control-label" style="font-weight: bold;">Ship To<span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-9" style="margin-top:2px;">
                                            <select class="my-select2 form-seteing" type="text" name="ship_to_id"
                                                id="select_ship_to">
                                                <option value="">Select Ship To</option>

                                            </select>
                                        </div>
                                        <div class="container" style="padding-top:11px">
                                            <div id="ship_to_address">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <input type="hidden" class="form-seteing" id="dispatch" name="dispatch_form" value="" placeholder=""
                        readonly style="border:none;">

                    <div class="row cuss fuss" style="margin-top: 15px;">

                        <div class=" col-sm-3">
                            <button type="submit" class="mt-2 btn btn-primary disableme">Submit</button>
                            <a class="mt-2 btn btn-primary" href="{{url($prefix.'/orders') }}"> Back</a>
                        </div>
                    </div>
                    <!-- Row -->

                </form>
            </div>

        </div>
    </div>
</div>

@endsection
@section('js')
<script>
    // $(function() {
    //     $('.basic').selectpicker();
    // });
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

</script>
@endsection