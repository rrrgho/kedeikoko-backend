<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMerk extends Model
{
    use HasFactory;
    protected $table = "product_merk";
    protected $guarded = [];
    protected $appends = ['product_type'];

    // Product Type Appends
    public function product_type_appends(){
        return $this->belongsTo(ProductType::class, 'producttype_id', 'id');
    }
    public function getProductTypeAttribute(){
        return $this->product_type_appends->name ?? '-';
    }
}
