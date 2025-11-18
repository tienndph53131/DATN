<header class="bg-white shadow-sm p-3 mb-4 rounded d-flex justify-content-between align-items-center sticky-top">
    <div class="d-flex align-items-center">
        <span class="h4 mb-0 me-3">@yield('title', 'Dashboard')</span>
        <i class="fa-solid fa-gauge-high text-primary me-2"></i>
        <span class="text-muted">Quản trị hệ thống</span>
    </div>

    <div class="dropdown">
        @php
            $admin = auth()->user();
            $unreadCount = $admin ? $admin->unreadNotifications()->count() : 0;
            $unreads = $admin ? $admin->unreadNotifications()->take(5)->get() : collect();
        @endphp

        <div class="btn-group me-3">
            <button type="button" class="btn btn-light position-relative" id="notifBell" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bell"></i>
                @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadCount }}</span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifBell" style="min-width:300px;">
                <li class="dropdown-header">Thông báo</li>
                @if($unreads->isEmpty())
                    <li class="dropdown-item text-muted">Không có thông báo mới</li>
                @else
                    @foreach($unreads as $n)
                        @php $data = $n->data ?? []; @endphp
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.notifications.read', $n->id) }}">
                                <div class="small text-muted">{{ $n->created_at->diffForHumans() }}</div>
                                <div>{{ $data['message'] ?? 'Có thông báo mới' }} @if(isset($data['order_code'])) - <strong>{{ $data['order_code'] }}</strong>@endif</div>
                            </a>
                        </li>
                    @endforeach
                    <li><hr class="dropdown-divider"></li>
                    {{-- Return-request links removed --}}
                @endif
            </ul>
        </div>

        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-user-circle fa-lg me-2 text-secondary"></i>
            <span>{{ $admin->name ?? 'Admin' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i> Hồ sơ</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear me-2"></i> Cài đặt</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="{{ url('/logout') }}"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>
</header>
