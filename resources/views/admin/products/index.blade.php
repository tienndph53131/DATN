@extends('layouts.admin.admin')



@section('content')

<form method="GET" action="{{ route('products.index') }}" class="mb-3 d-flex gap-2">
    <!-- Tìm theo tên -->
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên" class="form-control">

    <!-- Chọn danh mục -->
    <select name="category_id" class="form-select">
        <option value="">-- Chọn danh mục --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <!-- Chọn trạng thái (1 = còn, 0 = hết) -->
    <select name="status" class="form-select">
        <option value="">-- Chọn trạng thái --</option>
        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Hết hàng </option>
        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Còn hàng</option>
    </select>

    <button class="btn btn-primary">Tìm kiếm</button>
</form>

<div class="container mt-4">
    <h2>Danh sách sản phẩm</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">+ Thêm sản phẩm</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
 @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Danh mục</th>
              <th>Số lượng</th>
               <th> Giá & Biến thể</th>
                
                <th>Trạng thái</th>
                <th>Ngày tạo</th> <!-- Chỉ hiển thị ngày -->
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $pro)
                <tr>
                    <td>{{ $products->firstItem() + $index }}</td>
                    <td>
                        @if($pro->image)
                            <img src="{{ asset('uploads/products/' . $pro->image) }}" width="60">
                        @endif
                    </td>
                    <td>{{ $pro->name }}</td>
                    <td>{{ $pro->category->name ?? 'Không có' }}</td>
                   
                   <td>{{ $pro->variants_sum_stock_quantity ?? 0 }}</td>
                     <td>
                        @forelse($pro->variants as $variant)
                            <div class="border-bottom py-1">
                                <strong>{{ number_format($variant->price, 0, ',', '.') }}₫</strong>
                                <br>
                                @if($variant->attributeValues->count())
                                    @foreach($variant->attributeValues as $attr)
                                        <small>{{ $attr->value }}</small>@if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    <small>Không có thuộc tính</small>
                                @endif
                            </div>
                        @empty
                            <em>Không có biến thể</em>
                        @endforelse
                    </td>
                  
                    <td>{{ $pro->status ? 'còn' : 'hết hàng' }}</td>
                    <td>{{ $pro->created_at->format('d/m/Y') }}</td> <!-- Chỉ lấy ngày -->
                    <td>
                         <a href="{{ route('products.show', $pro->id) }}" class="btn btn-info btn-sm">Xem</a>
                        <a href="{{ route('products.edit', $pro->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('products.destroy', $pro->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa sản phẩm này?')" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

   {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection
