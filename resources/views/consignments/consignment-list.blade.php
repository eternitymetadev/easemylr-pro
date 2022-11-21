@extends('layouts.main')
@section('content')
<style>
.accordion {
    overflow-anchor: none;
    font-weight: bold;
}
.accepted {
    color: #ffffff !important;
    background: #007bff;
    padding: 3px 5px;
    border-radius: 5px;
}
.started {
    color: #ffffff !important;
    background: #e2a03f;
    padding: 3px 5px;
    border-radius: 5px;
}
.successful {
    color: #ffffff !important;
    background: #009688;
    padding: 3px 5px;
    border-radius: 5px;
}
.cbp_tmtimeline {
    margin: 0;
    padding: 0;
    list-style: none;
    position: relative
}

.cbp_tmtimeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #eee;
    left: 20%;
    margin-left: -6px
}

.cbp_tmtimeline>li {
    position: relative
}

.cbp_tmtimeline>li:first-child .cbp_tmtime span.large {
    color: #444;
    font-size: 17px !important;
    font-weight: 700
}

.cbp_tmtimeline>li:first-child .cbp_tmicon {
    background: #fff;
    color: #666
}

.cbp_tmtimeline>li:nth-child(odd) .cbp_tmtime span:last-child {
    color: #444;
    font-size: 13px
}

.cbp_tmtimeline>li:nth-child(odd) .cbp_tmlabel {
    background: #f0f1f3
}

.cbp_tmtimeline>li:nth-child(odd) .cbp_tmlabel:after {
    border-right-color: #f0f1f3
}

.cbp_tmtimeline>li .empty span {
    color: #777
}

.cbp_tmtimeline>li .cbp_tmtime {
    display: block;
    width: 23%;
    padding-right: 70px;
    position: absolute
}

.cbp_tmtimeline>li .cbp_tmtime span {
    display: block;
    text-align: right
}

.cbp_tmtimeline>li .cbp_tmtime span:first-child {
    font-size: 15px;
    color: #3d4c5a;
    font-weight: 700
}

.cbp_tmtimeline>li .cbp_tmtime span:last-child {
    font-size: 14px;
    color: #444;
    margin-top: 10px;
}

.cbp_tmtimeline>li .cbp_tmlabel {
    margin: 0 0 15px 25%;
    background: #f0f1f3;
    padding: 1.2em;
    position: relative;
    border-radius: 5px
}

.cbp_tmtimeline>li .cbp_tmlabel:after {
    right: 100%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-right-color: #f0f1f3;
    border-width: 10px;
    top: 10px
}

.cbp_tmtimeline>li .cbp_tmlabel blockquote {
    font-size: 16px
}

.cbp_tmtimeline>li .cbp_tmlabel .map-checkin {
    border: 5px solid rgba(235, 235, 235, 0.2);
    -moz-box-shadow: 0px 0px 0px 1px #ebebeb;
    -webkit-box-shadow: 0px 0px 0px 1px #ebebeb;
    box-shadow: 0px 0px 0px 1px #ebebeb;
    background: #fff !important
}

.cbp_tmtimeline>li .cbp_tmlabel h2 {
    margin: 0px;
    padding: 0 0 10px 0;
    line-height: 26px;
    font-size: 16px;
    font-weight: normal
}

.cbp_tmtimeline>li .cbp_tmlabel h2 a {
    font-size: 15px
}

.cbp_tmtimeline>li .cbp_tmlabel h2 a:hover {
    text-decoration: none
}

.cbp_tmtimeline>li .cbp_tmlabel h2 span {
    font-size: 15px
}

.cbp_tmtimeline>li .cbp_tmlabel p {
    color: #444
}

.cbp_tmtimeline>li .cbp_tmicon {
    width: 15px;
    height: 15px;
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    font-size: 1.4em;
    line-height: 40px;
    -webkit-font-smoothing: antialiased;
    position: absolute;
    color: #fff;
    background: #1a545a;
    border-radius: 50%;
    box-shadow: 0 0 0 5px #f4f4f4;
    text-align: center;
    left: 21%;
    top: 12px;
    margin: 0 0 0 -21px;
}

@media screen and (max-width: 992px) and (min-width: 768px) {
    .cbp_tmtimeline>li .cbp_tmtime {
        padding-right: 60px
    }
}

@media screen and (max-width: 65.375em) {
    .cbp_tmtimeline>li .cbp_tmtime span:last-child {
        font-size: 12px
    }
}

@media screen and (max-width: 47.2em) {
    .cbp_tmtimeline:before {
        display: none
    }
    .cbp_tmtimeline>li .cbp_tmtime {
        width: 100%;
        position: relative;
        padding: 0 0 20px 0
    }
    .cbp_tmtimeline>li .cbp_tmtime span {
        text-align: left
    }
    .cbp_tmtimeline>li .cbp_tmlabel {
        margin: 0 0 30px 0;
        padding: 1em;
        font-weight: 400;
        font-size: 95%
    }
    .cbp_tmtimeline>li .cbp_tmlabel:after {
        right: auto;
        left: 20px;
        border-right-color: transparent;
        border-bottom-color: #f5f5f6;
        top: -20px
    }
    .cbp_tmtimeline>li .cbp_tmicon {
        position: relative;
        float: right;
        left: auto;
        margin: -64px 5px 0 0px
    }
    .cbp_tmtimeline>li:nth-child(odd) .cbp_tmlabel:after {
        border-right-color: transparent;
        border-bottom-color: #f5f5f6
    }
}

.bg-green {
    background-color: #50d38a !important;
    color: #fff;
}

.bg-blush {
    background-color: #ff758e !important;
    color: #fff;
}

.bg-orange {
    background-color: #ffc323 !important;
    color: #fff;
}

.bg-info {
    background-color: #2CA8FF !important;
}
.dt--top-section {
    margin:none;
}
div.relative {
    position: absolute;
    left: 9px;
    top: 14px;
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
.btn-group > .btn, .btn-group .btn {
    padding: 0px 0px;
    padding: 10px;
}
.btn {
    font-size: 10px;
}

tr.shown td.dt-control {
    background: url('/assets/img/details_close.png') no-repeat center center !important;
}
td.dt-control {
    background: url('/assets/img/details_open.png') no-repeat center center !important;
    cursor: pointer;
}
.theads {
    text-align: center;
    padding: 5px 0;
    color: #279dff;
}
.ant-timeline {
    box-sizing: border-box;
    font-size: 14px;
    font-variant: tabular-nums;
    line-height: 1.5;
    font-feature-settings: "tnum","tnum";
    margin: 0;
    padding: 0;
    list-style: none;
}
.css-b03s4t {
    color: rgb(0, 0, 0);
    padding: 6px 0px 2px;
}
.css-16pld72 {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: capitalize;
}
.css-16pld73 {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: capitalize;
}
.ellipse{
    width:320px;
}
.ellipse2{
    width:200px;
}
.ellipse:hover{
    overflow: visible;
    white-space: normal;
    width:100%; /* just added this line */
}
.ellipse2:hover{
    overflow: visible;
    white-space: normal;
    width:100%; /* just added this line */
}
.ant-timeline-item-tail {
    position: absolute;
    top: 10px;
    left: 4px;
    height: calc(100% - 10px);
    border-left: 2px solid #e8e8e8;
}
.ant-timeline-item-last>.ant-timeline-item-tail {
    display: none;
}

.ant-timeline-item-head-red {
    background-color: #f5222d;
    border-color: #f5222d;
}
.ant-timeline-item-head-green {
    background-color: #52c41a;
    border-color: #52c41a;
}
.ant-timeline-item-content {
    position: relative;
    top: -6px;
    margin: 0 0 0 18px;
    word-break: break-word;
}
.css-phvyqn {
    color: rgb(0, 0, 0);
    padding: 0px;
    height: 34px !important;
}
.ant-timeline-item {
    position: relative;
    margin: 0;
    padding: 0 0 5px;
    font-size: 14px;
    list-style: none;
}
.ant-timeline-item-head {
    position: absolute;
    width: 10px;
    height: 10px;
}
.bg-cust {
    background: #01010314;
    color: #e7515a;
}
.css-ccw3oz .ant-timeline-item-head {
    padding: 0px;
    border-radius: 0px !important;
}
.labels{
    color:#4361ee;
}
a.badge.alert.bg-secondary.shadow-sm {
    color: #fff;
}
#map {
    height: 400px;
    width: 600px;
}

</style>
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Consignment
                                List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">

                    <div class="container-fluid">
                        <div class="row winery_row_n spaceing_2n mb-3">
                            <!-- <div class="col-xl-5 col-lg-3 col-md-4">
                                <h4 class="win-h4">List</h4>
                            </div> -->
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
                                    <a class="btn-primary btn-cstm btn ml-2"
                                        style="font-size: 15px; padding: 9px; width: 130px"
                                        href="{{'consignments/create'}}"><span><i class="fa fa-plus"></i> Add
                                            New</span></a>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-cstm reset_filter ml-2"
                                        style="font-size: 15px; padding: 9px;"
                                        data-action="<?php echo url()->current(); ?>"><span><i
                                                class="fa fa-refresh"></i> Reset Filters</span></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @csrf
                    <div class="main-table table-responsive">
                        @include('consignments.consignment-list-ajax')
                    </div>
                </div>
            </div>

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img class="d-block w-100" src="..." alt="First slide">
                                    </div>
                                    <div class="carousel-item">
                                        <img class="d-block w-100" src="..." alt="Second slide">
                                    </div>
                                    <div class="carousel-item">
                                        <img class="d-block w-100" src="..." alt="Third slide">
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('models.delete-user')
@include('models.common-confirm')
@include('models.manual-updatrLR')
@endsection

@section('js')
<script>

// jQuery(document).on("click", ".card-header", function () {
function row_click(row_id, job_id, url)
{
    $('.append-modal').empty();
    $('.cbp_tmtimeline').empty();

    var modal_container = '';
    var modal_html = '';
    var modal_html1 = '';

    var job_id = job_id;
    var url =url;
    jQuery.ajax({
        url: url,
        type: "get",
        cache: false,
        data: { job_id: job_id },
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                "content"
            ),
        },
        success: function (response) {
            if (response.success) {

                var modal_html = '';
                var trackinglink = '';

                console.log(response);
                // return false;
                if(response.job_data){
                    var modal_container = '<div class="container" oncontextmenu="return true;"><div class="row"><div class="col-md-10"><ul class="cbp_tmtimeline">';
                    $.each(response.job_data, function (index, task) {
                        var timestamp = task.creation_datetime;
                        var type = task.type;
                        var des_data = '';
                        var result = '';

                        if (type == 'image_added') {
                            var uploaded_by = 'Attachment uploaded by';
                            var view_text = 'View Attachment';
                            var title = 'Attachment';
                            var image = '<img src="'+ task.description +'" width="100%" seamless="">';
                        }else if (type == 'signature_image_added') {
                            var uploaded_by = 'Signature Added by';
                            var view_text = 'View Signatures';
                            var title = 'Signature';
                            var image = '<img src="'+ task.description +'" width="100%" height="298" seamless="" />';
                        }

                        if(type == 'image_added' || type == 'signature_image_added'){
                            modal_html += '<span style="padding-left:245px; font-size: 12px;">'+uploaded_by+' '+task.fleet_name +'</span><br />';
                            modal_html += '<button type="button" style="margin-left:245px;" class="btn btn-primary mb-2 mr-2" data-toggle="modal" data-target="#mod_'+task.id+'">'+ view_text +'</button>';

                            //  Modal start //
                            modal_html += '<div class="modal fade" id="mod_'+task.id+'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                            modal_html += '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header">';
                            modal_html += '<h5 class="modal-title" id="exampleModalLabel">'+ title +'</h5>';
                            modal_html += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                            modal_html += '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"> <line x1="18" y1="6" x2="6" y2="18"> </line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                            modal_html += '</button></div>';
                            modal_html += '<div class="modal-body">'+ image +'</div>';
                            modal_html += '<div class="modal-footer"><button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Close</button></div></div></div></div>';
                        }else{
                            var text = task.label_description;
                            var label_text = text.replace("at", " by");
                            var result = label_text +' '+task.fleet_name;
                            // var result = task.label_description.replace(/^\s+|\s+$/gm,'at');+ ' by ' + task.fleet_name;
                            // var str = trim(task.label_description, 'at') + ' by ' + task.fleet_name;
                            // if (str_contains(str, 'CREATED')) {
                            //     var deresults_data += "LR Created";
                            // } else {
                                // var des_data += str;
                            // }
                        }
                        var text_date = task.creation_datetime;
                        var creation_datetime = text_date.replace("T", " ");
                        var creation_datetime = creation_datetime.replace("Z", "");
                        var creation_datetime = creation_datetime.split('.')[0];

                        modal_html += '<li><time class="cbp_tmtime" datetime="'+ creation_datetime +'"><span class="hidden">'+creation_datetime+'</span></time>';
                        if(result){
                        modal_html += '<div class="cbp_tmicon"><i class="zmdi zmdi-account"></i></div><div class="cbp_tmlabel empty"> <span>'+ result +'</span></div>';
                        }

                    });

                    modal_container += '</li></ul></div></div></div>';

                    $('.append-modal').append(modal_container);
                    $('.cbp_tmtimeline').append(modal_html);

                } else{
                    var modal_html1 = 'No data available';
                    $('.append-modal').html(modal_html1);
                }
                if(( response.job_id != '') && (response.delivery_status != 'Successful')){
                    var trackinglink = '<iframe id="iGmap-'+row_id+'" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+response.tracking_link+'" ></iframe>';
                    $("#mapdiv-"+row_id).html(trackinglink);
                }
                else{
                    var trackinglink = '<div id="map-'+row_id+'" style="height: 100%; width: 100%"> </div>';
                    $("#mapdiv-"+row_id).html(trackinglink);
                    initMap(response, row_id);
                }

            }
        }

    });

}


var map;
function initMap(response, row_id)
{
    var map = new google.maps.Map(document.getElementById('map-'+row_id), {zoom: 8, center: 'Changigarh',});
    var directionsDisplay = new google.maps.DirectionsRenderer({'draggable': false});
    var directionsService = new google.maps.DirectionsService();
    var travel_mode = 'DRIVING';
    var origin = response.cnr_pincode;
    var destination = response.cne_pincode;
    directionsService.route({
        "origin": origin,
        "destination": destination,
        "travelMode": travel_mode,
        "avoidTolls": true,
    }, function (response, status) {
        if (status === 'OK') {
            directionsDisplay.setMap(map);
            directionsDisplay.setDirections(response);
            console.log(response);
        } else {
            directionsDisplay.setMap(null);
            directionsDisplay.setDirections(null);
            // alert('Unknown route found with error code 0, contact your manager');
        }
    });
}
</script>

@endsection
