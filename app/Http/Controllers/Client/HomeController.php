<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\VariantAttribute;
use Illuminate\Support\Str;

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
    $product = Product::with(['category', 'variants.attributeValues.attribute', 'variants.images'])->findOrFail($id);

        // Tăng lượt xem
        $product->increment('view');

        // Sản phẩm liên quan
        // [TỐI ƯU] Thêm with('variants') để tránh lỗi N+1 query trong view
        $relatedProducts = Product::with('variants')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->take(4)
            ->get();

        // [TỐI ƯU] Lấy thuộc tính và nhóm lại để giảm query
        $allAttributeValues = $product->variants->flatMap(function ($variant) {
            return $variant->attributeValues;
        })->unique('id');

<<<<<<< HEAD
        $colors = $allAttributeValues->filter(function ($av) {
            return Str::slug($av->attribute->name) === 'mau-sac';
        });
        $sizes = $allAttributeValues->filter(function ($av) {
            return Str::slug($av->attribute->name) === 'kich-co';
=======
        // Lấy thuộc tính Màu sắc & Kích thước
        $sizeAttribute = Attribute::where('name', 'Kích cỡ')->first();
        $colorAttribute = Attribute::where('name', 'Màu sắc')->first();

        $sizes = $sizeAttribute
            ? $sizeAttribute->values()->whereIn('id', $attrValueIds)->get()
            : collect();

        $colors = $colorAttribute
            ? $colorAttribute->values()->whereIn('id', $attrValueIds)->get()
            : collect();
 // Map color values to CSS safe strings to avoid declaring functions in views
        $colors = $colors->map(function ($c) {
            return (object) [
                'value' => $c->value,
                'css' => $this->colorToCss($c->value),
            ];
>>>>>>> f49dd4e00beb01fa55f92881903902919f24b138
        });

        // Tạo dữ liệu JSON cho view JS
        $variantData = $product->variants->map(function ($v) use ($product) {
            // attribute maps keyed by slug: ids and text
            $attrMapId = [];
            $attrMapText = [];
            foreach ($v->attributeValues as $av) {
                $attrName = $av->attribute ? $av->attribute->name : null;
                if ($attrName) {
                    $slug = Str::slug($attrName, '-');
                    $attrMapId[$slug] = $av->id; // use attribute_value id for matching
                    $attrMapText[$slug] = $av->value; // human-readable text for display
                }
            }

            // pick variant image if exists, else use product image
            $vImage = $v->images->first();
            $imageUrl = $vImage ? asset('uploads/products/' . $vImage->link_images) : ($product->image ? asset('uploads/products/' . $product->image) : null);

            return [
                'id' => $v->id,
<<<<<<< HEAD
                'price' => $v->effective_price ?? $v->price,
                'image' => $imageUrl,
                'stock_quantity' => $v->stock_quantity, // [THÊM] stock_quantity cho JS
                'attributes' => $attrMapId,
                'attributes_text' => $attrMapText,
=======
                'price' => $v->price,
                'stock_quantity' => $v->stock_quantity ?? 0,
                'available' => ($v->stock_quantity ?? 0) > 0,
                'attributes' => $v->attributeValues->pluck('value', 'attribute.name')->toArray()
>>>>>>> f49dd4e00beb01fa55f92881903902919f24b138
            ];
        });
        // Lấy bình luận đã duyệt để hiển thị
        $comments = $product->comments()->where('status', 1)->with('account')->orderByDesc('date')->get();

        // Rating aggregates
        $avgRating = $comments->count() ? round($comments->avg('rating'), 1) : 0;
        $totalReviews = $comments->count();
        $ratingCounts = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingCounts[$i] = $comments->where('rating', $i)->count();
        }


        return view('client.product-detail', compact(
            'categories',
            'product',
            'relatedProducts',
            'sizes',
            'colors',
            'variantData',
            'comments',
            'avgRating',
            'totalReviews',
            'ratingCounts'
        ));
    }
     // Helper: map color value to CSS color. Kept private to avoid global redeclare issues in views.
    private function colorToCss(?string $value): string
    {
        $map = [
            'trắng' => 'white',
            'đen' => 'black',
            'vàng' => 'yellow',
            'hồng' => 'pink',
            'xanh dương' => 'blue',
            'xanh lá' => 'green',
            'đỏ' => 'red',
            'xám' => 'gray',
            'nâu' => '#8B4513',
            'tím' => 'purple',
        ];

        if (!$value) return '';
        $v = trim(mb_strtolower($value));
        if (str_starts_with($v, '#')) return $value;
        return $map[$v] ?? $value;
    }
}
