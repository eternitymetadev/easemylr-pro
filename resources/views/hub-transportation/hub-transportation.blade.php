@extends('layouts.main')
@section('content')

    <style>
        .hrsBlock {
            column-gap: 2rem;
            row-gap: 0.3rem;
            border-radius: 12px;
            overflow: hidden;
            background: #e8e8e8;
            margin-bottom: 10px;
        }

        .hrsNumber {
            width: min(20%, 200px);
            font-size: 1rem;
            color: #000;
            text-align: center;
            display: flex;
            flex-flow: column;
            align-items: center;
            padding-left: 1.5rem;
        }

        @media (max-width: 800px) {
            .hrsNumber {
                width: 100%;
                justify-content: center;
                padding-top: 1rem;
            }
        }

        .hrsTimelineBlock {
            min-width: 600px;
            flex: 1;
            gap: 10px;
            padding: 3rem 0.5rem 0.5rem;
            background: #fff;
            border-radius: 12px;
            margin: 5px;
        }

        .hrsTimelineSpotContainer {
            width: min(90%, 900px);
            justify-content: space-between;
        }

        .hrsTimelineSpotContainer .nav-link {
            color: #fff;
            background-color: #2b2b2b;
            padding: 2px 16px;
            width: 150px;
            text-align: center;
            text-overflow: ellipsis;
            border-radius: 8px;
            white-space: nowrap;
        }

        .hrsTimelineSpotContainer .nav-link.active {
            background-color: #2b2b2b;
        }

        .hrsTimelineSpotContainer .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #fff !important;
        }

        .hrsTimelineSpotContainer .nav-link svg {
            height: 18px;
            width: 18px;
        }

        .hrsTimelineSpotContainer .nav-link.currentPosition {
            background-color: #b88000;
        }

        .hrsTimelineSpotContainer .nav-link.currentPosition.active:after {
            border-color: #b88000;
        }

        .hrsTimelineSpotContainer .nav-link.donePosition {
            background-color: #319702;
        }

        .hrsTimelineSpotContainer .nav-link.donePosition.active:after {
            border-color: #319702;
        }

        .nav-link svg {
            display: none !important;
        }

        .nav-link.donePosition svg {
            display: block !important;
        }

        .hrsTimelineSpotContainer .nav-link.currentPosition:before {
            position: absolute;
            content: "";
            display: block;
            height: 2rem;
            width: 5rem;
            left: 50%;
            transform: translateX(-50%);
            top: -2.2rem;
            background-image: url(http://cdn.onlinewebfonts.com/svg/img_10551.png);
            background-size: 55%;
            background-repeat: no-repeat;
            background-position: center;
        }

        .hrsTimelineSpotContainer .nav-link.active:after {
            position: absolute;
            content: "";
            display: block;
            height: 1rem;
            width: 1rem;
            left: 50%;
            border-color: #2b2b2b;
            transform: translateX(-50%) rotate(45deg);
            bottom: -8px;
            border-style: solid;
            border-width: 0 10px 10px 0;
            border-radius: 50vh 0 0 0;
        }

        .hrsTimelineSpotDetail {
            width: 90%;
            box-shadow: inset 0 0 12px -3px #83838380;
            padding: 0.7rem;
            border-radius: 8px;
        }

        .nav-item {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;

        }

        .nav-item:last-of-type {
            flex: inherit;
        }

        .nav-item .divider {
            height: 3px;
            background: #c6c6c6;
            flex: 1;
        }

        .nav-item:last-of-type .divider {
            display: none;
        }

        .hrsTimelineSpotContainer .nav-item:after {
            /*content: "";*/
            position: absolute;
            background: #6a6a6a;
            height: 4px;
            width: 100%;
            display: inline-flex;
            top: 50%;
            left: calc(100% + 1rem);
            transform: translateY(-50%);
        }

        .hrsTimelineSpotContainer .nav-item:last-of-type:after {
            display: none;
        }


        .detailsTab {
            gap: 1rem;
            min-height: 58px;
        }

        .detailsTab p {
            text-align: center;
            flex: 1;
            min-width: min-content;
            margin-bottom: 0;
            font-weight: 700;
        }

        .detailsTab p span {
            font-weight: 500;
        }

        .actionButton {
            text-decoration: none;
            box-shadow: none;
            border: 1px solid;
            padding: 3px 8px;
            line-height: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #f9b60063;
            border-radius: 8px;
        }

    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Hub Transportation</h2>
        </div>

        <div class="widget-content widget-content-area br-6" style="padding: 20px 0">

            @for($i = 0; $i < 6; $i++)
                <div class="hrsBlock d-flex justify-content-between align-items-center flex-wrap">
                    <div class="hrsNumber">HRS: <br/><strong style="font-size: 1.2rem">1234532</strong></div>
                    <div class="hrsTimelineBlock d-flex justify-content-center align-items-center flex-column"
                         style="flex: 1">
                        <ul class="hrsTimelineSpotContainer nav nav-pills" id="taskTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link donePosition" id="taskDetailTab" data-toggle="pill"
                                   href="#detailsTab" role="tab" aria-controls="detailsTab"
                                   aria-selected="true">
                                    Details
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-check-circle">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </a>
                                <div class="divider"></div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active currentPosition" id="customerDetailTab" data-toggle="pill"
                                   href="#customerInfoTab" role="tab" aria-controls="customerInfoTab"
                                   aria-selected="true">
                                    Customer
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-check-circle">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </a>
                                <div class="divider"></div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="customerHistoryTab" data-toggle="pill"
                                   href="#historyTab" role="tab" aria-controls="historyTab"
                                   aria-selected="false">
                                    History
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-check-circle">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </a>
                                <div class="divider"></div>
                            </li>
                        </ul>


                        <div class="hrsTimelineSpotDetail">
                            <div class="tab-content" id="taskTabContent">
                                <div class="tab-pane fade" id="detailsTab" role="tabpanel"
                                     aria-labelledby="taskDetailTab">
                                    <div class="detailsTab d-flex justify-content-between align-items-center flex-wrap"
                                         style="gap: 1rem">
                                        <p><span class="detailTitle">Trip No.: </span>23213221</p>
                                        <p><span class="detailTitle">Total Boxes: </span>564</p>
                                        <p><span class="detailTitle">Total Weight: </span>1024 Kg</p>

                                        <div class="col-12 text-right">
                                            <a href="#" class="actionButton">Action Buttton</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade show active" id="customerInfoTab" role="tabpanel"
                                     aria-labelledby="customerDetailTab">
                                    <div class="detailsTab d-flex justify-content-between align-items-center flex-wrap"
                                         style="gap: 1rem">
                                        <p><span class="detailTitle">Trip No.: </span>23213221</p>
                                        <p><span class="detailTitle">Total Boxes: </span>564</p>
                                        <p><span class="detailTitle">Total Weight: </span>1024 Kg</p>

                                        <div class="col-12 text-right">
                                            <a href="#" class="actionButton">Action Buttton</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="historyTab" role="tabpanel"
                                     aria-labelledby="customerHistoryTab">
                                    <div class="detailsTab d-flex justify-content-between align-items-center flex-wrap"
                                         style="gap: 1rem">
                                        <p><span class="detailTitle">Trip No.: </span>23213221</p>
                                        <p><span class="detailTitle">Total Boxes: </span>564</p>
                                        <p><span class="detailTitle">Total Weight: </span>1024 Kg</p>

                                        <div class="col-12 text-right">
                                            <a href="#" class="actionButton">Action Buttton</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endfor

        </div>


    </div>

@endsection

@section('js')

@endsection
