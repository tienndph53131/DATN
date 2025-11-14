@extends('layouts.admin.admin')

@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container">
    <h2 class="py-4">Danh sách tài khoản</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">Thêm tài khoản mới</a>
    </div>

    <!-- Form tìm kiếm và lọc -->
    <form method="GET" action="{{ route('accounts.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên hoặc email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role_id" class="form-control">
                    <option value="">-- Lọc theo vai trò --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">-- Lọc theo trạng thái --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Deactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info w-100">Lọc</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>{{ $account->id }}</td>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->email }}</td>
                        <td>{{ $account->role->name ?? 'N/A' }}</td>
                        <td>
                            @if($account->status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Deactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này?');">
                                    @csrf
                                    @method('DELETE')
                                    @if($account->status == 1)
                                        <button type="submit" class="btn btn-danger btn-sm">Deactive</button>
                                    @else
                                        <button type="submit" class="btn btn-success btn-sm">Active</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Không tìm thấy tài khoản nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $accounts->appends(request()->query())->links() }}
    </div>
</div>
@endsection

<style>
    .table th {
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.9em;
    }
</style>

