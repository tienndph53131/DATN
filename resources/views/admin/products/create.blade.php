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

    <div class="mb-3 d-flex align-items-center gap-2">
        <input type="number" id="variant-count" class="form-control" placeholder="Nhập số lượng biến thể" min="1" style="width:200px;">
        <button type="button" id="generate-variants" class="btn btn-secondary">Tạo biến thể</button>
    </div>

    <div id="variants"></div>

    {{--  Input ẩn để gửi tổng số lượng --}}
    <input type="hidden" name="total_quantity" id="total_quantity" value="0">

    {{-- Hiển thị tổng số lượng bên ngoài cho admin xem --}}
    <div class="alert alert-info mt-3" id="totalDisplay" style="display:none;">
        Tổng số lượng sản phẩm: <strong id="totalCount">0</strong>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Thêm sản phẩm</button>
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

    for (let i = 0; i < count; i++) {
        const index = currentCount + i;
        const html = `
            <div class="variant-item border p-3 mb-3 rounded position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Biến thể #${index + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa</button>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="number" name="variants[${index}][price]" class="form-control" placeholder="Giá biến thể">
                        <small class="text-danger error-text"></small>
                    </div>

                    <div class="col-md-3 mb-2">
                        <input type="number" name="variants[${index}][stock_quantity]" class="form-control stock-input" placeholder="Số lượng" >
                        <small class="text-danger error-text"></small>
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="variants[${index}][attributes][color]" class="form-control">
                            ${colorOptions}
                        </select>
                        <small class="text-danger error-text"></small>
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="variants[${index}][attributes][size]" class="form-control">
                            ${sizeOptions}
                        </select>
                        <small class="text-danger error-text"></small>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    document.getElementById('variant-count').value = '';
    updateTotalQuantity();
});

// Xóa biến thể
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variant')) {
        e.target.closest('.variant-item').remove();
        document.querySelectorAll('.variant-item').forEach((item, idx) => {
            item.querySelector('h6').textContent = `Biến thể #${idx + 1}`;
        });
        updateTotalQuantity();
    }
});

// Tự động tính tổng số lượng mỗi khi thay đổi số lượng
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('stock-input')) {
        updateTotalQuantity();
    }
});

//  Hàm tính tổng số lượng biến thể
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

//  Kiểm tra lỗi trước khi submit 
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let hasError = false;

    // Xóa lỗi cũ
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-control').forEach(el => el.style.border = '');

    const form = e.target;

    // Kiểm tra tên sản phẩm
    const name = form.querySelector('input[name="name"]');
    if (!name.value.trim()) {
        hasError = true;
        name.style.border = '2px solid red';
        name.nextElementSibling.textContent = 'Vui lòng nhập tên sản phẩm.';
    }

    // Kiểm tra danh mục
    const category = form.querySelector('select[name="category_id"]');
    if (!category.value) {
        hasError = true;
        category.style.border = '2px solid red';
        category.nextElementSibling.textContent = 'Vui lòng chọn danh mục.';
    }

    // Kiểm tra ảnh
    const image = form.querySelector('input[name="image"]');
    @if(!old('image'))
    if (!image.value) {
        hasError = true;
        image.style.border = '2px solid red';
        image.nextElementSibling.textContent = 'Vui lòng chọn ảnh sản phẩm.';
    }
    @endif

    const variants = document.querySelectorAll('.variant-item');
    const seen = new Set();

    if (variants.length === 0) {
        alert('Vui lòng thêm ít nhất một biến thể sản phẩm!');
        return;
    }

    // Kiểm tra từng biến thể
    variants.forEach(item => {
        const price = item.querySelector('input[name*="[price]"]');
        const stock = item.querySelector('input[name*="[stock_quantity]"]');
        const color = item.querySelector('select[name*="[color]"]');
        const size = item.querySelector('select[name*="[size]"]');
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

        if (!color.value) {
            hasError = true;
            color.style.border = '2px solid red';
            errors[2].textContent = 'Chọn màu.';
        }

        if (!size.value) {
            hasError = true;
            size.style.border = '2px solid red';
            errors[3].textContent = 'Chọn size.';
        }

        // Kiểm tra trùng màu + size
        const combo = `${color.value}-${size.value}`;
        if (seen.has(combo)) {
            hasError = true;
            color.style.border = '2px solid red';
            size.style.border = '2px solid red';
            errors[2].textContent = 'Trùng với biến thể khác.';
            errors[3].textContent = 'Trùng với biến thể khác.';
        }
        seen.add(combo);
    });

    if (!hasError) {
        updateTotalQuantity();
        form.submit();
    }
});
</script>

<style>
.error-text {
    font-size: 13px;
    margin-top: 2px;
    display: block;
}
</style>

@endsection
