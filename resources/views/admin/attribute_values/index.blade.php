@extends('layouts.admin.admin')

@section('title', 'Quản lý Giá trị Thuộc tính')

@section('content')
<div class="container mt-4">
    <h2>Danh sách Giá trị thuộc tính</h2>
    <a href="{{ route('attribute_values.create') }}" class="btn btn-success mb-3">+ Thêm giá trị</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thuộc tính</th>
                <th>Giá trị</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($values as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->attribute->name ?? 'N/A' }}</td>
                <td>{{ $item->value }}</td>
                <td>
                    <a href="{{ route('attribute_values.edit', $item->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('attribute_values.destroy', $item->id) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa giá trị này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
