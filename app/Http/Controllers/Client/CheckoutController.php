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
        try {
            if ($request->payment_id == 1) {
                DB::beginTransaction();
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
                $cart->details()->delete();
                $cart->delete();  // Xóa giỏ hàng sau khi đặt hàng thành công
                DB::commit();
                return redirect()->route('order.success')->with('success', 'Đặt hàng thành công');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã có lỗi xảy ra trong quá trình thanh toán: ' . $e->getMessage());
        }
        $cartDetailsArray = $cartDetails->map(function ($item) {
            return [
                'product_variant_id' => $item->product_variant_id,
                'product_id' => $item->ProductVariant->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'amount' => $item->amount,
            ];
        })->toArray(); // Luu product details thanh toan momo vao session

        if ($request->payment_id == 2) {
            session([
                'momo_order' => [
                    'account_id' => auth()->id(),
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address_id' => $request->address_id,
                    'total' => $total,
                    'note' => $request->note ?? null,
                    'cart_details' => $cartDetailsArray, // Luu product details thanh toan momo vao session
                    'payment_id' => 2,
                ]
            ]);
            return $this->momopayment($total);
        }
        if ($request->payment_id == 3) {
            session([
                'vnpay_order' => [
                    'account_id' => auth()->id(),
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address_id' => $request->address_id,
                    'total' => $total,
                    'note' => $request->note ?? null,
                    'cart_details' => $cartDetailsArray,
                    'payment_id' => 3,

                ]
            ]);
            return $this->vnpay_payment();
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

        $amount = $total; // set price <10.000
        if ($amount < 10000) {
            return redirect()->back()->with('error', 'MoMo chỉ hỗ trợ giao dịch tối thiểu 10.000đ');
        }
        $orderId = 'DH' . $requestId;
        $redirectUrl = route('momo.return');
        $ipnUrl = route('momo.ipn');
        $extraData = "";
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType; // chuoi sap xep theo key=value
        $signature = hash_hmac("sha256", $rawHash, $secretKey); // tao chu ki bao mat tranh gia mao 
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
                'address_id' => $data['address_id'],
                'booking_date' => Carbon::now(),
                'total' => $data['total'],
                'note' => $data['note'] ?? null,
                'payment_id' => $data['payment_id'],
                'status_id' => 1,
                'payment_status_id' => 2, // Thanh toán online → đã thanh toán
            ]);
            foreach ($data['cart_details'] as $item) {
                DB::table('order_details')->insert([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'product_id' => $item['product_id'], // lay product_id tu session ProductVariant khong con
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $item['amount'],
                ]);
            }

            $cart = Cart::where('account_id', $data['account_id'])->first(); // lay cart theo account_id luu trong session momo_order
            if ($cart) { // xoa cart sau khi thanh toan
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
                    'address_id' => $data['address_id'],
                    'booking_date' => Carbon::now(),
                    'total' => $data['total'],
                    'note' => $data['note'] ?? null,
                    'payment_id' => $data['payment_id'],
                    'status_id' => 1,
                    'payment_status_id' => 2, // Thanh toán online → đã thanh toán
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
