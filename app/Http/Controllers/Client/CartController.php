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
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng.');
        }

        $account = Auth::guard('client')->user();
        $cart = Cart::firstOrCreate(['account_id' => $account->id]);
        $cartDetails = $cart->details()->with('productVariant.product', 'productVariant.attributeValues')->get();
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

<<<<<<< HEAD
        // Kiểm tra số lượng tồn kho
        if ($variant->stock_quantity < $quantity) {
            return back()->with('error', 'Số lượng sản phẩm không đủ.');
        }

        $cart = session()->get('cart', []);

        // Kiểm tra nếu sản phẩm đã có trong giỏ
        if (isset($cart[$variantId])) {
            $newQuantity = $cart[$variantId]['quantity'] + $quantity;
            // Kiểm tra lại tổng số lượng trong giỏ và số lượng tồn kho
            if ($variant->stock_quantity < $newQuantity) {
                return back()->with('error', 'Số lượng sản phẩm trong giỏ hàng vượt quá số lượng tồn kho.');
            }
            $cart[$variantId]['quantity'] = $newQuantity;
        } else {
            $cart[$variantId] = [
                'product_name' => $variant->product->name,
                'variant' => $variant->attributeValues->pluck('value')->join(', '),
                'price' => $variant->price,
                'quantity' => $quantity,
                'image' => $variant->product->image,
            ];
        }
=======
        $variantId = $request->variant_id;
        $quantity = $request->quantity;
        $variant = ProductVariant::find($variantId);

        if (!$variant) return back()->with('error', 'Không tìm thấy biến thể sản phẩm.');
        if ($quantity > $variant->stock_quantity) return back()->with('error', 'Số lượng sản phẩm không đủ!');
>>>>>>> f49dd4e00beb01fa55f92881903902919f24b138

        $account = Auth::guard('client')->user();
        $cart = Cart::firstOrCreate(['account_id' => $account->id]);

        $detail = CartDetail::firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantId,
        ]);

        $newQty = ($detail->exists ? $detail->quantity : 0) + $quantity;

        if ($newQty > $variant->stock_quantity) return back()->with('error', 'Số lượng sản phẩm không đủ trong kho!');

        $detail->quantity = $newQty;
        $detail->product_id = $variant->product_id;
        $detail->price = $variant->price;
        $detail->amount = $variant->price * $newQty;
        $detail->save();

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // Xóa sản phẩm (AJAX)
    public function remove(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập']);
        }

        $variantId = $request->variant_id;
        $account = Auth::guard('client')->user();
        $cart = Cart::where('account_id', $account->id)->first();

        if ($cart) {
            CartDetail::where('cart_id', $cart->id)
                ->where('product_variant_id', $variantId)
                ->delete();
        }

        // Tính tổng mới
        $total = $cart ? $cart->details()->sum('amount') : 0;

        return response()->json(['success' => true, 'total' => $total]);
    }

    // Cập nhật số lượng (AJAX)
    public function update(Request $request)
    {
<<<<<<< HEAD
        $variantId = $request->input('variant_id');
        $quantity = (int) $request->input('quantity');

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return back()->with('error', 'Sản phẩm không tồn tại.');
        }

        if ($variant->stock_quantity < $quantity) {
            return back()->with('error', 'Số lượng cập nhật vượt quá số lượng tồn kho.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$variantId]) && $quantity > 0) {
            $cart[$variantId]['quantity'] = $quantity;
            session()->put('cart', $cart);
=======
        if (!Auth::guard('client')->check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập']);
>>>>>>> f49dd4e00beb01fa55f92881903902919f24b138
        }

        $variantId = $request->variant_id;
        $quantity = (int) $request->quantity;

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy biến thể']);
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

        $total = $cart ? $cart->details()->sum('amount') : 0;

        return response()->json(['success' => true, 'total' => $total]);
    }
}
