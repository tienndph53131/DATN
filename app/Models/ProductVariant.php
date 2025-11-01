<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'price', 'stock_quantity', 'status'];

    // Cast price to decimal when retrieved
    protected $casts = [
        'price' => 'decimal:2',
    ];

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

    // Quan hệ many-to-many tới AttributeValue qua bảng variant_attributes
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attributes', 'variant_id', 'attribute_value_id');
    }

    // Ảnh của biến thể (nếu có)
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }

    // Accessor: effective price của biến thể.
    // Nếu variant có `price` không null thì trả về đó, ngược lại lấy `product->price`.
    public function getEffectivePriceAttribute()
    {
        // Ưu tiên trả về giá của biến thể nếu nó được thiết lập.
        if ($this->price !== null) {
            return $this->price;
        }

        // Nếu product chưa được load, nó sẽ được load tự động khi truy cập.
        // Điều này giúp tránh query riêng lẻ và tận dụng lazy/eager loading.
        // Toán tử `?->` (nullsafe) và `??` đảm bảo không có lỗi ngay cả khi product không tồn tại.
        return $this->product?->price ?? 0;
    }

}
