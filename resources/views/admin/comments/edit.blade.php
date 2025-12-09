@extends('layouts.admin.admin')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-3">Chỉnh sửa bình luận #{{ $comment->id }}</h3>

    <form action="{{ route('comments.update', $comment->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label">Sản phẩm</label>
            <div class="form-control">{{ optional($comment->product)->name }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Tài khoản</label>
            <div class="form-control">{{ optional($comment->account)->name }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Đánh giá</label>
            <select name="rating" class="form-select" required>
                @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}" {{ $comment->rating == $i ? 'selected' : '' }}>{{ $i }}★</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="content" class="form-control" rows="6" required>{{ $comment->content }} </textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" {{ $comment->status ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$comment->status ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Lưu</button>
            <a href="{{ route('comments.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection