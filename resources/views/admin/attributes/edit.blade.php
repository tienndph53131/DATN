@extends('layouts.admin.admin')

@section('title', 'Sửa thuộc tính')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">Sửa thuộc tính</h2>

    {{-- Hiển thị lỗi validate --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Vui lòng kiểm tra lại:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('attributes.update', $attribute->id) }}" method="POST" class="w-50 mx-auto">
        @csrf
        @method('PUT')

        {{-- Chọn loại thuộc tính --}}
        <div class="mb-3">
            <label class="form-label">Loại thuộc tính</label>
            <select name="name" class="form-control @error('name') is-invalid @enderror" required>
                <option value="">-- Chọn loại thuộc tính --</option>
                <option value="Màu sắc" {{ (old('name', $attribute->name) == 'Màu sắc') ? 'selected' : '' }}>Màu sắc</option>
                <option value="Kích cỡ" {{ (old('name', $attribute->name) == 'Kích cỡ') ? 'selected' : '' }}>Kích cỡ</option>
            </select>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Giá trị thuộc tính --}}
        <div class="mb-3">
            <label class="form-label">Giá trị thuộc tính</label>
            <div id="values-wrapper">
                {{-- Nếu có lỗi thì load lại dữ liệu người dùng nhập --}}
                @if(old('values'))
                    @foreach(old('values') as $val)
                        <div class="input-group mb-2">
                            <input type="text" name="values[]" class="form-control" value="{{ $val }}" placeholder="Nhập giá trị...">
                            <button type="button" class="btn btn-danger remove-value">Xóa</button>
                        </div>
                    @endforeach
                @else
                    @foreach($attribute->values as $val)
                        <div class="input-group mb-2">
                            <input type="text" name="values[]" class="form-control" value="{{ $val->value }}" placeholder="Nhập giá trị...">
                            <button type="button" class="btn btn-danger remove-value">Xóa</button>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" id="add-value" class="btn btn-secondary btn-sm">+ Thêm giá trị</button>

            {{-- Hiển thị lỗi --}}
            @error('values')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
            @error('values.*')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nút lưu --}}
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('attributes.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

{{-- JS thêm/xóa giá trị --}}
<script>
    document.getElementById('add-value').addEventListener('click', function() {
        const wrapper = document.getElementById('values-wrapper');
        const newInput = document.createElement('div');
        newInput.classList.add('input-group', 'mb-2');
        newInput.innerHTML = `
            <input type="text" name="values[]" class="form-control" placeholder="Nhập giá trị mới...">
            <button type="button" class="btn btn-danger remove-value">Xóa</button>
        `;
        wrapper.appendChild(newInput);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-value')) {
            e.target.parentElement.remove();
        }
    });
</script>
@endsection
