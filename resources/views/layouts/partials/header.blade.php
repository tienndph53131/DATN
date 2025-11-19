<!-- 🌟 Topbar Start -->
<div class="container-fluid border-bottom">
    <div class="row bg-light py-2 px-xl-5 align-items-center">
        <div class="col-lg-6 d-none d-lg-block">
            <div class="d-inline-flex align-items-center">
                <a class="text-muted small" href="#">FAQs</a>
                <span class="text-muted px-2">|</span>
                <a class="text-muted small" href="#">Hỗ trợ</a>
                <span class="text-muted px-2">|</span>
                <a class="text-muted small" href="#">Liên hệ</a>
            </div>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <div class="d-inline-flex align-items-center">
                <a class="text-dark px-2" href="#"><i class="fab fa-facebook-f"></i></a>
                <a class="text-dark px-2" href="#"><i class="fab fa-twitter"></i></a>
                <a class="text-dark px-2" href="#"><i class="fab fa-instagram"></i></a>
                <a class="text-dark px-2" href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <div class="row align-items-center py-3 px-xl-5">
        <div class="col-lg-3 d-none d-lg-block">
            <a href="/" class="text-decoration-none">
                <h1 class="m-0 display-5 font-weight-semi-bold text-dark">
                    <span class="text-primary font-weight-bold border px-3 mr-1">SM</span>Shopper
                </h1>
            </a>
        </div>
        <div class="col-lg-6 col-6 text-left">
            <form action="{{ url('/search') }}" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control border-primary" name="keyword" placeholder="Tìm kiếm sản phẩm...">
                    <div class="input-group-append">
                        <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

       <!-- 🛒 GIỎ HÀNG -->
@if(Auth::guard('client')->check())
    @php
        $user = Auth::guard('client')->user();
        // Lấy giỏ hàng hoặc tạo mới
        $cart = $user->cart ?? \App\Models\Cart::firstOrCreate(['account_id' => $user->id]);
        $cartItems = $cart->details()->with('productVariant.product')->get();

        $cartCount = $cartItems->count(); 
        $cartTotal = $cartItems->sum('amount');
    @endphp

    <div class="col-lg-3 col-6 text-right">
        <!-- Yêu thích -->
        <a href="#" class="btn border position-relative me-2">
            <i class="fas fa-heart text-primary"></i>
            <span class="badge position-absolute top-0 start-100 translate-middle bg-danger text-white rounded-pill">0</span>
        </a>

        <!-- Giỏ hàng -->
        <a href="{{ route('client.cart.index') }}" class="btn border position-relative me-2">
            <i class="fas fa-shopping-cart text-primary"></i>
            <span class="badge position-absolute top-0 start-100 translate-middle bg-danger text-white rounded-pill">
                {{ $cartCount }}
            </span>
        </a>

        <!-- Hiển thị tổng tiền -->
        <span class="fw-bold text-dark small">
            🛒 {{ $cartCount }} SP - {{ number_format($cartTotal, 0, ',', '.') }}₫
        </span>
    </div>
@endif

<!-- 🌟 Topbar End -->


<!-- 🧭 Navbar Start -->
<div class="container-fluid mb-3">
    <div class="row border-top px-xl-5">
        <!-- Categories -->
        <div class="col-lg-3 d-none d-lg-block">
            <a class="btn d-flex align-items-center justify-content-between bg-primary text-white w-100"
               data-toggle="collapse" href="#navbar-vertical"
               style="height: 65px; margin-top: -1px; padding: 0 30px;">
                <h6 class="m-0"><i class="fa fa-list mr-2"></i>Danh mục</h6>
                <i class="fa fa-angle-down text-dark"></i>
            </a>

            <nav id="navbar-vertical"
                 class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 
                        border border-top-0 border-bottom-0 bg-white shadow-sm"
                 style="width: calc(100% - 30px); z-index: 1000; border-radius: 0 0 8px 8px;">
                <div class="navbar-nav w-100 overflow-auto" style="max-height: 410px;">
                    @forelse($categories as $category)
                        <a href="{{ url('category/' . $category->id) }}"
                           class="nav-item nav-link px-3 py-2 border-bottom text-dark"
                           style="transition: all 0.2s;">
                            <i class="fa fa-tag text-primary mr-2"></i>{{ $category->name }}
                        </a>
                    @empty
                        <span class="nav-item nav-link text-muted text-center py-3">
                            <i class="fa fa-box-open mr-2"></i>Chưa có danh mục
                        </span>
                    @endforelse
                </div>
            </nav>
        </div>

        <!-- Navbar -->
        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0">
                <a href="/" class="text-decoration-none d-block d-lg-none">
                    <h1 class="m-0 display-5 font-weight-semi-bold">
                        <span class="text-primary font-weight-bold border px-3 mr-1">SM</span>Shopper
                    </h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav mr-auto py-0">
                        <a href="/" class="nav-item nav-link {{ request()->is('/') ? 'active text-primary' : '' }}">Trang chủ</a>
                        <a href="/shop" class="nav-item nav-link {{ request()->is('shop') ? 'active text-primary' : '' }}">Cửa hàng</a>
                        <a href="/contact" class="nav-item nav-link {{ request()->is('contact') ? 'active text-primary' : '' }}">Liên hệ</a>
                    </div>

                    <div class="navbar-nav ml-auto py-0">
    {{-- Nếu đã đăng nhập --}}
                        @if (Auth::guard('client')->check())
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                    👤 {{ Auth::guard('client')->user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right rounded-0 m-0">
                                    <a href="{{ route('client.profile.edit') }}" class="dropdown-item">Thông tin cá nhân</a>
                                    <a href="{{ route('client.orders.index') }}" class="dropdown-item">Lịch sử đơn hàng</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item"
                                        onclick="event.preventDefault(); document.getElementById('client-logout-form').submit();">
                                        Đăng xuất
                                    </a>
                                    <form id="client-logout-form" action="{{ route('client.logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('client.login') }}" class="nav-item nav-link">Đăng nhập</a>
                            <a href="{{ route('client.register') }}" class="nav-item nav-link">Đăng ký</a>
                        @endif


                </div>
            </nav>
        </div>
    </div>
</div>
@include('layouts.partials.banner')
<!-- 🧭 Navbar End -->
