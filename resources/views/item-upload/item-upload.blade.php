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
        <h2 class="pageHeading">Consignor Products</h2>
        <div class="d-flex align-content-center" style="gap: 1rem;">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#manual_item">
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
                Products
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

                    <th scope="col">Manufacturer</th>
                    <th scope="col">Brand Name</th>
                    <th scope="col">Technical Formula</th>
                    <th scope="col">Net Weight</th>
                    <th scope="col">Gross Weight</th>
                    <th scope="col">Chargable Weight</th>
                    <th scope="col">Erp Mat Code</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>

                    <td>{{$item->manufacturer}}</td>
                    <td>{{$item->brand_name}}</td>
                    <td>{{$item->technical_formula}}</td>
                    <td>{{$item->net_weight}}</td>
                    <td>{{$item->gross_weight}}</td>
                    <td>{{$item->chargable_weight}}</td>
                    <td>{{$item->erp_mat_code}}</td>
                    <td><a id="editConsignerIcon" href="#" class="edit editIcon editItems" data-id="{{$item->id}}"><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg></a></td>
                </tr>
                @endforeach

            </tbody>
        </table>


        {{--modal for technical master upload--}}
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form id="item_master" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload Products</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="formGroupExampleInput">Excel File*</label>
                            <input required type="file" class="form-control form-control-sm" id="formGroupExampleInput"
                                name="item_file" placeholder="Example input">
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

        {{--modal for item add form--}}
        <div class="modal fade" id="manual_item" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px">
                <form id="add_item_form" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload Products</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Manufacturer*</label>
                                <input required type="text" class="form-control form-control-sm"
                                    id="formGroupExampleInput" name="manufacturer" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Brand Name*</label>
                                <input required class="form-control form-control-sm" id="formGroupExampleInput"
                                    name="brand_name" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Technical Formula*</label>
                                <input required class="form-control form-control-sm" id="formGroupExampleInput"
                                    name="technical_formula" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Net Weight*</label>
                                <input required class="form-control form-control-sm" id="formGroupExampleInput"
                                    name="net_weight" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Gross Weight*</label>
                                <input required class="form-control form-control-sm" id="formGroupExampleInput"
                                    name="gross_weight" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Chargable Weight*</label>
                                <input required class="form-control form-control-sm" id="formGroupExampleInput"
                                    name="chargable_weight" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Erp Mat Code*</label>
                                <input required class="form-control form-control-sm" id="formGroupExampleInput"
                                    name="erp_mat_code" placeholder="Example input">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><span class="indicator-label">Submit</span>
                            <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit modal for item add form--}}
        <div class="modal fade" id="edit_item_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px">
                <form id="update_item_form" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload Products</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="item_id" value="">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Manufacturer*</label>
                                <input required type="text" class="form-control form-control-sm" id="edit_manufacturer"
                                    name="manufacturer" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Brand Name*</label>
                                <input required class="form-control form-control-sm" id="edit_brand_name"
                                    name="brand_name" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Technical Formula*</label>
                                <input required class="form-control form-control-sm" id="edit_technical_formula"
                                    name="technical_formula" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Net Weight*</label>
                                <input required class="form-control form-control-sm" id="edit_net_weight"
                                    name="net_weight" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Gross Weight*</label>
                                <input required class="form-control form-control-sm" id="edit_gross_weight"
                                    name="gross_weight" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Chargable Weight*</label>
                                <input required class="form-control form-control-sm" id="edit_chargable_weight"
                                    name="chargable_weight" placeholder="Example input">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formGroupExampleInput">Erp Mat Code*</label>
                                <input required class="form-control form-control-sm" id="edit_erp_mat_code"
                                    name="erp_mat_code" placeholder="Example input">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><span class="indicator-label">Update</span>
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
    $("#add_item_form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "add-items-name",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
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
                    swal("success!", data.success_message, "success");
                    window.location.href = "item-view";
                } else {
                    swal("error", data.error_message, "error");
                }
            },
        });
    });
    //
    jQuery(document).on('click', '.editItems', function(event) {
        event.preventDefault();
        var item_id = $(this).attr('data-id');
        // alert(item_id);
        $('#edit_item_model').modal('show');
        $.ajax({
            type: "GET",
            url: "edit-items-name",
            data: {
                item_id: item_id
            },
            beforeSend: //reinitialize Datatables
                function() {
                    $('#edit_manufacturer').empty()
                    $('#edit_brand_name').empty()
                    $('#edit_technical_formula').empty()
                    $('#edit_net_weight').empty()
                    $('#edit_gross_weight').empty()
                    $('#edit_chargable_weight').empty()
                    $('#edit_erp_mat_code').empty()
                },
            success: function(data) {
                $('#edit_manufacturer').val(data.getItem.manufacturer)
                $('#edit_brand_name').val(data.getItem.brand_name)
                $('#edit_technical_formula').val(data.getItem.technical_formula)
                $('#edit_net_weight').val(data.getItem.net_weight)
                $('#edit_gross_weight').val(data.getItem.gross_weight)
                $('#edit_chargable_weight').val(data.getItem.chargable_weight)
                $('#edit_erp_mat_code').val(data.getItem.erp_mat_code)
                $('#item_id').val(data.getItem.id)
            }
        });
    });
    //
    $("#update_item_form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "update-items-name",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {},
            success: (data) => {
                if (data.success == true) {
                    swal("success", data.success_message, "success");
                    window.location.reload();
                } else {
                    swal("error", data.error_message, "error");
                }
            },
        });
    });
    </script>
    ////
    @endsection