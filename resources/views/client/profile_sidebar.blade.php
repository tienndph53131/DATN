<div class="card">
    <div class="card-header">
        <strong>Tài khoản</strong>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('client.profile.edit') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('client.profile.edit') ? 'active' : '' }}">
            <i class="fa fa-user-circle mr-2"></i>Thông tin cá nhân
        </a>
        <a href="{{ route('client.orders.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('client.orders.index') || request()->routeIs('client.orders.show') ? 'active' : '' }}">
            <i class="fa fa-history mr-2"></i>Lịch sử đơn hàng
        </a>
        <a href="#" class="list-group-item list-group-item-action text-danger" onclick="event.preventDefault(); document.getElementById('client-logout-form-sidebar').submit();">
            <i class="fa fa-sign-out-alt mr-2"></i>Đăng xuất
        </a>
        <form id="client-logout-form-sidebar" action="{{ route('client.logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>