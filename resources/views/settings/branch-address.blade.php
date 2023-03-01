@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Update Consigner</h5></div> -->

                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/settings/branch-address')}}"
                            id="createbranchadd" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="branchadd_id" value="{{$branchaddvalue->id}}">
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Name<span class="text-danger">*</span></label>
                                <input class="form-control" name="name" id="name" placeholder=""
                                    value="{{old('name',isset($branchaddvalue->name)?$branchaddvalue->name:'')}}"
                                    disabled>
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Email Address<span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder=""
                                    value="{{old('email',isset($branchaddvalue->email)?$branchaddvalue->email:'')}}"
                                    disabled>
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Phone<span class="text-danger">*</span></label>
                                <input type="text" class="form-control mbCheckNm" name="phone" id="phone" placeholder=""
                                    value="{{old('phone',isset($branchaddvalue->phone)?$branchaddvalue->phone:'')}}"
                                    maxlength="10" disabled>
                            </div>



                            <button style="display:none;" type="submit" name=""
                                class="mt-4 mb-4 btn btn-primary submitBtn">Submit</button>
                            <a href="javascript:void(0)" class="btn btn-primary editBranchadd" title="Edit Meta Value">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                            <!-- <a class="btn btn-primary" href="{{url($prefix.'/users') }}"> Back</a> -->
                        </form>
                    </div>
                    <div class="statbox widget box box-shadow">
                        <div class="btn-group relative">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter" style="font-size: 11px;">
                                Add New Address
                            </button>
                        </div>
                        <h4>GST Registered Address of Eternity Forwarders</h4>
                        <table id="unverified-table" class="table table-hover" style="width:100%">

                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Branch Nick name</th>
                                    <th>GSTN No</th>
                                    <th>State</th>
                                    <th>Address</th>
                                    <th>Tagged Branches</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i = 0;
                                ?>
                                @foreach($gstaddresses as $address)
                                <?php
                                    $i++;
                                ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$address->Branch->nick_name}}</td>
                                    <td>{{$address->gst_no}}</td>
                                    <td>{{$address->state}}</td>
                                    <td>{{$address->address_line_1}}</td>
                                    <td>{{$address->Branch->name}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add New Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="new_gst_address">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">GST NO.</label>
                            <input type="text" class="form-control" name="gst_no">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Branch Nick Name</label>
                            <select class="form-control  my-select2" id="branch_id" name="branch_id"
                                        tabindex="-1">
                                        <option selected disabled>Select..</option>
                                        @foreach($branchs as $branch)
                                        <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                        @endforeach
                                    </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">State</label>
                            <select class="form-control  my-select2" name="state"
                                        tabindex="-1">
                                        <option selected disabled>Select..</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->name }}">{{ucwords($state->name)}}</option>
                                        @endforeach
                                    </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Address Line 1</label>
                            <input type="text" class="form-control" name="address_line_1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Address Line 2</label>
                            <input type="text" class="form-control" name="address_line_2">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Upload GST RC</label>
                            <input type="file" class="form-control" name="upload_gst">
                        </div>
                    </div>
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="crt_pytm"><span class="indicator-label">Submit</span>
                            <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
$('#new_gst_address').submit(function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    $.ajax({
        url: "add-gst-address",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function() {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal('success', data.success_message, 'success');
                window.location.reload();
            } else if (data.validation === false){
                swal("error", data.error_message.gst_no[0], "error");
            } else{
                swal('error', data.error_message, 'error');
            }

        }
    });
});

</script>
@endsection