@extends('layouts.main')
@section('content')
<style>
     .row.layout-top-spacing {
    width: 80%;
    margin: auto;

}
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url($prefix.'/clients')}}">Client</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update Client</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/clients/update-client')}}" id="updateclient">
                            <input type="hidden" name="baseclient_id" value="{{$getClient->id}}">
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Client Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="client_name" value="{{old('client_name',isset($getClient->client_name)?$getClient->client_name:'')}}">
                                </div>
                                <div class="form-group col-md-6">
                                    
                                </div>
                            </div>
                            <table id="myTable">
                                <tbody>
                                    <tr>
                                        <th><label for="exampleFormControlInput2">Regional Client Name<span class="text-danger">*</span></label></th>
                                        <th><label for="exampleFormControlInput2">Location<span class="text-danger">*</span></label></th>
                                        <th><label for="exampleFormControlInput2">Multiple Invoice </label></th>
                                    </tr>
                                    
                                    <?php
                                    $i=0;
                                    foreach($getClient->RegClients as $key=>$regclientdata){ 
                                        ?>
                                    <input type="hidden" name="data[{{$i}}][isRegionalClientNull]" value="0">
                                    <input type="hidden" name="data[{{$i}}][hidden_id]" value="{!! $regclientdata->id !!}">
                                    <tr class="rowcls">
                                        <td>
                                            <input type="text" class="form-control name" name="data[{{$i}}][name]" value="{{old('name',isset($regclientdata->name)?$regclientdata->name:'')}}">
                                        </td>
                                        <td>
                                            <select class="form-control location_id" name="data[{{$i}}][location_id]">
                                                <option value="">Select</option>
                                                <?php 
                                                if(count($locations)>0) {
                                                    foreach ($locations as $key => $location) {
                                                ?>
                                                    <option value="{{ $key }}" {{$regclientdata->location_id == $key ? 'selected' : ''}}>{{ucwords($location)}}</option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="check-box d-flex">
                                                <div class="checkbox radio">
                                                    <label class="check-label"> Yes
                                                        <input type="radio" class="is_multiple_invoice" value="1" name="data[{{$i}}][is_multiple_invoice]" {{ ($regclientdata->is_multiple_invoice =="1")?"checked" : "" }} checked="">
                                                    </label>
                                                </div>
                                                <div class="checkbox radio">
                                                    <label class="check-label"> No
                                                        <input type="radio" class="is_multiple_invoice" value="0" name="data[{{$i}}][is_multiple_invoice]" {{ ($regclientdata->is_multiple_invoice =="0")?"checked" : "" }} >
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary" id="addRow" onclick="addrow()"><i class="fa fa-plus-circle"></i></button>
                                            @if($i>0)
                                            <!-- <button type="button" class="btn btn-danger removeRow delete_client"><i class="fa fa-minus-circle"></i></button> -->

                                            <button type="button" class="btn btn-danger delete_client" data-id="{{ $regclientdata->id }}" data-action="<?php echo URL::to($prefix.'/clients/delete-client'); ?>"><i class="fa fa-minus-circle"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                    <?php $i++; } ?> 
                                </tbody>
                            </table>
                            
                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/clients') }}"> Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('models.delete-client')
@endsection
@section('js')
<script>
    // $("a").click(function(){
    function addrow(){
        var i = $('.rowcls').length;
        // i  = i + 1;
        var rows = '';

        rows+= '<tr class="rowcls">';
        rows+= '<td>';
        rows+= '<input type="text" class="form-control name" name="data['+i+'][name]" placeholder="">';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<select class="form-control location_id" name="data['+i+'][location_id]">';
        rows+= '<option value="">Select</option>';
        <?php if(count($locations)>0) {
            foreach ($locations as $key => $location) {
        ?>
            rows+= '<option value="{{ $key }}">{{ucwords($location)}}</option>';
            <?php
            }
        }
        ?>
        rows+= '</select>';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<div class="check-box d-flex">';
        rows+= '<div class="checkbox radio">';
        rows+= '<label class="check-label"> Yes';
        rows+= '<input type="radio" class="is_multiple_invoice" name="data['+i+'][is_multiple_invoice]" value="1" checked="">';
        rows+= '</label>';
        rows+= '</div>';
        rows+= '<div class="checkbox radio">';
        rows+= '<label class="check-label"> No';
        rows+= '<input type="radio" class="is_multiple_invoice" name="data['+i+'][is_multiple_invoice]" value="0">';
        rows+= '</label>';
        rows+= '</div>';
        rows+= '</div>';
        rows+= '</td>';
        rows+= '<td>';
        rows+= '<button type="button" class="btn btn-danger removeRow" data-id="{{ $regclientdata->id }}" data-action="<?php echo URL::to($prefix.'/clients/delete-client'); ?>"><i class="fa fa-minus-circle"></i></button>';
        rows+= '</td>';
        rows+= '</tr>';

        $('#myTable tbody').append(rows);
  
    }

    //Remove the current row
    $(document).on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
    });

</script>
@endsection