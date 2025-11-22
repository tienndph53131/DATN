@extends('layouts.admin.admin')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Tao tài khoản nhân viên</h2>

        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Tên -->
            <div class="mb-3">
                <label class="form-label">Họ tên</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <!-- Số điện thoại -->
            <div class="mb-3">
                <label class="form-label">Điện thoại</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Ảnh đại diện -->
            <div class="mb-3">
                <label class="form-label">Ảnh đại diện</label>
                <input type="file" name="avatar" class="form-control">
            </div>


            <!-- Giới tính -->
            <div class="mb-3">
                <label class="form-label">Giới tính</label>
                <select name="sex" class="form-select">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="male" @selected(old('sex') === 'male')>Nam</option>
                    <option value="female" @selected(old('sex') === 'female')>Nữ</option>
                    <option value="other" @selected(old('sex') === 'other')>Khác</option>
                </select>
            </div>

            {{-- <!-- Trạng thái -->
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="1" @selected(old('status')==1)>Hoạt động</option>
                    <option value="0" @selected(old('status')==0)>Ngừng hoạt động</option>
                </select>
            </div> --}}
            <!-- Nút -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Thêm</button>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection