<?php

namespace App\Imports;

use App\Models\TechnicalMaster;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use DB;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderBookedImport implements ToModel, WithHeadingRow//ToCollection

{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {

       foreach($row as $sd){
        echo'<pre>'; print_r($sd); 
       }
       die;
        // $qty = explode(",",$row['qtycases']);
        // echo'<pre>'; print_r($qty); die;
        // $authuser = Auth::user();
        // $cc = explode(',', $authuser->branch_id);
       
        //     $consignmentsave['regclient_id'] = $row['billing_client'];
        //     $consignmentsave['consigner_id'] = $row['consigner_id'];
        //     $consignmentsave['consignee_id'] = $row['consinee_id'];
        //     $consignmentsave['consignment_date'] = $row['billing_client'];
        //     $consignmentsave['payment_type'] = $row['payment_terms'];

        //     // $consignmentsave['user_id'] = $row['billing_client'];
        //     // $consignmentsave['branch_id'] = $row['billing_client'];
        //      $saveconsignment = ConsignmentNote::create($consignmentsave);
        //      $consignment_id = $saveconsignment->id;
        //     // $cd = ConsignmentNote::where('id', $consignment_id)->where('regclient_id', $row['billing_client'])->where('consigner_id', $row['consigner_id'])->first();
        //     // if(!empty($cd)){
                
        //     // $consignmentsave['quantity'] = $row['qtycases'];
        //     // $consignmentsave['weight'] = $row['net_weight'];
        //     // $consignmentsave['gross_weight'] = $row['gross_weight'];
        //     // $consignmentsave['chargeable_weight'] = $row['chargeble_weight'];
        //     $consignmentItem['order_id'] = $row['order_id'];
        //     $consignmentItem['invoice_no'] = $row['invoice_no'];
        //     $consignmentItem['invoice_date'] = $row['invoice_date'];
        //     $consignmentItem['invoice_amount'] = $row['invoice_value'];
        //     $consignmentItem['e_way_bill'] = $row['eway_no'];
        //     $consignmentItem['e_way_bill_date'] = $row['eway_date'];
        //     $consignmentItem['consignment_id'] = $consignment_id;

        //     $saveconsignment = ConsignmentItem::create($consignmentItem);

        // }

    }
}
