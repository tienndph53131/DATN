<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'variant_id',
        'link_images',
    ];

    // Quan hệ tới sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ tới biến thể (nếu có)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
