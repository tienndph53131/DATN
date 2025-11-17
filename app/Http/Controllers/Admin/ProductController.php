<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // Hiển thị danh sách sản phẩm
  public function index(Request $request)
{
    $query = Product::with(['category','variants.attributeValues'])
        ->withSum('variants', 'stock_quantity') // cộng dồn stock_quantity
        ->orderBy('created_at', 'desc');

    //  Tìm kiếm theo tên sản phẩm
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('name', 'like', "%$search%");
    }

    //  Tìm kiếm theo danh mục
    if ($request->filled('category_id')) {
        $categoryId = $request->input('category_id');
        $query->where('category_id', $categoryId);
    }
     // Tìm kiếm theo trạng thái
    if ($request->filled('status')) {
    $status = $request->input('status'); // 1 = còn, 0 = hết
    $query->where('status', $status);
}


    $products = $query->paginate(10)->withQueryString(); // giữ query string khi paginate

    $categories = Category::all(); // truyền danh sách categories cho view filter

    return view('admin.products.index', compact('products', 'categories'));
}

    // Form thêm sản phẩm
    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('values')->get(); // truyền $attributes cho view
        return view('admin.products.create', compact('categories', 'attributes'));
    }

    // Lưu sản phẩm mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'nullable|exists:categories,id',
           
            'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:10048',
           
             'description' => 'nullable|string',
            
            'status' => 'boolean',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'nullable|integer',
            'variants.*.attributes' => 'nullable|array'
        ]);

        DB::transaction(function() use ($request) {
            $data = $request->only(['name','category_id','description','status']);

            // Xử lý ảnh
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/products'), $fileName);
                $data['image'] = $fileName;
            }

            $product = Product::create($data);

            // Lưu biến thể
            if ($request->has('variants')) {
                foreach ($request->variants as $v) {
                    $variant = $product->variants()->create([
                        'price' => $v['price'] ?? $product->price,
                        'stock_quantity' => $v['stock_quantity'] ?? 0,
                        'status' => 1,
                    ]);

                    if (!empty($v['attributes'])) {
                        foreach ($v['attributes'] as $attrValueId) {
                            $variant->attributes()->create([
                                'attribute_value_id' => $attrValueId
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('products.index')->with('success','Thêm sản phẩm thành công!');
    }

    // Form chỉnh sửa sản phẩm
    public function edit(Product $product)
{
    $categories = Category::all();
    $attributes = Attribute::with('values')->get();

    // Load biến thể + các attribute values
    $product->load('variants.attributes.attributeValue');

    return view('admin.products.edit', compact('product', 'categories', 'attributes'));
}
  public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:10048',
        'description' => 'nullable|string',
        'status' => 'boolean',
        'variants.*.price' => 'nullable|numeric',
        'variants.*.stock_quantity' => 'nullable|integer',
        'variants.*.attributes' => 'nullable|array',
        'deleted_variants.*' => 'nullable|integer'
    ]);

    DB::transaction(function() use ($request, $product) {
        $data = $request->only(['name','category_id','description','status']);

        // Xử lý ảnh mới
        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path('uploads/products/'.$product->image))) {
                File::delete(public_path('uploads/products/'.$product->image));
            }
            $file = $request->file('image');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $fileName);
            $data['image'] = $fileName;
        }

        $product->update($data);

        // Xóa các biến thể bị remove
        if ($request->deleted_variants ?? false) {
            foreach ($request->deleted_variants as $delId) {
                $v = $product->variants()->find($delId);
                if ($v) {
                    $v->attributes()->delete();
                    $v->delete();
                }
            }
        }

        // Lưu / update biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $v) {
                if (isset($v['id'])) {
                    // Update biến thể cũ
                    $variant = $product->variants()->find($v['id']);
                    if ($variant) {
                        $variant->update([
                            'price' => $v['price'] ?? $variant->price,
                            'stock_quantity' => $v['stock_quantity'] ?? $variant->stock_quantity,
                        ]);

                        // Update attributes
                        $attrIds = array_filter($v['attributes'] ?? []);
                        $variant->attributes()->delete();
                        foreach ($attrIds as $attrId) {
                            $variant->attributes()->create(['attribute_value_id' => $attrId]);
                        }
                    }
                } else {
                    // Tạo biến thể mới
                    $variant = $product->variants()->create([
                        'price' => $v['price'] ?? 0,
                        'stock_quantity' => $v['stock_quantity'] ?? 0,
                        'status' => 1,
                    ]);

                    $attrIds = array_filter($v['attributes'] ?? []);
                    foreach ($attrIds as $attrId) {
                        $variant->attributes()->create(['attribute_value_id' => $attrId]);
                    }
                }
            }
        }
    });

    return redirect()->route('products.index')->with('success','Cập nhật sản phẩm thành công!');
}

    // Xóa sản phẩm
    public function destroy(Product $product)
    {
        // Kiểm tra nếu sản phẩm đã có trong đơn hàng
    if ($product->orderDetails()->exists()) {
        return redirect()->route('products.index')
                         ->with('error', 'Sản phẩm này đã có đơn hàng, không thể xóa!');
    }
        DB::transaction(function() use ($product) {
            if ($product->image && File::exists(public_path('uploads/products/'.$product->image))) {
                File::delete(public_path('uploads/products/'.$product->image));
            }

            foreach ($product->variants as $variant) {
                $variant->attributes()->delete();
                $variant->delete();
            }

            $product->delete();
        });

        return redirect()->route('products.index')->with('success','Xóa sản phẩm thành công!');
    }
    //  show
    public function show($id)
{
    $product = Product::with(['category', 'variants.attributeValues'])->findOrFail($id);

    return view('admin.products.show', compact('product'));
}

}
