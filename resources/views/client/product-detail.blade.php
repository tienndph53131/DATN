@extends('layouts.partials.client')

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
                <img src="{{ asset('uploads/products/' . $product->image) }}" class="img-fluid rounded shadow-sm"
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
                        {{ $defaultVariant ? number_format($defaultVariant->price, 0, ',', '.') . '₫' : 'Liên hệ' }}
                    </h2>
                </div>

                <!-- Stock for selected variant -->
                <p id="variant-stock" class="text-muted mb-3">
                    Kho: <span id="variant-stock-number">{{ $defaultVariant->stock_quantity ?? 0 }}</span>
                </p>

                <!-- Chọn màu sắc -->
                @if($colors->count())
                    @php
                        // Colors are pre-mapped to CSS in the controller to avoid redeclaring functions in views.
                    @endphp
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2">Màu sắc:</label>
                        <div class="d-flex align-items-center">
                            @foreach($colors as $color)
                                <button type="button" class="btn border rounded-circle p-3 me-2 color-option"
                                    style="background-color: {{ $color->css }};" data-attr="Màu sắc"
                                    data-value="{{ $color->value }}" title="{{ $color->value }}"></button>
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
                                <button type="button" class="btn btn-outline-secondary me-2 mb-2 size-option" data-attr="Kích cỡ"
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

                        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
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
                    <form action="{{ route('product.comment.store', $product->id) }}" method="POST" class="mb-4">
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
                            <div class="list-group-item comment-item" data-rating="{{ $c->rating }}"
                                data-date="{{ $c->date->format('YmdHis') }}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ optional($c->account)->name ?? 'Khách' }}</strong>
                                        <small class="text-muted ms-2">{{ $c->date->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="star-display" style="color:#f5b301">@for($i = 1; $i <= 5; $i++)
                                        @if($i <= $c->rating)
                                    <span>★</span> @else <span style="color:#ddd">★</span> @endif @endfor
                                    </div>
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
                            <img src="{{ asset('uploads/products/' . $item->image) }}" class="card-img-top" alt="{{ $item->name }}">
                            <div class="card-body text-center">
                                <h6 class="fw-bold">{{ $item->name }}</h6>

                                @if($defaultVariant && $defaultVariant->price)
                                    <p class="text-danger fw-bold mb-2">
                                        {{ number_format($defaultVariant->price, 0, ',', '.') }}₫
                                    </p>
                                @else
                                    <p class="text-muted mb-2">Liên hệ</p>
                                @endif

                                <a href="{{ route('product.show', $item->id) }}" class="btn btn-outline-primary btn-sm">
                                    Xem chi tiết
                                </a>

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

            // Initialize default selected variant (first one) so price/stock/variant-id are set
            const defaultVariant = variantData.length ? variantData[0] : null;
            if (defaultVariant) {
                document.getElementById('variant-id').value = defaultVariant.id;
                // set initial stock shown
                const stockEl = document.getElementById('variant-stock-number');
                if (stockEl) stockEl.textContent = defaultVariant.stock_quantity ?? 0;
                // enforce max quantity
                const qtyInput = document.getElementById('quantity');
                if (qtyInput) {
                    qtyInput.max = defaultVariant.stock_quantity ?? 99999;
                    if (parseInt(qtyInput.value) > (defaultVariant.stock_quantity ?? 0)) {
                        qtyInput.value = Math.max(1, defaultVariant.stock_quantity ?? 1);
                        document.getElementById('input-quantity').value = qtyInput.value;
                    }
                }
            }

            // Chọn màu hoặc kích thước
            document.querySelectorAll('.color-option, .size-option').forEach(btn => {
                btn.addEventListener('click', function () {
                    const attr = this.dataset.attr;
                    const value = this.dataset.value;
                    selected[attr] = value;

                    // Remove active
                    document.querySelectorAll(`[data-attr="${attr}"]`).forEach(b => b.classList.remove('active', 'border-primary'));
                    this.classList.add('active', 'border-primary');

                    // Chỉ hiển thị tên màu khi chọn
                    if (attr === 'Màu sắc') {
                        document.getElementById('selected-color').textContent = value;
                    }

                    // Cập nhật giá và variant_id dựa trên cả màu và kích thước
                    const variant = variantData.find(v =>
                        Object.entries(selected).every(([a, val]) => v.attributes[a] === val)
                    );

                    if (variant) {
                        document.getElementById('variant-price').textContent =
                            new Intl.NumberFormat('vi-VN').format(variant.price) + '₫';
                        document.getElementById('variant-id').value = variant.id;
                        // update stock display
                        const stockEl = document.getElementById('variant-stock-number');
                        if (stockEl) stockEl.textContent = variant.stock_quantity ?? 0;
                        // enforce max quantity according to stock
                        const qtyInput = document.getElementById('quantity');
                        const hiddenQty = document.getElementById('input-quantity');
                        const maxStock = variant.stock_quantity ?? 0;
                        if (qtyInput) {
                            qtyInput.max = maxStock || 99999;
                            if (parseInt(qtyInput.value) > maxStock) {
                                qtyInput.value = Math.max(1, maxStock);
                                if (hiddenQty) hiddenQty.value = qtyInput.value;
                            }
                        }
                    }
                });
            });

            // Tăng giảm số lượng

            const qty = document.getElementById('quantity');
            const hiddenQty = document.getElementById('input-quantity');

            // khi tăng giảm
            document.getElementById('increase').addEventListener('click', () => {
                const max = parseInt(qty.max) || 99999;
                qty.value = Math.min(max, parseInt(qty.value) + 1);
                hiddenQty.value = qty.value;
            });

            document.getElementById('decrease').addEventListener('click', () => {
                qty.value = Math.max(1, parseInt(qty.value) - 1);
                hiddenQty.value = qty.value;
            });

            // khi nhập tay vào input
            qty.addEventListener('input', () => {
                const max = parseInt(qty.max) || 99999;
                if (qty.value < 1 || isNaN(qty.value)) qty.value = 1;
                if (parseInt(qty.value) > max) qty.value = max;
                hiddenQty.value = qty.value;
            });

            // Đảm bảo đồng bộ trước khi submit form
            document.getElementById('buyForm').addEventListener('submit', () => {
                hiddenQty.value = qty.value;
            });
        </script>

        <style>
            /* Hiệu ứng chọn giống Shopee */
            .size-option.active,
            .color-option.active {
                border: 2px solid #dc3545 !important;
                box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
            }

            /* Nút chọn kích thước (size) */
            .size-option {
                font-weight: 600;
                /* chữ rõ hơn */
                border: 2px solid #aaa;
                /* viền rõ hơn */
                color: #333;
                /* chữ đậm hơn */
                background-color: #fff;
                transition: all 0.2s ease;
            }

            .size-option:hover {
                border-color: #dc3545;
                color: #dc3545;
                font-weight: 700;
                /* đậm hơn khi hover */
            }

            .size-option.active {
                border: 2px solid #dc3545 !important;
                font-weight: 700;
                color: #dc3545;
                box-shadow: 0 0 6px rgba(220, 53, 69, 0.5);
            }

            /* Nút chọn màu (color) */
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
            #increase,
            #decrease {
                font-weight: 700;
                font-size: 18px;
                color: #333;
                border: 2px solid #aaa;
            }

            #increase:hover,
            #decrease:hover {
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
                background-color: #dc3545 !important;
                /* Đỏ nổi bật */
                border-color: #dc3545 !important;
            }

            .color-option {
                border: 2px solid #ccc !important;
                /* viền dày hơn */
                box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
                /* hiệu ứng đổ bóng nhẹ */
                transition: all 0.2s ease-in-out;
            }

            .color-option:hover {
                transform: scale(1.1);
                border-color: #000;
                /* viền đậm khi hover */
            }

            .color-option.active {
                border: 3px solid #000 !important;
                /* khi được chọn thì viền đậm rõ */
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

            .star-display span {
                font-size: 18px;
            }
        </style>
@endsection

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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

                function highlightStars(n) {
                    document.querySelectorAll('.star-label').forEach(l => {
                        const v = parseInt(l.dataset.value);
                        l.style.color = v <= n ? '#f5b301' : '#ddd';
                    });
                }

                function restoreStars() {
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
                        items.sort((a, b) => {
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