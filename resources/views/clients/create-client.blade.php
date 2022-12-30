@extends('layouts.main')
@section('content')
    <style>
        .pageContainer {
            min-height: min(75vh, 600px);
            box-shadow: 0 0 16px -3px #83838370;
            border-radius: 12px;
        }

        #myTable td, #myTable th {
            padding-inline: 1rem;
        }
    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Create Client</h2>
        </div>

        <form class="general_form pageContainer widget-content widget-content-area d-flex flex-column" method="POST"
              action="{{url($prefix.'/clients')}}" id="createclient">

            <div class="form-row mb-0">
                <div class="form-group col-12">
                    <label for="exampleFormControlInput2">Client Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="client_name" placeholder=""/>
                </div>

            </div>
            <table id="myTable" style="width: 100%; margin-inline: auto">
                <tbody>
                <tr>
                    <th><label for="exampleFormControlInput2">Regional Client Name<span
                                class="text-danger">*</span></label></th>
                    <th><label for="exampleFormControlInput2">Location<span
                                class="text-danger">*</span></label></th>
                    <th><label for="exampleFormControlInput2">Multiple Invoice </label></th>
                </tr>
                <tr class="rowcls">
                    <td>
                        <input type="text" class="form-control name" name="data[1][name]"
                               placeholder="Enter client name">
                    </td>
                    <td>
                        <select class="form-control location_id" name="data[1][location_id]">
                            <option value="">Select</option>
                            @if(count($locations) > 0)
                                @foreach ($locations as $key => $location)
                                    <option value="{{ $key }}">{{ucwords($location)}}</option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <div class="check-box d-flex align-items-center" style="gap: 1rem;">
                            <div class="checkbox radio">
                                <label class="check-label">
                                    <input type="radio" value="1" name="data[1][is_multiple_invoice]" checked>
                                    Yes</label>
                            </div>
                            <div class="checkbox radio">
                                <label class="check-label">
                                    <input type="radio" name="data[1][is_multiple_invoice]" value="0">
                                    No</label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary" id="addRow" onclick="addrow()">
                            <i class="fa fa-plus-circle"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="d-flex justify-content-end align-items-end" style="gap: 1rem; flex: 1">
                <a class="btn btn-outline-primary" href="{{url($prefix.'/clients') }}" style="width: 100px">Back</a>
                <button type="submit" class="btn btn-primary" style="width: 100px">Submit</button>
            </div>
        </form>

    </div>

@endsection
@section('js')
    <script>
        // $("a").click(function(){
        function addrow() {
            var i = $('.rowcls').length;
            i = i + 1;
            var rows = '';

            rows += '<tr class="rowcls">';
            rows += '<td><input type="text" class="form-control name" name="data[' + i + '][name]" placeholder="Enter client name"></td>';
            rows += '<td><select class="form-control location_id" name="data[' + i + '][location_id]">';
            rows += '<option value="">Select</option>';
            <?php if(count($locations) > 0) {
                foreach ($locations as $key => $location) {
                ?>
                rows += '<option value="{{ $key }}">{{ucwords($location)}}</option>';
            <?php
                }
                }
                ?>
                rows += '</select></td>';
            rows += '<td><div class="check-box d-flex align-items-center" style="gap: 1rem;"><div class="checkbox radio">';
            rows += '<label class="check-label"><input type="radio" value="1" name="data[' + i + '][is_multiple_invoice]" checked> Yes</label>';
            rows += '</div><div class="checkbox radio">';
            rows += '<label class="check-label"><input type="radio" name="data[' + i + '][is_multiple_invoice]" value="0"> No</label>';
            rows += '</div></div></td>';
            rows += '<td><button type="button" class="btn btn-danger removeRow"><i class="fa fa-minus-circle"></i></button></td>';
            rows += '</tr>';

            $('#myTable tbody').append(rows);

        }

        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
        });

    </script>
@endsection
