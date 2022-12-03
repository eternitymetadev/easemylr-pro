@extends('layouts.main')
@section('content')
    <style>
        .form-row {
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 0 3px #83838360;
            margin-bottom: 1rem;

        }

        .form-row h6 {
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .form-control {
            padding: 8px 10px;
        }

        .radioInputBox {
            display: flex;
            width: 100%;
            background: #f7f7f7;
            height: 46px;
            padding: 0 1rem;
            border-radius: 8px;
        }

        #vendor-master span.select2 {
            margin-bottom: 0 !important;
        }

        .select2-results__options {
            list-style: none;
            margin: 0;
            padding: 0;
            height: 160px;
            /* scroll-margin: 38px; */
            overflow: auto;
        }
    </style>

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Create Vendor</h2>
        </div>


        <div class="widget-content br-6">
            <div class="col-lg-12 col-12 layout-spacing">
                <form id="vendor-master">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Vendor Name<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vendor_name" name="name" placeholder=""/>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleFormControlSelect1">Vendor Type<span
                                    class="text-danger">*</span></label>
                            <select class="form-control" id="vendor_type" name="vendor_type"
                                    tabindex="-1">
                                <option selected disabled>Select</option>
                                <option value="Individual">Individual</option>
                                <option value="Proprietorship">Proprietorship</option>
                                <option value="Company">Company</option>
                                <option value="Firm">Firm</option>
                                <option value="HUF">HUF</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleFormControlSelect1">Branch Location</label>
                            <select class="form-control  my-select2" id="branch_id" name="branch_id" tabindex="-1">
                                @foreach($branchs as $branch)
                                    <option value="{{ $branch->id }}">{{ucwords($branch->name)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Transporter Name</label>
                            <input type="text" class="form-control" id="transporter_name" name="transporter_name"
                                   placeholder=""/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Driver </label>
                            <select class="form-control  my-select2" id="driver_id" name="driver_id" tabindex="-1">
                                <option value="">Select driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                            '-'}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="exampleFormControlInput2">Pan<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pan_no" name="pan" placeholder=""
                                   maxlength="10">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleFormControlInput2">Pan Upload</label>
                            <input type="file" class="form-control" name="pan_upload" placeholder="">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleFormControlInput2">GST</label>
                            <select class="form-control  my-select2" id="gst_register" name="gst_register"
                                    tabindex="-1">
                                <option value="Unregistered">Unregistered</option>
                                <option value="Registered">Registered</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleFormControlInput2">Gst No</label>
                            <input type="text" class="form-control" id="gst_no" name="gst_no" placeholder=""
                                   maxlength="15" disabled>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="exampleFormControlSelect1">Declaration Available<span
                                    class="text-danger">*</span></label>
                            <div class="radioInputBox">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input no_decl" type="radio"
                                           name="decalaration_available" id="cds" value="1"/>
                                    <label class="form-check-label" for="cds">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input no_decl" type="radio"
                                           name="decalaration_available" id="decalarationNo" value="2" checked/>
                                    <label class="form-check-label" for="decalarationNo">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="declaration_file">Declaration</label>
                            <input type="file" class="form-control" id="declaration_file" name="declaration_file"
                                   placeholder=""/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Applicable TDS Rate</label>
                            <input type="text" class="form-control" id="tds_rate" name="tds_rate" placeholder=""
                                   readonly/>
                        </div>

                    </div>

                    <div class="form-row">
                        <h6 class="col-12">Contact details</h6>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlSelect1">Contact Number</label>
                            <input type="number" class="form-control" name="contact_person_number"
                                   placeholder="" maxlength="10">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlSelect1">Contact Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="">
                        </div>
                    </div>

                    <div class="form-row">
                        <h6 class="col-12">Vendor NEFT details</h6>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Account Holder Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="acc_holder_name"
                                   name="acc_holder_name" placeholder="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name" placeholder="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Branch Name</label>
                            <input type="text" class="form-control" name="branch_name" placeholder="">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="exampleFormControlSelect1">Account No.<span
                                    class="text-danger">*</span><span class="ml-1" style="color:red;" id="acc_no_error"></span></label>
                            <input type="number" class="form-control" id="account_no" name="account_no"
                                   placeholder="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Ifsc Code<span
                                    class="text-danger">*</span><span class="ml-1" style="color:red;" id="ifsc-error"></span></label>
                            <input type="text" class="form-control" id="ifsc" name="ifsc_code"
                                   placeholder="" maxlength="11">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleFormControlInput2">Cancel Cheaque</label>
                            <input type="file" class="form-control" name="cancel_cheaque" placeholder="">
                        </div>
                    </div>

                    <button type="submit" style="width: 120px; float: right;" class="mt-4 mb-4 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        jQuery(function () {
            $('.my-select2').each(function () {
                $(this).select2({
                    theme: "bootstrap-5",
                    dropdownParent: $(this).parent(), // fix select2 search input focus bug
                })
            })

            // fix select2 bootstrap modal scroll bug
            $(document).on('select2:close', '.my-select2', function (e) {
                var evt = "scroll.select2"
                $(e.target).parents().off(evt)
                $(window).off(evt)
            })
        })

        $('#vendor_type').change(function () {
            var v_typ = $(this).val();
            var declaration = ($('input[name=decalaration_available]:checked').val());
            if (declaration == '2') {
                if (v_typ == 'Individual') {
                    $('#tds_rate').val('1');
                } else if (v_typ == 'Proprietorship') {
                    $('#tds_rate').val('1');
                } else if (v_typ == 'Company') {
                    $('#tds_rate').val('2');
                } else if (v_typ == 'Firm') {
                    $('#tds_rate').val('2');
                } else if (v_typ == 'HUF') {
                    $('#tds_rate').val('2');
                }
            } else {
                $('#tds_rate').val('0');
            }

        });

        $('.no_decl').on('change', function () {
            var declaration = ($('input[name=decalaration_available]:checked').val());
            if (declaration == 1) {
                $('#tds_rate').val('0');
            } else if (declaration == 2) {
                $('#vendor_type').val('');
                $('#tds_rate').val('');
            }
        });

        $('#gst_register').on('change', function () {

            var g_typ = $(this).val();
            if (g_typ == 'Registered') {
                $("#gst_no").prop('disabled', false);
            } else {
                $("#gst_no").prop('disabled', true);
                $("#gst_no").val('');
            }
        });

        $('#account_no').blur(function () {

            var acc_no = $(this).val();
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "check-account-no",
                method: "POST",
                data: {
                    acc_no: acc_no,
                    _token: _token
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (result) {
                    if (result.success == false) {
                        $('#acc_no_error').html('Please enter 11 digit number')
                        // swal('Error', result.success_message, 'error')
                        $('#account_no').val('');
                    }

                }
            })
        });

        $('#ifsc').blur(function () {
            var ifsc = $(this).val();
            var count = ifsc.length;
            if (count < 11) {
                $('#ifsc-error').html('Please enter 11 digit number')
            } else {
                $('#ifsc-error').empty();
            }

        });
    </script>


@endsection
