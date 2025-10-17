@extends('layouts.admin.admin')

@section('title', 'Danh sách các thuộc tính')

@section('content')
<div class="container">
    <h2>Danh sách các thuộc tính</h2>
    <div class="text-end mb-3">
        <a href="{{ route('attributes.create') }}" class="btn btn-primary">+ Thêm thuộc tính</a>
    </div>

    <table class="table text-center">
    <thead>
        <tr>
            <th>ID</th>
            <th>Loại thuộc tính</th>
            <th>Giá trị</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attributes as $attribute)
            <tr>
                <td>{{ $attribute->id }}</td>
                <td>{{ $attribute->name }}</td>
                <td>
                    @foreach($attribute->values as $val)
                        {{ $val->value }}<br>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('attributes.edit', $attribute->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('attributes.destroy', $attribute->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
