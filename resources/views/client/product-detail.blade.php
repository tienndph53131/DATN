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
    @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
         @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
                {{-- Trạng thái: 
                @if($product->status == 1)
                    <span class="text-success">Còn hàng</span>
                @else
                    <span class="text-danger">Hết hàng</span>
                @endif --}}
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
 <!-- Stock for selected variant -->
            <p id="variant-stock" class="text-muted mb-3">
                Kho: <span id="variant-stock-number">{{ $defaultVariant->stock_quantity ?? 0 }}</span>
            </p>
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
                <form action="{{ route('client.cart.add') }}" method="POST" id="buyForm">
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
     <!-- Bình luận & Đánh giá -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="fw-bold mb-3">Đánh giá & Bình luận</h4>

            {{-- Form gửi bình luận (yêu cầu đăng nhập) --}}
                @if(auth('client')->check())
                    <form action="{{ route('client.product.comment.store', $product->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Đánh giá</label>
                            <div class="rating-input mb-2" style="font-size:22px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" style="display:none;" {{ old('rating') == $i ? 'checked' : '' }}>
                                            <label for="star{{ $i }}" class="star-label" data-value="{{ $i }}">★</label>
                                        @endfor
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nội dung</label>
                            <textarea name="content" class="form-control" rows="4" required></textarea>
                        </div>
                        <button class="btn btn-primary">Gửi bình luận</button>
                    </form>
            @else
                <p class="mb-4">Bạn cần <a href="{{ route('client.login') }}">đăng nhập</a> để gửi bình luận.</p>
            @endif

            {{-- Danh sách bình luận đã duyệt --}}
            @if(isset($comments) && $comments->count())
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <label class="me-2">Sắp xếp:</label>
                        <select id="comment-sort" class="form-select form-select-sm d-inline-block" style="width:auto;">
                            <option value="newest">Mới nhất</option>
                            <option value="highest">Điểm cao nhất</option>
                            <option value="lowest">Điểm thấp nhất</option>
                        </select>
                    </div>
                </div>

                <div class="list-group" id="comments-list">
                    @foreach($comments as $c)
                        <div class="list-group-item comment-item" data-rating="{{ $c->rating }}" data-date="{{ $c->date->format('YmdHis') }}">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ optional($c->account)->name ?? 'Khách' }}</strong>
                                    <small class="text-muted ms-2">{{ $c->date->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="star-display" style="color:#f5b301">@for($i=1;$i<=5;$i++) @if($i <= $c->rating) <span>★</span> @else <span style="color:#ddd">★</span> @endif @endfor</div>
                            </div>
                            <p class="mt-2 mb-0">{{ $c->content }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Chưa có bình luận nào cho sản phẩm này.</p>
            @endif
        </div>
    </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
@if($relatedProducts->count())
<div class="mt-5">
    <h4 class="mb-4 fw-bold">Sản phẩm liên quan</h4>
    <div class="row">
        @foreach($relatedProducts as $item)
            @php
                // Lấy biến thể đầu tiên làm mặc định
                $defaultVariant = $item->variants->first();
            @endphp

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <img src="{{ asset('uploads/products/' . $item->image) }}" 
                         class="card-img-top" 
                         alt="{{ $item->name }}">
                    <div class="card-body text-center">
                        <h6 class="fw-bold">{{ $item->name }}</h6>

                        @if($defaultVariant && $defaultVariant->price)
                            <p class="text-danger fw-bold mb-2">
                                {{ number_format($defaultVariant->price, 0, ',', '.') }}₫
                            </p>
                        @else
                            <p class="text-muted mb-2">Liên hệ</p>
                        @endif

                        <a href="{{ route('products.show', $item->id) }}" 
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
* Nút chọn kích thước (size) */
.size-option {
    font-weight: 600; /* chữ rõ hơn */
    border: 2px solid #aaa; /* viền rõ hơn */
    color: #333; /* chữ đậm hơn */
    background-color: #fff;
    transition: all 0.2s ease;
}
.size-option:hover {
    border-color: #dc3545;
    color: #dc3545;
    font-weight: 700; /* đậm hơn khi hover */
}
.size-option.active {
    border: 2px solid #dc3545 !important;
    font-weight: 700;
    color: #dc3545;
    box-shadow: 0 0 6px rgba(220, 53, 69, 0.5);
}

.color-option.disabled, .size-option.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.color-option {
    width: 35px;
    height: 35px;
    padding: 0 !important;
    border: 2px solid #aaa;
    transition: all 0.2s ease;
}
.color-option:hover {
    transform: scale(1.1);
    border-color: #dc3545;
}
.color-option.active {
    border: 2px solid #dc3545 !important;
    box-shadow: 0 0 6px rgba(220, 53, 69, 0.5);
}

/* Nút tăng giảm số lượng */
#increase, #decrease {
    font-weight: 700;
    font-size: 18px;
    color: #333;
    border: 2px solid #aaa;
}
#increase:hover, #decrease:hover {
    background-color: #f8f9fa;
    border-color: #dc3545;
    color: #dc3545;
}

/* Input số lượng */
#quantity {
    font-weight: 600;
    color: #333;
    border: 2px solid #aaa;
}

/* Nút "Thêm vào giỏ" */
#buyForm button {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #dc3545;
    color: #fff;
    border: none;
    transition: 0.2s ease;
}
#buyForm button:hover {
    background-color: #c82333;
}

.btn-outline-primary.btn-sm:hover {
    background-color: #0d6efd;
    color: #fff;
    font-weight: 700;
    
}
.size-option {
    color: #000 !important;               
    border-color: #000 !important;        
    background-color: #fff !important;    
    opacity: 1 !important;                
    font-weight: 600;                     
    transition: all 0.2s ease-in-out;
}

/* Khi hover chuột */
.size-option:hover {
    color: #fff !important;
    background-color: #000 !important;
    border-color: #000 !important;
}

/* Khi được chọn */
.size-option.active {
    color: #fff !important;
    background-color: #dc3545 !important; /* Đỏ nổi bật */
    border-color: #dc3545 !important;
}
.color-option {
    border: 2px solid #ccc !important; /* viền dày hơn */
    box-shadow: 0 0 3px rgba(0, 0, 0, 0.3); /* hiệu ứng đổ bóng nhẹ */
    transition: all 0.2s ease-in-out;
}

.color-option:hover {
    transform: scale(1.1);
    border-color: #000; /* viền đậm khi hover */
}

.color-option.active {
    border: 3px solid #000 !important; /* khi được chọn thì viền đậm rõ */
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.5);
}
/* Star rating labels */
.star-label {
    cursor: pointer;
    color: #ddd;
    transition: color 0.15s ease-in-out, transform 0.08s;
    font-size: 22px;
    margin-right: 6px;
}
.star-label:hover {
    transform: translateY(-2px);
    color: #f5b301;
}
.rating-input input[type="radio"] {
    display: none;
}
.star-display span { font-size:18px; }
</style>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star rating selector UI
    document.querySelectorAll('.star-label').forEach(label => {
        const val = parseInt(label.dataset.value);
        label.addEventListener('mouseover', () => highlightStars(val));
        label.addEventListener('mouseout', () => restoreStars());
        label.addEventListener('click', () => {
            const radio = document.getElementById('star' + val);
            if (radio) radio.checked = true;
            restoreStars();
        });
    });

    function highlightStars(n){
        document.querySelectorAll('.star-label').forEach(l => {
            const v = parseInt(l.dataset.value);
            l.style.color = v <= n ? '#f5b301' : '#ddd';
        });
    }

    function restoreStars(){
        const checked = document.querySelector('.rating-input input[type=radio]:checked');
        let current = checked ? parseInt(checked.value) : 0;
        document.querySelectorAll('.star-label').forEach(l => {
            const v = parseInt(l.dataset.value);
            l.style.color = v <= current ? '#f5b301' : '#ddd';
        });
    }

    // Initialize stars display
    restoreStars();

    // Sorting comments client-side
    const sortSelect = document.getElementById('comment-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            const list = document.getElementById('comments-list');
            const items = Array.from(list.querySelectorAll('.comment-item'));
            const mode = sortSelect.value;
            items.sort((a,b) => {
                if (mode === 'newest') return b.dataset.date.localeCompare(a.dataset.date);
                if (mode === 'highest') return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
                if (mode === 'lowest') return parseInt(a.dataset.rating) - parseInt(b.dataset.rating);
                return 0;
            });
            items.forEach(i => list.appendChild(i));
        });
    }
});
</script>
@endsection
