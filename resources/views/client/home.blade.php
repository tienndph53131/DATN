@extends('layouts.partials.client')

@section('title', 'Trang chủ')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center fw-bold text-uppercase">Sản phẩm mới nhất</h2>

    <div class="row">
        @foreach($products as $product)
            @php
                $defaultVariant = $product->variants->first();
            @endphp
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-shadow">
                     <a href="{{ route('product.show', $product->id) }}">
                    <img   src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" 
                         class="card-img-top" style="height: 260px; object-fit: cover;">
                    <div class="card-body text-center"></a>
                   <a  href="{{ route('product.show', $product->id) }}" class="text-decoration-none"> 
                    <h6 class="card-title fw-semibold mb-2">{{ $product->name }}</h6></a>

                        @if($defaultVariant)
                            <p class="fw-bold text-danger mb-3">{{ number_format($defaultVariant->price, 0, ',', '.') }} đ</p>
                        @else
                            <p class="text-muted mb-3">Chưa có giá</p>
                        @endif

                        <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
