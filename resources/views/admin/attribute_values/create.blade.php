@extends('layouts.admin.admin')

@section('title', 'Thêm giá trị thuộc tính')

@section('content')
<div class="container mt-4">
    <h2>Thêm giá trị thuộc tính</h2>
    <form action="{{ route('attribute_values.store') }}" method="POST">
        @csrf

        {{-- Chọn thuộc tính --}}
        <div class="mb-3">
            <label for="attribute_id" class="form-label">Thuộc tính</label>
            <select name="attribute_id" id="attribute_id"
                    class="form-control @error('attribute_id') is-invalid @enderror"
                    >
                <option value="">-- Chọn thuộc tính --</option>
                @foreach($attributes as $attr)
                    <option value="{{ $attr->id }}"
                        {{ old('attribute_id') == $attr->id ? 'selected' : '' }}>
                        {{ $attr->name }}
                    </option>
                @endforeach
            </select>
            @error('attribute_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nhập giá trị --}}
        <div class="mb-3">
            <label for="value" class="form-label">Giá trị</label>
            
            <input type="text" name="value" id="value"
                   class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value') }}">
            @error('value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('attribute_values.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
