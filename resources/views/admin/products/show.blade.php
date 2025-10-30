@extends('layouts.admin.admin')

@section('title', 'Chi tiết sản phẩm')

@section('content')

<div class="container mt-4">
    <h2>Chi tiết sản phẩm</h2>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mb-3">← Quay lại</a>

```
<div class="card p-4 shadow-sm">
    <div class="row">
        <div class="col-md-4 text-center">
            @if($product->image)
                <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
            @else
                <p>Không có ảnh chính</p>
            @endif

            {{-- Hiển thị ảnh phụ từ bảng product_images --}}
            @if($product->images->count())
                <h6>Ảnh chi tiết:</h6>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    @foreach($product->images as $img)
                        <img src="{{ asset($img->link_images) }}" alt="Ảnh phụ" class="img-thumbnail" width="80" height="80">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <h4>{{ $product->name }}</h4>
            <p><strong>Danh mục:</strong> {{ $product->category->name ?? 'Không có' }}</p>
            <p><strong>Mô tả:</strong> {{ $product->description ?? 'Chưa có mô tả' }}</p>
            <p><strong>Trạng thái:</strong> {{ $product->status ? 'Còn hàng' : 'Hết hàng' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>

            <hr>
            <h5>Biến thể & Giá</h5>
            @forelse($product->variants as $variant)
                <div class="variant-box">
                    <div class="variant-price">
                        Giá: {{ number_format($variant->price, 0, ',', '.') }}₫
                    </div>
                    <div class="variant-attrs">
                        Màu, Size: {{ $variant->attributeValues->pluck('value')->join(', ') }}
                    </div>
                    <div class="variant-stock text-muted">
                        Số lượng: {{ $variant->stock_quantity }}
                    </div>

                    {{-- Hiển thị ảnh riêng của biến thể (nếu có) --}}
                    @if($variant->images->count())
                        <div class="variant-images mt-2">
                            @foreach($variant->images as $vimg)
                                <img src="{{ asset($vimg->link_images) }}" alt="Ảnh biến thể" class="img-thumbnail" width="70" height="70">
                            @endforeach
                        </div>
                    @endif
                </div>

                @if(!$loop->last)
                    <hr class="variant-divider">
                @endif
            @empty
                <em>Không có biến thể</em>
            @endforelse
        </div>
    </div>
</div>
```

</div>

<style>
.variant-box { margin-bottom: 10px; }
.variant-price { font-weight: bold; color: #222; font-size: 15px; }
.variant-attrs { color: #555; font-size: 13px; margin-top: 2px; }
.variant-stock { font-size: 13px; color: #777; }
.variant-divider { border: none; border-top: 1px solid #eee; margin: 8px 0; }
.variant-images img { border-radius: 8px; margin-right: 4px; }
</style>

@endsection
