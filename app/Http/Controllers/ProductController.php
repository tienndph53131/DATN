<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // Tăng lượt xem sản phẩm
        $product->increment('view');

        // Load các mối quan hệ cần thiết
        $product->load([
            'variants.attributeValues.attribute', // Cần load cả attribute để lấy tên thuộc tính
            'variants.images'
        ]);

        // Chuẩn bị dữ liệu biến thể cho JavaScript
        $variantData = $product->variants->map(function ($variant) {
            $attributes = [];
            $attributesText = [];
            foreach ($variant->attributeValues as $attrValue) {
                $slug = Str::slug($attrValue->attribute->name, '-');
                $attributes[$slug] = (string)$attrValue->id; // Lưu ID của giá trị thuộc tính
                $attributesText[$slug] = $attrValue->value; // Lưu giá trị text của thuộc tính
            }

            // Lấy ảnh đầu tiên của biến thể, nếu có
            $variantImage = $variant->images->first();
            $imageUrl = $variantImage ? asset('uploads/products/' . $variantImage->link_images) : null;

            return [
                'id' => $variant->id,
                'price' => $variant->effective_price, // Sử dụng accessor effective_price
                'stock_quantity' => $variant->stock_quantity,
                'attributes' => $attributes,
                'attributes_text' => $attributesText, // Đây là phần quan trọng để hiển thị tên màu
                'image' => $imageUrl,
            ];
        })->toArray();

        // Lấy các giá trị thuộc tính (Màu sắc, Kích cỡ) để hiển thị nút chọn
        // Chỉ lấy những màu/kích cỡ thực sự có trong các biến thể của sản phẩm này
        $colors = AttributeValue::whereHas('attribute', function ($query) {
            $query->where('name', 'Màu sắc');
        })->whereHas('productVariants', function ($query) use ($product) {
            $query->whereIn('product_variants.id', $product->variants->pluck('id'));
        })->get();

        $sizes = AttributeValue::whereHas('attribute', function ($query) {
            $query->where('name', 'Kích cỡ');
        })->whereHas('productVariants', function ($query) use ($product) {
            $query->whereIn('product_variants.id', $product->variants->pluck('id'));
        })->get();

        // Lấy sản phẩm liên quan (cùng danh mục, trừ sản phẩm hiện tại)
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->limit(4)
                                ->get();

        return view('client.product-detail', compact('product', 'variantData', 'colors', 'sizes', 'relatedProducts'));
    }
}