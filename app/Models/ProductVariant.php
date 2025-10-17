<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'price', 'stock_quantity', 'status'];

    // Quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ với VariantAttribute
    public function attributes()
    {
        return $this->hasMany(VariantAttribute::class, 'variant_id');
 
    }
    public function attributeValues()
{
    return $this->belongsToMany(AttributeValue::class, 'variant_attributes', 'variant_id', 'attribute_value_id');
}

}
