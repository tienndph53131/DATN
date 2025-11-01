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
        <label>Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
        @if($product->image)
            <img src="{{ asset('uploads/products/'.$product->image) }}" width="120" class="mt-2 rounded border">
        @endif
    </div>

    
<div class="mb-3">
    <label>Mô tả sản phẩm</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
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
    @php
        // Chuẩn bị giá trị thuộc tính an toàn (không phụ thuộc hoa/ thường)
        $colorsAttr = $attributes->first(function($a) { return mb_strtolower($a->name) === mb_strtolower('Màu sắc'); });
        $colorValues = $colorsAttr ? $colorsAttr->values : collect();
        $sizesAttr = $attributes->first(function($a) { return mb_strtolower($a->name) === mb_strtolower('Kích cỡ'); });
        $sizeValues = $sizesAttr ? $sizesAttr->values : collect();
    @endphp
    {{-- Nhập số biến thể mới muốn thêm --}}
    <div class="mb-3 d-flex align-items-center gap-2">
        <input type="number" id="variant-count" class="form-control" placeholder="Nhập số lượng biến thể muốn thêm" min="1" style="width:250px;">
        <button type="button" id="generate-variants" class="btn btn-secondary">Tạo biến thể mới</button>
    </div>

    <div id="variants">
        {{-- Biến thể hiện có --}}
        @foreach($product->variants as $i => $variant)
        <div class="variant-item border p-3 mb-2 rounded">
            <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Biến thể #{{ $i + 1 }}</h6>
                <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa</button>
            </div>

            <div class="row mt-2">
                <div class="col-md-3 mb-2">
                    <input type="number" name="variants[{{ $i }}][price]" class="form-control" value="{{ $variant->price }}" placeholder="Giá biến thể">
                </div>

                <div class="col-md-3 mb-2">
                    <input type="number" name="variants[{{ $i }}][stock_quantity]" class="form-control" value="{{ $variant->stock_quantity }}" placeholder="Số lượng">
                </div>

                <div class="col-md-3 mb-2">
                    <select name="variants[{{ $i }}][attributes][color]" class="form-control">
                        <option value="">Chọn màu</option>
                        @foreach($colorValues as $val)
                            <option value="{{ $val->id }}" {{ $variant->attributes->pluck('attribute_value_id')->contains($val->id) ? 'selected' : '' }}>
                                {{ $val->value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select name="variants[{{ $i }}][attributes][size]" class="form-control">
                        <option value="">Chọn kích cỡ</option>
                        @foreach($sizeValues as $val)
                            <option value="{{ $val->id }}" {{ $variant->attributes->pluck('attribute_value_id')->contains($val->id) ? 'selected' : '' }}>
                                {{ $val->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-2">
                    <label class="form-label">Ảnh biến thể</label>
                    <input type="file" name="variant_images[{{ $i }}][]" class="form-control" accept="image/*" multiple>
                    @if($variant->images && $variant->images->count())
                        <div class="mt-2 d-flex gap-2">
                            @foreach($variant->images as $img)
                                <img src="{{ asset('uploads/products/'.$img->link_images) }}" width="80" class="rounded border">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary mt-3">Cập nhật sản phẩm</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Hủy</a>
</form>

<script>
document.getElementById('generate-variants').addEventListener('click', function() {
    const count = parseInt(document.getElementById('variant-count').value);
    const container = document.getElementById('variants');
    const currentCount = container.querySelectorAll('.variant-item').length;

    if (isNaN(count) || count <= 0) {
        alert('Vui lòng nhập số lượng biến thể hợp lệ!');
        return;
    }

    const colorOptions = `
        <option value="">Chọn màu</option>
        @foreach($colorValues as $val)
            <option value="{{ $val->id }}">{{ $val->value }}</option>
        @endforeach
    `;
    const sizeOptions = `
        <option value="">Chọn kích cỡ</option>
        @foreach($sizeValues as $val)
            <option value="{{ $val->id }}">{{ $val->value }}</option>
        @endforeach
    `;

    for (let i = 0; i < count; i++) {
        const index = currentCount + i;
        const html = `
            <div class="variant-item border p-3 mb-2 rounded">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Biến thể #${index + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa</button>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3 mb-2">
                        <input type="number" name="variants[${index}][price]" class="form-control" placeholder="Giá biến thể" min="0">
                    </div>

                    <div class="col-md-3 mb-2">
                        <input type="number" name="variants[${index}][stock_quantity]" class="form-control" placeholder="Số lượng" min="0">
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="variants[${index}][attributes][color]" class="form-control">
                            ${colorOptions}
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="variants[${index}][attributes][size]" class="form-control">
                            ${sizeOptions}
                        </select>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    document.getElementById('variant-count').value = '';
});

// Xóa biến thể
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variant')) {
        e.target.closest('.variant-item').remove();
        document.querySelectorAll('.variant-item').forEach((item, idx) => {
            item.querySelector('h6').textContent = `Biến thể #${idx + 1}`;
        });
    }
});

//  Kiểm tra trước khi submit form
document.querySelector('form').addEventListener('submit', function(e) {
    const variants = document.querySelectorAll('.variant-item');
    if (variants.length === 0) {
        e.preventDefault();
        alert('Phải có ít nhất một biến thể sản phẩm!');
        return;
    }

    let hasError = false;
    const seen = new Set();

    variants.forEach((item) => {
        const priceInput = item.querySelector('input[name*="[price]"]');
        const stockInput = item.querySelector('input[name*="[stock_quantity]"]');
        const colorSelect = item.querySelector('select[name*="[attributes][color]"]');
        const sizeSelect = item.querySelector('select[name*="[attributes][size]"]');

        const price = priceInput.value.trim();
        const stock = stockInput.value.trim();
        const color = colorSelect.value;
        const size = sizeSelect.value;

        // Reset viền trước khi kiểm tra
        item.querySelectorAll('input, select').forEach(el => el.style.border = '');

        //  Kiểm tra thiếu thông tin
        if (!price || !stock || !color || !size) {
            hasError = true;
            item.querySelectorAll('input, select').forEach(el => {
                if (!el.value) el.style.border = '2px solid red';
            });
        }

        //  Kiểm tra giá và số lượng nhỏ hơn 0
        if (parseFloat(price) < 0) {
            hasError = true;
            priceInput.style.border = '2px solid red';
            alert('Giá không được nhỏ hơn 0!');
        }

        if (parseInt(stock) < 0) {
            hasError = true;
            stockInput.style.border = '2px solid red';
            alert('Số lượng không được nhỏ hơn 0!');
        }

        //  Kiểm tra trùng lặp (nếu đủ thông tin)
        if (color && size) {
            const key = `${color}-${size}`;
            if (seen.has(key)) {
                hasError = true;
                colorSelect.style.border = sizeSelect.style.border = '2px solid red';
                alert('Có biến thể bị trùng màu và size!');
            }
            seen.add(key);
        }
    });

    if (hasError) {
        e.preventDefault();
    }
});

// Tự động cập nhật số lượng biến thể về 0 khi chọn trạng thái "Hết hàng"
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === '0') { // '0' là giá trị của "Hết hàng"
                const confirmation = confirm('Bạn có muốn đặt số lượng của tất cả các biến thể về 0 không?');
                if (confirmation) {
                    document.querySelectorAll('input[name*="[stock_quantity]"]').forEach(input => {
                        input.value = 0;
                    });
                } else {
                    // Nếu người dùng hủy, trả lại lựa chọn "Còn hàng"
                    this.value = '1';
                }
            }
        });
    }
});
</script>




@endsection
