<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;
    protected $table = "product_stock";
    protected $guarded = [];
    protected $appends = ['product_merk','product_type'];

    // Product Merk Relation
    public function product_merk_appends(){
        return $this->belongsTo(ProductMerk::class, 'productmerk_id');
    }
    public function getProductMerkAttribute(){
        return $this->product_merk_appends->name ?? '-';
    }
    public function getProductTypeAttribute(){
        return $this->product_merk_appends->product_type ?? '-';
    }
}
