<!-- /////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="add_prsamount" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="/assets/images/close-bottle.png" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title">Add Purchase Price</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form id="purchase_amt_form">
                    <input type="hidden" class="form-control" id="drs_num" name="drs_no" value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Purchase Price</label>
                            <input type="text" class="form-control" id="purchse" name="purchase_price" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle Type</label>
                            <select class="form-control my-select2" id="vehicle_type" name="vehicle_type" tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach($vehicletype as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="btn-section w-100 P-0">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a type="" class="btn btn-modal" data-dismiss="modal">Cancel</a>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>