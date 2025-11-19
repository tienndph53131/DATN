@extends('layouts.admin.admin')

@section('title', 'Quản lý Giá trị Thuộc tính')

@section('content')
<div class="container mt-4">
    <h2>Danh sách Giá trị thuộc tính</h2>
    <a href="{{ route('admin.attribute_values.create') }}" class="btn btn-success mb-3">+ Thêm giá trị</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Màu sắc</th>
                <th>Kích cỡ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                {{-- Cột Màu sắc --}}
                <td>
                    @foreach($values->where('attribute.name', 'Màu sắc') as $val)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>{{ $val->value }}</span>
                            <div>
                                <a href="{{ route('admin.attribute_values.edit', $val->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('admin.attribute_values.destroy', $val->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa giá trị này?')">Xóa</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </td>

                {{-- Cột Kích cỡ --}}
                <td>
                    @foreach($values->where('attribute.name', 'Kích cỡ') as $val)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>{{ $val->value }}</span>
                            <div>
                                <a href="{{ route('admin.attribute_values.edit', $val->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('admin.attribute_values.destroy', $val->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa giá trị này?')">Xóa</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
