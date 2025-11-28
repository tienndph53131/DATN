<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Address;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected string $ghnToken;

    public function __construct()
    {
        $this->ghnToken = env('GHN_TOKEN');
    }

    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $terminalStatuses = [5, 6, 7];
    $terminalStatusesString = implode(', ', $terminalStatuses);
    $filterStatusId = $request->input('status_id') ?? '';
    $query = Order::with('account', 'details.productVariant.product');
    
    

   if ($filterStatusId !== '') {
        // Thêm điều kiện WHERE nếu có trạng thái được chọn 
        $query->where('status_id', $filterStatusId);
    }
        $orders = $query->orderByRaw("CASE 
            WHEN status_id IN ({$terminalStatusesString}) THEN 1
            ELSE 0
        END ASC") 
        ->latest() 
        ->paginate(10);;
        $status = OrderStatus::all(); // bảng order_status
        return view('admin.orders.index', compact('orders', 'status','filterStatusId'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $order->load(['details.product', 'details.variant.attributeValues', 'status', 'payment', 'paymentStatus']);
        $order = $this->loadGhnAddress($order);

        $status = OrderStatus::all(); // bảng order_status
        // Đã bỏ $paymentStatusList vì không còn dùng select box nữa

        return view('admin.orders.detail', compact('order', 'status'));
    }

    /**
     * Cập nhật đơn hàng
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_detail' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
           
            'status_id' => 'required|exists:order_status,id',
            
        ]);
        
        // LẤY ID TRẠNG THÁI CŨ VÀ MỚI
        $newStatusId = (int) $data['status_id'];
        $oldStatusId = (int) $order->status_id;

        // --- KHAI BÁO CÁC ID CẦN THIẾT (Giả định) ---
        $ORDER_SUCCESS_ID = 5; // ID trạng thái đơn hàng "Thành công"
        $PAYMENT_PAID_ID = 2;  //ID trạng thái thanh toán "Đã thanh toán"
        $terminalStatuses = [5, 6,7]; // Trạng thái kết thúc: 5 (Thành công), 6 (Hoàn hàng), 7 (Hủy)

        // --- BẮT ĐẦU LOGIC NGĂN QUAY NGƯỢC TRẠNG THÁI ---
        if (in_array($oldStatusId, $terminalStatuses) && $newStatusId !== $oldStatusId) {
            return redirect()->back()->withErrors([
                'status_id' => 'Đơn hàng đã ở trạng thái kết thúc (ID: ' . $oldStatusId . '), không thể thay đổi trạng thái nữa.'
            ]);
        }

        if ($newStatusId < $oldStatusId && $newStatusId !== 8) {
            return redirect()->back()->withErrors([
                'status_id' => 'Không thể quay lại trạng thái trước đó. Trạng thái mới phải lớn hơn hoặc bằng trạng thái hiện tại.'
            ]);
        }
        // --- KẾT THÚC LOGIC NGĂN QUAY NGƯỢC TRẠNG THÁI ---
        
        // Cập nhật thông tin khách hàng và ghi chú
        $order->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
        ]);

        // Cập nhật địa chỉ nếu có (logic giữ nguyên)
        if ($order->address_id) {
            $address = Address::find($order->address_id);
            if ($address) {
                $address->update([
                    'address_detail' => $data['address_detail'] ?? $address->address_detail,
                    'province_name' => $data['province'] ?? $address->province_name,
                    'district_name' => $data['district'] ?? $address->district_name,
                    'ward_name' => $data['ward'] ?? $address->ward_name,
                ]);
            }
        }

        // Cập nhật trạng thái đơn hàng
        $updateData = ['status_id' => $newStatusId];
        
        // --- LOGIC TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI THANH TOÁN ---
        if ($newStatusId == $ORDER_SUCCESS_ID) {
            // Nếu đơn hàng thành công, tự động cập nhật trạng thái thanh toán thành Đã thanh toán (ID 2)
            $updateData['payment_status_id'] = $PAYMENT_PAID_ID;
        }
        // --- KẾT THÚC LOGIC TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI THANH TOÁN ---

        $order->update($updateData);

        return redirect()->route('orders.show', $order)
                         ->with('success', 'Cập nhật đơn hàng thành công.');
    }

    /**
     * Load GHN address (giữ nguyên)
     */
    private function loadGhnAddress(Order $order): Order
    {
        // ... (giữ nguyên) ...
        $order->ghn_address = null;

        if (!$order->address_id) return $order;

        $address = Address::find($order->address_id);
        if (!$address) return $order;

        $province_id = $address->province_id;
        $district_id = $address->district_id;
        $ward_id = $address->ward_id;

        // Province
        $provinces = Cache::remember('ghn_provinces', 86400, function () {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
        $province = collect($provinces)->firstWhere('ProvinceID', $province_id) ?? [];

        // District
        $districts = Cache::remember("ghn_districts_{$province_id}", 86400, function () use ($province_id) {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', ['province_id' => $province_id]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
        $district = collect($districts)->firstWhere('DistrictID', $district_id) ?? [];

        // Ward
        $wards = Cache::remember("ghn_wards_{$district_id}", 86400, function () use ($district_id) {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', ['district_id' => $district_id]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
        $ward = collect($wards)->firstWhere('WardCode', $ward_id) ?? [];

        $order->ghn_address = [
            'province' => $province['ProvinceName'] ?? $address->province_name,
            'district' => $district['DistrictName'] ?? $address->district_name,
            'ward' => $ward['WardName'] ?? $address->ward_name,
            'address_detail' => $address->address_detail ?? ''
        ];

        return $order;
    }
}
