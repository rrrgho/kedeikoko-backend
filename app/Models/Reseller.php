<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    use HasFactory;
    protected $table = "reseller";
    protected $primaryKey = "uid";
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['customers','debt','income','sale_value'];

    // Customer Relation
    public function customer_append(){
        return $this->hasMany(Customer::class, 'reseller_uid', 'uid');
    }
    public function getCustomersAttribute(){
        return count($this->customer_append()->where('deleted_at',null)->get());
    }

    // Financial
    public function getDebtAttribute(){
        $total = 0;
        $order = Order::where('reseller_uid',$this->uid)->where('is_paid',false)->get();
        foreach($order as $item)
            $total+=$item['need_to_be_paid'];
        return $total;
    }
    public function getIncomeAttribute(){
        $total = 0;
        $order = ResellerIncome::where('reseller_uid',$this->uid)->get();
        foreach($order as $item)
            $total+=$item['amount'];
        return $total;
    }
    public function getSaleValueAttribute(){
        $total = 0;
        $order = Order::where('reseller_uid',$this->uid)->get();
        foreach($order as $item)
            $total+=$item['payment_total'];
        return $total;
    }
}
