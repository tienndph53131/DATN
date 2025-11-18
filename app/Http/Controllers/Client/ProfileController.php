<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    protected string $ghnToken = '';

    public function __construct()
    {
        $this->ghnToken = env('GHN_TOKEN', ''); // Token GHN của bạn (safely default to empty string)
    }

    /**
     * Hiển thị form chỉnh sửa profile
     */
   public function edit()
{
    /** @var Account $account */
    $account = Auth::guard('client')->user();

    // Lấy địa chỉ mặc định hoặc tạo rỗng
    $address = $account->addresses()->where('is_default', true)->first();

    if (!$address) {
        $address = $account->addresses()->first();
    }

    if (!$address) {
        $address = new Address([
            'province_id' => null,
            'district_id' => null,
            'ward_id' => null,
            'address_detail' => '',
        ]);
    }

    // Lấy danh sách tỉnh/thành
    $provinces = Cache::remember('ghn_provinces', 60 * 60 * 24, function () {
        $res = Http::withHeaders(['Token' => $this->ghnToken])
            ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        return $res->ok() ? ($res->json()['data'] ?? []) : [];
    });

    // Nếu có province_id thì mới load quận huyện
    $districts = [];
    if ($address->province_id) {
        $districts = Cache::remember("ghn_districts_{$address->province_id}", 60 * 60 * 24, function () use ($address) {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                    'province_id' => $address->province_id
                ]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
    }

    // Nếu có district_id thì mới load phường xã
    $wards = [];
    if ($address->district_id) {
        $wards = Cache::remember("ghn_wards_{$address->district_id}", 60 * 60 * 24, function () use ($address) {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                    'district_id' => $address->district_id
                ]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
    }

    return view('client.profile', compact('account', 'address', 'provinces', 'districts', 'wards'));
}

    /**
     * Cập nhật profile và địa chỉ
     */
     /**
     * Cập nhật profile và địa chỉ lưu lại db mới tạo nhiều chứ không phải cùng 1 db
     */
    public function update(Request $request)
    {
        /** @var Account $account */
        $account = Auth::guard('client')->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'birthday' => 'nullable|date',
            'email' => 'required|email|max:100|unique:accounts,email,' . $account->id,
            'phone' => 'nullable|string|max:20',
            'sex' => 'nullable|in:male,female,other',
            'province_id' => 'required|integer',
            'district_id' => 'required|integer',
            'ward_id' => 'required|string',
            'address_detail' => 'nullable|string|max:255',
        ]);

        // Cập nhật thông tin account
        $account->update($request->only('name', 'birthday', 'email', 'phone', 'sex'));

        // Reset is_default cho các địa chỉ khác
        Address::where('account_id', $account->id)->update(['is_default' => false]);

        // Lấy hoặc tạo địa chỉ mặc định
        $address = $account->addresses()->firstOrNew(['is_default' => true]);

        $address->province_id = $request->province_id;
        $address->district_id = $request->district_id;
        $address->ward_id = $request->ward_id;
        $address->address_detail = $request->address_detail ?? '';
        $address->is_default = true;

//   mỗi tài khoản 1 địa chỉ cố định
    // public function update(Request $request)  
    // {
    //     /** @var Account $account */
    //     $account = Auth::guard('client')->user();

    //     $request->validate([
    //         'name' => 'required|string|max:100',
    //         'birthday' => 'nullable|date',
    //         'email' => 'required|email|max:100|unique:accounts,email,' . $account->id,
    //         'phone' => 'nullable|string|max:20',
    //         'sex' => 'nullable|in:male,female,other',
    //         'province_id' => 'required|integer',
    //         'district_id' => 'required|integer',
    //         'ward_id' => 'required|string',
    //         'address_detail' => 'nullable|string|max:255',
    //     ]);

    //     // Cập nhật thông tin tài khoản
    //     $account->update($request->only('name', 'birthday', 'email', 'phone', 'sex'));

    //     // Tìm địa chỉ mặc định
    //     $address = $account->addresses()->where('is_default', true)->first();

    //     if (!$address) {
    //         // Nếu chưa có thì tạo mới
    //         $address = new Address();
    //         $address->account_id = $account->id;
    //     }

    //     // Reset is_default của các địa chỉ khác
    //     Address::where('account_id', $account->id)->update(['is_default' => false]);

    //     // Gán dữ liệu địa chỉ mới
    //     $address->province_id = $request->province_id;
    //     $address->district_id = $request->district_id;
    //     $address->ward_id = $request->ward_id;
    //     $address->address_detail = $request->address_detail ?? '';
    //     $address->is_default = true;

        // --- Lấy tên tỉnh/huyện/xã (có kiểm tra lỗi) ---
        $provinces = Cache::remember('ghn_provinces', 60 * 60 * 24, function () {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
        $province = collect($provinces)->firstWhere('ProvinceID', $request->province_id);
        $address->province_name = $province['ProvinceName'] ?? '';

        $districts = Cache::remember("ghn_districts_{$request->province_id}", 60 * 60 * 24, function () use ($request) {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                    'province_id' => $request->province_id
                ]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
        $district = collect($districts)->firstWhere('DistrictID', $request->district_id);
        $address->district_name = $district['DistrictName'] ?? '';

        $wards = Cache::remember("ghn_wards_{$request->district_id}", 60 * 60 * 24, function () use ($request) {
            $res = Http::withHeaders(['Token' => $this->ghnToken])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                    'district_id' => $request->district_id
                ]);
            return $res->ok() ? ($res->json()['data'] ?? []) : [];
        });
        $ward = collect($wards)->firstWhere('WardCode', $request->ward_id);
        $address->ward_name = $ward['WardName'] ?? '';

        // Lưu lại
        $address->save();

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /** 
     * AJAX load districts
     */
   public function getDistricts(Request $request)
{
    $provinceId = (int) $request->province_id;
    $token = $this->ghnToken;

    $res = Http::withHeaders([
        'Token' => $token,
        'Content-Type' => 'application/json'
    ])->post(
        'https://online-gateway.ghn.vn/shiip/public-api/master-data/district',
        ['province_id' => $provinceId]
    );

    if ($res->failed()) {
        return response()->json(['error' => 'GHN API failed', 'details' => $res->json()], 500);
    }

    return response()->json($res->json()['data'] ?? []);
}

public function getWards(Request $request)
{
    $districtId = (int) $request->district_id;
    $token = $this->ghnToken;

    $res = Http::withHeaders([
        'Token' => $token,
        'Content-Type' => 'application/json'
    ])->post(
        'https://online-gateway.ghn.vn/shiip/public-api/master-data/ward',
        ['district_id' => $districtId]
    );

    if ($res->failed()) {
        return response()->json(['error' => 'GHN API failed', 'details' => $res->json()], 500);
    }

    return response()->json($res->json()['data'] ?? []);
}

}
