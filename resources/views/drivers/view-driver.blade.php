@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Drivers</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">View Driver</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Driver Details</h5></div> -->
                    <div class="col-md-10 text-right">
                        
                    </div>
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Driver Name</th>
                                    <td>{{isset($getdriver->name)?ucfirst($getdriver->name):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone</th>
                                    <td>{{isset($getdriver->phone) ? ucfirst($getdriver->phone) : "-" }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Driver License Number</th>
                                    <td>{{isset($getdriver->license_number)?ucfirst($getdriver->license_number):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Driver License Image</th>
                                    <td>{{isset($getdriver->license_image) ? $getdriver->license_image:'-'}}</td>
                                </tr>
                                <!-- <tr>
                                    <th scope="row">Bank Name</th>
                                    <td>{{isset($getdriver->BankDetail->bank_name) ? $getdriver->BankDetail->bank_name:'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Branch Name</th>
                                    <td>{{isset($getdriver->BankDetail->branch_name) ? $getdriver->BankDetail->branch_name:'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">IFSC</th>
                                    <td>{{isset($getdriver->BankDetail->ifsc) ? $getdriver->BankDetail->ifsc:'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Account No</th>
                                    <td>{{isset($getdriver->BankDetail->account_number) ? $getdriver->BankDetail->account_number:'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Account Holder Name</th>
                                    <td>{{isset($getdriver->BankDetail->account_holdername) ? $getdriver->BankDetail->account_holdername:'-'}}</td>
                                </tr> -->

                            </tbody>
                        </table>  
                        <a href="{{url($prefix.'/drivers/'.Crypt::encrypt($getdriver->id).'/edit')}}" class="btn btn-primary my-3" style="background:#fff;" title="Edit Driver"><i class="fa fa-edit m-0"></i> Edit</a>
                        <a class="btn btn-primary" href="{{url($prefix.'/drivers') }}"> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
