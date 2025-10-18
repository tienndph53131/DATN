<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHNService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.base_url', 'https://online-gateway.ghn.vn');
        $this->token = env('GHN_TOKEN');
    }

    /**
     * Lấy danh sách tỉnh/huyện/ward (ví dụ endpoints GHN)
     */
    public function getProvinces()
    {
        $res = Http::withHeaders([
            'Token' => $this->token,
            'Content-Type' => 'application/json'
        ])->get($this->baseUrl . '/shiip/public-api/master-data/province');

        return $res->successful() ? $res->json() : null;
    }
}
