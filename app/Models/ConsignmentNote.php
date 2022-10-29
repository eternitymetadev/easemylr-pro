<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'regclient_id',
        'consigner_id',
        'consignee_id',
        'ship_to_id',
        'is_salereturn',
        'consignment_no',
        'consignment_date',
        'payment_type',
        'freight',
        'description',
        'packing_type',
        'dispatch',
        'invoice_no',
        'invoice_date',
        'invoice_amount',
        'vehicle_id',
        'total_quantity',
        'total_weight',
        'total_gross_weight',
        'total_freight',
        'pdf_name',
        'transporter_name',
        'vehicle_type',
        'purchase_price',
        'user_id',
        'branch_id',
        'driver_id',
        'bar_code',
        'reason_to_cancel',
        'edd',
        'order_id',
        'status',
        'job_id',
        'tracking_link',
        'delivery_status',
        'delivery_date',
        'signed_drs',
        'e_way_bill',
        'e_way_bill_date',
        'created_at',
        'updated_at'
    ];

    public function Consignee(){
        return $this->belongsTo('App\Models\Consignee','consignee_id');
    }
    public function ConsignmentItems()
    {
        return $this->hasMany('App\Models\ConsignmentItem','consignment_id','id');
    }
    public function ConsigneeDetail()
    {
        return $this->hasOne('App\Models\Consignee','id','consignee_id');
    }
    public function ConsignerDetail()
    {
        return $this->hasOne('App\Models\Consigner','id','consigner_id');
    }
    public function ShiptoDetail()
    {
        return $this->hasOne('App\Models\Consignee','id','ship_to_id');
    }

    public function VehicleDetail()
    {
        return $this->hasOne('App\Models\Vehicle','id','vehicle_id');
    }
    public function DriverDetail()
    {
        return $this->hasOne('App\Models\Driver','id','driver_id');
    }
    public function JobDetail()
    {
        return $this->belongsTo('App\Models\Job','job_id','job_id');
    }
    public function vehicletype()
    {
        return $this->belongsTo('App\Models\VehicleType','vehicle_type');
    }
    public function RegClientdetail()
    {
        return $this->hasOne('App\Models\RegionalClientDetail','regclient_id','regclient_id');
    }
    public function RegClient()
    {
        return $this->hasOne('App\Models\RegionalClient','id','regclient_id');
    }

}
