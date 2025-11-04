<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    // Danh sách
    public function index()
    {
        $values = AttributeValue::with('attribute')->get();
        return view('admin.attribute_values.index', compact('values'));
    }

    // Form thêm
    public function create()
    {
        $attributes = Attribute::all();
        return view('admin.attribute_values.create', compact('attributes'));
    }

    // Xử lý thêm
    public function store(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    // Kiểm tra trùng lặp giá trị trong cùng thuộc tính (không phân biệt hoa thường)
                    $exists = AttributeValue::where('attribute_id', $request->attribute_id)
                        ->whereRaw('LOWER(value) = ?', [strtolower($value)])
                        ->exists();

                    if ($exists) {
                        $fail('Giá trị "' . $value . '" đã tồn tại cho thuộc tính này!');
                    }
                },
            ],
        ], [
            'attribute_id.required' => 'Vui lòng chọn thuộc tính!',
            'attribute_id.exists' => 'Thuộc tính được chọn không hợp lệ!',
            'value.required' => 'Vui lòng nhập giá trị thuộc tính!',
            'value.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự!',
        ]);

        AttributeValue::create($request->all());

        return redirect()->route('attribute_values.index')->with('success', 'Thêm giá trị thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $value = AttributeValue::findOrFail($id);
        $attributes = Attribute::all();
        return view('admin.attribute_values.edit', compact('value', 'attributes'));
    }

    // Xử lý sửa
    public function update(Request $request, $id)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $id) {
                    // Kiểm tra trùng lặp trừ chính bản ghi hiện tại
                    $exists = AttributeValue::where('attribute_id', $request->attribute_id)
                        ->whereRaw('LOWER(value) = ?', [strtolower($value)])
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($exists) {
                        $fail('Giá trị "' . $value . '" đã tồn tại cho thuộc tính này!');
                    }
                },
            ],
        ], [
            'attribute_id.required' => 'Vui lòng chọn thuộc tính!',
            'attribute_id.exists' => 'Thuộc tính được chọn không hợp lệ!',
            'value.required' => 'Vui lòng nhập giá trị thuộc tính!',
            'value.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự!',
        ]);

        $value = AttributeValue::findOrFail($id);
        $value->update($request->all());

        return redirect()->route('attribute_values.index')->with('success', 'Cập nhật thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $value = AttributeValue::findOrFail($id);
        $value->delete();
        return redirect()->route('attribute_values.index')->with('success', 'Xóa thành công!');
    }
}
