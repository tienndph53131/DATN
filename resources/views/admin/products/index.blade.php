@extends('layouts.admin.admin')

@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container mt-4">
    <h2>Danh sách sản phẩm</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">+ Thêm sản phẩm</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Danh mục</th>
<<<<<<< HEAD
                <th>Giá</th>
                <th>Giá KM</th>
                <th>SL</th>
=======
              <th>Số lượng</th>
               <th> Giá & Biến thể</th>
                
>>>>>>> origin/tien
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
<<<<<<< HEAD
                    <td>{{ number_format($pro->price) }} đ</td>
                    <td>
                        @if($pro->sale_price && $pro->sale_price < $pro->price)
                            {{ number_format($pro->sale_price) }} đ
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $pro->quantity }}</td>
                    <td>{{ $pro->status ? 'còn' : 'hết hàng' }}</td>
                    <td>{{ $pro->created_at->format('d/m/Y') }}</td> <!-- Chỉ lấy ngày -->
                    <td>
=======
                   
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
>>>>>>> origin/tien
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
