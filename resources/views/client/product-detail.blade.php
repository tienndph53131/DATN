@extends('layouts.partials.client')

@php
if (!function_exists('colorToCss')) {
    /**
     * Chuyển đổi tên màu tiếng Việt sang mã màu CSS.
     * @param string $colorName Tên màu (VD: "Đỏ", "Xanh lá", "Trắng")
     * @return string Mã màu CSS (VD: "red", "green", "white") hoặc trả về chính nó nếu không tìm thấy.
     */
    function colorToCss($colorName) {
        $colorNameLower = mb_strtolower(trim($colorName));
        $map = [
            'đỏ' => 'red',
            'xanh dương' => 'blue',
            'xanh lam' => 'blue',
            'xanh lá' => 'green',
            'vàng' => 'yellow',
            'đen' => 'black',
            'trắng' => 'white',
            'hồng' => 'pink',
            'tím' => 'purple',
            'cam' => 'orange',
            'nâu' => 'brown',
            'xám' => 'gray',
        ];
        return $map[$colorNameLower] ?? $colorName;
    }
}
@endphp

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Ảnh sản phẩm -->
        <div class="col-md-5 text-center">
            <img id="main-product-image" src="{{ $product->image ? asset('uploads/products/' . $product->image) : '' }}" 
                 class="img-fluid rounded shadow-sm" 
                 alt="{{ $product->name }}">
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-7">
            <h3 class="fw-bold mb-2">{{ $product->name }}</h3>
            <p class="text-muted mb-1">
                Trạng thái: 
                @if($product->status == 1)
                    <span class="text-success">Còn hàng</span>
                @else
                    <span class="text-danger">Hết hàng</span>
                @endif
            </p>

            <!-- Giá sản phẩm -->
            @php
                $defaultVariant = $product->variants->first();
            @endphp
            <div class="d-flex align-items-center mb-3">
                <h2 id="variant-price" class="text-danger fw-bold me-3 mb-0">
                    {{ $defaultVariant ? number_format($defaultVariant->price, 0, ',', '.') . '₫' : ( $product->price ? number_format($product->price,0,',','.').'₫' : 'Liên hệ' ) }}
                </h2>
            </div>

            <!-- Chọn màu sắc -->
            @if($colors->count())
            <div class="mb-3">
                <label class="fw-bold d-block mb-2">Màu sắc:</label>
                <div class="d-flex align-items-center">
                    @foreach($colors as $color)
                        <button type="button"
                                class="btn border rounded-circle p-3 me-2 color-option"
                                style="background-color: {{ colorToCss($color->value) }};"
                                data-attr-slug="{{ Illuminate\Support\Str::slug('Màu sắc','-') }}"
                                data-value-id="{{ $color->id }}"
                                data-value="{{ $color->value }}"
                                title="{{ $color->value }}"></button>
                    @endforeach
                    <span id="selected-color" class="fw-bold ms-2"></span>
                </div>
            </div>
            @endif

            <!-- Chọn kích thước -->
            @if($sizes->count())
            <div class="mb-4">
                <label class="fw-bold d-block mb-2">Kích thước:</label>
                <div class="d-flex align-items-center flex-wrap">
                    @foreach($sizes as $size)
                        <button type="button"
                                class="btn btn-outline-secondary me-2 mb-2 size-option"
                                data-attr-slug="{{ Illuminate\Support\Str::slug('Kích cỡ','-') }}"
                                data-value-id="{{ $size->id }}"
                                data-value="{{ $size->value }}">
                            {{ $size->value }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Số lượng -->
            <div class="mb-4">
                <label class="fw-bold d-block mb-2">Số lượng:</label>
                <div class="input-group w-25">
                    <button class="btn btn-outline-secondary" type="button" id="decrease">-</button>
                    <input type="number" class="form-control text-center" value="1" min="1" id="quantity">
                    <button class="btn btn-outline-secondary" type="button" id="increase">+</button>
                </div>
            </div>

            <!-- Thêm vào giỏ -->
            <div class="d-flex gap-3 mb-3">
                <form action="{{ route('cart.add') }}" method="POST" id="buyForm">
                    @csrf
                    <input type="hidden" name="variant_id" id="variant-id">
                    <input type="hidden" name="quantity" id="input-quantity" value="1">

                    <button type="submit" id="add-to-cart-btn" class="btn btn-danger px-4 py-2 fw-bold" disabled>
                        <i class="fa fa-shopping-cart me-2"></i>THÊM VÀO GIỎ
                    </button>
                </form>
            </div>

            <p class="text-muted mt-1">Lượt xem: {{ $product->view ?? 0 }}</p>

            <!-- Mô tả sản phẩm -->
            @if($product->description)
            <div class="mt-4">
                <h5 class="fw-bold">Mô tả sản phẩm</h5>
                <p>{{ $product->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    @if($relatedProducts->count())
    <div class="mt-5">
        <h4 class="mb-4 fw-bold">Sản phẩm liên quan</h4>
        <div class="row">
            @foreach($relatedProducts as $item)
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="{{ asset('uploads/products/' . $item->image) }}" 
                             class="card-img-top" 
                             alt="{{ $item->name }}">
                        <div class="card-body text-center">
                            <h6 class="fw-bold">{{ $item->name }}</h6>
                            @php $itemPrice = $item->variants->min('price'); @endphp
                            @if($itemPrice)
                                <p class="text-danger fw-bold mb-2">
                                    {{ number_format($itemPrice, 0, ',', '.') }}₫
                                </p>
                            @else
                                <p class="text-muted mb-2">Liên hệ</p>
                            @endif
                            <a href="{{ route('product.show', $item->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Script -->
<script>
const variantData = @json($variantData);
let selected = {};
// Use static slugs to avoid Blade parsing issues
const COLOR_SLUG = 'mau-sac';
const SIZE_SLUG = 'kich-co';

const priceEl = document.getElementById('variant-price');
const variantIdInput = document.getElementById('variant-id');
const addToCartBtn = document.getElementById('add-to-cart-btn');

function normalize(v) {
    return (v || '').toString().trim().toLowerCase();
}

function updateUIForVariant(variant) {
    if (!variant) return;
    // price
    priceEl.textContent = new Intl.NumberFormat('vi-VN').format(variant.price) + '₫';
    // variant id
    variantIdInput.value = variant.id || '';
    // image
    if (variant.image) {
        const imgEl = document.getElementById('main-product-image');
        imgEl.src = variant.image;
    }
    // set selected color text if exists (use attributes_text map keyed by slug)
    if (variant.attributes_text && variant.attributes_text[COLOR_SLUG]) {
        document.getElementById('selected-color').textContent = variant.attributes_text[COLOR_SLUG];
    }
    // mark active buttons for color and size
    document.querySelectorAll('.color-option, .size-option').forEach(b => b.classList.remove('active', 'border-primary'));
    if (variant.attributes) {
        Object.entries(variant.attributes).forEach(([slug, val]) => {
            const buttons = document.querySelectorAll(`[data-attr-slug="${slug}"]`);
            buttons.forEach(b => {
                // compare by attribute_value id (data-value-id)
                if (b.dataset.valueId && b.dataset.valueId.toString() === val.toString()) {
                    b.classList.add('active', 'border-primary');
                    selected[slug] = val.toString();
                }
            });
        });
    }
}

function findVariantByAttributes(attrs) {
    const selectedCount = Object.keys(attrs).length;
    if (selectedCount === 0) return null;

    return variantData.find(v => {
        if (!v.attributes) return false;
        const variantAttrCount = Object.keys(v.attributes).length;
        // Phải khớp số lượng thuộc tính
        if (variantAttrCount !== selectedCount) return false;
        // Tất cả thuộc tính được chọn phải khớp với biến thể
        return Object.entries(attrs).every(([slug, val]) =>
            v.attributes[slug] && v.attributes[slug].toString() === val.toString()
        );
    });
}

function updateAvailableOptions() {
    // Lấy tất cả các nút tùy chọn
    const colorOptions = document.querySelectorAll('.color-option');
    const sizeOptions = document.querySelectorAll('.size-option');
    const hasColor = document.querySelectorAll('.color-option').length > 0;
    const hasSize = document.querySelectorAll('.size-option').length > 0;
    const totalAttrs = (hasColor ? 1 : 0) + (hasSize ? 1 : 0);

    // Reset tất cả các tùy chọn về trạng thái ban đầu (enabled)
    colorOptions.forEach(opt => { opt.disabled = false; opt.classList.remove('disabled'); });
    sizeOptions.forEach(opt => { opt.disabled = false; opt.classList.remove('disabled'); });

    // Hàm trợ giúp để kiểm tra và disable/enable các tùy chọn
    const checkAndSetOptions = (optionsToCheck, otherAttributeSlug) => {
        optionsToCheck.forEach(option => {
            const currentAttrSlug = option.dataset.attrSlug;
            const currentAttrValueId = option.dataset.valueId; 

            // Tạo một lựa chọn tiềm năng
            const potentialSelection = { ...selected };
            potentialSelection[currentAttrSlug] = currentAttrValueId;

            const variant = findVariantByAttributes(potentialSelection);

            // Vô hiệu hóa nếu không tìm thấy biến thể HOẶC biến thể đã hết hàng
            if (!variant || variant.stock_quantity === 0) {
                option.disabled = true;
                option.classList.add('disabled'); // Thêm class để làm mờ
            } else {
                option.disabled = false;
                option.classList.remove('disabled');
            }
        });
    };

    // Logic chính: Dựa trên những gì đã được chọn, kiểm tra các tùy chọn còn lại
    // 1. Nếu đã chọn MÀU, kiểm tra các KÍCH CỠ có sẵn
    if (selected[COLOR_SLUG]) {
        checkAndSetOptions(sizeOptions, COLOR_SLUG);
    }
    // 2. Nếu đã chọn KÍCH CỠ, kiểm tra các MÀU SẮC có sẵn
    if (selected[SIZE_SLUG]) {
        checkAndSetOptions(colorOptions, SIZE_SLUG); 
    }

    // Cập nhật nút "Thêm vào giỏ" và giá khi đã chọn đủ
    if (Object.keys(selected).length === totalAttrs) {
        const finalVariant = findVariantByAttributes(selected);
        if (finalVariant) {
            if (finalVariant.stock_quantity > 0) {
                addToCartBtn.disabled = false;
                addToCartBtn.textContent = 'THÊM VÀO GIỎ';
            } else {
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = 'HẾT HÀNG';
            }
            updateUIForVariant(finalVariant); // Cập nhật giá, ảnh...
        } else {
            addToCartBtn.disabled = true;
            // Trường hợp không tìm thấy tổ hợp (dữ liệu lỗi)
            priceEl.textContent = 'Tổ hợp không có sẵn';
        }
    } else {
        // Nếu chưa chọn đủ, vô hiệu hóa nút
        addToCartBtn.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Init: set first variant as default and mark buttons
    const initialVariant = variantData[0] || null;
    if (initialVariant) {
        updateUIForVariant(initialVariant);
        // Kích hoạt nút nếu biến thể đầu tiên còn hàng
        if (initialVariant.stock_quantity > 0) {
            addToCartBtn.disabled = false;
        } else {
            addToCartBtn.textContent = 'HẾT HÀNG';
        }
    } else {
        // Nếu không có biến thể nào, disable nút ngay từ đầu
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'HẾT HÀNG';
    }

    // Chọn màu hoặc kích thước
    document.querySelectorAll('.color-option, .size-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.attrSlug;
            const valueId = this.dataset.valueId;

            // Nếu nút đã active, tức là người dùng đang bỏ chọn
            if (this.classList.contains('active')) {
                this.classList.remove('active', 'border-primary');
                delete selected[slug];
            } else {
                // Cập nhật lựa chọn mới và giao diện nút
                document.querySelectorAll(`[data-attr-slug="${slug}"]`).forEach(b => b.classList.remove('active', 'border-primary'));
                this.classList.add('active', 'border-primary');
                selected[slug] = valueId;
            }

            // Cập nhật text màu sắc
            if (slug === COLOR_SLUG && selected[slug]) {
                document.getElementById('selected-color').textContent = this.dataset.value;
            } else if (slug === COLOR_SLUG && !selected[slug]) {
                document.getElementById('selected-color').textContent = '';
            }

            updateAvailableOptions();
        });
    });

    // Tăng giảm số lượng
    const qtyInput = document.getElementById('quantity');
    const formQtyInput = document.getElementById('input-quantity');
    document.getElementById('increase').onclick = () => { qtyInput.value = parseInt(qtyInput.value) + 1; formQtyInput.value = qtyInput.value; };
    document.getElementById('decrease').onclick = () => { qtyInput.value = Math.max(1, parseInt(qtyInput.value) - 1); formQtyInput.value = qtyInput.value; };
    qtyInput.addEventListener('input', () => formQtyInput.value = qtyInput.value);
});
</script>

<style>
/* Hiệu ứng chọn giống Shopee */
.size-option.active,
.color-option.active {
    border: 2px solid #dc3545 !important;
    box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
}

.color-option.disabled, .size-option.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.color-option {
    width: 35px;
    height: 35px;
    padding: 0 !important;
}
</style>
@endsection
