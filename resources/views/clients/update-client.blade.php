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
            <h2 class="pageHeading">Update Client</h2>
        </div>

        <form class="general_form pageContainer widget-content widget-content-area d-flex flex-column" method="POST"
              action="{{url($prefix.'/clients/update-client')}}" id="updateclient">
            <input type="hidden" name="baseclient_id" value="{{$getClient->id}}">
            <div class="form-row mb-0">
                <div class="form-group col-12">
                    <label for="exampleFormControlInput2">Client Name<span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="client_name"
                           value="{{old('client_name',isset($getClient->client_name)?$getClient->client_name:'')}}">
                </div>
            </div>
            <table id="myTable" style="width: 100%; margin-inline: auto">
                <tbody>
                <tr>
                    <th><label for="exampleFormControlInput2">Regional Client Name<span
                                class="text-danger">*</span></label></th>
                    <th><label for="exampleFormControlInput2">Location<span class="text-danger">*</span></label></th>
                    <th><label for="exampleFormControlInput2">Multiple Invoice </label></th>
                </tr>

                <?php $i = 0;
                foreach($getClient->RegClients as $key=>$regclientdata){
                ?>
                <input type="hidden" name="data[{{$i}}][isRegionalClientNull]" value="0">
                <input type="hidden" name="data[{{$i}}][hidden_id]" value="{!! $regclientdata->id !!}">
                <tr class="rowcls">
                    <td>
                        <input type="text" class="form-control name" name="data[{{$i}}][name]"
                               value="{{old('name',isset($regclientdata->name)?$regclientdata->name:'')}}">
                    </td>
                    <td>
                        <select class="form-control location_id" name="data[{{$i}}][location_id]">
                            <option value="">Select</option>
                            @if(count($locations)>0)
                                @foreach ($locations as $key => $location)
                                    <option
                                        value="{{ $key }}" {{$regclientdata->location_id == $key ? 'selected' : ''}}>
                                        {{ucwords($location)}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <div class="check-box d-flex align-items-center" style="gap: 1rem;">
                            <div class="checkbox radio">
                                <label class="check-label">
                                    <input type="radio" class="is_multiple_invoice" value="1"
                                           name="data[{{$i}}][is_multiple_invoice]"
                                           {{ ($regclientdata->is_multiple_invoice =="1")?"checked" : "" }} checked> Yes
                                </label>
                            </div>
                            <div class="checkbox radio">
                                <label class="check-label">
                                    <input type="radio" class="is_multiple_invoice" value="0"
                                           name="data[{{$i}}][is_multiple_invoice]" {{ ($regclientdata->is_multiple_invoice =="0")?"checked" : "" }} >
                                    No
                                </label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary" id="addRow" onclick="addrow()"><i
                                class="fa fa-plus-circle"></i></button>
                    @if($i>0)
                            <button type="button" class="btn btn-danger delete_client"
                                    data-id="{{ $regclientdata->id }}"
                                    data-action="<?php echo URL::to($prefix . '/clients/delete-client'); ?>"><i
                                    class="fa fa-minus-circle"></i></button>
                        @endif
                    </td>
                </tr>
                <?php $i++; } ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-end align-items-end" style="gap: 1rem; flex: 1">
                <a class="btn btn-outline-primary" href="{{url($prefix.'/clients') }}" style="width: 100px">Back</a>
                <button type="submit" class="btn btn-primary" style="width: 100px">Submit</button>
            </div>

        </form>


    </div>
    @include('models.delete-client')
@endsection
@section('js')
    <script>
        // $("a").click(function(){
        function addrow() {
            var i = $('.rowcls').length;
            // i  = i + 1;
            var rows = '';

            rows += '<tr class="rowcls">';
            rows += '<td>';
            rows += '<input type="text" class="form-control name" name="data[' + i + '][name]" placeholder="Enter client name">';
            rows += '</td>';
            rows += '<td>';
            rows += '<select class="form-control location_id" name="data[' + i + '][location_id]">';
            rows += '<option value="">Select</option>';
            <?php if(count($locations) > 0) {
                foreach ($locations as $key => $location) {
                ?>
                rows += '<option value="{{ $key }}">{{ucwords($location)}}</option>';
            <?php
                }
                }
                ?>
                rows += '</select>';
            rows += '</td>';
            rows += '<td>';
            rows += '<div class="check-box d-flex align-items-center" style="gap: 1rem;">';
            rows += '<div class="checkbox radio">';
            rows += '<label class="check-label">';
            rows += '<input type="radio" class="is_multiple_invoice" name="data[' + i + '][is_multiple_invoice]" value="1" checked=""> Yes';
            rows += '</label>';
            rows += '</div>';
            rows += '<div class="checkbox radio">';
            rows += '<label class="check-label">';
            rows += '<input type="radio" class="is_multiple_invoice" name="data[' + i + '][is_multiple_invoice]" value="0"> No';
            rows += '</label>';
            rows += '</div>';
            rows += '</div>';
            rows += '</td>';
            rows += '<td>';
            rows += '<button type="button" class="btn btn-danger removeRow" data-id="{{ $regclientdata->id }}" data-action="<?php echo URL::to($prefix . '/clients/delete-client'); ?>"><i class="fa fa-minus-circle"></i></button>';
            rows += '</td>';
            rows += '</tr>';

            $('#myTable tbody').append(rows);

        }

        //Remove the current row
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
        });

    </script>
@endsection
