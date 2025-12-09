@extends('layouts.admin.admin')

@section('title','Chỉnh sửa sản phẩm')

@section('content')
<h2>Chỉnh sửa sản phẩm</h2>

{{-- Thêm ID cho form để dễ truy cập trong JS --}}
<form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data" id="product-edit-form">
    @csrf
    @method('PUT')

    {{-- PHẦN THÔNG TIN SẢN PHẨM KHÔNG ĐỔI --}}
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

    {{-- Nhập số biến thể mới muốn thêm --}}
    <div class="mb-3 d-flex align-items-center gap-2">
        <input type="number" id="variant-count" class="form-control" placeholder="Nhập số lượng biến thể muốn thêm" min="1" style="width:250px;">
        <button type="button" id="generate-variants" class="btn btn-secondary">Tạo biến thể mới</button>
    </div>

    <div id="variants">
        {{-- Biến thể hiện có (sử dụng chỉ số liên tục) --}}
        @foreach($product->variants as $i => $variant)
        <div class="variant-item border p-3 mb-2 rounded" data-variant-id="{{ $variant->id }}">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Biến thể #{{ $i + 1 }}</h6>
                <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa</button>
            </div>

            {{-- ID ẩn --}}
            <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">

            <div class="row mt-2">
                <div class="col-md-3 mb-2">
                    <input type="number" name="variants[{{ $i }}][price]" class="form-control" value="{{ $variant->price }}" placeholder="Giá biến thể" min="0">
                </div>

                <div class="col-md-3 mb-2">
                    <input type="number" name="variants[{{ $i }}][stock_quantity]" class="form-control" value="{{ $variant->stock_quantity }}" placeholder="Số lượng" min="0">
                </div>

                <div class="col-md-3 mb-2">
                    <select name="variants[{{ $i }}][attributes][color]" class="form-control">
                        <option value="">Chọn màu</option>
                        @foreach($attributes->where('name', 'Màu sắc')->first()->values ?? [] as $val)
                            <option value="{{ $val->id }}" {{ $variant->attributes->pluck('attribute_value_id')->contains($val->id) ? 'selected' : '' }}>
                                {{ $val->value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select name="variants[{{ $i }}][attributes][size]" class="form-control">
                        <option value="">Chọn kích cỡ</option>
                        @foreach($attributes->where('name', 'Kích cỡ')->first()->values ?? [] as $val)
                            <option value="{{ $val->id }}" {{ $variant->attributes->pluck('attribute_value_id')->contains($val->id) ? 'selected' : '' }}>
                                {{ $val->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary mt-3">Cập nhật sản phẩm</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Hủy</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantManager = (function() {
            const form = document.getElementById('product-edit-form');
            const variantsContainer = document.getElementById('variants');
            const generateButton = document.getElementById('generate-variants');
            const variantCountInput = document.getElementById('variant-count');
            
            // Dữ liệu tùy chọn Màu sắc và Kích cỡ được lấy từ Blade
            const colorOptionsHtml = `@foreach($attributes->where('name', 'Màu sắc')->first()->values ?? [] as $val)<option value="{{ $val->id }}">{{ $val->value }}</option>@endforeach`;
            const sizeOptionsHtml = `@foreach($attributes->where('name', 'Kích cỡ')->first()->values ?? [] as $val)<option value="{{ $val->id }}">{{ $val->value }}</option>@endforeach`;

            /**
             * Cập nhật lại chỉ số (index) cho tất cả các biến thể còn lại.
             * Quan trọng để Laravel nhận đúng dữ liệu mảng.
             */
            function reIndexVariants() {
                document.querySelectorAll('#variants .variant-item').forEach((item, idx) => {
                    // 1. Cập nhật tiêu đề hiển thị
                    item.querySelector('h6').textContent = `Biến thể #${idx + 1}`;

                    // 2. Cập nhật thuộc tính name của các input/select
                    item.querySelectorAll('input, select').forEach(el => {
                        const currentName = el.name;
                        if (currentName) {
                            // Thay thế chỉ số cũ bằng chỉ số mới (idx)
                            el.name = currentName.replace(/variants\[\d+\]|variants\[\]/, `variants[${idx}]`);
                        }
                    });
                });
            }

            /**
             * Tạo mã HTML cho một biến thể mới.
             * @param {number} index - Chỉ số của biến thể
             * @returns {string} Mã HTML
             */
            function createNewVariantHtml(index) {
                return `
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
                                    <option value="">Chọn màu</option>
                                    ${colorOptionsHtml}
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select name="variants[${index}][attributes][size]" class="form-control">
                                    <option value="">Chọn kích cỡ</option>
                                    ${sizeOptionsHtml}
                                </select>
                            </div>
                        </div>
                    </div>
                `;
            }

            /**
             * Xử lý tạo các biến thể mới theo số lượng người dùng nhập.
             */
            function handleGenerateVariants() {
                const count = parseInt(variantCountInput.value);
                const currentCount = variantsContainer.querySelectorAll('.variant-item').length;

                if (isNaN(count) || count <= 0) {
                    alert('Vui lòng nhập số lượng biến thể hợp lệ!');
                    return;
                }

                for (let i = 0; i < count; i++) {
                    const index = currentCount + i;
                    variantsContainer.insertAdjacentHTML('beforeend', createNewVariantHtml(index));
                }

                variantCountInput.value = '';
            }

            /**
             * Xử lý xóa một biến thể (sử dụng Event Delegation).
             */
            function handleRemoveVariant(e) {
                if (!e.target.classList.contains('remove-variant')) return;

                const item = e.target.closest('.variant-item');
                const hiddenId = item.querySelector('input[name*="[id]"]');

                // Nếu có ID, đây là biến thể cũ, cần thêm input ẩn để xóa trên server
                if (hiddenId && hiddenId.value) {
                    const deletedInput = document.createElement('input');
                    deletedInput.type = 'hidden';
                    deletedInput.name = 'deleted_variants[]';
                    deletedInput.value = hiddenId.value;
                    form.appendChild(deletedInput);
                }

                item.remove();
                reIndexVariants();
            }

            /**
             * Xử lý Validation (kiểm tra hợp lệ) trước khi submit form.
             */
            function handleFormSubmit(e) {
                // Luôn chạy re-indexing trước khi validate để đảm bảo chỉ số đúng
                reIndexVariants(); 
                
                const variants = document.querySelectorAll('.variant-item');
                
                if (variants.length === 0) {
                    e.preventDefault();
                    alert('Phải có ít nhất một biến thể sản phẩm!');
                    return;
                }

                let hasError = false;
                const seen = new Set(); // Dùng để kiểm tra trùng lặp Màu & Size

                variants.forEach((item) => {
                    const priceInput = item.querySelector('input[name*="[price]"]');
                    const stockInput = item.querySelector('input[name*="[stock_quantity]"]');
                    const colorSelect = item.querySelector('select[name*="[attributes][color]"]');
                    const sizeSelect = item.querySelector('select[name*="[attributes][size]"]');

                    // Reset border
                    item.querySelectorAll('input, select').forEach(el => el.style.border = '');

                    const price = priceInput?.value?.trim() ?? '';
                    const stock = stockInput?.value?.trim() ?? '';
                    const color = colorSelect?.value ?? '';
                    const size = sizeSelect?.value ?? '';

                    // 1. Kiểm tra trường bắt buộc
                    if (!price || !stock || !color || !size) {
                        hasError = true;
                        if (!price && priceInput) priceInput.style.border = '2px solid red';
                        if (!stock && stockInput) stockInput.style.border = '2px solid red';
                        if (!color && colorSelect) colorSelect.style.border = '2px solid red';
                        if (!size && sizeSelect) sizeSelect.style.border = '2px solid red';
                    }

                    // 2. Kiểm tra giá và số lượng >= 0
                    if (price && parseFloat(price) < 0) {
                        hasError = true;
                        if(priceInput) priceInput.style.border = '2px solid red';
                    }

                    if (stock && parseInt(stock) < 0) {
                        hasError = true;
                        if(stockInput) stockInput.style.border = '2px solid red';
                    }

                    // 3. Kiểm tra trùng lặp
                    if (color && size) {
                        const key = `${color}-${size}`;
                        if (seen.has(key)) {
                            hasError = true;
                            if(colorSelect) colorSelect.style.border = '2px solid red';
                            if(sizeSelect) sizeSelect.style.border = '2px solid red';
                            alert('Lỗi: Có biến thể bị trùng màu và size!');
                        }
                        seen.add(key);
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    // Bạn có thể thêm thông báo lỗi chung ở đây nếu muốn.
                }
            }

            /**
             * Khởi tạo các sự kiện.
             */
            function init() {
                // Sự kiện Tạo biến thể
                generateButton.addEventListener('click', handleGenerateVariants);

                // Sự kiện Xóa biến thể (Event Delegation)
                document.addEventListener('click', handleRemoveVariant);

                // Sự kiện Submit Form
                form.addEventListener('submit', handleFormSubmit);

                // Chạy re-indexing ban đầu để chắc chắn (thường không cần thiết nếu Blade đã index đúng)
                // reIndexVariants();
            }

            return {
                init: init
            };
        })();

        variantManager.init();
    });
</script>

@endsection