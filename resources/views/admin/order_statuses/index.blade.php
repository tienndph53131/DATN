@extends('layouts.admin.admin')

@section('title', 'Quản lý Trạng Thái Đơn Hàng')
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-dark">Trạng Thái Đơn Hàng</h1>
            <a href="{{ route('order-statuses.create') }}" class="btn btn-primary">Thêm trạng thái</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tên trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statuses as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->status_name }}</td>
                            <td>
                                <a href="{{ route('order-statuses.edit', $s->id) }}" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                                <form action="{{ route('order-statuses.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $statuses->links() }}
    </div>
@endsection
