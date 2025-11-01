@extends('layouts.admin.admin')

@section('content')
<div class="container mt-4">
    <h2>Danh sách danh mục</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-success mb-3">+ Thêm danh mục</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('categories.edit', $item->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('categories.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
