@extends('layouts.partials.client')

@section('title', 'Tìm kiếm sản phẩm')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 text-center fw-bold text-uppercase">
        Kết quả tìm kiếm: "{{ $keyword }}"
    </h2>

    @if($products->count() == 0)
        <p class="text-center text-muted">Không tìm thấy sản phẩm nào phù hợp.</p>
    @endif

    <div class="row">
        @foreach($products as $product)
            @php
                $defaultVariant = $product->variants->first();
            @endphp
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-shadow">

                    <a href="{{ route('product.show', $product->id) }}">
                        <img src="{{ asset('uploads/products/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="card-img-top"
                             style="height: 260px; object-fit: cover;">
                    </a>

                    <div class="card-body text-center">

                        <a href="{{ route('product.show', $product->id) }}"
                           class="text-decoration-none">
                            <h6 class="card-title fw-semibold mb-2">{{ $product->name }}</h6>
                        </a>

                        @if($defaultVariant)
                            <p class="fw-bold text-danger mb-3">
                                {{ number_format($defaultVariant->price, 0, ',', '.') }} đ
                            </p>
                        @else
                            <p class="text-muted mb-3">Chưa có giá</p>
                        @endif

                        <a href="{{ route('product.show', $product->id) }}"
                           class="btn btn-outline-primary btn-sm">
                            Xem chi tiết
                        </a>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $products->links() }}
    </div>

</div>
@endsection
