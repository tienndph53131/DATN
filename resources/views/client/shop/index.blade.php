@extends('layouts.partials.client')

@section('title', 'Cửa hàng')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <aside class="col-md-3 mb-4">
                <div class="card sticky-top" style="top:20px;">
                    <div class="card-body">
                        <h5 class="card-title">Bộ lọc</h5>

                        <form id="shopFilterForm" method="GET" action="{{ route('shop.index') }}">
                            <div class="mb-3">
                                <label class="form-label small">Giá</label>
                                <div class="d-flex gap-2">
                                    <input type="number" name="min_price" id="minPrice" class="form-control form-control-sm" value="{{ $min ?? '' }}" placeholder="Từ">
                                    <input type="number" name="max_price" id="maxPrice" class="form-control form-control-sm" value="{{ $max ?? '' }}" placeholder="Đến">
                                </div>
                                <div class="form-text small mt-1">Nhập khoảng giá hoặc dùng các nút sẵn có bên dưới.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Danh mục</label>
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => ''])) }}" class="d-flex justify-content-between align-items-center py-1 {{ request('category') ? '' : 'fw-bold' }}"> 
                                            <span>Tất cả</span>
                                            <span class="badge bg-secondary">{{ $categories->sum('active_products_count') }}</span>
                                        </a>
                                    </li>
                                    @foreach($categories as $c)
                                        <li>
                                            <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $c->id])) }}" class="d-flex justify-content-between align-items-center py-1 {{ request('category') == $c->id ? 'fw-bold text-primary' : '' }}">
                                                <span>{{ $c->name }}</span>
                                                <span class="badge bg-light text-dark">{{ $c->active_products_count }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Sắp xếp</label>
                                <div class="btn-group d-flex" role="group">
                                    <button type="button" data-sort="" class="btn btn-sm btn-outline-secondary w-100 sort-btn {{ request('sort') == '' ? 'active' : '' }}">Mới nhất</button>
                                    <button type="button" data-sort="price_asc" class="btn btn-sm btn-outline-secondary w-100 sort-btn {{ request('sort') == 'price_asc' ? 'active' : '' }}">Giá ↑</button>
                                    <button type="button" data-sort="price_desc" class="btn btn-sm btn-outline-secondary w-100 sort-btn {{ request('sort') == 'price_desc' ? 'active' : '' }}">Giá ↓</button>
                                </div>
                                <input type="hidden" name="sort" id="sortInput" value="{{ request('sort') }}">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
                                <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm">Xóa</a>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Sản phẩm</h3>
                    <div class="small text-muted">Tổng: {{ $products->total() }} sản phẩm</div>
                </div>

                <div class="row g-3">
                    @foreach($products as $product)
                        <div class="col-6 col-md-4">
                            <div class="card h-100 shadow-sm">
                                @if($product->image)
                                    <div style="height:160px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f8f9fa">
                                        <img src="{{ asset('uploads/' . $product->image) }}" style="max-height:150px;max-width:100%;object-fit:contain;" alt="{{ $product->name }}">
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title mb-1">{{ \\Illuminate\Support\Str::limit($product->name, 60) }}</h6>
                                    <p class="text-muted small mb-2">{{ $product->category?->name }}</p>
                                    @php
                                        $prices = $product->variants->pluck('price')->filter();
                                        $minPrice = $prices->min();
                                        $maxPrice = $prices->max();
                                    @endphp
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($minPrice && $maxPrice && $minPrice != $maxPrice)
                                                <strong class="text-primary">{{ number_format($minPrice,0,',','.') }}₫</strong>
                                                <small class="text-muted"> - {{ number_format($maxPrice,0,',','.') }}₫</small>
                                            @elseif($minPrice)
                                                <strong class="text-primary">{{ number_format($minPrice,0,',','.') }}₫</strong>
                                            @else
                                                <small class="text-muted">Liên hệ</small>
                                            @endif
                                        </div>
                                        <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            </main>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (function () {
        // debounce helper
        function debounce(fn, ms) {
            let t;
            return function () {
                clearTimeout(t);
                const args = arguments;
                t = setTimeout(() => fn.apply(this, args), ms);
            };
        }

        const form = document.getElementById('shopFilterForm');
        if (!form) return;

        // auto-submit when changing price inputs (debounced)
        ['minPrice','maxPrice'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', debounce(function () {
                // keep page at first when filtering
                const page = form.querySelector('input[name="page"]');
                if (page) page.parentNode.removeChild(page);
                form.submit();
            }, 700));
        });

        // sort buttons
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const sort = this.getAttribute('data-sort') || '';
                const sortInput = document.getElementById('sortInput');
                if (sortInput) sortInput.value = sort;
                // submit form preserving other filters
                form.submit();
            });
        });
    })();
</script>
@endsection
