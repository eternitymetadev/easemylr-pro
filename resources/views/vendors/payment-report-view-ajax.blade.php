<p class="totalcount">Total Count: <span class="reportcount">{{$payment_lists->total()}}</span></p>
<div class="custom-table">
    <table id="" class="table table-hover" style="width:100%">
    <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Transaction id</th>
                        <th>Date</th> 
                        <th>Client</th>
                        <th>Depot</th>
                        <th>Station</th>
                        <th>Drs No</th>
                        <th>LR No</th>
                        <th>Invoice No</th>
                        <th>Type of Vehicle </th>
                        <th>No. of Cartons</th>
                        <th>Net Weight</th>
                        <th>Gross weight</th>
                        <th>Truck No.</th>
                        <th>Vendor Name</th>
                        <th>Vendor Type</th>
                        <th>Declaration</th>
                        <th>TDS Rate</th>
                        <th>Bank Name</th>
                        <th>Account No</th>
                        <th>IFSC Code</th>
                        <th>Vendor Pan</th>
                        <th>Purchase Freight</th>
                        <th>Paid Amount</th>
                        <th>Tds Amount</th>
                        <th>Balance Due</th>
                        <th>Advance</th>
                        <th>Balance Amount</th>
                        <th>Payment Date</th>
                        <th>UTR No</th>
                        <th>Balance Amount</th>
                        <th>Tds Deduct</th>
                        <th>Payment Date</th>
                        <th>UTR No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;?>
                    @foreach($payment_lists as $payment_list)
                    <?php
                    $i++;
                    $bankdetails = json_decode($payment_list->PaymentRequest[0]->VendorDetails->bank_details);
                        
                        $date = date('d-m-Y',strtotime($payment_list->created_at));
                        $lr_arra = array();
                        $consigneecity = array();
                        $itm_arra = array();
                        $qty = array();
                        $totlwt = array();
                        $grosswt = array();
                        $drsvehicel = array();
                        $vel_type = array();
                        $regn_clt = array();
                        foreach($payment_list->PaymentRequest as $lr_no){

                            $drsvehicel[] = $lr_no->vehicle_no;
                            $qty[] = Helper::totalQuantity($lr_no->drs_no);
                            $totlwt[] = Helper::totalWeight($lr_no->drs_no);
                            $grosswt[] = Helper::totalGrossWeight($lr_no->drs_no);
                            foreach($lr_no->TransactionDetails as $lr_group){
                                $lr_arra[] = $lr_group->consignment_no;
                               $consigneecity[] = @$lr_group->ConsignmentNote->ShiptoDetail->city;
                               $vel_type[] = @$lr_group->ConsignmentNote->vehicletype->name;
                               $regn_clt[] = @$lr_group->ConsignmentNote->RegClient->name;
                            } 
                            
                            foreach($lr_group->ConsignmentNote->ConsignmentItems as $lr_no_item){
                                $itm_arra[] = $lr_no_item->invoice_no;
                            }
                        }
                        $csd = array_unique($vel_type);
                        $group_vehicle_type = implode('/',$csd);
                        $group_vehicle = implode('/',$drsvehicel);
                        // $ttqty = implode('/', $qty);
                        $totalqty = array_sum($qty);
                        $groupwt = array_sum($totlwt);
                        $groupgross = array_sum($grosswt);
                        // $groupwt = implode('/', $totlwt);
                        // $groupgross = implode('/', $grosswt);
                        $city = implode('/', $consigneecity);
                        $multilr = implode('/', $lr_arra);
                        $lr_itm = implode('/', $itm_arra);

                        $unique_regn = array_unique($regn_clt);
                        $regn = implode('/', $unique_regn);

                        if($payment_list->PaymentRequest[0]->VendorDetails->declaration_available == 1){
                            $decl = 'Yes';
                        }else{
                            $decl = 'No';
                        }

                        $exp_drs = explode(',',$payment_list->drs_no);
                        $exp_arra = array();
                        foreach($exp_drs as $exp){
                             $exp_arra[] = 'DRS-'.$exp;
                        }
                        $newDrs = implode(',',$exp_arra);

                        $trans_id = DB::table('payment_histories')->where('transaction_id', $payment_list->transaction_id)->get();
                        $histrycount = count($trans_id);
                        if($histrycount > 1){
                           $paid_amt = $trans_id[0]->tds_deduct_balance + $trans_id[1]->tds_deduct_balance ;
                           $curr_paid_amt = $trans_id[1]->current_paid_amt;
                           $paymt_date_2 = $trans_id[1]->payment_date;
                           $ref_no_2 = $trans_id[1]->bank_refrence_no;
                           $tds_amt = $payment_list->PaymentRequest[0]->total_amount - $paid_amt ;

                           $sumof_paid_tds = $paid_amt + $tds_amt ;
                           $balance_due =  $payment_list->PaymentRequest[0]->total_amount - $sumof_paid_tds ;

                        }else{
                            $paid_amt = $trans_id[0]->tds_deduct_balance ;
                            $curr_paid_amt = '';
                            $paymt_date_2 = '';
                            $ref_no_2 = '';
                            if($payment_list->payment_type == 'Balance'){
                                $tds_amt =  $payment_list->balance - $payment_list->tds_deduct_balance ;
                            }else{
                            $tds_amt =  $payment_list->advance - $payment_list->tds_deduct_balance ;
                            }
                            $sumof_paid_tds = $paid_amt + $tds_amt ;
                            $balance_due =  $payment_list->PaymentRequest[0]->total_amount - $sumof_paid_tds ;
                        }

                        
                    ?>

                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$payment_list->transaction_id ?? '-'}}</td>
                        <td>{{$date}}</td>
                        <td>{{$regn ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->Branch->nick_name ?? '-'}}</td>
                        <td>{{$city ?? '-'}}</td>
                        <td>{{$newDrs ?? '-'}}</td>
                        <td>{{$multilr ?? '-'}}</td>
                        <td>{{$lr_itm ?? '-'}}</td>
                        <td>{{$group_vehicle_type ?? '-'}}</td>
                        <td>{{$totalqty}}</td>
                        <td>{{$groupwt}}</td>
                        <td>{{$groupgross}}</td>
                        <td>{{$group_vehicle}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->name ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->vendor_type ?? '-'}}</td>
                        <td>{{$decl}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->tds_rate ?? '-'}}</td>
                        <td>{{$bankdetails->bank_name ?? '-'}}</td>
                        <td>{{$bankdetails->account_no ?? '-'}}</td>
                        <td>{{$bankdetails->ifsc_code ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->VendorDetails->pan ?? '-'}}</td>
                        <td>{{$payment_list->PaymentRequest[0]->total_amount ?? '-'}}</td>
                        <td>{{$paid_amt}}</td>
                        <td>{{$tds_amt}}</td>
                        <td>{{$balance_due}}</td>
                        <?php if($payment_list->payment_type == 'Balance'){ ?>
                        <td>{{$payment_list->tds_deduct_balance ?? '-'}}</td>
                        <?php }else{ ?>
                        <td>{{$payment_list->tds_deduct_balance ?? '-'}}</td>
                        <?php } $tds_cut = $payment_list->current_paid_amt - $payment_list->tds_deduct_balance?>
                        <td>{{$tds_cut}}</td>
                        <td>{{$payment_list->payment_date ?? '-'}}</td>
                        <td>{{$payment_list->bank_refrence_no ?? '-'}}</td>
                        <?php
                        $trans_id = DB::table('payment_histories')->where('transaction_id', $payment_list->transaction_id)->get();
                        $histrycount = count($trans_id);
                        if($histrycount > 1){
                            $tds_cut1 = $trans_id[1]->current_paid_amt - $trans_id[1]->tds_deduct_balance ;
                        ?>
                        <td>{{$trans_id[1]->tds_deduct_balance ?? '-'}}</td>
                        <td>{{$tds_cut1}}</td>
                        <td>{{$trans_id[1]->payment_date ?? '-'}}</td>
                        <td>{{$trans_id[1]->bank_refrence_no ?? '-'}}</td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php  } ?>
                    </tr>
                    @endforeach
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
            {{$payment_lists->appends(request()->query())->links()}}
        </nav>
    </div>
</div>