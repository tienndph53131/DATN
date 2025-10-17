<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;


class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
{
    $request->validate([
    'name' => 'required|in:Màu sắc,Kích cỡ',
    'values' => 'required|array|min:1',
    'values.*' => 'required|string|max:50',
], [
    'name.required' => 'Vui lòng chọn loại thuộc tính.',
    'name.in' => 'Thuộc tính chỉ được chọn Màu sắc hoặc Kích cỡ.',
    'values.required' => 'Vui lòng nhập ít nhất một giá trị.',
    'values.*.required' => 'Không được để trống giá trị thuộc tính.',
]);
    // Tạo thuộc tính (màu sắc hoặc kích cỡ)
    $attribute = Attribute::create([
        'name' => $request->name,
    ]);

    // Lưu các giá trị thuộc tính (attribute_values)
    if ($request->has('values')) {
        foreach ($request->values as $val) {
            if (!empty($val)) {
                $attribute->values()->create([
                    'value' => $val
                ]);
            }
        }
    }

    return redirect()->route('attributes.index')->with('success', 'Thêm thuộc tính thành công!');
}

    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|in:Màu sắc,Kích cỡ',
        'values' => 'required|array|min:1',
        'values.*' => 'required|string|max:50',
    ], [
        'name.required' => 'Vui lòng chọn loại thuộc tính.',
        'name.in' => 'Thuộc tính chỉ được chọn Màu sắc hoặc Kích cỡ.',
        'values.required' => 'Vui lòng nhập ít nhất một giá trị.',
        'values.*.required' => 'Không được để trống giá trị thuộc tính.',
    ]);

    $attribute = Attribute::findOrFail($id);
    $attribute->update(['name' => $request->name]);

    // Xóa giá trị cũ và thêm mới
    $attribute->values()->delete();
    foreach ($request->values as $value) {
        $attribute->values()->create(['value' => $value]);
    }

    return redirect()->route('attributes.index')->with('success', 'Cập nhật thuộc tính thành công!');
}


    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();
        return redirect()->route('attributes.index')->with('success', 'Xóa thành công!');
    }
}