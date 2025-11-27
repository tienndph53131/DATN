@extends('layouts.partials.client')

@section('title', 'Cửa hàng')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3 mb-4">
                <h5>Bộ lọc</h5>
                <form method="GET" action="{{ route('shop.index') }}">
                    <div class="mb-3">
                        <label class="form-label">Giá từ</label>
                        <input type="number" name="min_price" class="form-control" value="{{ $min ?? '' }}" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Đến</label>
                        <input type="number" name="max_price" class="form-control" value="{{ $max ?? '' }}" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="">Mặc định (mới nhất)</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: thấp → cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: cao → thấp</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">Áp dụng</button>
                    <a href="{{ route('shop.index') }}" class="btn btn-secondary ms-2">Xóa</a>
                </form>
            </div>
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Sản phẩm</h3>
                    <div>Tổng: {{ $products->total() }} sản phẩm</div>
                </div>

                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                @if($product->image)
                                    <img src="{{ asset('uploads/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text">{{ $product->category?->name }}</p>
                                    @php
                                        $prices = $product->variants->pluck('price')->filter();
                                        $minPrice = $prices->min();
                                        $maxPrice = $prices->max();
                                    @endphp
                                    <div class="mt-auto">
                                        @if($minPrice && $maxPrice && $minPrice != $maxPrice)
                                            <strong>{{ number_format($minPrice,0,',','.') }}₫ - {{ number_format($maxPrice,0,',','.') }}₫</strong>
                                        @elseif($minPrice)
                                            <strong>{{ number_format($minPrice,0,',','.') }}₫</strong>
                                        @else
                                            <span class="text-muted">Liên hệ</span>
                                        @endif
                                        <div class="mt-2">
                                            <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
