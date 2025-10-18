<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .product-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            border-color: #0d6efd;
        }

        .thumbnail.active {
            border-color: #0d6efd;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.active {
            border-color: #212529;
            box-shadow: 0 0 0 2px white, 0 0 0 4px #212529;
        }

        .size-option {
            min-width: 60px;
            padding: 0.5rem 1rem;
            border: 2px solid #dee2e6;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 6px;
            font-weight: 500;
        }

        .size-option:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .size-option.active {
            border-color: #0d6efd;
            background-color: #0d6efd;
            color: white;
        }

        .quantity-control {
            display: inline-flex;
            align-items: center;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }

        .quantity-control button {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .quantity-control button:hover {
            background: #f8f9fa;
        }

        .quantity-control input {
            width: 60px;
            height: 40px;
            border: none;
            text-align: center;
            font-weight: 600;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
        }

        .quantity-control input:focus {
            outline: none;
        }

        .price-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 1.25rem;
        }

        .price-current {
            color: #dc3545;
            font-size: 2rem;
            font-weight: 700;
        }

        .discount-badge {
            background: #dc3545;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .btn-add-cart {
            background: #0d6efd;
            color: white;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.3s ease;
        }

        .btn-add-cart:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .btn-wishlist {
            width: 50px;
            height: 50px;
            border: 2px solid #dee2e6;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-wishlist:hover {
            border-color: #dc3545;
            color: #dc3545;
        }

        .product-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: #198754;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .rating {
            color: #ffc107;
        }

        .selected {
            border-color: #0d6efd;
            background-color: #0d6efd;
            color: white;
        }

        @media (max-width: 768px) {
            .main-image {
                height: 300px;
            }

            .product-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="product-container">
            <div class="row">
                Product Images
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="position-relative">
                        <img src="{{ $product->image }}" alt="Product" class="main-image"
                            style="width:100%; max-width:400px; height:400px; object-fit:cover; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.15)";
                            id="mainImage">
                    </div>
                    4 image
                    <div class="d-flex gap-2 mt-3">
                        {{-- foreach --}}
                        @foreach ($product->images as $image)
                            <img src="{{  $image->link_images }}" alt="Thumbnail"
                                class="thumbnail active" onclick="changeImage(this)" width="150px" height="150px">
                        @endforeach

                    </div>
                </div>

                Product Details
                <div class="col-lg-6">
                    <div class="mb-2">
                        <span class="badge bg-secondary">{{ $product->name }}</span>
                    </div>

                    <h1 class="h2 fw-bold mb-3">{{ $product->category->name }}</h1>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <span class="text-muted">(4.5) 328 Reviews</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="price-current">$199.99</span>
                        <span class="price-original">$299.99</span>
                        <span class="discount-badge">33% OFF</span>
                    </div>

                    <p class="text-muted mb-4">
                        Experience premium sound quality with our latest wireless headphones.
                        Featuring active noise cancellation, 30-hour battery life, and comfortable
                        over-ear design perfect for music lovers and professionals.
                    </p>
                    @if (isset($attributes['color']))
                        @foreach ($attributes as $attributeName => $values)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">{{ ucfirst($attributeName) }}</h6>
                                <div class="d-flex gap-2 attribute-group-{{ $attributeName }}">
                                    @foreach ($values as $value)
                                        @if ($attributeName === 'color')
                                            <div class="color-option" style="background-color: {{ $value }};"
                                                onclick="selectColor(this)" title="{{ $value }}"></div>
                                        @else
                                            <button class="btn btn-outline-secondary"
                                                onclick="selectAttribute('{{ $attributeName }}','{{ $value }}')">{{ $value }}</button>
                                        @endif
                                    @endforeach
                                </div>
                                <input type="hidden" id="selected-{{ $attributeName }}" name="{{ $attributeName }}">
                            </div>
                        @endforeach
                    @endif
                    Quantity
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3">Quantity</h6>
                        <div class="quantity-control">
                            <button onclick="decreaseQuantity()">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" id="quantity" value="1" min="1" max="10" readonly>
                            <button onclick="increaseQuantity()">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>

                    Action Buttons
                    <div class="d-flex gap-3 mb-4">
                        <button class="btn-add-cart flex-grow-1">
                            <i class="bi bi-cart-plus me-2"></i>
                            Add to Cart
                        </button>
                        <button class="btn-wishlist">
                            <i class="bi bi-heart fs-5"></i>
                        </button>
                    </div>

                    Product Info
                    <div class="border-top pt-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-truck fs-5 text-primary"></i>
                                    <div>
                                        <small class="d-block fw-semibold">Free Shipping</small>
                                        <small class="text-muted">On orders over $50</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-repeat fs-5 text-primary"></i>
                                    <div>
                                        <small class="d-block fw-semibold">Easy Returns</small>
                                        <small class="text-muted">30-day return policy</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check fs-5 text-primary"></i>
                                    <div>
                                        <small class="d-block fw-semibold">Secure Payment</small>
                                        <small class="text-muted">100% secure checkout</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-headset fs-5 text-primary"></i>
                                    <div>
                                        <small class="d-block fw-semibold">24/7 Support</small>
                                        <small class="text-muted">Dedicated support</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeImage(thumbnail) {
            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });

            // Add active class to clicked thumbnail
            thumbnail.classList.add('active');

            // Change main image
            document.getElementById('mainImage').src = thumbnail.src.replace('height=80&width=80', 'height=500&width=500');
        }

        function selectColor(element) {
            document.querySelectorAll('.color-option').forEach(option => {
                option.classList.remove('active');
            });
            element.classList.add('active');
        }

        function selectAttribute(attributeName, value) {
            document.querySelectorAll(`.attribute-group-${attributeName} button`).forEach(option => {
                option.classList.remove('selected');
            });
            event.target.classList.add('selected');

            document.querySelector(`#selected-${attributeName}`).value = value;
        }

        function increaseQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue < parseInt(input.max)) {
                input.value = currentValue + 1;
            }
        }

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue > parseInt(input.min)) {
                input.value = currentValue - 1;
            }
        }
    </script>
</body>

</html>
