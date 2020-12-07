<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['reseller','customer','product', 'need_to_be_paid'];

    // Reseller Relation
    public function reseller_appends(){
        return $this->belongsTo(Reseller::class, 'reseller_uid', 'uid');
    }
    public function getResellerAttribute(){
        return $this->reseller_appends->name ?? '-';
    }

    // Customer Relation
    public function customer_appends(){
        return $this->belongsTo(Customer::class, 'customer_uid', 'uid');
    }
    public function getCustomerAttribute(){
        return $this->customer_appends->name ?? '-';
    }

    // Product Relation
    public function product_appends(){
        return $this->belongsTo(ProductMerk::class, 'productmerk_id', 'id');
    }
    public function getProductAttribute(){
        return $this->product_appends->name ?? '-';
    }

    // Payment History
    public function getNeedToBePaidAttribute(){
        $total = $this->payment_total;
        $data = OrderPaymentLog::where('order_id',$this->id)->get();
        foreach($data as $item){
            $total-=$item['amount'];
        }
        return $total;
    }
}
