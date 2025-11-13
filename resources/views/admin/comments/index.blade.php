@extends('layouts.admin.admin')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-3">Quản lý bình luận</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <form id="bulkForm" action="{{ route('comments.bulk') }}" method="POST">
                @csrf
                <div class="d-flex mb-3 gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm nội dung/sản phẩm/người dùng" class="form-control form-control-sm" style="width:320px">
                    <select name="status" class="form-select form-select-sm" style="width:120px">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                    <select name="rating" class="form-select form-select-sm" style="width:120px">
                        <option value="">Tất cả đánh giá</option>
                        @for($r=5;$r>=1;$r--)
                            <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }}★</option>
                        @endfor
                    </select>
                    <button type="submit" formaction="{{ route('comments.index') }}" formmethod="GET" class="btn btn-sm btn-outline-primary">Lọc</button>

                    <div class="ms-auto d-flex gap-2">
                        <select id="bulk-action" name="action" class="form-select form-select-sm" style="width:160px">
                            <option value="">Chọn hành động hàng loạt</option>
                            <option value="approve">Duyệt</option>
                            <option value="hide">Ẩn</option>
                            <option value="delete">Xóa</option>
                        </select>
                        <button type="button" id="bulkApply" class="btn btn-sm btn-danger">Áp dụng</button>
                    </div>
                </div>

                <table class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Tài khoản</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comments as $comment)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $comment->id }}" class="selectItem"></td>
                            <td>{{ $comment->id }}</td>
                            <td>{{ optional($comment->product)->name }}</td>
                            <td>{{ optional($comment->account)->name }}</td>
                            <td>
                                @if($comment->rating && $comment->rating > 0)
                                    @for($s=1;$s<=5;$s++)
                                        @if($s <= $comment->rating)
                                            <span style="color:#f5b301">★</span>
                                        @else
                                            <span style="color:#ddd">★</span>
                                        @endif
                                    @endfor
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="max-width:400px;">{{ $comment->content }}</td>
                            <td>{{ $comment->date->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($comment->status)
                                    <span class="badge bg-success">Hiển thị</span>
                                @else
                                    <span class="badge bg-secondary">Ẩn</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-info">Chỉnh sửa</a>
                                <button type="button" class="btn btn-sm btn-{{ $comment->status ? 'warning' : 'success' }} toggleStatusBtn" data-id="{{ $comment->id }}" data-status="{{ $comment->status ? 1 : 0 }}">{{ $comment->status ? 'Tắt' : 'Duyệt' }}</button>

                                <button type="button" class="btn btn-sm btn-danger deleteBtn" data-id="{{ $comment->id }}">Xóa</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            </form>

            <div class="mt-3">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper to show Bootstrap toast notifications
    function showToast(message, variant = 'primary') {
        try {
            const toastEl = document.getElementById('globalToast');
            const toastBody = document.getElementById('globalToastBody');
            if (!toastEl || !toastBody) {
                alert(message);
                return;
            }
            // apply variant class (text-bg-*)
            toastEl.className = 'toast align-items-center text-bg-' + (variant === 'danger' ? 'danger' : (variant === 'success' ? 'success' : 'primary')) + ' border-0';
            toastBody.textContent = message;
            const bsToast = new bootstrap.Toast(toastEl);
            bsToast.show();
        } catch (e) {
            console.error('toast error', e);
            alert(message);
        }
    }
    document.getElementById('selectAll')?.addEventListener('change', function(){
        document.querySelectorAll('.selectItem').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('bulkApply')?.addEventListener('click', async function(){
        const action = document.getElementById('bulk-action').value;
        if (!action) { showToast('Vui lòng chọn hành động.', 'danger'); return; }
        const selected = Array.from(document.querySelectorAll('.selectItem')).filter(cb => cb.checked).map(cb => cb.value);
        if (!selected.length) { showToast('Vui lòng chọn ít nhất một bình luận.', 'danger'); return; }
        if (action === 'delete' && !confirm('Bạn chắc chắn muốn xóa các bình luận đã chọn?')) return;

        const form = document.getElementById('bulkForm');
        // Prepare form data
        const formData = new FormData();
        formData.append('action', action);
        selected.forEach(id => formData.append('ids[]', id));
        // CSRF token from the form
        const tokenEl = form.querySelector('input[name="_token"]');
        if (tokenEl) formData.append('_token', tokenEl.value);

        const bulkBtn = document.getElementById('bulkApply');
        const originalHtml = bulkBtn ? bulkBtn.innerHTML : null;
        try {
            if (bulkBtn) {
                bulkBtn.disabled = true;
                bulkBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';
            }

            const resp = await fetch('{{ route('comments.bulk') }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await resp.json();
            if (!resp.ok) throw new Error(data.message || 'Lỗi khi thực hiện hành động');

            showToast(data.message || 'Thành công', 'success');

            // Update UI in-place
            if (data.action === 'approve' || data.action === 'hide') {
                const newStatus = data.action === 'approve' ? 1 : 0;
                data.ids.forEach(id => {
                    const cb = document.querySelector('.selectItem[value="' + id + '"]');
                    if (!cb) return;
                    const row = cb.closest('tr');
                    if (!row) return;
                    const statusTd = row.querySelector('td:nth-child(8)'); // status column
                    if (statusTd) {
                        statusTd.innerHTML = newStatus ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Ẩn</span>';
                    }
                });
            }

            if (data.action === 'delete') {
                data.ids.forEach(id => {
                    const cb = document.querySelector('.selectItem[value="' + id + '"]');
                    if (!cb) return;
                    const row = cb.closest('tr');
                    if (row) row.remove();
                });
            }

        } catch (err) {
            console.error(err);
            showToast(err.message || 'Đã có lỗi xảy ra', 'danger');
        } finally {
            if (bulkBtn) {
                bulkBtn.disabled = false;
                if (originalHtml) bulkBtn.innerHTML = originalHtml;
            }
        }
    });

    // Inline toggle status handlers
    document.querySelectorAll('.toggleStatusBtn').forEach(btn => {
        btn.addEventListener('click', async function(){
            const id = this.dataset.id;
            const current = parseInt(this.dataset.status);
            const newStatus = current ? 0 : 1;
            const token = document.querySelector('input[name="_token"]')?.value;
            if (!token) { showToast('Missing CSRF token', 'danger'); return; }

            const originalHtml = this.innerHTML;
            try {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang...';

                const formData = new FormData();
                formData.append('_token', token);
                formData.append('_method', 'PATCH');
                formData.append('status', newStatus);

                const resp = await fetch('/admin/comments/' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
                const data = await resp.json();
                if (!resp.ok) throw new Error(data.message || 'Lỗi cập nhật');

                // update UI
                const row = this.closest('tr');
                const statusTd = row.querySelector('td:nth-child(8)');
                if (statusTd) statusTd.innerHTML = newStatus ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Ẩn</span>';
                // update button appearance
                this.dataset.status = newStatus;
                this.className = 'btn btn-sm btn-' + (newStatus ? 'warning' : 'success') + ' toggleStatusBtn';
                this.textContent = newStatus ? 'Tắt' : 'Duyệt';

                showToast(data.message || 'Cập nhật thành công', 'success');
            } catch (err) {
                console.error(err);
                showToast(err.message || 'Lỗi khi cập nhật', 'danger');
            } finally {
                this.disabled = false;
                if (originalHtml) this.innerHTML = this.textContent; // set to text only
            }
        });
    });

    // Inline delete via modal confirm
    let pendingDeleteId = null;
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    const confirmBtn = document.getElementById('confirmModalYes');
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function(){
            pendingDeleteId = this.dataset.id;
            // set modal text
            const body = document.getElementById('confirmModalBody');
            if (body) body.textContent = 'Bạn chắc chắn muốn xóa bình luận #' + pendingDeleteId + '?';
            if (confirmModal) confirmModal.show();
        });
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', async function(){
            if (!pendingDeleteId) return;
            const id = pendingDeleteId;
            const token = document.querySelector('input[name="_token"]')?.value;
            if (!token) { showToast('Missing CSRF token', 'danger'); return; }

            try {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xóa...';
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('_method', 'DELETE');

                const resp = await fetch('/admin/comments/' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
                const data = await resp.json();
                if (!resp.ok) throw new Error(data.message || 'Lỗi xóa');

                // remove row
                const cb = document.querySelector('.selectItem[value="' + id + '"]');
                if (cb) {
                    const row = cb.closest('tr');
                    if (row) row.remove();
                }

                showToast(data.message || 'Đã xóa', 'success');
            } catch (err) {
                console.error(err);
                showToast(err.message || 'Lỗi khi xóa', 'danger');
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Xác nhận';
                if (confirmModal) confirmModal.hide();
                pendingDeleteId = null;
            }
        });
    }
});
</script>
@endsection