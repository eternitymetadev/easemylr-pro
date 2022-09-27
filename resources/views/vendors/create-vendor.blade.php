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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Vendors</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create Vendors</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Create Vehicle</h5></div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form id="vendor-master">
                            @csrf
                            <h3>Vendoer Contact Details</h3>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Vendor Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="" name="name" placeholder="" maxlength="12">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Transporter Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="transporter_name" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Driver </label>
                                    <input type="text" class="form-control" name="driver_name" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Contact Number<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="contact_person_number" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">                          
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Contact Email<span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="">
                                </div>
                            </div>
                            <h3>Vendor NEFT details</h3>
                            <div class="form-row mb-0">   
                            <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Account Holder Name </label>
                                    <input type="text" class="form-control" name="acc_holder_name" placeholder="">
                                </div>                       
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlSelect1">Account No.</label>
                                    <input type="text" class="form-control" name="account_no" placeholder="">
                                </div>
                            </div>
                            
                            <div class="form-row mb-0">     
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Ifsc Code</label>
                                    <input type="text" class="form-control" id="" name="ifsc_code" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" placeholder="">
                                </div> 
                            </div>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Branch Name</label>
                                    <input type="text" class="form-control" name="branch_name" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Cancel Cheaque</label>
                                    <input type="file" class="form-control" name="cancel_cheaque" placeholder="">
                                </div>
                            </div>

                            <h3>Vendor Documents</h3>
                            <div class="form-row mb-0">
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pan</label>
                                    <input type="text" class="form-control" name="pan" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleFormControlInput2">Pan Upload</label>
                                    <input type="file" class="form-control" name="pan_upload" placeholder="">
                                </div>
                            </div>
                            <div class="form-row mb-0">
                               
                            </div>
                            
                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <!-- <a class="btn btn-primary" href="{{url($prefix.'/vehicles')}}"> Back</a> -->
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

</script>


@endsection