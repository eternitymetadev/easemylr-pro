<p class="totalcount">Total Count: <span class="reportcount">{{$consignments->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>LR No</th>
                <th>LR Date</th>
                <th>LR Status</th>
                <th>Delivery Status</th>
                <th>Delivery Date</th>
                <th>Delivery Mode</th>
                <th>Order No</th>
                <th>Base Client</th>
                <th>Regional Client</th>
                <th>Invoice No</th>
                <th>POD</th>
                <th>Images</th>


            </tr>
        </thead>
        <tbody>
            @if(count($consignments)>0)
            @foreach($consignments as $consignment)
            <?php
$start_date = strtotime($consignment->consignment_date);
$end_date = strtotime($consignment->delivery_date);
$tat = ($end_date - $start_date) / 60 / 60 / 24;
?>
            <tr>
                <td>{{ $consignment->id ?? "-" }}</td>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->consignment_date ?? "-" )}}</td>
                <?php
if ($consignment->status == 0) {?>
                <td>Cancel</td>
                <?php } elseif ($consignment->status == 1) {?>
                <td>Active</td>
                <?php } elseif ($consignment->status == 2) {?>
                <td>Unverified</td>
                <?php }?>
                <!-- delivery status -->
                <?php
if ($consignment->delivery_status == 'Assigned') {?>
                <td>Assigned</td>
                <?php } elseif ($consignment->delivery_status == 'Unassigned') {?>
                <td>Unassigned</td>
                <?php } elseif ($consignment->delivery_status == 'Started') {?>
                <td>Started</td>
                <?php } elseif ($consignment->delivery_status == 'Successful') {?>
                <td>Successful</td>
                <?php } elseif ($consignment->delivery_status == 'Cancel') {?>
                <td>Cancel</td>
                <?php } else {?>
                <td>Unknown</td>
                <?php }?>
                <td>{{ Helper::ShowDayMonthYearslash($consignment->delivery_date )}}</td>
                <?php if ($consignment->job_id == '') {?>
                <td>Manual</td>
                <?php } else {?>
                <td>Shadow</td>
                <?php }?>

                <?php if (empty($consignment->order_id)) {
    if (!empty($consignment->ConsignmentItems)) {
        $order = array();
        $invoices = array();
        $inv_date = array();
        $inv_amt = array();
        foreach ($consignment->ConsignmentItems as $orders) {

            $order[] = $orders->order_id;
            $invoices[] = $orders->invoice_no;
            $inv_date[] = Helper::ShowDayMonthYearslash($orders->invoice_date);
            $inv_amt[] = $orders->invoice_amount;
        }
        //echo'<pre>'; print_r($order); die;
        $order_item['orders'] = implode(',', $order);
        $order_item['invoices'] = implode(',', $invoices);
        $invoice['date'] = implode(',', $inv_date);
        $invoice['amt'] = implode(',', $inv_amt);?>

                <td>{{ $orders->order_id ?? "-" }}</td>

                <?php } else {?>
                <td>-</td>
                <?php }} else {?>
                <td>{{ $consignment->order_id ?? "-" }}</td>
                <?php }?>
                <td>{{ $consignment->ConsignerDetail->GetRegClient->BaseClient->client_name ?? "-" }}</td>
                <td>{{ $consignment->ConsignerDetail->GetRegClient->name ?? "-" }}</td>
                <?php if (empty($consignment->invoice_no)) {?>
                <td>{{ $order_item['invoices'] ?? "-" }}</td>
                <?php } else {?>
                <td>{{ $consignment->invoice_no ?? "-" }}</td>
                <?php }?>
                <!--  -->
                <?php if (empty($consignment->job_id)) {
    if (empty($consignment->signed_drs)) {
        ?>
                <td>Not Available</td>
                <?php } else {?>
                <td>Avliable</td>
                <?php }?>
                <?php } else {
    $job = DB::table('jobs')->where('job_id', $consignment->job_id)->orderBy('id', 'desc')->first();

    if (!empty($job->response_data)) {
        $trail_decorator = json_decode($job->response_data);
        $img_group = array();
        foreach ($trail_decorator->task_history as $task_img) {
            if ($task_img->type == 'image_added') {
                $img_group[] = $task_img->description;
            }
        }
        if (empty($img_group)) {?>
                <td>Not Available</td>
                <?php } else {?>
                <td>Available</td>
                <?php }
    }
    ?>
                <?php }if (empty($consignment->job_id)) {
    $img = URL::to('/drs/image/' . $consignment->signed_drs)?>
                <td style="max-width: 260px">
                    <?php if (!empty($consignment->signed_drs)) {?>
                    <div class="d-flex flex-wrap" style="gap: 4px; width: 220px;">
                        <img src="{{$img}}" class="viewImageInNewTab"
                            style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;" />
                    </div>
                    <?php } else {?>
                    -
                    <?php }?>
                </td>
                <?php } else {?>
                <td style="max-width: 260px">
                  <?php if(!empty($img_group)){ ?>
                    <div class="d-flex flex-wrap" style="gap: 4px; width: 220px;">
                        @foreach($img_group as $img)
                        <img src="{{$img}}" class="viewImageInNewTab" data-toggle="modal" data-target="#exampleModal"
                            style="width: 100%; height: 100%; max-width: 98px; max-height: 50px; border-radius: 4px; cursor: pointer; box-shadow: 0 0 2px #838383fa;" />
                        @endforeach
                    </div>
                    <?php }else{?>
                    -
                    <?php }?>
                </td>
                <?php }?>

            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="12" class="text-center">No Record Found </td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="perpage container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-8 col-xl-9">
            </div>
            <div class="col-md-12 col-lg-4 col-xl-3">
                <div class="form-group mt-3 brown-select">
                    <div class="row">
                        <div class="col-md-6 pr-0">
                            <label class=" mb-0">items per page</label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control report_perpage" data-action="<?php echo url()->current(); ?>">
                                <option value="10" {{$peritem == '10' ? 'selected' : ''}}>10</option>
                                <option value="50" {{$peritem == '50' ? 'selected' : ''}}>50</option>
                                <option value="100" {{$peritem == '100'? 'selected' : ''}}>100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ml-auto mr-auto">
        <nav class="navigation2 text-center" aria-label="Page navigation">
            {{$consignments->appends(request()->query())->links()}}
        </nav>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: max-content">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="#" id="toggledImageView" style="max-height: 90vh; max-width: 90vw" />
                </div>
            </div>
        </div>
    </div>
</div>