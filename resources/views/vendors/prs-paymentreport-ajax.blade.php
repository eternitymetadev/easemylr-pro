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
                <th>PRS No</th>
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
                <th>Account No.</th>
                <th>IFSC Code</th>
                <th>Vendor Pan</th>
                <th>Purchase Freight</th>
                <th>Paid Amount</th>
                <th>Tds Amount</th>
                <th>Balance Due</th>
                <th>Advance</th>
                <th>Tds Deduct</th>
                <th>Payment Date</th>
                <th>Ref. No</th>
                <th>Blance Amount</th>
                <th>Tds Deduct</th>
                <th>payment date</th>
                <th>Ref. No</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0;
        foreach($payment_lists as $value){
        $i++; 
        $date = date('d-m-Y',strtotime($value->created_at));

        $lr_no = array();
        $regclient_name = array();
        $cnee_city = array();
        $vel_type = array();
        $regn_no = array();
        $total_qty = array();
        $total_wt = array();
        $total_gross_wt = array();
        // echo'<pre>'; print_r(json_decode($value->PrsPaymentRequest)); die;

        foreach($value->PrsPaymentRequest as $reqval){
            if($reqval->Branch){
                $branch_name = $reqval->Branch->nick_name;
            }else{
                $branch_name = '';
            }
            $vendor_name = @$reqval->VendorDetails->name;
            $vendor_type = @$reqval->VendorDetails->vendor_type;
            $tds_rate = @$reqval->VendorDetails->tds_rate;
            $vendor_pan = @$reqval->VendorDetails->pan;
            $purchase_freight = @$reqval->total_amount;

            if($reqval->VendorDetails->declaration_available == 1){
                $vendor_decl = 'Yes';
            }else{
                $vendor_decl = 'No';
            }
            
            $bankdetails = json_decode($reqval->VendorDetails->bank_details);
            $invc_no = array();
            foreach($reqval->PickupRunSheet->Consignments as $lr_group){
                // echo'<pre>'; print_r(json_decode($value->PrsPaymentRequest)); die;
                $lr_no[] = @$lr_group->id;
                $vel_type[] = @$lr_group->vehicletype->name;
                $regclient_name[] = @$lr_group->RegClient->name;
                $cnee_city[] = @$lr_group->ShiptoDetail->city;
                $regn_no[] = @$lr_group->VehicleDetail->regn_no;
                
                foreach($lr_group->ConsignmentItems as $lr_no_item){
                    $invc_no[] = @$lr_no_item->invoice_no;
                }

                $total_qty[] = @$lr_group->total_quantity;
                $total_wt[] = @$lr_group->total_weight;
                $total_gross_wt[] = @$lr_group->total_gross_weight;
                
            }
        }
        $lr_nos = implode('/',$lr_no );

        $regclient_names = implode('/',array_unique($regclient_name) );
        $cnee_citys = implode('/',$cnee_city );
        $invc_nos = implode('/',$invc_no );
        $vel_types = implode('/',array_unique($vel_type));
        $regn_no = implode('/',array_unique($regn_no));
        $total_qty = array_sum($total_qty);
        $total_wt = array_sum($total_wt);
        $total_gross_wt = array_sum($total_gross_wt);

        $trans_id = DB::table('prs_payment_histories')->where('transaction_id', $value->transaction_id)->get();
        $histrycount = count($trans_id);
        if($histrycount > 1){
            $paid_amt = $trans_id[0]->tds_deduct_balance + $trans_id[1]->tds_deduct_balance ;
            $curr_paid_amt = $trans_id[1]->current_paid_amt;
            $paymt_date_2 = $trans_id[1]->payment_date;
            $ref_no_2 = $trans_id[1]->bank_refrence_no;
            $tds_amt = $value->PrsPaymentRequest[0]->total_amount - $paid_amt ;

            $sumof_paid_tds = $paid_amt + $tds_amt ;
            $balance_due =  $value->PrsPaymentRequest[0]->total_amount - $sumof_paid_tds ;

            $tds_cut1 = $trans_id[1]->current_paid_amt - $trans_id[1]->tds_deduct_balance ;

        }else{
            $paid_amt = $trans_id[0]->tds_deduct_balance ;
            $curr_paid_amt = '';
            $paymt_date_2 = '';
            $ref_no_2 = '';
            if($value->payment_type == 'Balance'){
                $tds_amt =  $value->balance - $value->tds_deduct_balance ;
            }else{
            $tds_amt =  $value->advance - $value->tds_deduct_balance ;
            }
            $sumof_paid_tds = $paid_amt + $tds_amt ;
            $balance_due =  $value->PrsPaymentRequest[0]->total_amount - $sumof_paid_tds ;
        }
        ?>
            <tr>
                <td>{{$i}}</td>
                <td>{{@$value->transaction_id ?? '-'}}</td>
                <td>{{$date}}</td>
                <td>{{@$regclient_names}}</td>
                <td>{{@$branch_name}}</td>
                <td>{{@$cnee_citys}}</td>
                <td>{{@$value->prs_no ?? '-'}}</td>
                <td>{{@$lr_nos}}</td>
                <td>{{@$invc_nos}}</td>
                <td>{{@$vel_types}}</td>
                <td>{{@$total_qty}}</td>
                <td>{{@$total_wt}}</td>
                <td>{{@$total_gross_wt}}</td>
                <td>{{@$regn_no}}</td>
                <td>{{@$vendor_name}}</td>
                <td>{{@$vendor_type}}</td>
                <td>{{@$vendor_decl}}</td>
                <td>{{@$tds_rate}}</td>
                <td>{{$bankdetails->bank_name ?? '-'}}</td>
                <td>{{$bankdetails->account_no ?? '-'}}</td>
                <td>{{$bankdetails->ifsc_code ?? '-'}}</td>
                <td>{{@$vendor_pan}}</td>
                <td>{{@$purchase_freight}}</td>
                
                <td>{{$paid_amt}}</td>
                <td>{{$tds_amt}}</td>
                <td>{{$balance_due}}</td>
                <td>{{$value->tds_deduct_balance ?? '-'}}</td>
                <?php $tds_cut = $value->current_paid_amt - $value->tds_deduct_balance?>
                <td>{{$tds_cut}}</td>
                <td>{{$value->payment_date ?? '-'}}</td>
                <td>{{$value->bank_refrence_no ?? '-'}}</td>
                <?php
                $trans_id = $lrdata = DB::table('prs_payment_histories')->where('transaction_id', $value->transaction_id)->get();
                $histrycount = count($trans_id);
                if($histrycount > 1){
                    $tds_cut1 = $trans_id[1]->current_paid_amt - $trans_id[1]->tds_deduct_balance;
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
            <?php } ?>

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