@extends('layouts.main')
@section('content')
    <style>
        .widget-four .widget-content .w-summary-info .summary-count {
            display: block;
            /* font-size: 16px; */
            margin-top: 4px;
            font-weight: 600;
            color: #515365;
            background: #03a9f4 ! important;

        }

        .widget-four .widget-content .w-summary-info h6 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 0;
            color: #fbfbfc;
        }

        .widget-four .widget-content .summary-list:nth-child(1) .w-icon svg {
            color: #ffffff;
            /* fill: rgb(255 255 255 / 16%); */
        }

        .widget-four .widget-content .w-icon {
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            width: 50px;
            margin-right: 12px;
        }

    </style>

    <div class="layout-px-spacing">

        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Technical Master</h2>
            <div class="d-flex align-content-center" style="gap: 1rem;">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#manual_form">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-upload-cloud mr-1">
                        <polyline points="16 16 12 12 8 16"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                        <polyline points="16 16 12 12 8 16"></polyline>
                    </svg>
                    Form
                </button>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-upload-cloud mr-1">
                        <polyline points="16 16 12 12 8 16"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                        <polyline points="16 16 12 12 8 16"></polyline>
                    </svg>
                    Master
                </button>
                <button type="button" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-download-cloud">
                        <polyline points="8 17 12 21 16 17"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                    </svg>
                    Excel
                </button>
            </div>
        </div>

        <div>
            <table class="table table-sm">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">UN ID</th>
                    <th scope="col">Technical Name</th>
                    <th scope="col">Brand Name</th>

                </tr>
                </thead>
                <tbody>
                    @foreach($technicals as $technical)
                    <?php
                       $formula_array = array();
                      foreach($technical->TechnicalFormula as $formula){
                           $formula_array[] = $formula->brand_name;
 
                      }
                        $brand_name = implode(',', $formula_array);

                    ?>
                <tr>
                    <th scope="row">{{$technical->id}}</th>
                    <td>{{$technical->technical_formula}}</td>
                    <td>{{$brand_name}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>


            {{--modal for product upload--}}
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form id="upload_techical" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Upload Technical Master</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="formGroupExampleInput">Excel File*</label>
                                <input required type="file" class="form-control form-control-sm" name="technical_file"
                                       id="formGroupExampleInput" placeholder="Example input">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><span class="indicator-label">Upload</span>
                        <span class="indicator-progress" style="display: none;">Please wait...
            	        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button> 
                        </div>
                    </form>
                </div>
            </div>

            {{--modal for Manual Form--}}
            <div class="modal fade" id="manual_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form id="technical_manual_form" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Upload Technical Master</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="formGroupExampleInput">Technical Name*</label>
                                <input required type="text" class="form-control form-control-sm" name="technical_name"
                                       id="formGroupExampleInput" placeholder="Example input">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><span class="indicator-label">Upload</span>
                        <span class="indicator-progress" style="display: none;">Please wait...
            	        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button> 
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
@section('js')
<script>
$("#technical_manual_form").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "add-technical-name",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal("success!", data.success_message, "success");
                window.location.href = "technical-master";
            }else if(data.success_message == false){
                swal("error!", data.error_message, "error");
            } else {
                swal("error", data.error_message, "error");
            }
        },
    });
});
</script>
////
@endsection
