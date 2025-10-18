@extends('layouts.admin.admin')

@section('title','Thêm sản phẩm')

@section('content')
<h2>Thêm sản phẩm</h2>

<form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
    </div>

    <div class="mb-3">
        <label>Danh mục</label>
        <select name="category_id" class="form-control">
            <option value="">Chọn danh mục</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Giá cơ bản</label>
        <input type="number" name="price" class="form-control" value="{{ old('price') }}">
    </div>

    <div class="mb-3">
        <label>Số lượng</label>
        <input type="number" name="quantity" class="form-control" value="{{ old('quantity',0) }}">
    </div>

    <div class="mb-3">
        <label>Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label>Giá khuyến mãi</label>
        <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price') }}">
    </div>

    <div class="mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            <option value="1">còn hàng</option>
            <option value="0">hết hàng</option>
        </select>
    </div>

    <hr>
    <h5>Biến thể sản phẩm</h5>
   <div id="variants"></div>
<button type="button" id="add-variant" class="btn btn-secondary mb-3">Thêm biến thể</button>
<button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
<script>
document.getElementById('add-variant').addEventListener('click', function() {
    const index = document.querySelectorAll('.variant-item').length;

    // Lấy danh sách giá trị màu và size từ Blade
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
