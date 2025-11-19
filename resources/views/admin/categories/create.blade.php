@extends('layouts.admin.admin')

@section('content')
<div class="container mt-4">
    <h2>Thêm danh mục mới</h2>
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

    <div class="mb-3">
    <label>Tên danh mục</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
