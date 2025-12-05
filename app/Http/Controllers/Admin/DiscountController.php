<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

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
        $data = $request->validate([
            'code' => 'required|max:50',
            'description' => 'nullable',
            'discount_type' => 'required|in:percent,fixed|',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'required|boolean',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|numeric|min:1'
        ]);
        Discount::create($data);
        return redirect()->route('discounts.index')->with('success', 'Them ma giam gia thanh cong');
    }
    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        return view('admin.discounts.edit', compact('discount'));
    }
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
        $data = $request->validate([
            'code' => 'required|max:50',
            'description' => 'nullable',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'required|boolean',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|numeric|min:1'
        ]);
        $discount->update($data);
        return redirect()->route('discounts.index')->with('success', 'Cap nhat ma giam gia thanh cong');
    }
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return redirect()->route('discounts.index');
    }
}
