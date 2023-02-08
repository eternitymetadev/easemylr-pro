@extends('layouts.main')
@section('page-heading')Track LR @endsection
@section('content')
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <style>

        #searchInputBox input, #driverSearchInputBox input {
            width: 0;
            background: none;
            border: none;
            transition: all 200ms ease-in-out;
        }

        #searchInputBox.focused input, #driverSearchInputBox.focused input {
            border-bottom: 1px solid;
            width: 220px;
            transition: all 200ms ease-in-out;
        }


        .taskContainer {
            margin-top: 6px;
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .driverInfo img {
            height: 40px;
            width: 40px;
            background: aqua;
            border-radius: 8px;
            border: none;
        }

        .driverInfo span {
            font-size: 11px;
        }

        .taskSelection option {
            background: #000;
        }


        .taskDetailContainer {
            margin: 1rem;
            border-radius: 12px;
            padding: 1rem;
        }

        .taskDetailContainer p {
            display: flex;
            gap: 6px;
            justify-content: space-between;
        }

        .historyTimeLineDetail {
            margin-left: 9px;
            padding: 0 0.5rem 1rem 1.56rem;
            font-size: 12px;
            line-height: 22px;
            border-left: 2px dashed;
            margin-block: 4px;
            color: #232323
        }

        .historyTimeline .marker {
            width: 20px;
            height: 20px;
            border-radius: 20px;
            background: var(--statusColor);
        }

        .historyTimeline:first-child .marker {
            background: #db144c
        }

        .historyTimeline:first-child .status {
            color: #db144c
        }

        .historyTimeline:last-child .marker {
            background: #28a745
        }

        .historyTimeline:last-child .status {
            color: #28a745
        }

        .historyTimeline:last-child .historyTimeLineDetail {
            border-left: none;
        }

        .historyTimeline .status {
            border: 1px solid;
            border-radius: 4px;
            padding: 1px 7px;
            font-size: 12px;
            font-weight: 600;
            color: var(--statusColor);
        }

        .historyTimeLineContainer {
            cursor: default;
        }

        .historyTimeLineContainer:hover > :not(:hover) {
            opacity: 0.9;
        }

        .pointer {
            cursor: pointer;
        }

        .filterBar input, .filterBar select {
            border-radius: 8px;
            background: none;
            width: 100%;
            max-width: 180px;
            border: 1px solid;
            height: 30px;
            padding-inline: 8px;
        }

        .filterBar select option {
            background: #000;
        }


        .topSearchInput {
            width: clamp(240px, 90vw, 450px);
            margin: auto;
            border-radius: 50vh;
            height: 50px;
            padding: 8px 16px;
            border: 2px solid #8d8d8d;
            font-size: 1.1rem;
        }

        .topSearchInput:focus {
            border: 2px solid #f9b600;
        }

        .topSearchInputButton {
            border: none;
            height: 44px;
            width: 60px;
            background: #f9b600;
            border-radius: 50vh;
            position: absolute;
            right: 3px;
        }

        .lrInfo p {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            font-size: 1rem;
            font-weight: 600;
            padding-left: 1rem;
        }

        .lrInfo p span {
            font-size: 0.92rem;
            font-weight: 500;
            min-width: 100px;
        }

    </style>

    <div class="layout-px-spacing" style="padding: 0 !important;">


        <div class="widget-content widget-content-area br-6 p-0">

            <div class="d-flex flex-column driverMapContainer"
                 style="min-height: min(90vh, 600px); gap: 1rem ">

                <div class="d-flex align-items-center justify-content-center" style="width: 100%">
                    <form class="d-flex align-items-center justify-content-center" style="position:relative;">
                        <input class="topSearchInput" placeholder="Search LR Number"/>
                        <button class="topSearchInputButton">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </form>
                </div>

                <div id="lrTimeLineContainer" class="d-flex flex-wrap justify-content-center align-items-center"
                     style="flex: 1; padding: 1rem; border-radius: 20px; border: 2px solid; margin-inline: 2rem">

{{--                    <img id="imageIllustration" alt="logo" src="{{asset('assets/img/saerch-illustration.png')}}"--}}
{{--                         style="max-height: 200px;">--}}

                    <div class="lrInfo" style="flex:1; align-self: flex-start; padding: 1rem; background: #ffffff">
                        <h4 style="font-weight: 600; margin-bottom: 1.5rem">LR Info</h4>
                        <p><span>LR Number</span>: 21345643dsdfx</p>
                        <p><span>Consigner</span>: 21345643dsdfx</p>
                        <p><span>Consignee</span>: 21345643dsdfx</p>
                    </div>

                    <div style="flex:1; padding: 1rem; align-self: stretch; background: #e5e5e5; border-radius: 10px">
                        <div class="historyTimeLineContainer taskContainer taskDetailContainer"
                             style="">

                            @for($i = 0; $i < 5; $i++)
                                <div class="historyTimeline">
                                    <div class="d-flex align-items-center flex-wrap"
                                         style="gap: 1rem; --statusColor: #1e90ff;">
                                        <span class="marker"></span>
                                        <span class="status">In Transit</span><br/>

                                    </div>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <div class="historyTimeLineDetail">
                                            <span style="font-size: 14px; font-weight: 700">
                                                12 Dec 2022, 10:30pm
                                            </span><br/>
                                            Pickup created for Shipment at <strong>Hisar</strong>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>

    </script>

@endsection
