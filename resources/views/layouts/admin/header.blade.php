<header class="bg-white shadow-sm p-3 mb-4 rounded d-flex justify-content-between align-items-center sticky-top">
    <div class="d-flex align-items-center">
        <span class="h4 mb-0 me-3">@yield('title', 'Dashboard')</span>
        <i class="fa-solid fa-gauge-high text-primary me-2"></i>
        <span class="text-muted">Quản trị hệ thống</span>
    </div>

    <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-user-circle fa-lg me-2 text-secondary"></i>
            <span>Admin</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i> Hồ sơ</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear me-2"></i> Cài đặt</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="{{ url('/logout') }}"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>
</header>
