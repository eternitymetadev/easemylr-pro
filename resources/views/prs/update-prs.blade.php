@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">PRS</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Update
                                PRS</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/prs/update-prs')}}"
                            id="updateprs">
                            <?php $authuser = Auth::user(); ?>
                            <!-- <input type="hidden" class="form-seteing date-picker" id="prsDate" name="prs_date"
                                placeholder="" value="<?php// echo date('d-m-Y'); ?>" /> -->

                            <input type="hidden" class="form-seteing" name="prs_id" placeholder=""
                                value="{{$getprs->id}}" />
                            <div class="d-flex align-items-center justify-content-center flex-wrap mb-4">

                                <div class="col-md-6">
                                    <label>Pickup From Branch</label>
                                    <select name="location_id" class="form-control tagging" id="location_id">
                                        <option value="">Select</option>
                                        <?php 
                                        if(count($locations)>0) {
                                            foreach ($locations as $key => $location) {  
                                        ?>
                                        <option value="{{ $location->id }}"
                                            {{ $location->id == $getprs->location_id ? 'selected' : ''}}>
                                            {{ucwords($location->name)}}</option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Pickup Drop Hub</label>
                                    <select name="hub_location_id" class="form-control tagging" id="hub_location_id">
                                        <option value="">Select</option>
                                        <?php 
                                        if(count($hub_locations)>0) {
                                            foreach ($hub_locations as $key => $hubloc) {  
                                        ?>
                                        <option value="{{ $hubloc->id }}"
                                            {{ $hubloc->id == $getprs->hub_location_id ? 'selected' : ''}}>
                                            {{ucwords($hubloc->name)}}</option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>

                            </div>

                            <div class="iterationRows">
                                <div id="rowToIterate" class="amit d-flex align-items-center" style="gap: 8px;">
                                    <table id="participantTable" style="width: 100%; border-spacing: 30px 5px;">
                                        <thead>
                                            <tr>
                                                <th>Regional Client*</th>
                                                <th>Consigner</th>
                                                <th width="24px"></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                        $i=0;
                                        if(count($getprs->PrsRegClients)>0) {
                                        foreach($getprs->PrsRegClients as $key=>$regclientdata){ 
                                        ?>
                                            <tr class="rrow">
                                                <td valign="middle" class="p-2">
                                                    <select class="form-control tagging select_prsregclient disabled"
                                                        onchange="onChangePrsRegClient(this)"
                                                        name="data[{{$i}}][regclient_id]" readonly disabled>
                                                        <option selected="selected">
                                                            Select client..
                                                        </option>
                                                        <?php
                                                        if(count($regclients)>0) {
                                                        foreach ($regclients as $key => $client) {
                                                        ?>
                                                        <option value="{{ $client->id }}"
                                                            {{$regclientdata->regclient_id == $client->id ? 'selected' : ''}}>
                                                            {{ucwords($client->name)}}</option>
                                                        <?php
                                                        }}
                                                        ?>
                                                    </select>
                                                </td>
                                                <?php  
                                                $cnr_ids= array();
                                                if(count($regclientdata->RegConsigner)>0) {
                                                    foreach($regclientdata->RegConsigner as $key => $regcnr){
                                                        $regclient_ids = DB::table('prs_reg_consigners')->where('prs_regclientid',$regcnr->prs_regclientid)->select('prs_regclientid','id','consigner_id')->get();

                                                        if(count($regclient_ids)>0) {
                                                            foreach($regclient_ids as $key => $clientcnr){
                                                                $cnr_ids[] = $clientcnr->consigner_id;
                                                            }
                                                        }else{
                                                            $cnr_ids[] ='';
                                                        }
                                                    $cnrs_name = Helper::getConsignerName($cnr_ids);
                                                }} ?>
                                                <td style="width: 70%" valign="top" class="p-2">
                                                    <input class="form-control" name="data[{{$i}}][consigner_id][]" value="{{$cnrs_name}}" readonly>
                                                </td>
                                            </tr>
                                            <?php $i++; }} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-row mb-0">
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Vehicle Type</label>
                                    <select class="form-control tagging my-select2" id="vehicle_type"
                                        name="vehicletype_id" tabindex="-1">
                                        <option value="">Select vehicle type</option>
                                        @foreach($vehicletypes as $vehicletype)
                                        <option value="{{ $vehicletype->id }}"
                                            {{$getprs->vehicletype_id == $vehicletype->id ? 'selected' : ''}}>
                                            {{ucwords($vehicletype->name)}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Vehicle Number</label>
                                    <select class="form-control tagging my-select2" id="vehicle_no" name="vehicle_id"
                                        tabindex="-1">
                                        <option value="">Select vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            {{$getprs->vehicle_id == $vehicle->id ? 'selected' : ''}}>
                                            {{ucwords($vehicle->regn_no)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleFormControlInput2">Driver Name</label>
                                    <select class="form-control tagging my-select2" id="driver_id" name="driver_id"
                                        tabindex="-1">
                                        <option value="">Select driver</option>
                                        @foreach($drivers as $driver)
                                        <option value="{{$driver->id}}">
                                            {{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ?? '-'}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/prs')}}"> Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script>
// add prs date
$('#prsDate').val(new Date().toJSON().slice(0, 10));

//multiple select //
var ss = $(".basic").select2({
    tags: true,
});


function addrow() {
    var i = $('.rrow').length;
    console.log(i);
    i = i + 1;
    var rows = '';

    rows += '<tr class="rrow">';
    rows += '<td valign="middle" class="p-2">';
    rows +=
        '<select class="form-control tagging select_prsregclient" id="" onchange="onChangePrsRegClient(this)" name="data[' +
        i + '][regclient_id]">';
    rows += '<option selected="selected" disabled>Select client..</option>';
    <?php
        foreach ($regclients as $key => $client) {
        ?>
    rows += '<option value="{{ $client->id }}">{{ucwords($client->name)}}</option>';
    <?php
        }
        ?>
    rows += '<input type="hidden" name="data[' + i + '][branch_id]" value="{{$client->location_id}}" />';
    rows += '</select></td>';
    rows += '<td valign="middle" class="p-2">';
    rows += '<select class="form-control consigner_prs tagging" multiple="multiple" name="data[' + i +
        '][consigner_id][]">';
    rows += '<option disabled>Select</option></select></td>';
    rows += '<td valign="middle" class="p-2" width="24px">';
    rows +=
        '<button type="button" class="btn btn-primary rowAddButton" id="addRowButton" onclick="addrow()"><i class="fa fa-plus-circle"></i></button>';
    rows += '<button type="button" class="btn btn-danger rowClearButton"><i class="fa fa-minus-circle"></i></button>';
    rows += '</td></tr>';
    // 


    $('#participantTable tbody').append(rows);
    $('.tagging').select2();
}

$(document).on('click', '.rowClearButton', function() {
    $(this).closest('tr').remove();
});
</script>
@endsection