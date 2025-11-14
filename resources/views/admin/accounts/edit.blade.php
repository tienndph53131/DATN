@extends('layouts.admin.admin')

@section('title', 'Chỉnh sửa tài khoản')

@section('content')
<div class="container">
    <h2 class="py-4">Chỉnh sửa tài khoản: {{ $account->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accounts.update', $account->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $account->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $account->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Vai trò</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="">-- Chọn vai trò --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $account->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-control" id="status" name="status" required>
                <option value="1" {{ old('status', $account->status) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $account->status) == 0 ? 'selected' : '' }}>Deactive</option>
            </select>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection

