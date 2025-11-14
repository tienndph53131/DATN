@extends('layouts.partials.client')

@section('title', 'Đăng ký')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">Đăng ký tài khoản</h2>
    <form action="{{ route('client.register.post') }}" method="POST" class="w-50 mx-auto border p-4 rounded shadow-sm bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
        <p class="text-center mt-3">Đã có tài khoản? <a href="{{ route('client.login') }}">Đăng nhập</a></p>
    </form>
</div>
@endsection
