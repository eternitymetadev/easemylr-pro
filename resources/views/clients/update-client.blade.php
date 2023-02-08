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
                                            <select class="form-control" name="data[{{$i}}][is_multiple_invoice]" >
                                                <option value="">Select..</option>
                                                <option value="1" {{$regclientdata->is_multiple_invoice == '1' ? 'selected' : ''}}>Per invoice-Item wise</option>
                                                <option value="2" {{$regclientdata->is_multiple_invoice == '2' ? 'selected' : ''}}>Multiple Invoice-Item wise</option>
                                                <option value="3" {{$regclientdata->is_multiple_invoice == '3' ? 'selected' : ''}}>per invoice-Without Item</option>
                                                <option value="4" {{$regclientdata->is_multiple_invoice == '4' ? 'selected' : ''}}>LR Multiple invoice-Without item</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="check-box d-flex">
                                                <div class="checkbox radio">
                                                    <label class="check-label">Yes
                                                        <input type="radio"  value='1' name="data[{{$i}}][is_prs_pickup]" {{ ($regclientdata->is_prs_pickup=="1")? "checked" : "" }}>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="checkbox radio">
                                                    <label class="check-label">No
                                                        <input type="radio" name="data[1][is_prs_pickup]" value='0' {{ ($regclientdata->is_prs_pickup=="0")? "checked" : "" }}>
                                                        <span class="checkmark"></span>
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
        rows+= '<select class="form-control" name="data['+i+'][is_multiple_invoice]" ><option value="">Select..</option><option value="1">Per invoice-Item wise</option><option value="2">Multiple Invoice-Item wise</option><option value="3">per invoice-Without Item</option><option value="4">LR Multiple invoice-Without item</option></select>';
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