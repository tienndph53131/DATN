<?php

namespace App\Http\Controllers\Client;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;

class CartController extends Controller
{
    // Trang xem giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('client.cart', compact('cart', 'total'));
    }

    // Thêm vào giỏ hàng
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $categories = Category::orderBy('name')->get();
        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity', 1);

        $variant = ProductVariant::with('product')->find($variantId);

        if (!$variant) {
            return back()->with('error', 'Không tìm thấy biến thể sản phẩm.');
        }

        $cart = session()->get('cart', []);
        $quantityInCart = isset($cart[$variantId]) ? $cart[$variantId]['quantity'] : 0;
        if ($quantity + $quantityInCart > $variant->stock_quantity) {
            return back()->with('error', 'Số lượng sản phẩm không đủ!');
        }
        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $cart[$variantId] = [
                'product_name' => $variant->product->name,
                'variant' => $variant->attributeValues->pluck('value')->join(', '),
                'price' => $variant->price,
                'quantity' => $quantity,
                'image' => $variant->product->image,
            ];
        }
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // Xóa sản phẩm khỏi giỏ
    public function remove(Request $request)
    {
        $variantId = $request->input('variant_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    // Cập nhật số lượng
    public function update(Request $request)
    {
        $variantId = $request->input('variant_id');
        $quantity = (int) $request->input('quantity');

        $cart = session()->get('cart', []);
        $variant = ProductVariant::find($variantId);
        if ($quantity > $variant['stock_quantity']) {
            return back()->with('error', 'Số lượng sản phẩm không đủ!');
        } // check so luong ton kho voi cap nhat so luong san pham
        if (isset($cart[$variantId]) && $quantity > 0) {
            $cart[$variantId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Đã cập nhật giỏ hàng!');
    }
}
