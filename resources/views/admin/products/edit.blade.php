@extends('layouts.admin.admin')

@section('title','Chỉnh sửa sản phẩm')

@section('content')
<h2>Chỉnh sửa sản phẩm</h2>

<form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}">
    </div>

    <div class="mb-3">
        <label>Danh mục</label>
        <select name="category_id" class="form-control">
            <option value="">Chọn danh mục</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Giá cơ bản</label>
        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}">
    </div>

    <div class="mb-3">
        <label>Số lượng</label>
        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}">
    </div>

    <div class="mb-3">
        <label>Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
        @if($product->image)
            <img src="{{ asset('uploads/products/'.$product->image) }}" width="120" class="mt-2">
        @endif
    </div>

    <div class="mb-3">
        <label>Giá khuyến mãi</label>
        <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
    </div>

    <div class="mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            <option value="1" {{ $product->status ? 'selected' : '' }}>Còn hàng</option>
            <option value="0" {{ !$product->status ? 'selected' : '' }}>Hết hàng</option>
        </select>
    </div>

    <hr>
    <h5>Biến thể sản phẩm</h5>
    <div id="variants">
        @foreach($product->variants as $i => $variant)
        <div class="variant-item border p-3 mb-2">
            <input type="number" name="variants[{{ $i }}][price]" class="form-control mb-2" value="{{ $variant->price }}" placeholder="Giá biến thể">
            <input type="number" name="variants[{{ $i }}][stock_quantity]" class="form-control mb-2" value="{{ $variant->stock_quantity }}" placeholder="Số lượng">

            <select name="variants[{{ $i }}][attributes][color]" class="form-control mb-2">
                <option value="">Chọn màu</option>
                @foreach($attributes->where('name', 'Màu sắc')->first()->values ?? [] as $val)
                    <option value="{{ $val->id }}" {{ $variant->attributes->pluck('attribute_value_id')->contains($val->id) ? 'selected' : '' }}>
                        {{ $val->value }}
                    </option>
                @endforeach
            </select>

            <select name="variants[{{ $i }}][attributes][size]" class="form-control mb-2">
                <option value="">Chọn kích cỡ</option>
                @foreach($attributes->where('name', 'Kích cỡ')->first()->values ?? [] as $val)
                    <option value="{{ $val->id }}" {{ $variant->attributes->pluck('attribute_value_id')->contains($val->id) ? 'selected' : '' }}>
                        {{ $val->value }}
                    </option>
                @endforeach
            </select>

            <button type="button" class="btn btn-danger remove-variant">Xóa</button>
        </div>
        @endforeach
    </div>

    <button type="button" id="add-variant" class="btn btn-secondary mb-3">Thêm biến thể</button>
    <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
</form>

<script>
document.getElementById('add-variant').addEventListener('click', function() {
    const index = document.querySelectorAll('.variant-item').length;

    const colorOptions = `
        <option value="">Chọn màu</option>
        @foreach($attributes->where('name', 'Màu sắc')->first()->values ?? [] as $val)
            <option value="{{ $val->id }}">{{ $val->value }}</option>
        @endforeach
    `;
    const sizeOptions = `
        <option value="">Chọn kích cỡ</option>
        @foreach($attributes->where('name', 'Kích cỡ')->first()->values ?? [] as $val)
            <option value="{{ $val->id }}">{{ $val->value }}</option>
        @endforeach
    `;

    const html = `
        <div class="variant-item border p-3 mb-2">
            <input type="number" name="variants[${index}][price]" class="form-control mb-2" placeholder="Giá biến thể">
            <input type="number" name="variants[${index}][stock_quantity]" class="form-control mb-2" placeholder="Số lượng">

            <select name="variants[${index}][attributes][color]" class="form-control mb-2">
                ${colorOptions}
            </select>

            <select name="variants[${index}][attributes][size]" class="form-control mb-2">
                ${sizeOptions}
            </select>

            <button type="button" class="btn btn-danger remove-variant">Xóa</button>
        </div>
    `;
    document.getElementById('variants').insertAdjacentHTML('beforeend', html);
});

// Xóa biến thể
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-variant')){
        e.target.closest('.variant-item').remove();
    }
});
</script>
<a href="{{ route('products.index') }}" class="btn btn-secondary">Hủy</a>
@endsection
