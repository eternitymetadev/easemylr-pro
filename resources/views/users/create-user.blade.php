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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Create User</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Create User</h5></div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <form class="general_form" method="POST" action="{{url($prefix.'/users')}}" id="createuser">
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Login ID<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="login_id" id="login_id" placeholder="Login ID">
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Email Address<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Password<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="password" id="password" placeholder="Password">
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlInput2">Phone</label>
                                <input type="text" class="form-control mbCheckNm" name="phone" id="phone" placeholder=""  maxlength="10">
                            </div>
                            <div class="form-group mb-4">
                                <label for="exampleFormControlSelect1">Select Role</label>
                                <select name="role_id" class="form-control" id="role_id">
                                    <option value="">Select</option>
                                    <?php 
                                    if(count($getroles)>0) {
                                        foreach ($getroles as $key => $getrole) {  
                                    ?> 
                                    <option value="{{ $getrole->id }}">{{ucwords($getrole->name)}}</option> 
                                    <?php 
                                    }
                                }
                                ?>                            
                                </select>
                            </div>
                            <div class="form-group mb-4 singleLocation">
                                <label for="exampleFormControlSelect1">Select Location<span class="text-danger">*</span></label>
                                <select class="form-control" id="branch_id" name="branch_id[]">
                                    <option value="">Select</option>
                                    <?php 
                                    if(count($branches)>0) {
                                        foreach ($branches as $key => $branch) {
                                    ?>
                                        <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mb-4 multiLocation" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Location<span class="text-danger">*</span></label>
                                <select class="form-control tagging" multiple="multiple" name="branch_id[]">
                                    <option value="">Select</option>
                                    <?php 
                                    if(count($branches)>0) {
                                        foreach ($branches as $key => $branch) {
                                    ?>
                                        <option value="{{ $key }}">{{ucwords($branch)}}</option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mb-4 selectClient" style="display: none;">
                                <label for="exampleFormControlSelect1">Select Regional Clients<span class="text-danger">*</span></label>
                                <select class="form-control tagging" multiple="multiple" name="regionalclient_id[]" id="select_regclient">
                                    <option value="">Select</option>
                                    
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <hr class="brown-border">
                                <h4 class="mt-3 mb-3">Permissions</h4>
                                <div class="checkbox selectAll">
                                    <label class="check-label">Select All
                                        <input id="ckbCheckAll" type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="permis">
                                    <div class="row">
                                        <?php 
                                        if(count($getpermissions)>0) {
                                            foreach ($getpermissions as $key => $getpermission) {  
                                        ?> 
                                        <div class="col-lg-2 mt-2">
                                            <div class="checkbox">
                                                <label class="check-label">{{ucfirst($getpermission->name)}}
                                                    <input type="checkbox" name="permisssion_id[]" value="{{ $getpermission->id }}" class="chkBoxClass">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                            <?php 
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="mt-4 mb-4 btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{url($prefix.'/users') }}"> Back</a>
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
//

$('#role_id').change(function() {
    var role_id = $(this).val();
    var checkbox = $('.chkBoxClass').val();
    
    if(role_id == 1) {            //role_id = 1 for Admin
        $('.multiLocation').hide();
        $('.singleLocation').show();

        $('.selectClient').hide();

        $('#ckbCheckAll').attr('checked',true);
        $('.chkBoxClass[value="1"]').prop('checked', true)
        $('.chkBoxClass[value="2"]').prop('checked', true)
        $('.chkBoxClass').prop('checked', true);
    }
    else if(role_id == 2) {     //role_id = 2 for Branch Manager
        $('#ckbCheckAll').attr('checked', false);
        $('.chkBoxClass').prop('checked', true)
        $('.chkBoxClass[value="1"]').prop('checked', false)
        $('.chkBoxClass[value="2"]').prop('checked', false)

        $('.multiLocation').hide();
        $('.singleLocation').show();
        $('.selectClient').hide();

    }else if(role_id == '') {
        $('#ckbCheckAll').attr('checked', false);
        $('.chkBoxClass').prop('checked', false)
    }else if(role_id == 3) {            //role_id = 3 for regional manager
        $('#ckbCheckAll').attr('checked', false);
        $('.chkBoxClass').prop('checked', true)
        $('.chkBoxClass[value="1"]').prop('checked', false)
        $('.chkBoxClass[value="2"]').prop('checked', false)

        $('.multiLocation').show();
        $('.singleLocation').hide();
        $('.selectClient').hide();
       
    }else if(role_id == 4) {            //role_id = 4 for branch User
        $('#ckbCheckAll').attr('checked', false);
        $('.chkBoxClass').prop('checked', true)
        $('.chkBoxClass[value="1"]').prop('checked', false)
        $('.chkBoxClass[value="2"]').prop('checked', false)
        
        $('.selectClient').show();
        $('.singleLocation').show();
        $('.multiLocation').hide();
    }else if(role_id == 7) {            //role_id = 7 for client user
        $('#ckbCheckAll').attr('checked', false);
        $('.chkBoxClass').prop('checked', false)
        $('.chkBoxClass[value="7"]').prop('checked', true)

        $('.multiLocation').show();
        $('.singleLocation').hide();
        $('.selectClient').hide();
       
    }else{
        $('.multiLocation').hide();
        $('.singleLocation').show();
        $('.selectClient').hide();

        $('#ckbCheckAll').attr('checked', false);
        $('.chkBoxClass').prop('checked', true)
        $('.chkBoxClass[value="1"]').prop('checked', false)
        $('.chkBoxClass[value="2"]').prop('checked', false)
    }
});

$('#branch_id').change(function() {
    $('#select_regclient').empty();
    let branch_id = $(this).val();
        $.ajax({
            type      : 'get',
            url       : APP_URL+'/get_regclients',
            data      : {branch_id:branch_id},
            headers   : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType  : 'json',
            success:function(res){
                console.log(res);
                if(res.data){
                    $('#select_regclient').append('<option value="">Select regional client</option>');
                    $.each(res.data,function(key,value){
                        $('#select_regclient').append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                }
            }
        });

});

</script>
@endsection
