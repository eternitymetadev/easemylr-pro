<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSheet extends Model
{
    use HasFactory;
    protected $table = 'transaction_sheets';
    protected $fillable = [
        'consignment_id','consignee_id','consignment_no','consignment_date','city','pincode','total_quantity', 'total_weight','order_no','vehicle_no', 'driver_name','driver_no','status','job_id','delivery_status','delivery_date','drs_no','branch_id','purchase_amount','created_at','updated_at'
    ];

    public function ConsignmentDetail()
    {
        return $this->hasOne('App\Models\ConsignmentNote','id','consignment_no');
    }

    public function consigneeDetail()
    {
        return $this->hasOne('App\Models\Consignee','nick_name','consignee_id');
    }

    public function ConsignmentNote(){
        return $this->belongsTo('App\Models\ConsignmentNote','consignment_no');
    }

    public function ConsignmentItem(){
        return $this->hasMany('App\Models\ConsignmentItem','consignment_id','consignment_no');
    }

    

}
