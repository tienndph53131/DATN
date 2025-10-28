@extends('layouts.partials.client')

@section('title', $category->name)

@section('content')
<div class="container py-4">

    <h2 class="text-center mb-4 fw-bold text-uppercase">
        Danh mục: {{ $category->name }}
    </h2>

    <div class="row">
        @forelse($products as $product)
            @php
                $defaultVariant = $product->variants->first();
            @endphp
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                    <img src="{{ $product->image ? asset('uploads/products/' . $product->image) : asset('images/no-image.png') }}"
                         class="card-img-top"
                         alt="{{ $product->name }}"
                         style="object-fit: cover; height: 250px;">
                    <div class="card-body text-center">
                        <h6 class="card-title fw-semibold text-truncate">{{ $product->name }}</h6>

                        @if($defaultVariant)
                            <p class="fw-bold text-danger mb-3">{{ number_format($defaultVariant->price, 0, ',', '.') }} đ</p>
                        @else
                            <p class="text-muted mb-3">Chưa có giá</p>
                        @endif

                        <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">Không có sản phẩm nào trong danh mục này.</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
