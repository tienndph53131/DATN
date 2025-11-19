<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        // Sửa lỗi: Sử dụng đúng guard 'client' để lấy thông tin người dùng đã đăng nhập
        $account = auth('client')->user();
        if (!$account) {
            return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $cart = Cart::where('account_id', $account->id) // Bây giờ $account->id sẽ không bị lỗi
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
        $addresses = Address::where('account_id', $account->id)
            ->with(['province', 'district', 'ward']) // Tải thông tin chi tiết địa chỉ
            ->get();
        $total = $cartDetails->sum('amount');

        // Lấy danh sách tỉnh/thành từ GHN API (sử dụng Cache)
        $provinces = Cache::remember('ghn_provinces', 60 * 60 * 24, function () {
            $ghnToken = env('GHN_TOKEN');
            $res = Http::withHeaders(['Token' => $ghnToken])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
            if ($res->ok()) {
                return $res->json()['data'] ?? [];
            }
            // Ghi lại lỗi nếu không lấy được dữ liệu
            Log::error('GHN API Error - Get Provinces: ' . $res->body());
            return [];
        });

        return view('client.checkout', compact(
            'cartDetails', 
            'total', 
            'addresses', 
            'account', 
            'provinces'
        ));
    }

    public function getDistricts(Request $request)
    {
        $provinceId = $request->input('province_id');
        if (!$provinceId) {
            return response()->json(['error' => 'Thiếu ID tỉnh thành'], 400);
        }

        $districts = Cache::remember('ghn_districts_' . $provinceId, 60 * 60 * 24, function () use ($provinceId) {
            $ghnToken = env('GHN_TOKEN');
            $res = Http::withHeaders(['Token' => $ghnToken, 'Content-Type' => 'application/json'])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', ['province_id' => (int)$provinceId]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });

        return response()->json($districts);
    }

    public function getWards(Request $request)
    {
        $districtId = $request->input('district_id');
        if (!$districtId) {
            return response()->json(['error' => 'Thiếu ID quận huyện'], 400);
        }

        $ghnToken = env('GHN_TOKEN');
        $res = Http::withHeaders(['Token' => $ghnToken, 'Content-Type' => 'application/json'])
            ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', ['district_id' => (int)$districtId]);

        return $res->ok() ? response()->json($res->json()['data'] ?? []) : response()->json(['error' => 'Không thể lấy dữ liệu phường xã'], 500);
    }

    public function checkout(Request $request)
    {
        // Sửa lỗi: Sử dụng đúng guard 'client'
        $account = auth('client')->user();
        if (!$account) {
            return redirect()->route('client.login')->with('error', 'Phiên đăng nhập đã hết hạn.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:11',
        ]);
        $cart = Cart::where('account_id', $account->id)
            ->first();
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống không thể thanh toán');
        }
        $cartDetails = $cart->details()->with('productVariant.product')->get();
        $total = $cartDetails->sum('amount');
        // Tạo đơn hàng
        DB::beginTransaction();
        try {
            // Xử lý thanh toán khi nhận hàng (COD)
            if ($request->payment_id == 1) { // Giả sử 1 là ID của COD
                $order = Order::create([
                    'order_code' => 'DH' . time(),
                    'account_id' => $account->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'booking_date' => Carbon::now(),
                    'total' => $total,
                    'note' => $request->note ?? null,
                    'payment_id' => $request->payment_id,
                    'status_id' => 1, // Trạng thái chờ xác nhận
                ]);

                foreach ($cartDetails as $item) {
                    DB::table('order_details')->insert([
                        'order_id' => $order->id,
                        'product_variant_id' => $item->variant_id,
                        'product_id' => $item->ProductVariant->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'amount' => $item->amount,
                    ]);

                    $variant = $item->productVariant;
                    if (!$variant || $variant->stock_quantity < $item->quantity) {
                        throw new \Exception('Sản phẩm "' . $variant->product->name . '" không đủ tồn kho.');
                    }
                    $variant->decrement('stock_quantity', $item->quantity);
                }

                $cart->details()->delete();
                $cart->delete();  // Xóa giỏ hàng sau khi đặt hàng thành công
                DB::commit();
                return redirect()->route('order.success')->with('success', 'Đặt hàng thành công');
            }

            // Chuẩn bị dữ liệu cho thanh toán online (Momo, VNPAY)
            $cartDetailsArray = $cartDetails->map(function ($item) {
                return [
                    'product_variant_id' => $item->variant_id, // No change needed here, it's correct
                    'product_id' => $item->ProductVariant->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'amount' => $item->amount,
                ];
            })->toArray();

            // Xử lý thanh toán Momo
            if ($request->payment_id == 2) {
                session([
                    'momo_order' => [
                        'account_id' => $account->id,
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'total' => $total,
                        'note' => $request->note ?? null,
                        'cart_details' => $cartDetailsArray, // Luu product details thanh toan momo vao session
                        'payment_id' => 2,
                    ]
                ]);
                DB::commit(); // Commit transaction trước khi chuyển hướng
                return $this->momopayment($total);
            }

            // Xử lý thanh toán VNPAY
            if ($request->payment_id == 3) {
                session([
                    'vnpay_order' => [
                        'account_id' => $account->id,
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'total' => $total,
                        'note' => $request->note ?? null,
                        'cart_details' => $cartDetailsArray,
                        'payment_id' => 3,

                    ]
                ]);
                DB::commit(); // Commit transaction trước khi chuyển hướng
                return $this->vnpay_payment();
            }

            // Nếu không phải các phương thức trên, rollback và báo lỗi
            throw new \Exception("Phương thức thanh toán không hợp lệ.");
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
    public function momopayment($total)
    {

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";


        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo";

        $requestId = time() . "";
        $requestType = "payWithATM";

        $amount = $total;
        if ($amount < 10000) {
            $amount = 10000;
        }
        $orderId = 'DH' . $requestId;
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
    }
    public function momoReturn(Request $request)
    {
        if ($request->resultCode != 0) {
            return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại');
        }
        $data = session('momo_order');
        if (!$data) {
            return redirect()->route('cart.index')->with('error', 'Dữ liệu đơn hàng không tồn tại');
        }
        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code' => 'DH' . time(),
                'account_id' => $data['account_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'booking_date' => Carbon::now(),
                'total' => $data['total'],
                'note' => $data['note'] ?? null,
                'payment_id' => $data['payment_id'],
                'status_id' => 2,
            ]);
            foreach ($data['cart_details'] as $item) {
    DB::table('order_details')->insert([
        'order_id' => $order->id,
        'product_variant_id' => $item['product_variant_id'],
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'amount' => $item['amount'],
    ]);

    // Giảm tồn kho
    $variant = \App\Models\ProductVariant::find($item['product_variant_id']);
    if (!$variant || $variant->stock_quantity < $item['quantity']) {
        throw new \Exception('Sản phẩm không đủ tồn kho.');
    }

    $variant->decrement('stock_quantity', $item['quantity']);
}


            $cart = Cart::where('account_id', $data['account_id'])->first(); // lay cart theo account_id luu trong session momo_order
            if ($cart) {
                $cart->details()->delete();
                $cart->delete();
            }
            session()->forget('momo_order');
            DB::commit();
            return redirect()->route('order.success')->with('success', 'Đặt hàng thành công qua MoMo');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Đã có lỗi xảy ra: ' . $th->getMessage());
        }
    }
    public function vnpay_payment()
    {
        $data = session('vnpay_order');
        $code_cart = 'DH' . time();
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return');
        $vnp_TmnCode = "C0PZ3IV4"; //Mã website tại VNPAY 
        $vnp_HashSecret = "JKG2BXY1SP03KK7OVHIA3C49VJVAQMVD"; //Chuỗi bí mật

        $vnp_TxnRef = $code_cart; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này 
        $vnp_OrderInfo = "Thanh toan don hang:" . $code_cart;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $data['total'] * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        // //Add Params of 2.0.1 Version
        // $vnp_ExpireDate = $_POST['txtexpire'];
        // //Billing
        // $vnp_Bill_Mobile = $_POST['txt_billing_mobile'];
        // $vnp_Bill_Email = $_POST['txt_billing_email'];
        // $fullName = trim($_POST['txt_billing_fullname']);
        // if (isset($fullName) && trim($fullName) != '') {
        //     $name = explode(' ', $fullName);
        //     $vnp_Bill_FirstName = array_shift($name);
        //     $vnp_Bill_LastName = array_pop($name);
        // }
        // $vnp_Bill_Address = $_POST['txt_inv_addr1'];
        // $vnp_Bill_City = $_POST['txt_bill_city'];
        // $vnp_Bill_Country = $_POST['txt_bill_country'];
        // $vnp_Bill_State = $_POST['txt_bill_state'];
        // // Invoice
        // $vnp_Inv_Phone = $_POST['txt_inv_mobile'];
        // $vnp_Inv_Email = $_POST['txt_inv_email'];
        // $vnp_Inv_Customer = $_POST['txt_inv_customer'];
        // $vnp_Inv_Address = $_POST['txt_inv_addr1'];
        // $vnp_Inv_Company = $_POST['txt_inv_company'];
        // $vnp_Inv_Taxcode = $_POST['txt_inv_taxcode'];
        // $vnp_Inv_Type = $_POST['cbo_inv_type'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
            // "vnp_ExpireDate" => $vnp_ExpireDate,
            // "vnp_Bill_Mobile" => $vnp_Bill_Mobile,
            // "vnp_Bill_Email" => $vnp_Bill_Email,
            // "vnp_Bill_FirstName" => $vnp_Bill_FirstName,
            // "vnp_Bill_LastName" => $vnp_Bill_LastName,
            // "vnp_Bill_Address" => $vnp_Bill_Address,
            // "vnp_Bill_City" => $vnp_Bill_City,
            // "vnp_Bill_Country" => $vnp_Bill_Country,
            // "vnp_Inv_Phone" => $vnp_Inv_Phone,
            // "vnp_Inv_Email" => $vnp_Inv_Email,
            // "vnp_Inv_Customer" => $vnp_Inv_Customer,
            // "vnp_Inv_Address" => $vnp_Inv_Address,
            // "vnp_Inv_Company" => $vnp_Inv_Company,
            // "vnp_Inv_Taxcode" => $vnp_Inv_Taxcode,
            // "vnp_Inv_Type" => $vnp_Inv_Type
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        if (isset($_POST['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            return redirect()->to($vnp_Url);
        }
    }
    public function vnpayReturn(Request $request)
    {
        $data = session('vnpay_order');
        $vnp_ResponseCode = $request->get('vnp_ResponseCode');
        if ($vnp_ResponseCode == '00') {
            // Thanh toan thanh cong
            DB::beginTransaction();
            try {
                $order = Order::create([
                    'order_code' => 'DH' . time(),
                    'account_id' => $data['account_id'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'booking_date' => Carbon::now(),
                    'total' => $data['total'],
                    'note' => $data['note'] ?? null,
                    'payment_id' => $data['payment_id'],
                    'status_id' => 2,
                ]);
                foreach ($data['cart_details'] as $item) {
    DB::table('order_details')->insert([
        'order_id' => $order->id,
        'product_variant_id' => $item['product_variant_id'],
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'amount' => $item['amount'],
    ]);

    $variant = \App\Models\ProductVariant::find($item['product_variant_id']);
    if (!$variant || $variant->stock_quantity < $item['quantity']) {
        throw new \Exception('Sản phẩm không đủ tồn kho.');
    }

    $variant->decrement('stock_quantity', $item['quantity']);
}

                $cart = Cart::where('account_id', $data['account_id'])->first();
                if ($cart) {
                    $cart->details()->delete();
                    $cart->delete();
                }
                session()->forget('vnpay_order');
                DB::commit();
                return redirect()->route('order.success')->with('success', 'Đặt hàng thành công qua VNPAY');
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại');
            }
        } else {
            return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại');
        }
    }
}