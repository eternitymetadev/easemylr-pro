@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consigner</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create
                                Consigner</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/consigners')}}"
                            id="createconsigner">
                            <div class="form-row mb-0">
                            <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Regional Client<span
                                            class="text-danger">*</span></label>
                                    <?php $authuser = Auth::user();
                                    ?>
                                    <select class="form-control tagging" id="regionalclient_id" multiple="multiple" name="regclient_id[]">
                                    <option value="">Select</option>
                                        <?php 
                                        if(count($regclients)>0) {
                                            foreach ($regclients as $key => $client) {
                                        ?>
                                        <option data-locationid="{{$client->location_id}}" value="{{ $client->id }}">{{ucwords($client->name)}}</option>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="branch_id" value="{{$client->location_id}}">
                                    
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Consigner Nick Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nick_name" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">PRS Type</label>
                                    <select class="form-control" id="prs_type" name="prs_type">
                                        <option value="">Select</option>
                                        <option value="0">One Time</option>
                                        <option value="1">Recuring</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Vehicle Number</label>
                                    <select class="form-control my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                                    <option value="">Select vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                                    </option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Driver Name</label>
                                    <select class="form-control my-select2" id="driver_id" name="driver_id" tabindex="-1">
                                    <option value="">Select driver</option>
                                    @foreach($drivers as $driver)
                                    <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ?? '-'}}
                                    </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Mobile No.<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control mbCheckNm" name="phone"
                                        placeholder="Enter 10 digit mobile no" maxlength="10">
                                </div>                                
                            </div>
                            
                            <button type="submit" name="time" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/consigners') }}"> Back</a>
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

//multiple select //
var ss = $(".basic").select2({
    tags: true,
});

</script>
@endsection
