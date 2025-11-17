<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    protected string $ghnToken;

    public function __construct()
    {
        $this->ghnToken = env('GHN_TOKEN'); // Token GHN
    }

    // Danh sách đơn hàng
    public function history()
    {
        $orders = Order::where('account_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->with(['status', 'payment'])
            ->get();

        return view('client.orders.history', compact('orders'));
    }

    // Chi tiết đơn hàng
    public function detail($order_code)
    {
        $order = Order::where('order_code', $order_code)
            ->where('account_id', Auth::id())
            ->with(['details.product', 'details.variant.attributeValues', 'status', 'payment'])
            ->firstOrFail();

        $order->ghn_address = null;

        if ($order->address_id) {
            // Lấy Address model
            $address = Address::find($order->address_id);

            if ($address) {
                $province_id = $address->province_id;
                $district_id = $address->district_id;
                $ward_id = $address->ward_id;
                $address_detail = $address->address_detail;

                // GHN API lấy danh sách tỉnh
                $provinces = Cache::remember('ghn_provinces', 60*60*24, function() {
                    $res = Http::withHeaders(['Token' => $this->ghnToken])
                        ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
                    return $res->ok() ? ($res->json()['data'] ?? []) : [];
                });

                $province = collect($provinces)->firstWhere('ProvinceID', $province_id) ?? [];

                // GHN API lấy districts theo province_id
                $districts = Cache::remember("ghn_districts_{$province_id}", 60*60*24, function() use ($province_id) {
                    $res = Http::withHeaders(['Token' => $this->ghnToken])
                        ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', ['province_id' => $province_id]);
                    return $res->ok() ? ($res->json()['data'] ?? []) : [];
                });
                $district = collect($districts)->firstWhere('DistrictID', $district_id) ?? [];

                // GHN API lấy wards theo district_id
                $wards = Cache::remember("ghn_wards_{$district_id}", 60*60*24, function() use ($district_id) {
                    $res = Http::withHeaders(['Token' => $this->ghnToken])
                        ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', ['district_id' => $district_id]);
                    return $res->ok() ? ($res->json()['data'] ?? []) : [];
                });
                $ward = collect($wards)->firstWhere('WardCode', $ward_id) ?? [];

                $order->ghn_address = [
                    'province' => $province['ProvinceName'] ?? $address->province_name,
                    'district' => $district['DistrictName'] ?? $address->district_name,
                    'ward' => $ward['WardName'] ?? $address->ward_name,
                    'address_detail' => $address_detail ?? ''
                ];
            }
        }

        return view('client.orders.detail', compact('order'));
    }
    public function cancel($order_code)
{
    $order = Order::where('order_code', $order_code)
        ->where('account_id', Auth::id())
        ->firstOrFail();

    if($order->status_id != 1) {
        return back()->with('error', 'Đơn hàng không thể hủy vì đã được xác nhận hoặc đang xử lý.');
    }

    // Cập nhật trạng thái sang Hủy đơn hàng
    $order->status_id = 11; // 11 = Hủy đơn hàng
    $order->save();

    return back()->with('success', 'Bạn đã hủy đơn hàng thành công.');
}

}
