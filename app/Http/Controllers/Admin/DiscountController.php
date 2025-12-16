<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::query();
        if ($request->filled('search')) {
            $query->where('code', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }
        $discounts = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }
    public function create()
    {
        return view('admin.discounts.create');
    }
    public function store(Request $request)
    {
         $code = strtoupper(Str::slug($request->code, ''));

    
    $request->merge([
        'code' => $code
    ]);
        $data = $request->validate([
            'code' => 'required|max:50|unique:discounts,code',
            'description' => 'nullable',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'required|boolean',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|numeric|min:1'
        ],[
        'code.unique' => 'Mã giảm giá đã tồn tại',
    ]);


        Discount::create($data);
        return redirect()->route('discounts.index')->with('success', 'Thêm mã giảm giá thành công');
    }
    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        return view('admin.discounts.edit', compact('discount'));
    }
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
         $code = strtoupper(Str::slug($request->code, ''));

    // 2️ Ghi đè lại request
    $request->merge([
        'code' => $code
    ]);
        $data = $request->validate([
            'code' => 'required|max:50|unique:discounts,code,' . $discount->id,
            'description' => 'nullable',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'required|boolean',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|numeric|min:1'
        ],[
        'code.unique' => 'Mã giảm giá đã tồn tại',
    ]);
        $discount->update($data);
        return redirect()->route('discounts.index')->with('success', 'Cập nhật mã giảm giá thành công');
    }
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return redirect()->route('discounts.index');
    }
}