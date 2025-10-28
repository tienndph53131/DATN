@extends('layouts.admin.admin')

@section('title','Thêm sản phẩm')

@section('content')
<h2>Thêm sản phẩm</h2>

<form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data" id="productForm">
    @csrf

    <div class="mb-3">
        <label>Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        <small class="text-danger error-msg"></small>
    </div>

    <div class="mb-3">
        <label>Danh mục</label>
        <select name="category_id" class="form-control">
            <option value="">Chọn danh mục</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <small class="text-danger error-msg"></small>
    </div>

    <div class="mb-3">
        <label>Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
        <small class="text-danger error-msg"></small>
    </div>

    <div class="mb-3">
        <label>Mô tả sản phẩm</label>
        <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả sản phẩm...">{{ old('description') }}</textarea>
    </div>

    <div class="mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Còn hàng</option>
            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Hết hàng</option>
        </select>
    </div>

    <hr>
    <h5>Biến thể sản phẩm</h5>

    {{-- Chọn màu sắc và kích cỡ bằng checkbox --}}
    <div class="row mb-3">
        <div class="col-md-5">
            <label><strong>Chọn Màu sắc</strong></label><br>
            @foreach($attributes->where('name','Màu sắc')->first()->values ?? [] as $val)
                <label class="me-3">
                    <input type="checkbox" name="selectedColors[]" value="{{ $val->id }}"> {{ $val->value }}
                </label>
            @endforeach
        </div>
        <div class="col-md-5">
            <label><strong>Chọn Kích cỡ</strong></label><br>
            @foreach($attributes->where('name','Kích cỡ')->first()->values ?? [] as $val)
                <label class="me-3">
                    <input type="checkbox" name="selectedSizes[]" value="{{ $val->id }}"> {{ $val->value }}
                </label>
            @endforeach
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" id="generate-variants" class="btn btn-secondary w-100">Tạo biến thể</button>
        </div>
    </div>

    <div id="variants"></div>

    <input type="hidden" name="total_quantity" id="total_quantity" value="0">

    <div class="alert alert-info mt-3" id="totalDisplay" style="display:none;">
        Tổng số lượng sản phẩm: <strong id="totalCount">0</strong>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Thêm sản phẩm</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Hủy</a>
</form>


<script>
// 🔹 Tạo biến thể từ checkbox
document.getElementById('generate-variants').addEventListener('click', function() {
    const container = document.getElementById('variants');
    const colors = Array.from(document.querySelectorAll('input[name="selectedColors[]"]:checked'));
    const sizes = Array.from(document.querySelectorAll('input[name="selectedSizes[]"]:checked'));

    if (colors.length === 0 || sizes.length === 0) {
        alert('Vui lòng chọn ít nhất một màu và một kích cỡ!');
        return;
    }

    // Xóa danh sách cũ
    container.innerHTML = '';

    let index = 0;
    let stt = 1;

    for (const color of colors) {
        for (const size of sizes) {
            const colorLabel = color.nextSibling.textContent.trim();
            const sizeLabel = size.nextSibling.textContent.trim();

            const html = `
                <div class="variant-item border p-3 mb-3 rounded position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">#${stt}. Biến thể: ${colorLabel} - ${sizeLabel}</h6>
                        <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa</button>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="number" name="variants[${index}][price]" class="form-control" placeholder="Giá biến thể">
                            <small class="text-danger error-text"></small>
                        </div>

                        <div class="col-md-4 mb-2">
                            <input type="number" name="variants[${index}][stock_quantity]" class="form-control stock-input" placeholder="Số lượng">
                            <small class="text-danger error-text"></small>
                        </div>

                        <input type="hidden" name="variants[${index}][attributes][color]" value="${color.value}">
                        <input type="hidden" name="variants[${index}][attributes][size]" value="${size.value}">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            index++;
            stt++;
        }
    }

    updateTotalQuantity();
    updateVariantOrder();
});

// 🔹 Xóa biến thể
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variant')) {
        e.target.closest('.variant-item').remove();
        updateTotalQuantity();
        updateVariantOrder();
    }
});

// 🔹 Cập nhật lại số thứ tự khi xóa hoặc thêm
function updateVariantOrder() {
    const items = document.querySelectorAll('.variant-item h6');
    let i = 1;
    items.forEach(item => {
        item.innerHTML = item.innerHTML.replace(/#\d+/, `#${i}`);
        i++;
    });
}

// 🔹 Tự động tính tổng số lượng
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('stock-input')) {
        updateTotalQuantity();
    }
});

// 🔹 Hàm tính tổng số lượng
function updateTotalQuantity() {
    let total = 0;
    document.querySelectorAll('.stock-input').forEach(input => {
        total += parseInt(input.value || 0);
    });

    document.getElementById('total_quantity').value = total;
    const display = document.getElementById('totalDisplay');
    const count = document.getElementById('totalCount');

    if (total > 0) {
        display.style.display = 'block';
        count.textContent = total;
    } else {
        display.style.display = 'none';
    }
}

// 🔹 Validate trước khi submit
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let hasError = false;

    // Xóa lỗi cũ
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-control').forEach(el => el.style.border = '');

    const form = e.target;

    const name = form.querySelector('input[name="name"]');
    if (!name.value.trim()) {
        hasError = true;
        name.style.border = '2px solid red';
        name.nextElementSibling.textContent = 'Vui lòng nhập tên sản phẩm.';
    }

    const category = form.querySelector('select[name="category_id"]');
    if (!category.value) {
        hasError = true;
        category.style.border = '2px solid red';
        category.nextElementSibling.textContent = 'Vui lòng chọn danh mục.';
    }

    const image = form.querySelector('input[name="image"]');
    if (!image.value) {
        hasError = true;
        image.style.border = '2px solid red';
        image.nextElementSibling.textContent = 'Vui lòng chọn ảnh sản phẩm.';
    }

    const variants = document.querySelectorAll('.variant-item');
    if (variants.length === 0) {
        alert('Vui lòng tạo ít nhất một biến thể!');
        return;
    }

    variants.forEach(item => {
        const price = item.querySelector('input[name*="[price]"]');
        const stock = item.querySelector('input[name*="[stock_quantity]"]');
        const errors = item.querySelectorAll('.error-text');

        if (!price.value) {
            hasError = true;
            price.style.border = '2px solid red';
            errors[0].textContent = 'Nhập giá biến thể.';
        } else if (parseFloat(price.value) < 0) {
            hasError = true;
            price.style.border = '2px solid red';
            errors[0].textContent = 'Giá không được nhỏ hơn 0.';
        }

        if (!stock.value) {
            hasError = true;
            stock.style.border = '2px solid red';
            errors[1].textContent = 'Nhập số lượng.';
        } else if (parseInt(stock.value) < 0) {
            hasError = true;
            stock.style.border = '2px solid red';
            errors[1].textContent = 'Số lượng không được nhỏ hơn 0.';
        }
    });

    if (!hasError) {
        updateTotalQuantity();
        form.submit();
    }
});
</script>

<style>
.error-text { font-size: 13px; margin-top: 2px; display: block; }
label.me-3 { font-weight: normal; }
.variant-item { background: #f9f9f9; }
</style>
@endsection
