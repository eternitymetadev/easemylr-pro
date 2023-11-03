<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSheet extends Model

{ 

    protected $table = "transaction_sheets";

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $boolean = 1;

    

  /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'drs_no','consignment_no','consignee_id','consignment_date','city','pincode','total_quantity','total_weight','order_no','vehicle_no','driver_name','driver_no','branch_id','delivery_status','delivery_date','job_id','status','is_started','created_at','updated_at'

    ];

    //status = 4 for reattempt drs lr

     /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */

    protected $casts = [

        

'created_at' => 'datetime',

'updated_at' => 'datetime',

    ];

    use HasFactory;

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



