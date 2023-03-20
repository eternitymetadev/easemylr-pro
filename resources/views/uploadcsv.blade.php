@extends('layouts.main')
@section('content')

<h5 class="importmessage red-text" style="display: none;">Files imported successfully.</h5>
<!-- <h5 class="select-csvfile red-text" style="display: none;">Please select csv file.</h5> -->
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Import Data</a></li>
                        <!-- <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Consignee List</a></li> -->
                    </ol>
                </nav>
            </div>
            @php
                $authuser = Auth::user();
            @endphp

            <form method="POST" action="{{url($prefix.'/consignees/upload_csv')}}" id="importfiles" enctype="multipart/form-data">
                @csrf 
                <?php if($authuser->role_id == 1){ ?>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Consigners Sheet</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="consignersfile" id="consignerfile" class="consignerfile"> 
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-consigner')}}">Sample Download</a> 
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Consignees Sheet</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="consigneesfile" id="consigneefile" class="consigneefile"> 
                    </div> 
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-consignees')}}">Sample Download</a> 
                    </div>
                </div> 
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Consignees Phone No Update</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="consigneephonesfile" id="consigneephonesfile" class="consigneephonesfile"> 
                    </div> 
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-consignee-phone')}}">Sample Download</a> 
                    </div>
                </div> 
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Vehicles Sheet</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="vehiclesfile" id="vehiclefile" class="vehiclefile"> 
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-vehicle')}}">Sample Download</a> 
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Drivers Sheet</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="driversfile" id="driverfile" class="driverfile"> 
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-driver')}}">Sample Download</a> 
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Zones Sheet</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="zonesfile" id="zonefile" class="zonefile"> 
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-zone')}}">Sample Download</a> 
                    </div>
                </div>
                <br/>
                <?php }if($authuser->role_id == 1 || $authuser->role_id == 3){?>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse LR Type Changes</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="manualdeliveryfile" id="manualdeliveryfile" class="manualdeliveryfile"> 
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-manualdelivery')}}">Sample Download</a> 
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse Delivery Date Sheet</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="deliverydatesfile" id="deliverydatefile" class="deliverydatefile"> 
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                    <a class="btn btn-primary" href="{{url($prefix.'/sample-deliverydate')}}">Sample Download</a> 
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse POD Zip Folder(Image)</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="podsfile" id="podfile" class="podfile"> 
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-12">
                        <h4 class="win-h4">Browse to update lattitude</h4>
                    </div>
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <input type="file" name="lat_lang" id="lat_lang" class="lat_lang"> 
                    </div>
                </div>
                <?php } ?>

                <button type="submit" name="" class="mt-4 mb-4 btn btn-primary">Submit</button>
                <div class="spinner-border loader" style= "display:none;"></div>
                <a class="btn btn-primary" href="{{url($prefix.'/dashboard') }}"> Back</a>
            </form>
        </div>

    </div>
</div>

@endsection