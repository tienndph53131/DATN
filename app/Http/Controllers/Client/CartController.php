<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Trang xem giỏ hàng
    public function index()
    {
        // Chỉ hiển thị nếu user đăng nhập
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng.');
        }

        $account = Auth::guard('client')->user();

        // Lấy giỏ hàng hoặc tạo mới
        $cart = Cart::firstOrCreate(['account_id' => $account->id]);

        // Lấy chi tiết giỏ hàng kèm thông tin sản phẩm và biến thể
        $cartDetails = $cart->details()->with('productVariant.product')->get();

        // Tính tổng tiền
        $total = $cartDetails->sum('amount');

        return view('client.cart', ['cart' => $cartDetails, 'total' => $total]);
    }

    // Thêm vào giỏ hàng
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm.');
        }

        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity');
        $variant = ProductVariant::find($variantId);

        if (!$variant) {
            return back()->with('error', 'Không tìm thấy biến thể sản phẩm.');
        }

        if ($quantity > $variant->stock_quantity) {
            return back()->with('error', 'Số lượng sản phẩm không đủ!');
        }

        $account = Auth::guard('client')->user();
        $cart = Cart::firstOrCreate(['account_id' => $account->id]);

        $detail = CartDetail::firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantId,
        ]);

        $newQty = ($detail->exists ? $detail->quantity : 0) + $quantity;

        if ($newQty > $variant->stock_quantity) {
            return back()->with('error', 'Số lượng sản phẩm không đủ trong kho!');
        }

        $detail->quantity = $newQty;
        $detail->product_id = $variant->product_id;
        $detail->price = $variant->price;
        $detail->amount = $variant->price * $newQty;
        $detail->save();

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // Xóa sản phẩm khỏi giỏ
    public function remove(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để xóa sản phẩm.');
        }

        $variantId = $request->input('variant_id');
        $account = Auth::guard('client')->user();
        $cart = Cart::where('account_id', $account->id)->first();

        if ($cart) {
            CartDetail::where('cart_id', $cart->id)
                ->where('product_variant_id', $variantId)
                ->delete();
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    // Cập nhật số lượng
    public function update(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để cập nhật giỏ hàng.');
        }

        $variantId = $request->input('variant_id');
        $quantity = (int) $request->input('quantity');

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return back()->with('error', 'Không tìm thấy biến thể.');
        }

        if ($quantity > $variant->stock_quantity) {
            return back()->with('error', 'Số lượng sản phẩm không đủ!');
        }

        $account = Auth::guard('client')->user();
        $cart = Cart::where('account_id', $account->id)->first();

        if ($cart) {
            $detail = CartDetail::where('cart_id', $cart->id)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($detail) {
                $detail->quantity = $quantity;
                $detail->amount = $quantity * $detail->price;
                $detail->save();
            }
        }

        return back()->with('success', 'Đã cập nhật giỏ hàng!');
    }
}
