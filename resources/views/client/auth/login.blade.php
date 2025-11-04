@extends('layouts.partials.client')

@section('title', 'Đăng nhập')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">Đăng nhập</h2>
    <form action="{{ route('client.login.post') }}" method="POST" class="w-50 mx-auto border p-4 rounded shadow-sm bg-white">
        @csrf
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

        <button type="submit" class="btn btn-success w-100">Đăng nhập</button>
        <p class="text-center mt-3">Chưa có tài khoản? <a href="{{ route('client.register') }}">Đăng ký</a></p>
    </form>
</div>
@endsection
