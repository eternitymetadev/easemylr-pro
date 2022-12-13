@extends('layouts.main')
@section('content')

    <style>
        .driversTaskBlock.shrinked, .driversListBlock.shrinked {
            padding: 1rem 0;
            width: 1px;
            transition: all 400ms ease-in-out;
        }

        .driversTaskBlock, .driversListBlock {
            padding: 1rem 0;
            width: 100%;
            max-width: 400px;
            background: #2f2f2f;
            min-height: min(83vh, 500px);
            max-height: 83vh;
            overflow-y: hidden;
            transition: all 400ms ease-in-out;

        }

        .driversTaskBlock {
            border-radius: 0 0 12px 0;
        }

        .driversTaskBlock *, .driversListBlock * {
            color: #fff;
        }

        .driversListBlock {
            border-radius: 0 0 0 12px;
        }


        .driverMapContainer .nav-item {
            flex: 1;
        }

        .driverMapContainer .nav-link {
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50vh;
            padding: 4px 10px;
            transition: all 250ms ease-in-out;
        }

        .driverMapContainer .nav-link:not(.active):hover {
            font-weight: 600;
        }

        .driverMapContainer .nav-link.active {
            background-color: #f9b600;
            transition: all 250ms ease-in-out;
        }

        .tabContainer {
            margin-inline: 10px;
            border-radius: 41px;
            padding: 4px;
            box-shadow: inset 0 0 14px -1px #f9b6007d;
            background: #fff;
        }

        a.nav-link.active:hover {
            color: whitesmoke !important;
            background-color: #f1ac00;
        }

        #searchInputBox, #driverSearchInputBox {
            width: 30px;
            height: 30px;
            transition: all 200ms ease-in-out;
        }

        .searchIcon, .closeIcon {
            width: 0;
            height: 0;
            cursor: pointer;
            transition: all 200ms ease-in-out;
        }

        #searchInputBox.focused .closeIcon, #driverSearchInputBox.focused .closeIcon {
            width: 18px;
            height: 18px;
            transition: all 200ms ease-in-out;
        }

        #searchInputBox:not(.focused) .searchIcon, #driverSearchInputBox:not(.focused) .searchIcon {
            width: 18px;
            height: 18px;
            transition: all 200ms ease-in-out;
        }

        #searchInputBox input, #driverSearchInputBox input {
            width: 0;
            background: none;
            border: none;
            transition: all 200ms ease-in-out;
        }

        #searchInputBox.focused, #driverSearchInputBox.focused {
            padding: 1px 4px 1px 10px;
            width: 225px;
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

        .taskBlock {
            background: #f2f2f201;
            border-radius: 0px;
            padding: 8px 8px 8px 4px;
            border-bottom: 1px solid #787878;
            cursor: pointer;
        }

        .taskBlock:hover {
            background: #00000059;
            border-radius: 12px;
        }

        .driverInfo {
            width: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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

        .driverInfo span.taskDone {
            font-size: 10px;
        }

        .taskSelection {
            background: none;
            border-radius: 8px;
            font-size: 11px
        }

        .taskSelection option {
            background: #000;
        }

        .taskInfo {
            background: #f2f2f220;
            border-radius: 9px;
            padding: 2px 10px;
        }

        .taskStatus {
            background: green;
            padding: 1px 6px;
            font-size: 12px;
            line-height: 14px;
            border-radius: 4px;
        }

        .taskTime {
            font-size: 17px;
            font-weight: 600;
        }

        .delTimeStatus {
            font-size: 10px;
            border: 1px solid;
            border-radius: 4px;
            padding: 0 4px;
            line-height: 10px;
            margin-inline: auto;
            text-align: center;
            text-transform: uppercase;
        }


        .taskDetailContainer {
            background: #ffffff20;
            margin: 1rem;
            border-radius: 12px;
            padding: 1rem;
        }

        .taskDetailContainer p {
            display: flex;
            gap: 6px;
            justify-content: space-between;
        }

        .taskDetailContainer p span.dHeading {
            width: 69px;
            min-width: 85px;
            font-size: 12px;
        }

        .taskDetailContainer p span.dDescription {
            flex: 1;
            word-break: break-all;
        }

        .pendingTaskCount {
            height: 40px;
            min-width: 40px;
            background: #ffffff20;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            padding-inline: 4px;
        }

        .viewDetailLink {
            margin-left: 10px;
            background: #ffffff24;
            height: 16px;
            width: 16px;
            text-align: center;
            border-radius: 20px;
            font-size: 12px;
            line-height: 12px;
            display: grid;
            place-items: center;
        }


        .historyTimeLineDetail {
            margin-left: 9px;
            padding: 0 0.5rem 1rem 1.56rem;
            font-size: 12px;
            line-height: 22px;
            border-left: 2px dashed;
            margin-block: 4px;
        }

        .historyTimeline .marker {
            width: 20px;
            height: 20px;
            border-radius: 20px;
            background: #f9b808
        }

        .historyTimeline:last-child .marker {
            background: #28a745
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
        }

        .historyTimeLineContainer {
            cursor: default;
        }

        .historyTimeLineContainer:hover > :not(:hover) {
            opacity: 0.8;
        }

        .pointer {
            cursor: pointer;
        }

        /* filter bar css */
        .main-container {
            margin-top: 18px;
        }

        .filterBar {
            width: 100%;
            padding: 1rem;
            background: #2f2f2f;
        }

        .filterBar * {
            color: #fff;
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

        .hiddenTitleShow {
            transform: translateX(0) !important;
            transition: all 300ms ease-in-out;
        }

        .hiddenTitleLeft {
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 300ms ease-in-out;
            transform: translateX(-200px);
        }

        .hiddenTitleRight {
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 300ms ease-in-out;
            transform: translateX(200px);
        }
    </style>

    <div class="layout-px-spacing" style="padding: 0 !important;">


        <div class="widget-content widget-content-area br-6 p-0">
            <div class="d-flex flex-wrap align-content-start driverMapContainer"
                 style="min-height: min(90vh, 600px); background: #2f2f2f">

                <div class="filterBar d-flex flex-wrap justify-content-between align-items-center">
                    <span class="hiddenTitle hiddenTitleLeft" onclick="toggleTaskView()">
                        Tasks
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-right">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    </span>
                    <div class="d-flex justify-content-center align-items-center" style="gap: 8px;flex: 1;">
                        <input type="date"/>
                        <select>
                            <option>Location 1</option>
                            <option>Location 2</option>
                            <option>Location 3</option>
                        </select>
                        <input placeholder="search..."/>
                    </div>

                    <span class="hiddenTitle hiddenTitleRight" onclick="toggleDriverView()">
                        Drivers
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather pointer feather-chevron-left">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </span>

                </div>

                <div class="driversTaskBlock">

                    <div id="tasks">
                        <div class="d-flex align-items-center justify-content-between"
                             style="gap: 1rem; margin: 0 24px 8px;">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather pointer feather-chevron-left"
                                     onclick="toggleTaskView()">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                                Task
                            </h4>
                            <div id="searchInputBox" class="d-flex align-items-center justify-content-between">
                                <input id="searchInput" placeholder="search..."/>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-search searchIcon">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-search closeIcon">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                        </div>

                        <ul class="tabContainer nav nav-pills" id="taskTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="acknowledgedTaskTab" data-toggle="pill"
                                   href="#acknowledgedTab" role="tab" aria-controls="acknowledgedTab"
                                   aria-selected="true">
                                    Acknowledged
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="startedTaskTab" data-toggle="pill"
                                   href="#startedTab" role="tab" aria-controls="startedTab" aria-selected="true">
                                    Started
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="completedTaskTab" data-toggle="pill"
                                   href="#completedTab" role="tab" aria-controls="completedTab"
                                   aria-selected="false">
                                    Completed
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="taskTabContent">
                            <div class="tab-pane fade" id="acknowledgedTab" role="tabpanel"
                                 aria-labelledby="acknowledgedTaskTab">
                                <div class="taskContainer">
                                    @for($demoSr = 0; $demoSr < 7; $demoSr++)
                                        <div class="taskBlock d-flex align-items-center justify-content-between">
                                            <div class="driverInfo">
                                                <img
                                                    src="https://media.istockphoto.com/id/623278438/photo/like-father-like-son.jpg?b=1&s=170667a&w=0&k=20&c=LFXnrYbOREo-68MyKCjNHOS9PgSSDqwA9HBAbSDDs48="/>
                                                <span>Driver Name</span>
                                                <span class="taskDone">Completed: 0/3</span>
                                            </div>

                                            <div style="flex:1; overflow: hidden; align-self: flex-start;">
                                                <div class="d-flex justify-content-between align-items-center"
                                                     style="margin-bottom: 4px;">
                                                    <select class="taskSelection">
                                                        <option value="458643221">Id: #458643221</option>
                                                        <option value="458643222">Id: #458643222</option>
                                                        <option value="458643223">Id: #458643223</option>
                                                        <option value="458643224">Id: #458643224</option>
                                                    </select>

                                                    <span style="font-size: 12px">
                                                    @if($demoSr % 2 == 0)Pickup @else Delivery @endif
                                                </span>

                                                    <div class="taskStatus">Assigned</div>
                                                </div>

                                                <div
                                                    class="taskInfo d-flex flex-wrap justify-content-between align-items-center">
                                                    <p class="mb-0 textWrap" style="max-width: 150px">
                                                    <span class="consignerName swan-tooltip-right"
                                                          style="font-size: 13px"
                                                          title="Consigner Name">
                                                        Consigner NameConsigner NameConsigner Name
                                                    </span><br/>
                                                        <span class="consignerAddress" style="font-size: 11px">City Name, State</span>
                                                    </p>
                                                    <div class="taskTime">
                                                        <span class="estimateTaskTime">12:30 pm</span>

                                                        @if($demoSr % 2 == 0)
                                                            <div
                                                                class="delTimeStatus d-flex align-items-center justify-content-center"
                                                                style="gap: 4px; @if($demoSr % 4 == 0)background: #efc4c4; @else background: #caffb8; @endif">

                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                     height="12 " viewBox="0 0 24 24" fill="none"
                                                                     stroke="@if($demoSr % 4 == 0)red @else green @endif"
                                                                     stroke-width="2"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-clock">
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                                </svg>
                                                                <span class="timeStatus"
                                                                      style="@if($demoSr % 4 == 0)color: red; @else color: green; @endif">
                                                                @if($demoSr % 4 == 0)Delayed @else ON TIME @endif
                                                            </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div style="width: 100%;text-align: right; font-size: 10px;">
                                                    <a onclick="showTaskInfo()">Details >></a>
                                                </div>
                                            </div>

                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="tab-pane fade show active" id="startedTab" role="tabpanel"
                                 aria-labelledby="startedTaskTab">
                                <div class="taskContainer">
                                    @for($demoSr = 0; $demoSr < 7; $demoSr++)
                                        <div class="taskBlock d-flex align-items-center justify-content-between">
                                            <div class="driverInfo">
                                                <img
                                                    src="https://media.istockphoto.com/id/623278438/photo/like-father-like-son.jpg?b=1&s=170667a&w=0&k=20&c=LFXnrYbOREo-68MyKCjNHOS9PgSSDqwA9HBAbSDDs48="/>
                                                <span>Driver Name</span>
                                                <span class="vehicleNo">HR20AJ7830</span>
                                            </div>

                                            <div style="flex:1; overflow: hidden; align-self: flex-start;">
                                                <div class="d-flex justify-content-between align-items-center"
                                                     style="margin-bottom: 4px;">
                                                    <select class="taskSelection">
                                                        <option value="458643221">Id: #458643221</option>
                                                        <option value="458643222">Id: #458643222</option>
                                                        <option value="458643223">Id: #458643223</option>
                                                        <option value="458643224">Id: #458643224</option>
                                                    </select>

                                                    <span style="font-size: 12px">
                                                    @if($demoSr % 2 == 0)Pickup @else Delivery @endif
                                                </span>

                                                    <div class="taskStatus">Assigned</div>
                                                </div>

                                                <div
                                                    class="taskInfo d-flex flex-wrap justify-content-between align-items-center">
                                                    <p class="mb-0 textWrap" style="max-width: 150px">
                                                    <span class="consignerName swan-tooltip-right"
                                                          style="font-size: 13px"
                                                          title="Consigner Name">
                                                        Consigner NameConsigner NameConsigner Name
                                                    </span><br/>
                                                        <span class="consignerAddress" style="font-size: 11px">City Name, State</span>
                                                    </p>
                                                    <div class="taskTime">
                                                        <span class="estimateTaskTime">12:30 pm</span>

                                                        @if($demoSr % 2 == 0)
                                                            <div
                                                                class="delTimeStatus d-flex align-items-center justify-content-center"
                                                                style="gap: 4px; @if($demoSr % 4 == 0)background: #efc4c4; @else background: #caffb8; @endif">

                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                     height="12 " viewBox="0 0 24 24" fill="none"
                                                                     stroke="@if($demoSr % 4 == 0)red @else green @endif"
                                                                     stroke-width="2"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-clock">
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                                </svg>
                                                                <span class="timeStatus"
                                                                      style="@if($demoSr % 4 == 0)color: red; @else color: green; @endif">
                                                                @if($demoSr % 4 == 0)Delayed @else ON TIME @endif
                                                            </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center"
                                                     style="width: 100%; font-size: 10px;">
                                                    <span class="taskDone">Completed: 0/3</span>
                                                    <a onclick="showTaskInfo()">Details >></a>
                                                </div>
                                            </div>

                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="tab-pane fade" id="completedTab" role="tabpanel"
                                 aria-labelledby="completedTaskTab">
                                <div class="taskContainer">
                                    @for($demoSr = 0; $demoSr < 7; $demoSr++)
                                        <div class="taskBlock d-flex align-items-center justify-content-between">
                                            <div class="driverInfo">
                                                <img
                                                    src="https://media.istockphoto.com/id/623278438/photo/like-father-like-son.jpg?b=1&s=170667a&w=0&k=20&c=LFXnrYbOREo-68MyKCjNHOS9PgSSDqwA9HBAbSDDs48="/>
                                                <span>Driver Name</span>
                                                <span class="taskDone">Completed: 0/3</span>
                                            </div>

                                            <div style="flex:1; overflow: hidden; align-self: flex-start;">
                                                <div class="d-flex justify-content-between align-items-center"
                                                     style="margin-bottom: 4px;">
                                                    <select class="taskSelection">
                                                        <option value="458643221">Id: #458643221</option>
                                                        <option value="458643222">Id: #458643222</option>
                                                        <option value="458643223">Id: #458643223</option>
                                                        <option value="458643224">Id: #458643224</option>
                                                    </select>

                                                    <span style="font-size: 12px">
                                                    @if($demoSr % 2 == 0)Pickup @else Delivery @endif
                                                </span>

                                                    <div class="taskStatus">Assigned</div>
                                                </div>

                                                <div
                                                    class="taskInfo d-flex flex-wrap justify-content-between align-items-center">
                                                    <p class="mb-0 textWrap" style="max-width: 150px">
                                                    <span class="consignerName swan-tooltip-right"
                                                          style="font-size: 13px"
                                                          title="Consigner Name">
                                                        Consigner NameConsigner NameConsigner Name
                                                    </span><br/>
                                                        <span class="consignerAddress" style="font-size: 11px">City Name, State</span>
                                                    </p>
                                                    <div class="taskTime">
                                                        <span class="estimateTaskTime">12:30 pm</span>

                                                        @if($demoSr % 2 == 0)
                                                            <div
                                                                class="delTimeStatus d-flex align-items-center justify-content-center"
                                                                style="gap: 4px; @if($demoSr % 4 == 0)background: #efc4c4; @else background: #caffb8; @endif">

                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                     height="12 " viewBox="0 0 24 24" fill="none"
                                                                     stroke="@if($demoSr % 4 == 0)red @else green @endif"
                                                                     stroke-width="2"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-clock">
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                                </svg>
                                                                <span class="timeStatus"
                                                                      style="@if($demoSr % 4 == 0)color: red; @else color: green; @endif">
                                                                @if($demoSr % 4 == 0)Delayed @else ON TIME @endif
                                                            </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div style="width: 100%;text-align: right; font-size: 10px;">
                                                    <a onclick="showTaskInfo()">Details >></a>
                                                </div>
                                            </div>

                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="taskDetails" style="display: none">
                        <div class="d-flex align-items-center justify-content-between"
                             style="gap: 1rem; margin: 0 24px 8px;">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather pointer feather-chevron-left"
                                     onclick="toggleTaskView()">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                                Task <span> &nbsp; #78763638</span></h4>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-search" onclick="showTaskInfo()">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </div>

                        <ul class="tabContainer nav nav-pills" id="taskTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="taskDetailTab" data-toggle="pill"
                                   href="#detailsTab" role="tab" aria-controls="detailsTab"
                                   aria-selected="true">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="customerDetailTab" data-toggle="pill"
                                   href="#customerInfoTab" role="tab" aria-controls="customerInfoTab"
                                   aria-selected="true">
                                    Customer
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="customerHistoryTab" data-toggle="pill"
                                   href="#historyTab" role="tab" aria-controls="historyTab"
                                   aria-selected="false">
                                    History
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="taskTabContent">
                            <div class="tab-pane fade" id="detailsTab" role="tabpanel"
                                 aria-labelledby="taskDetailTab">
                                <div class="taskContainer taskDetailContainer">
                                    <p><span class="dHeading">Status:</span><span class="dDescription">Assigned</span>
                                    </p>
                                    <p><span class="dHeading">Delayed By:</span><span
                                            class="dDescription">2 hrs 43 mins</span></p>
                                    <p><span class="dHeading">Task Description:</span><span
                                            class="dDescription">-</span></p>
                                    <p><span class="dHeading">Start Before:</span><span class="dDescription">12 DEC 2022 12:30 PM</span>
                                    </p>
                                    <p><span class="dHeading">Complete Before:</span><span class="dDescription">12 DEC 2022 12:30 PM</span>
                                    </p>
                                    <p><span class="dHeading">Tracking link:</span><span class="dDescription"><a>Click here to track</a></span>
                                    </p>
                                    <p><span class="dHeading">Team</span><span class="dDescription">Chandigarh</span>
                                    </p>
                                    <p><span class="dHeading">Agent</span><span class="dDescription">Test Agent</span>
                                    </p>
                                    <p><span class="dHeading">Order ID:</span><span class="dDescription">473284</span>
                                    </p>
                                </div>
                            </div>

                            <div class="tab-pane fade show active" id="customerInfoTab" role="tabpanel"
                                 aria-labelledby="customerDetailTab">
                                <div class="taskContainer taskDetailContainer">
                                    <h5>Customer Details</h5>
                                    <p><span class="dHeading">Name:</span><span
                                            class="dDescription">Customer Name</span></p>
                                    <p><span class="dHeading">Email:</span><span class="dDescription">emailaddress@company.domain</span>
                                    </p>
                                    <p><span class="dHeading">Mobile:</span><span
                                            class="dDescription">+91-9898989898</span></p>
                                    <p><span class="dHeading">Address:</span><span class="dDescription">City Name, State -122121</span>
                                    </p>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="historyTab" role="tabpanel"
                                 aria-labelledby="customerHistoryTab">
                                <div class="d-flex align-items-center m-3"
                                     style="gap: 1rem; background: #ffffff20; border-radius: 18px; padding: 0.5rem ">
                                    <div
                                        style="background: #0ba360; height: 45px; width: 45px; border-radius: 14px;"></div>
                                    <div style="flex: 1; font-size: 1.2rem; font-weight: 600">
                                        Driver Name
                                    </div>
                                </div>

                                <div class="historyTimeLineContainer taskContainer taskDetailContainer"
                                     style="max-height: 300px">

                                    @for($i = 0; $i < 2; $i++)
                                        <div class="historyTimeline">
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 1rem">
                                                <span class="marker"></span>
                                                <span class="status">Updated</span><br/>

                                            </div>
                                            <div class="d-flex align-items-center flex-wrap">
                                                <div class="historyTimeLineDetail">
                                                    <span
                                                        style="font-size: 14px; font-weight: 700">12 Dec 2022, 10:30pm</span><br/>
                                                    Modified by Demo Manager-994950
                                                </div>
                                            </div>
                                        </div>
                                    @endfor

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex" style="flex: 1; background: #fff; border-radius: 12px 12px 0 0; overflow: hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9933.03090712429!2d76.74728258404285!3d30.68056108090217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fec06a86ddf67%3A0x74b5e9c7ee9369ba!2sBestech%20Business%20Tower!5e1!3m2!1sen!2sin!4v1670919542995!5m2!1sen!2sin"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div class="driversListBlock">

                    <div id="drivers">
                        <div class="d-flex align-items-center justify-content-between"
                             style="gap: 1rem; margin: 0 24px 8px;">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather pointer feather-chevron-left"
                                     onclick="toggleDriverView()">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                                Drivers
                            </h4>
                            <div id="driverSearchInputBox" class="d-flex align-items-center justify-content-between">
                                <input id="driverSearchInput" placeholder="search..."/>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-search searchIcon">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-search closeIcon">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                        </div>

                        <ul class="tabContainer nav nav-pills" id="driverList" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="freeDriversTab" data-toggle="pill"
                                   href="#freeTab" role="tab" aria-controls="freeTab" aria-selected="true">
                                    Free
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="busyDriversTab" data-toggle="pill"
                                   href="#busyTab" role="tab" aria-controls="busyTab" aria-selected="true">
                                    Busy
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inactiveDriversTab" data-toggle="pill"
                                   href="#inactiveTab" role="tab" aria-controls="inactiveTab"
                                   aria-selected="false">
                                    Inactive
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="driverListContent">
                            <div class="tab-pane fade" id="freeTab" role="tabpanel"
                                 aria-labelledby="freeDriversTab">

                            </div>
                            <div class="tab-pane fade show active" id="busyTab" role="tabpanel"
                                 aria-labelledby="busyDriversTab">
                                <div class="taskContainer">
                                    @for($demoSr = 0; $demoSr < 7; $demoSr++)
                                        <div class="taskBlock d-flex align-items-center justify-content-between">
                                            <div class="driverInfo flex-row">
                                                <div
                                                    style="width: 8px; height: 18px; border-radius: 20px; margin-right: 10px; background: green"></div>
                                                <img
                                                    src="https://media.istockphoto.com/id/623278438/photo/like-father-like-son.jpg?b=1&s=170667a&w=0&k=20&c=LFXnrYbOREo-68MyKCjNHOS9PgSSDqwA9HBAbSDDs48="/>
                                            </div>

                                            <div class="d-flex align-items-center"
                                                 style="flex:1; overflow: hidden; align-self: flex-start;">
                                                <p class="mb-0 textWrap" style="flex: 1; max-width: 150px">
                                                    <span class="consignerName swan-tooltip-right"
                                                          style="font-size: 13px"
                                                          title="Consigner Name">
                                                        Driver NameDriver NameDriver NameDriver Name
                                                    </span><br/>
                                                    <span class="consignerAddress"
                                                          style="font-size: 11px">+91-7394387928</span>
                                                </p>
                                                <div
                                                    class="pendingTaskCount d-flex justify-content-center align-items-center flex-column">
                                                    03
                                                    <span style="font-size: 10px">Pending Tasks</span>
                                                </div>
                                                <a class="viewDetailLink" onclick="showDriverInfo()">></a>
                                            </div>

                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="tab-pane fade" id="inactiveTab" role="tabpanel"
                                 aria-labelledby="inactiveDriversTab">

                            </div>
                        </div>
                    </div>

                    <div id="driverDetail" style="display: none">
                        <div class="d-flex align-items-center justify-content-between"
                             style="gap: 1rem; margin: 0 24px 8px;">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather pointer feather-chevron-left"
                                     onclick="toggleDriverView()">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                                Drivers <span> &nbsp; #12321321</span></h4>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-search" onclick="showDriverInfo()">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </div>

                        <ul class="tabContainer nav nav-pills" id="driverList" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="driverDetailTab" data-toggle="pill"
                                   href="#driverDetailsTab" role="tab" aria-controls="driverDetailsTab"
                                   aria-selected="true">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="timelineTab" data-toggle="pill"
                                   href="#driverTimelineTab" role="tab" aria-controls="driverTimelineTab"
                                   aria-selected="true">
                                    Timeline
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="driverTaskTab" data-toggle="pill"
                                   href="#driverTasksTab" role="tab" aria-controls="driverTasksTab"
                                   aria-selected="true">
                                    Tasks
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="driverListContent">
                            <div class="tab-pane fade show active" id="driverDetailsTab" role="tabpanel"
                                 aria-labelledby="driverDetailTab">
                                <div class="taskContainer taskDetailContainer">
                                    <div class="d-flex justify-content-end align-items-center mb-3"
                                         style="gap: 6px; width: 100%">
                                        <label class="switch s-outline s-outline-success">
                                            <input class="driverStatusSwitch" type="checkbox" checked="true">
                                            <span class="slider round"></span>
                                        </label>
                                        <label class="driverStatusLabel">On Duty</label>
                                    </div>

                                    <p><span class="dHeading">Name:</span><span class="dDescription">Driver Name</span>
                                    </p>
                                    <p><span class="dHeading">Mobile:</span><span
                                            class="dDescription">+91-8989898978</span></p>
                                    <p><span class="dHeading">Last Location:</span><span class="dDescription">1085, Sector 4, Bokaro Steel City, Jharkhand 827</span>
                                    </p>
                                    <p><span class="dHeading">Device:</span><span class="dDescription">Android</span>
                                    </p>
                                    <p><span class="dHeading">Battery:</span><span class="dDescription">96%</span></p>
                                    <p><span class="dHeading">Version:</span><span class="dDescription">10.1.8</span>
                                    </p>
                                    <p><span class="dHeading">Rating:</span><span class="dDescription">NA</span></p>
                                    <p><span class="dHeading">Tags:</span><span class="dDescription">-</span></p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="driverTimelineTab" role="tabpanel"
                                 aria-labelledby="timelineTab">
                                <div class="taskContainer taskDetailContainer">
                                    <h5>Customer Details</h5>
                                    <p><span class="dHeading">Name:</span><span
                                            class="dDescription">Customer Name</span></p>
                                    <p><span class="dHeading">Email:</span><span class="dDescription">emailaddress@company.domain</span>
                                    </p>
                                    <p><span class="dHeading">Mobile:</span><span
                                            class="dDescription">+91-9898989898</span></p>
                                    <p><span class="dHeading">Address:</span><span class="dDescription">City Name, State -122121</span>
                                    </p>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="driverTasksTab" role="tabpanel"
                                 aria-labelledby="driverTaskTab">
                                <input placeholder="search task..."
                                       style="width: calc(100% - 2rem);background: no-repeat;border: none;border-bottom: 1px solid;margin: 1rem 1rem 0.2rem;"/>
                                <div class="taskContainer" style="max-height: calc(100vh - 290px)">
                                    @for($demoSr = 0; $demoSr < 7; $demoSr++)
                                        <div class="taskBlock d-flex align-items-center justify-content-between">
                                            <div style="flex:1; overflow: hidden; align-self: flex-start;">
                                                <div class="d-flex justify-content-between align-items-center"
                                                     style="margin-bottom: 4px;">
                                                    <span>Id: #458643221</span>

                                                    <span style="font-size: 12px">
                                                    @if($demoSr % 2 == 0)Pickup @else Delivery @endif
                                                </span>

                                                    <div class="taskStatus">Assigned</div>
                                                </div>

                                                <div
                                                    class="taskInfo d-flex flex-wrap justify-content-between align-items-center">
                                                    <p class="mb-0 textWrap" style="max-width: 150px">
                                                    <span class="consignerName swan-tooltip-right"
                                                          style="font-size: 13px"
                                                          title="Consigner Name">
                                                        Consigner NameConsigner NameConsigner Name
                                                    </span><br/>
                                                        <span class="consignerAddress" style="font-size: 11px">City Name, State</span>
                                                    </p>
                                                    <div class="taskTime">
                                                        <span class="estimateTaskTime">12:30 pm</span>

                                                        @if($demoSr % 2 == 0)
                                                            <div
                                                                class="delTimeStatus d-flex align-items-center justify-content-center"
                                                                style="gap: 4px; @if($demoSr % 4 == 0)background: #efc4c4; @else background: #caffb8; @endif">

                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                     height="12 " viewBox="0 0 24 24" fill="none"
                                                                     stroke="@if($demoSr % 4 == 0)red @else green @endif"
                                                                     stroke-width="2"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-clock">
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                                </svg>
                                                                <span class="timeStatus"
                                                                      style="@if($demoSr % 4 == 0)color: red; @else color: green; @endif">
                                                                @if($demoSr % 4 == 0)Delayed @else ON TIME @endif
                                                            </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div style="width: 100%;text-align: right; font-size: 10px;">
                                                    <a onclick="showTaskInfo()">Details >></a>
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
        </div>
    </div>

@endsection
@section('js')
    <script>
        $('#searchInputBox svg.searchIcon').click(function (event) {
            $('#searchInputBox').addClass('focused');
            $('#searchInput').focus();
        });
        $('#searchInputBox svg.closeIcon').click(function (event) {
            $('#searchInputBox').removeClass('focused');
            $('#searchInput').blur();
            $('#searchInput').val('');
        });
        $('#driverSearchInputBox svg.searchIcon').click(function (event) {
            $('#driverSearchInputBox').addClass('focused');
            $('#driverSearchInput').focus();
        });
        $('#driverSearchInputBox svg.closeIcon').click(function (event) {
            $('#driverSearchInputBox').removeClass('focused');
            $('#driverSearchInput').blur();
            $('#driverSearchInput').val('');
        });


        $('.taskSelection').change(function (event) {
            $(this).siblings('.taskStatus').html('Delivered');
            $(this).parent().siblings().children().children('.consignerName').html('New Consigner Name');
            $(this).parent().siblings().children().children('.consignerAddress').html('Hamirpir, Himachal');
            $(this).parent().siblings().children().children('.estimateTaskTime').html('07:30 pm');
            $(this).parent().siblings().children().children('.timeStatus').html('On Time');
        });


        $('.driverStatusSwitch').change(function (event) {
            if (this.prop("checked", true))
                alert('sss');
            // $('.driverStatusSwitch').innerText('On Duty');
            else
                alert('ddd');
            // $('.driverStatusSwitch').innerText('Off Duty');


        });

        function showTaskInfo() {
            $('#tasks').toggle();
            $('#taskDetails').toggle();
        }

        function showDriverInfo() {
            $('#drivers').toggle();
            $('#driverDetail').toggle();
        }

        function toggleTaskView() {
            $('.driversTaskBlock').toggleClass('shrinked');
            $('.hiddenTitleLeft').toggleClass('hiddenTitleShow');
        }

        function toggleDriverView() {
            $('.driversListBlock').toggleClass('shrinked');
            $('.hiddenTitleRight').toggleClass('hiddenTitleShow');
        }


    </script>

@endsection
