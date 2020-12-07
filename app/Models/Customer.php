<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = "customers";
    protected $primaryKey = "uid";
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['reseller','debt','sale_value'];

    // Reseller Relation
    public function reseller_append(){
        return $this->belongsTo(Reseller::class, 'reseller_uid');
    }
    public function getResellerAttribute(){
        return $this->reseller_append->name ?? '-';
    }

    // Financial
    public function getDebtAttribute(){
        $total =  0;
        $data = Order::where('customer_uid',$this->uid)->where('is_paid',false)->get();
        foreach($data as $item)
            $total += $item['need_to_be_paid'];
        return $total;
    }
    public function getSaleValueAttribute(){
        $total =  0;
        $data = Order::where('customer_uid',$this->uid)->get();
        foreach($data as $item)
            $total += $item['payment_total'];
        return $total;
    }
}
