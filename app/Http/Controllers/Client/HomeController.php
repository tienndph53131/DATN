<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\VariantAttribute;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with(['category', 'variants'])
            ->where('status', 1)
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('client.home', compact('categories', 'products'));
    }

    public function showCategory($id)
    {
        $categories = Category::orderBy('name')->get();
        $category = Category::findOrFail($id);

        $products = Product::with(['category', 'variants'])
            ->where('category_id', $id)
            ->where('status', 1)
            ->latest('created_at')
            ->paginate(8);

        return view('client.category', compact('categories', 'category', 'products'));
    }

    // Chi tiết sản phẩm
    public function showProduct($id)
    {
        $categories = Category::orderBy('name')->get();
        $product = Product::with(['category', 'variants.attributeValues.attribute'])->findOrFail($id);

        // Tăng lượt xem
        $product->increment('view');

        // Sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->take(4)
            ->get();

        // Lấy tất cả id của biến thể
        $variantIds = $product->variants->pluck('id');
        $attrValueIds = VariantAttribute::whereIn('variant_id', $variantIds)->pluck('attribute_value_id');

        // Lấy thuộc tính Màu sắc & Kích thước
        $sizeAttribute = Attribute::where('name', 'Kích cỡ')->first();
        $colorAttribute = Attribute::where('name', 'Màu sắc')->first();

        $sizes = $sizeAttribute
            ? $sizeAttribute->values()->whereIn('id', $attrValueIds)->get()
            : collect();

        $colors = $colorAttribute
            ? $colorAttribute->values()->whereIn('id', $attrValueIds)->get()
            : collect();

        // Tạo dữ liệu JSON cho view JS
        $variantData = $product->variants->map(function ($v) {
            return [
                'id' => $v->id,
                'price' => $v->price,
                'attributes' => $v->attributeValues->pluck('value', 'attribute.name')->toArray()
            ];
        });

        return view('client.product-detail', compact(
            'categories',
            'product',
            'relatedProducts',
            'sizes',
            'colors',
            'variantData'
        ));
    }
}
