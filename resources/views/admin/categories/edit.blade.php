@extends('layouts.admin.admin')

@section('content')
<div class="container mt-4">
    <h2>Sửa danh mục</h2>
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

         <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input 
                type="text" 
                id="name"
                name="name" 
                class="form-control @error('name') is-invalid @enderror" 
                value="{{ old('name', $category->name) }}"
               
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>
        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control">{{ $category->description }}</textarea>
        </div>
        <button class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
