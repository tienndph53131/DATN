@extends('layouts.admin.admin')

@section('title', 'Chỉnh sửa tài khoản')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Chỉnh sửa tài khoản</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accountsadmin.update', $account->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Họ tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $account->email) }}" required>
        </div>

        @if(Auth::guard('client')->user()->role_id == 1)
            <div class="mb-3">
                <label for="role_id" class="form-label">Role</label>
                <select name="role_id" class="form-control" required>
                    <option value="1" {{ $account->role_id == 1 ? 'selected' : '' }}>Admin</option>
                    <option value="3" {{ $account->role_id == 3 ? 'selected' : '' }}>Nhân viên</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-control" required>
                    <option value="1" {{ $account->status ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ !$account->status ? 'selected' : '' }}>Vô hiệu</option>
                </select>
            </div>
        @else
            <div class="mb-3">
                <label class="form-label">Role</label>
                <input type="text" class="form-control" value="{{ $account->role_id == 1 ? 'Admin' : 'Nhân viên' }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <input type="text" class="form-control" value="{{ $account->status ? 'Hoạt động' : 'Vô hiệu' }}" disabled>
            </div>
        @endif

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('accountsadmin.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
