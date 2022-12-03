@extends('layouts.main')
@section('content')
    <style>
        #reportName[readonly] {
            cursor: not-allowed;
            background-color: #f1f2f3 !important;
            color: #bfc9d4;
        }

        #reportName {
            width: min(85vw, 420px);
            font-size: 18px;
            FONT-WEIGHT: 600;
            padding: 2px 12px;
            border: 2px solid #b8b8b801;
            border-radius: 8px;
        }
        #reportName:focus {
            width: min(85vw, 420px);
            font-size: 18px;
            FONT-WEIGHT: 600;
            padding: 2px 12px;
            border: 2px solid #b8b8b8;
            border-radius: 8px;
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

        <div class="widget-content widget-content-area br-6" style="min-height: min(80vh, 600px)">
            <h1>Hello</h1>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createCustomReportModal" tabindex="-1" role="dialog"
         aria-labelledby="createCustomReportModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <from class="modal-content">
                <div class="modal-header">
                    <div>
                        <input name="reportName" id="reportName" readonly value="New Custom Report"/>

                        <span id="editReportName">Edit</span>
                        <span id="doneReportName" style="display: none">Update</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="min-height: min(70vh, 700px)">

                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Branch</label>
                        <select class="form-control" id="branch" name="branch">
                            <option value="">Select branch</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                            <option>Option 4</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Billing Client</label>
                        <select class="form-control" id="billingClient" name="billingClient">
                            <option value="">Select client</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                            <option>Option 4</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleFormControlInput2">Regional Client</label>
                        <select class="form-control" id="regionalClient" name="regionalClient">
                            <option value="">Select Client</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                            <option>Option 4</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                    <button id="createCustomReport" type="submit" class="btn btn-primary">Create</button>
                </div>
            </from>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $('#openCreateReportModal').click(() => {
            var initialTitle = `New custom report ${new Date().toISOString()}`;
            $('#reportName').val(initialTitle);
        });


        $('#editReportName').click(() => {
            $('#reportName').attr('readonly', 'false');
            $('#reportName').select();
            $('#editReportName').hide();
            $('#doneReportName').show();
        });
        $('#doneReportName').click(() => {
            $('#reportName').attr('readonly', 'true');
            $('#editReportName').show();
            $('#doneReportName').hide();
        });

        $('#createCustomReport').click(() => {
            var initialTitle = `New custom report ${new Date().toISOString()}`;
            var reportTitle = $('#reportName').val();

            console.log(reportTitle);
        });

    </script>
@endsection
