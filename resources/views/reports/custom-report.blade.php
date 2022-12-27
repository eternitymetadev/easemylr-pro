@extends('layouts.main')
@section('content')
    <style>
        .reportTemplateContainer {
            min-height: min(75vh, 600px);
            box-shadow: 0 0 16px -3px #83838370;
            border-radius: 12px;
        }

        .reportTemplateContainer h3 {
            font-size: 20px;
            font-weight: 600;
            background: #f9b80840;
            padding: 2px 1rem;
            width: max-content;
            border-radius: 8px;
            color: black;
        }

        #reportName[readonly] {
            cursor: not-allowed;
            background-color: #ffffff !important;
            color: #101010;
        }

        #reportName {
            width: min(85vw, 375px);
            font-size: 18px;
            FONT-WEIGHT: 600;
            padding: 2px 12px;
            border: 2px solid #b8b8b801;
            border-radius: 8px;
        }

        .focusedInput {
            border: 2px solid #b8b8b8 !important;
        }

        #editReportName svg {
            color: #1980c6;
            height: 18px;
            width: 18px;
            cursor: pointer;
        }

        #doneReportName svg {
            color: green;
            height: 24px;
            width: 24px;
            border-radius: 50vh;
            border: 1px solid #ffffff01;
            padding: 4px;
            box-shadow: 0px 2px 4px #ffffff01;
            cursor: pointer;
            transition: all 200ms ease-in-out;
        }

        #doneReportName:hover svg {
            border: 1px solid;
            box-shadow: 0px 2px 4px;
            transition: all 200ms ease-in-out;
        }

        .form-group {
            margin-bottom: 0;
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

        li, label {
            cursor: pointer;
        }

        .columnsSelection ul {
            list-style: none;
            padding: 0 0 0 14px;
            user-select: none;
        }

        .columnsSelection ul li svg {
            height: 14px;
            width: 14px;
            cursor: pointer;
        }

        .coloredBg {
            background: #fdf2f2;
        }

        .dateRangeBlock {
            background: #f4f4f4;
            border-radius: 12px;
            padding: 4px;
        }

        .dateRangeInput {
            padding: 3px;
            height: 30px;
        }

        #reportTemplates {
            list-style: none;
        }

        .reportTemplateRow p {
            font-size: 18px;
            font-weight: 500;
            color: #000;
            margin-bottom: 0;
        }

        .reportTemplateRow p a {
            font-size: 12px;
            font-weight: 500;
            color: #e83f3f;
        }

        .reportTemplateRow {
            border-radius: 0px;
            padding: 1rem 1rem;
            box-shadow: 0 0 12px #f9b80800 inset;
            transition: all 200ms ease-in-out;
            border-bottom: 1px solid rgba(131, 131, 131, 0.15);
        }

        .reportTemplateRow:hover, .focusedReportTemplateRow {
            border-radius: 12px;
            border-bottom: none;
            box-shadow: 0 0 12px #f9b808 inset;
        }

    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Custom Reports</h2>
            <button id="openCreateReportModal" class="btn btn-primary" data-toggle="modal"
                    data-target="#createCustomReportModal">
                Create New Report
            </button>
        </div>

        <div class="reportTemplateContainer widget-content widget-content-area">
            <h3>Report Templates</h3>

            <ul class="mt-4" id="reportTemplates">
                <li>
                    <div class="reportTemplateRow d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                            Custom Report Name Here
                            <span>
                            <a class="swan-tooltip" data-tooltip="Click to remove template" data-toggle="modal"
                               data-target="#deleteReportTemplate">
                                Remove this template
                            </a>
                        </span>
                        </p>
                        <button class="generateReportButton btn btn-sm btn-primary">Generate Report</button>

                        <div class="exportReportBlock col-12 p-2"
                             style="display: none;background: #ececec; border-radius: 8px; margin-top: 8px;">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px">
                                <label for="fromDate">From</label>
                                <input class="mr-2 form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <label for="toDate">To</label>
                                <input class="form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <button class="ml-4 btn btn-sm btn-primary exportReportButton" style="min-width: 100px">
                                    <span class="exportLoading" style="display: none">Exporting...</span>
                                    <span class="exportLabel">Export Report</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="reportTemplateRow d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                            Custom Report Name Here
                            <span>
                            <a class="swan-tooltip" data-tooltip="Click to remove template" data-toggle="modal"
                               data-target="#deleteReportTemplate">
                                Remove this template
                            </a>
                        </span>
                        </p>
                        <button class="generateReportButton btn btn-sm btn-primary">Generate Report</button>

                        <div class="exportReportBlock col-12 p-2"
                             style="display: none;background: #ececec; border-radius: 8px; margin-top: 8px;">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px">
                                <label for="fromDate">From</label>
                                <input class="mr-2 form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <label for="toDate">To</label>
                                <input class="form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <button class="ml-4 btn btn-sm btn-primary exportReportButton" style="min-width: 100px">
                                    <span class="exportLoading" style="display: none">Exporting...</span>
                                    <span class="exportLabel">Export Report</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="reportTemplateRow d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                            Custom Report Name Here
                            <span>
                            <a class="swan-tooltip" data-tooltip="Click to remove template" data-toggle="modal"
                               data-target="#deleteReportTemplate">
                                Remove this template
                            </a>
                        </span>
                        </p>
                        <button class="generateReportButton btn btn-sm btn-primary">Generate Report</button>

                        <div class="exportReportBlock col-12 p-2"
                             style="display: none;background: #ececec; border-radius: 8px; margin-top: 8px;">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px">
                                <label for="fromDate">From</label>
                                <input class="mr-2 form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <label for="toDate">To</label>
                                <input class="form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <button class="ml-4 btn btn-sm btn-primary exportReportButton" style="min-width: 100px">
                                    <span class="exportLoading" style="display: none">Exporting...</span>
                                    <span class="exportLabel">Export Report</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="reportTemplateRow d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                            Custom Report Name Here
                            <span>
                            <a class="swan-tooltip" data-tooltip="Click to remove template" data-toggle="modal"
                               data-target="#deleteReportTemplate">
                                Remove this template
                            </a>
                        </span>
                        </p>
                        <button class="generateReportButton btn btn-sm btn-primary">Generate Report</button>

                        <div class="exportReportBlock col-12 p-2"
                             style="display: none;background: #ececec; border-radius: 8px; margin-top: 8px;">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px">
                                <label for="fromDate">From</label>
                                <input class="mr-2 form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <label for="toDate">To</label>
                                <input class="form-control dateRangeInput" type="date" style="max-width: 200px"/>
                                <button class="ml-4 btn btn-sm btn-primary exportReportButton" style="min-width: 100px">
                                    <span class="exportLoading" style="display: none">Exporting...</span>
                                    <span class="exportLabel">Export Report</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>


    {{--modal for removing template--}}
    <div class="modal fade" id="deleteReportTemplate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="Delt-content text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-alert-circle deleteAlertIcon">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <h5 class="my-2">Delete Template</h5>
                            <span>Are you sure you want to delete this report template?</span>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="d-flex justify-content-end align-content-center mt-4" style="gap: 1rem;">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-danger btn-modal delete-btn-modal deleteReportTemplateconfirm">Yeah! Sure
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{--Modal for create new custom report--}}
    <div class="modal fade" id="createCustomReportModal" tabindex="-1" role="dialog"
         aria-labelledby="createCustomReportModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <from class="modal-content">
                <div class="modal-header" style="border-bottom: none">
                    <div>
                        <input name="reportName" id="reportName" readonly value="New Custom Report"/>

                        <span id="editReportName" class="swan-tooltip" data-tooltip="Edit Report Name">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-edit">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </span>
                        <span id="doneReportName" style="display: none" class="swan-tooltip"
                              data-tooltip="Save Report Name">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-check">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-end dateRangeBlock">
                        <div class="form-group">
                            <label for="fromDate">From</label>
                            <input class="form-control dateRangeInput" type="date" style="border-radius: 8px 0 0 8px"/>
                        </div>
                        <div class="form-group">
                            <label for="toDate">To</label>
                            <input class="form-control dateRangeInput" type="date" style="border-radius: 0 8px 8px 0"/>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="min-height: min(70vh, 700px)">
                    <div class="form-row">
                        <h6 class="col-12">Filter</h6>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Branch</label>
                            <select class="form-control form-control-sm" id="branch" name="branch">
                                <option value="">Select branch</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Billing Client</label>
                            <select class="form-control form-control-sm" id="billingClient" name="billingClient">
                                <option value="">Select client</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Regional Client</label>
                            <select class="form-control form-control-sm" id="regionalClient" name="regionalClient">
                                <option value="">Select Client</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row columnsSelection">
                        <h6 class="col-12">Columns</h6>

                        <ul style="width: 100%; display: grid; grid-template-columns: repeat(auto-fit, 250px); column-gap: 1rem; row-gap: 0.3rem; height: max-content">
                            <?php
                            $columnsAvailable = array(
                                array("label" => "label 1"),
                                array("label" => "label 2", "child" => array(array("label" => "L2 child 1"), array("label" => "L2 child 2"))),
                                array("label" => "label 3"),
                                array("label" => "label 4", "child" => array(array("label" => "L4 child 1"), array("label" => "L4 child 2"))),
                                array("label" => "label 5"),
                                array("label" => "label 6", "child" => array(array("label" => "L6 child 1"), array("label" => "L6 child 2"))),
                                array("label" => "label 7"),
                                array("label" => "label 8", "child" => array(array("label" => "L8 child 1"), array("label" => "L8 child 2"))),
                                array("label" => "label 9"),
                                array("label" => "label 10", "child" => array(array("label" => "L10 child 1"), array("label" => "L10 child 2"))),
                                array("label" => "label 11"),
                                array("label" => "label 12", "child" => array(array("label" => "L12 child 1"), array("label" => "L12 child 2"))),
                            );
                            ?>

                            @foreach($columnsAvailable as $columnsItem)
                                <li style="border-radius: 6px; padding: 6px 6px 6px 14px;">
                                    <input type="checkbox" name="{{$columnsItem['label']}}"
                                           id="{{$columnsItem['label']}}"/>
                                    <label for="{{$columnsItem['label']}}">
                                        {{$columnsItem['label']}}
                                    </label>
                                    @if(array_key_exists('child', $columnsItem))
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="feather feather-chevron-down toggleChildCols">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                        <ul style="display: none">
                                            @foreach($columnsItem['child'] as $childColumnsItem)
                                                <li>
                                                    <input type="checkbox"
                                                           name="{{$childColumnsItem['label']}}"
                                                           id="{{$childColumnsItem['label']}}"/>
                                                    <label for="{{$childColumnsItem['label']}}">
                                                        {{$childColumnsItem['label']}}
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="p-3 d-flex justify-content-between align-items-center" style="gap: 1rem;">
                    <div class="d-flex align-items-center" style="gap: 8px">
                        <input style="height: 1rem; width: 1rem" type="checkbox" checked name="isTemplate"
                               id="isTemplate"/>
                        <label for="isTemplate">
                            Save this as a template also.
                        </label>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary mr-3" data-dismiss="modal">Discard &
                            Close
                        </button>
                        <button id="createCustomReport" type="submit" class="btn btn-primary">Export Report</button>
                    </div>
                </div>
            </from>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $('#openCreateReportModal').click(() => {
            var initialTitle = `Custom report ${new Date().toISOString()}`;
            $('#reportName').val(initialTitle);
        });

        $('#editReportName').click(() => {
            $('#reportName').attr('readonly', false);
            $('#reportName').select();
            $('#reportName').addClass('focusedInput');
            $('#editReportName').hide();
            $('#doneReportName').show();
        });
        $('#doneReportName').click(() => {
            $('#reportName').attr('readonly', true);
            $('#reportName').removeClass('focusedInput');
            $('#editReportName').show();
            $('#doneReportName').hide();
        });

        $('#createCustomReport').click(() => {
            var initialTitle = `New custom report ${new Date().toISOString()}`;
            var reportTitle = $('#reportName').val();

            console.log(reportTitle);
        });

        $(".toggleChildCols").click(function () {
            $(this).closest('li').toggleClass('coloredBg');
            $(this).siblings("ul").toggle();
        });

        $(".generateReportButton").click(function () {
            $(this).parent().toggleClass('focusedReportTemplateRow');
            $(this).siblings('.exportReportBlock').toggle();
        });

        $(".exportReportButton").click(function () {
            $(this).children('.exportLabel').hide();
            $(this).children('.exportLoading').show();
            setTimeout(() => {
                $(this).closest('.reportTemplateRow').removeClass('focusedReportTemplateRow');
                $(this).closest('.exportReportBlock').hide();
                $(this).children('.exportLabel').show();
                $(this).children('.exportLoading').hide();
            }, 1000);
        });

        $('input[type="checkbox"]').change(function (e) {

            var checked = $(this).prop("checked"),
                container = $(this).parent(),
                siblings = container.siblings();

            container.find('input[type="checkbox"]').prop({
                indeterminate: false,
                checked: checked
            });

            function checkSiblings(el) {
                var parent = el.parent().parent(),
                    all = true;
                el.siblings().each(function () {
                    let returnValue = all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
                    return returnValue;
                });
                if (all && checked) {
                    parent.children('input[type="checkbox"]').prop({
                        indeterminate: false,
                        checked: checked
                    });
                    checkSiblings(parent);
                } else if (all && !checked) {
                    parent.children('input[type="checkbox"]').prop("checked", checked);
                    parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
                    checkSiblings(parent);

                } else {
                    el.parents("li").children('input[type="checkbox"]').prop({
                        indeterminate: true,
                        checked: false
                    });
                }
            }

            checkSiblings(container);
        });

    </script>
@endsection
