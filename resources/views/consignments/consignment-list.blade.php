@extends('layouts.main')
@section('content')

    <style>
        .consignmentItem {
            border-radius: 12px;
            background: #fff;
            box-shadow: 0px 2px 11px 0px #83838370;
            padding: 0.5rem 1rem;
            width: 98%;
            margin: 0 auto 12px;
        }

        .consignmentItem .statusBadge {
            border-radius: 50vh;
            background: #00e3cb;
            padding: 0px 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px !important;
            line-height: 16px;
            font-weight: 500;
            min-width: 100px;
            color: #fff;
            user-select: none;
            cursor: pointer;
        }

        .consignmentItem .statusBadge svg {
            height: 14px;
            width: 14px;
        }

        .consignmentItem .lrDetailBlock {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            flex: 1;
            max-width: 220px;
        }

        .consignmentItem .lrDetailBlock p {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #000;
            font-weight: 600;
            font-size: 12px;
        }

        .consignmentItem .lrDetailBlock p span {
            font-size: 15px;
        }

        .orderAndInvoice {
            align-self: flex-start;
            border-radius: 8px;
            padding: 2px;
            gap: 4px;
            font-weight: 600;
            background: #efefef;
            color: #000;
        }

        .orderAndInvoice div span {
            background: #ffffff;
            padding: 2px 4px;
            width: 148px;
            max-height: 56px;
            overflow-y: auto;
            border-radius: 6px;
            font-size: 13px;
            display: flex;
            color: #646464;
            justify-content: center;
        }

        .actionIcon {
            flex-direction: column;
            height: 45px !important;
            width: 60px !important;
            font-size: 11px;
        }

        .actionIcon svg {
            height: 18px;
            width: 18px;
        }

        .actionIcon:hover span {
            color: #fff !important;
        }


        .datesBlock {
            box-shadow: 0 0 9px -2px inset #83838370;
            border-radius: 8px;
            padding: 2px 6px;
        }

        .datesBlock .dateItem {
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 700;
            color: #4b4b4b;
            padding: 0 6px;
            border-radius: 4px;
        }

        .datesBlock .dateItem span {
            color: #919191;
            font-weight: 500;
        }


        .green {
            background: #009217 !important;
        }

        .orange {
            background: #f9b808 !important;
        }

        .red {
            background: #e70404 !important;
        }

        .extra {
            background: #0087e3 !important;
        }

        .extra2 {
            background: #00e3cb !important;
        }

        .extra3 {
            background: #b400e3 !important;
        }

        .extra4 {
            background: #009be3 !important;
        }

        .notAllowed {
            cursor: not-allowed !important;
            /*pointer-events: none;*/
        }

        /* tabs */
        .consignmentDetailBlock {
            background: #f0f0f0;
            min-height: 300px;
            border-radius: 12px;
            margin-block: 10px;
            padding: 6px;
        }

        .consignmentDetailBlock .nav-item {
            flex: 1;
        }

        .consignmentDetailBlock .nav-link {
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 6px;
            padding: 4px 10px;
            transition: all 250ms ease-in-out;
        }

        .consignmentDetailBlock .nav-link:not(.active):hover {
            font-weight: 600;
            color: #000 !important;
        }

        .consignmentDetailBlock .nav-link.active {
            color: #fff !important;
            background-color: #ffb100;
            transition: all 250ms ease-in-out;
        }

        .tabContainer {
            border-radius: 9px;
            padding: 3px;
            background: #fff;
        }

        .taskDetailContainer {
            /*background: #ffffff20;*/
            margin: 1rem;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid;
        }

        .taskDetailContainer p {
            display: flex;
            gap: 6px;
            justify-content: space-between;
        }

        .taskDetailContainer p span.dHeading {
            width: 95px;
            font-size: 12px;
            font-weight: 600;
        }

        .taskDetailContainer p span.dDescription {
            font-weight: 700;
            flex: 1;
            word-break: break-all;
            color: #282828;
        }

        .otherDetailsBlock {
            width: 80%;
            min-width: 300px;
            margin: 1rem auto;
        }

        .otherDetailsBlock .detailsCol {
            width: 50%;
            gap: 6px;
        }

        .otherDetailsBlock .detailsCol span {
            text-align: center;
            width: 100%;
            font-weight: 600;
            color: #000;
        }

        .otherDetailsBlock .detailsCol span.heading {
            border-bottom: 1px solid;
            font-weight: 700;
            color: #222;
        }

        .timelineBlock:hover {
            background: linear-gradient(180deg, #a4a4a412, #00000008);
            border-radius: 12px;
        }

        .timelineBlock:first-child .statusMarker {
            animation: pulseMarker 1000ms linear infinite;
        }

        @keyframes pulseMarker {
            from {
                outline: 1px solid var(--status);
                outline-offset: 0;
            }
            to {
                outline-offset: 6px;
            }
        }

        .statusMarker {
            height: 20px;
            width: 20px;
            border-radius: 50vh;
            background: var(--status);
        }

        .timelineBlock .timelineStatus {
            padding: 1px 12px;
            border: 1.5px solid var(--status);
            border-radius: 8px;
            background: #fff;
            color: var(--status);
            font-weight: 600;
            width: 105px;
        }

        .timelineBlock .timelineTime {
            font-weight: 700;
            margin-left: 10px;
        }

        .timelineBlock .timeLineDescription {
            margin-left: 10px;
            padding: 0.3rem 1.5rem 1.5rem;
            margin-bottom: 0;
            border-left: 2px dashed var(--status);
        }

        .timelineBlock:last-child .timeLineDescription {
            border-left: none;
            padding: 0.3rem 1.5rem 0.5rem;
        }

        .timelineBlock .timeLineDescription a {
            background: #fffcf2;
            border-radius: 8px;
            padding: 0 8px;
            color: #ffb100;
            border: 1px solid;
            cursor: pointer;
            font-size: 12px;
            margin-left: 1rem;
            font-weight: 500;
        }


        #get-delvery-dateLR input[type=date]{
            border-radius: 12px;
            border: 1px solid #838383;
            padding: 4px 10px;
        }

        {{--  new list css ends here      --}}

        .inlineHead {
            color: #3d3d3d;
            font-weight: 600;
            font-size: 12px;
        }


        .clearIcon {
            visibility: hidden;
            color: darkred;
            border-radius: 50vh;
            height: 20px;
            width: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            transition: all 150ms ease-in-out;
            cursor: pointer;
            position: absolute;
            right: 1rem;
            font-size: 1rem;

        }

        .inputDiv:hover .clearIcon {
            visibility: visible;
        }

        .clearIcon:hover {
            font-size: 1.2rem;
        }

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

        .cbp_tmtimeline > li {
            position: relative
        }

        .cbp_tmtimeline > li:first-child .cbp_tmtime span.large {
            color: #444;
            font-size: 17px !important;
            font-weight: 700
        }

        .cbp_tmtimeline > li:first-child .cbp_tmicon {
            background: #fff;
            color: #666
        }

        .cbp_tmtimeline > li:nth-child(odd) .cbp_tmtime span:last-child {
            color: #444;
            font-size: 13px
        }

        .cbp_tmtimeline > li:nth-child(odd) .cbp_tmlabel {
            background: #f0f1f3
        }

        .cbp_tmtimeline > li:nth-child(odd) .cbp_tmlabel:after {
            border-right-color: #f0f1f3
        }

        .cbp_tmtimeline > li .empty span {
            color: #777
        }

        .cbp_tmtimeline > li .cbp_tmtime {
            display: block;
            width: 23%;
            padding-right: 70px;
            position: absolute
        }

        .cbp_tmtimeline > li .cbp_tmtime span {
            display: block;
            text-align: right
        }

        .cbp_tmtimeline > li .cbp_tmtime span:first-child {
            font-size: 15px;
            color: #3d4c5a;
            font-weight: 700
        }

        .cbp_tmtimeline > li .cbp_tmtime span:last-child {
            font-size: 14px;
            color: #444;
            margin-top: 10px;
        }

        .cbp_tmtimeline > li .cbp_tmlabel {
            margin: 0 0 15px 25%;
            background: #f0f1f3;
            padding: 1.2em;
            position: relative;
            border-radius: 5px
        }

        .cbp_tmtimeline > li .cbp_tmlabel:after {
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

        .cbp_tmtimeline > li .cbp_tmlabel blockquote {
            font-size: 16px
        }

        .cbp_tmtimeline > li .cbp_tmlabel .map-checkin {
            border: 5px solid rgba(235, 235, 235, 0.2);
            -moz-box-shadow: 0px 0px 0px 1px #ebebeb;
            -webkit-box-shadow: 0px 0px 0px 1px #ebebeb;
            box-shadow: 0px 0px 0px 1px #ebebeb;
            background: #fff !important
        }

        .cbp_tmtimeline > li .cbp_tmlabel h2 {
            margin: 0px;
            padding: 0 0 10px 0;
            line-height: 26px;
            font-size: 16px;
            font-weight: normal
        }

        .cbp_tmtimeline > li .cbp_tmlabel h2 a {
            font-size: 15px
        }

        .cbp_tmtimeline > li .cbp_tmlabel h2 a:hover {
            text-decoration: none
        }

        .cbp_tmtimeline > li .cbp_tmlabel h2 span {
            font-size: 15px
        }

        .cbp_tmtimeline > li .cbp_tmlabel p {
            color: #444
        }

        .cbp_tmtimeline > li .cbp_tmicon {
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
            .cbp_tmtimeline > li .cbp_tmtime {
                padding-right: 60px
            }
        }

        @media screen and (max-width: 65.375em) {
            .cbp_tmtimeline > li .cbp_tmtime span:last-child {
                font-size: 12px
            }
        }

        @media screen and (max-width: 47.2em) {
            .cbp_tmtimeline:before {
                display: none
            }

            .cbp_tmtimeline > li .cbp_tmtime {
                width: 100%;
                position: relative;
                padding: 0 0 20px 0
            }

            .cbp_tmtimeline > li .cbp_tmtime span {
                text-align: left
            }

            .cbp_tmtimeline > li .cbp_tmlabel {
                margin: 0 0 30px 0;
                padding: 1em;
                font-weight: 400;
                font-size: 95%
            }

            .cbp_tmtimeline > li .cbp_tmlabel:after {
                right: auto;
                left: 20px;
                border-right-color: transparent;
                border-bottom-color: #f5f5f6;
                top: -20px
            }

            .cbp_tmtimeline > li .cbp_tmicon {
                position: relative;
                float: right;
                left: auto;
                margin: -64px 5px 0 0px
            }

            .cbp_tmtimeline > li:nth-child(odd) .cbp_tmlabel:after {
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
            margin: none;
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
            font-feature-settings: "tnum", "tnum";
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
            font-size: 13px;
            font-weight: 700;
            color: #2f2f2f;
        }

        .css-16pld73 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: capitalize;
        }

        .ellipse {
            width: 320px;
        }

        .ellipse2 {
            width: 200px;
        }

        .ellipse:hover {
            overflow: visible;
            white-space: normal;
            width: 100%; /* just added this line */
        }

        .ellipse2:hover {
            overflow: visible;
            white-space: normal;
            width: 100%; /* just added this line */
        }

        .ant-timeline-item-tail {
            position: absolute;
            top: 10px;
            left: 4px;
            height: calc(100% - 10px);
            border-left: 2px solid #e8e8e8;
        }

        .ant-timeline-item-last > .ant-timeline-item-tail {
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
            padding: 0;
            font-size: 14px;
            list-style: none;
        }

        .ant-timeline-item-head {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50vh;
        }

        .bg-cust {
            background: #01010314;
            color: #e7515a;
        }

        .css-ccw3oz .ant-timeline-item-head {
            padding: 0px;
            border-radius: 0px !important;
        }

        .labels {
            color: #4361ee;
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
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Consignment List</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
                <a href="{{'consignments/create'}}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Consignment
                </a>
            </div>
        </div>

        <div class="widget-content widget-content-area br-6" style="padding: 20px 0">
            <div class="col-12 d-flex justify-content-end align-items-center" style="gap: 8px;">
                <div class="inputDiv d-flex justify-content-center align-items-center"
                     style="flex: 1;max-width: 300px; border-radius: 12px; position: relative">
                    <input type="text" class="form-control" placeholder="Search" id="search"
                           style="width: 100%; height: 38px; border-radius: 12px;"
                           data-action="<?php echo url()->current(); ?>">
                    <span class="reset_filter clearIcon" data-action="<?php echo url()->current(); ?>">x</span>
                </div>
            </div>
            <div class="mb-4 mt-4">
                @csrf
                <div class="main-table">
                    @include('consignments.consignment-list-ajax')
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
        function row_click(row_id, job_id, url) {
            $('.append-modal').empty();
            $('.cbp_tmtimeline').empty();

            var modal_container = '';
            var modal_html = '';
            var modal_html1 = '';

            var job_id = job_id;
            var url = url;
            jQuery.ajax({
                url: url,
                type: "get",
                cache: false,
                data: {job_id: job_id},
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

                        if (response.job_data) {
                            let timelineBlockHtml = '';

                            $.each(response.job_data, function (index, task) {
                                let type = task.type;

                                let timelineTime = task.creation_datetime.replace("T", " ");
                                timelineTime = timelineTime.replace("Z", "");
                                timelineTime = timelineTime.split('.')[0];


                                if (type == 'state_changed') {
                                    let timelineStatus = task.description.replace(" at", "");

                                    if (task.description.includes('Successful')) {
                                        timelineBlockHtml += `<div class="d-flex flex-column justify-content-center align-items-center timelineBlock" style="--status: green">`;
                                    }
                                    if (task.description.includes('Started')) {
                                        timelineBlockHtml += `<div class="d-flex flex-column justify-content-center align-items-center timelineBlock" style="--status: #00c2c9">`;
                                    }
                                    if (task.description.includes('Accepted')) {
                                        timelineBlockHtml += `<div class="d-flex flex-column justify-content-center align-items-center timelineBlock" style="--status: #0087c4">`;
                                    }
                                    if (task.description.includes('Created')) {
                                        timelineBlockHtml += `<div class="d-flex flex-column justify-content-center align-items-center timelineBlock" style="--status: #ffc000">`;
                                    }

                                    timelineBlockHtml += `<div class="d-flex align-items-center" style="width: 100%">
                                                                <span class="statusMarker"></span>
                                                                <p class="mb-0 px-3">`;
                                    if (task.description.includes('Created')) {
                                        timelineBlockHtml += `<button class="timelineStatus">Created</button>`;
                                    } else {
                                        timelineBlockHtml += `<button class="timelineStatus">${timelineStatus}</button>`;
                                    }

                                    timelineBlockHtml += `<span class="timelineTime">${timelineTime}</span></p></div>
                                                           <div class="d-flex align-items-center" style="width: 100%">`;

                                    if (task.description.includes('Created')) {
                                        timelineBlockHtml += `<p class="timeLineDescription d-flex align-items-center flex-wrap"
                                                                   style="gap: 8px">
                                                               By <strong>${timelineStatus.replace("Created By", "")}</strong>
                                                           </p>`;
                                    } else {
                                        timelineBlockHtml += `<p class="timeLineDescription d-flex align-items-center flex-wrap"
                                                                   style="gap: 8px">
                                                               By <strong>${task.fleet_name}</strong>
                                                           </p>`;
                                    }

                                    timelineBlockHtml += `</div></div>`;
                                }

                                if (type == 'image_deleted') {
                                    timelineBlockHtml += ``;
                                }

                                if (type == 'image_added') {
                                    let status = 'POD Uploaded';

                                    timelineBlockHtml += `<div
                                        class="d-flex flex-column justify-content-center align-items-center timelineBlock"
                                        style="--status: #00d496">
                                        <div class="d-flex align-items-center" style="width: 100%">
                                            <span class="statusMarker"></span>
                                            <p class="mb-0 px-3">
                                                <button class="timelineStatus" style="width: auto">
                                                    ${status}
                                                </button>
                                                <span class="timelineTime">${task.creation_datetime}</span>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center" style="width: 100%">
                                            <p class="timeLineDescription d-flex align-items-center flex-wrap"
                                               style="gap: 8px">
                                                Uploaded by
                                                <strong>${task.fleet_name}</strong>
                                                <a href="${task.description}" target="_blank">View Attachment</a>
                                            </p>
                                        </div>
                                    </div>`;
                                }

                                if (type == 'signature_image_added') {
                                    let status = 'Signature Added';

                                    timelineBlockHtml += `<div
                                        class="d-flex flex-column justify-content-center align-items-center timelineBlock"
                                        style="--status: #00d748">
                                        <div class="d-flex align-items-center" style="width: 100%">
                                            <span class="statusMarker"></span>
                                            <p class="mb-0 px-3">
                                                <button class="timelineStatus" style="width: auto">
                                                    ${status}
                                                </button>
                                                <span class="timelineTime">${task.creation_datetime}</span>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center" style="width: 100%">
                                            <p class="timeLineDescription d-flex align-items-center flex-wrap"
                                               style="gap: 8px">
                                                By
                                                <strong>${task.fleet_name}</strong>
                                                <a href="${task.description}" target="_blank">View Signature</a>
                                            </p>
                                        </div>
                                    </div>`;
                                }
                            });

                            $('.append-modal').append(timelineBlockHtml);

                        } else {
                            var no_data_view = `<p style="padding: 5rem 1rem; text-align: center; font-weight: 600; font-size: 1.2rem">No data available</p>`;
                            $('.append-modal').html(no_data_view);
                        }


                        if ((response.job_id != '') && (response.delivery_status != 'Successful')) {
                            var trackinglink = '<iframe id="iGmap-' + row_id + '" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' + response.tracking_link + '" ></iframe>';
                            $("#mapdiv-" + row_id).html(trackinglink);
                        } else {
                            var trackinglink = '<div id="map-' + row_id + '" style="height: 100%; width: 100%"> </div>';
                            $("#mapdiv-" + row_id).html(trackinglink);
                            initMap(response, row_id);
                        }

                    }
                }

            });

        }


        var map;

        function initMap(response, row_id) {
            var map = new google.maps.Map(document.getElementById('map-' + row_id), {zoom: 8, center: 'Changigarh',});
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
