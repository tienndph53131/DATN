<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
        'description' => 'nullable|string',
    ], [
        'name.required' => 'Vui lòng nhập tên danh mục!',
        'name.max' => 'Tên danh mục không được vượt quá 255 ký tự!',
    ]);

    Category::create($request->only(['name', 'description']));

    return redirect()->route('categories.index')->with('success', 'Thêm danh mục thành công!');
}

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
        ],[
             'name.required' => 'Vui lòng nhập tên danh mục!',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự!',

        

        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

   public function destroy(Category $category)
{
    if ($category->products()->count() > 0) {
        return redirect()->route('categories.index')
            ->with('error', 'Danh mục này còn sản phẩm, không thể xóa!');
    }

    $category->delete();
    return redirect()->route('categories.index')->with('success', 'Xóa danh mục thành công!');
}
}
