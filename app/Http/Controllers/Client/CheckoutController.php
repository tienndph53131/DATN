<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $account = auth()->user();

        $cart = Cart::where('account_id', $account->id)
            ->first();
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống không thể thanh toán');
        }
        $cartDetails = $cart
            ->details()
            ->with('productVariant.product')
            ->get();
        if ($cartDetails->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống không thể thanh toán');
        }
        $address = Address::where('account_id', auth()->id())->get();
        $total = $cartDetails->sum('amount');
        return view('client.checkout', compact('cartDetails', 'total', 'address'));
    }
    public function checkout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:11',
            'address_id' => 'required',
        ]);
        $cart = Cart::where('account_id', auth()->id())
            ->first();
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống không thể thanh toán');
        }
        $cartDetails = $cart->details()->with('productVariant.product')->get();
        $total = $cartDetails->sum('amount');
        // Tạo đơn hàng
        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code' => 'DH' . time(),
                'account_id' => auth()->id(),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address_id' => $request->address_id,
                'booking_date' => Carbon::now(),
                'total' => $total,
                'note' => $request->note ?? null,
                'payment_id' => $request->payment_id,
                'status_id' => 1,
            ]);
            foreach ($cartDetails as $item) {
                DB::table('order_details')->insert([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_id' => $item->ProductVariant->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'amount' => $item->amount,
                ]);
            }
            if ($request->payment_id == 1) {
                $cart->details()->delete();
                $cart->delete();  // Xóa giỏ hàng sau khi đặt hàng thành công
                DB::commit();
                return redirect()->route('order.success')->with('success', 'Đặt hàng thành công');
            }

            if ($request->payment_id == 2) {
                DB::commit();
                return $this->momopayment($order);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã có lỗi xảy ra trong quá trình thanh toán: ' . $e->getMessage());
        }
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    public function momopayment($order)
    {

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";


        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo";

        $requestId = time() . "";
        $requestType = "payWithATM";

        $amount = $order->total;
        if ($amount < 10000) {
            $amount = 10000;
        }
        $orderId = $order->order_code;
        $redirectUrl = route('momo.return');
        $ipnUrl = route('momo.ipn');
        $extraData = "";

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        return redirect()->to($jsonResult['payUrl']);
        // dd($jsonResult);
        //Just a example, please check more in there

        // header("Location: " . $jsonResult['payUrl']);
    }
    // public function momoIpn(Request $request)
    // {

    //     $order = Order::where('order_code', $request->orderId)->first();
    //     if ($request->resultCode == 0) {
    //         $data = session('momo_order');
    //         DB::beginTransaction();
    //         try {
    //             $order = Order::create([
    //                 'order_code' => 'DH' . time(),
    //                 'account_id' => auth()->id(),
    //                 'name' => $data['name'],
    //                 'email' => $data['email'],
    //                 'phone' => $data['phone'],
    //                 'address_id' => $data['address_id'],
    //                 'booking_date' => Carbon::now(),
    //                 'total' => $data['total_momo'],
    //                 'note' => $data['note'] ?? null,
    //                 'payment_id' => 2,
    //                 'status_id' => 2,
    //             ]);
    //             foreach ($data['cart_details'] as $item) {
    //                 DB::table('order_details')->insert([
    //                     'order_id' => $order->id,
    //                     'product_variant_id' => $item['product_variant_id'],
    //                     'product_id' => $item['ProductVariant']['product_id'],
    //                     'quantity' => $item['quantity'],
    //                     'price' => $item['price'],
    //                     'amount' => $item['amount'],
    //                 ]);
    //             }
    //             $cart = Cart::where('account_id', auth()->id())->first();
    //             if ($cart) {
    //                 $cart->details()->delete();
    //                 $cart->delete();
    //             }
    //             session()->forget('momo_order');
    //             DB::commit();
    //             return response()->json(['message' => 'Thanh toán thành công'], 200);
    //         } catch (\Throwable $th) {
    //             DB::rollBack();
    //             return response()->json(['message' => 'Đã có lỗi xảy ra trong quá trình thanh toán: ' . $th->getMessage()], 500);
    //         }
    //     } else {
    //         $order->update(['status_id' => 4]); // canceled
    //         return response()->json(['message' => 'Thanh toán thất bại'], 400);
    //     }
    // }

    public function momoReturn(Request $request)
    {
        if ($request->resultCode != 0) {
            return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại');
        }
        $order = Order::where('order_code', $request->orderId)->first();
        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Đơn hàng không tồn tại');
        }
        $order->update(['status_id' => 2]);
        $cart = Cart::where('account_id', $order->account_id)->with('details')->first();
        if ($cart) {
            $cart->details()->delete();
            $cart->delete();
        }
        return redirect()->route('order.success')->with('success', 'Đặt hàng thành công qua MoMo');
    }
}
