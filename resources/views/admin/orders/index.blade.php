@extends('layouts.admin.admin')

@section('title', 'Đơn Hàng')
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-dark">Danh Sách Đơn Hàng</h1>
        </div>

        <form id="filterForm" method="GET" action="{{ route('orders.index') }}">
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input name="q" value="{{ request('q') }}" type="text" class="form-control" placeholder="Tìm kiếm theo mã đơn, khách hàng..." id="searchInput">
                </div>
                <div class="col-md-4 mb-2">
                    <select id="statusFilter" name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}" {{ request('status') == $s->id ? 'selected' : '' }}>{{ $s->status_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary">Lọc</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="ordersTable">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái đơn hàng</th>
                        <th>Trạng Thái Thanh Toán</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr data-order-id="{{ $order->id }}" data-status-id="{{ $order->status_id }}">
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->account->name ?? 'Khách lạ' }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($order->details as $detail)
                                       {{ $detail->productVariant?->product?->name ?? 'Không tìm thấy sản phẩm' }}
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $order->booking_date }}</td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}₫</td>
                           <td>
    @php
        $status = $order->status->status_name ?? 'Chưa xác định';
        $statusClass = match($status) {
            'Chưa xác nhận' => 'badge bg-secondary',
            'Đã thanh toán, chờ xác nhận' => 'badge bg-primary',
            'Đã xác nhận' => 'badge bg-primary',
            'Đang chuẩn bị hàng' => 'badge bg-info text-dark',
            'Đang giao' => 'badge bg-warning text-dark',
            'Đã giao' => 'badge bg-success',
            'Đã nhận' => 'badge bg-success',
            'Thành công' => 'badge bg-success',
            'Hoàn hàng' => 'badge bg-danger',
            'Hủy đơn hàng' => 'badge bg-dark',
            default => 'badge bg-light text-dark',
        };
    @endphp
    <span class="{{ $statusClass }} order-status-badge">{{ $status }}</span>
</td>

               <td>
                        <form method="POST" action="{{ route('orders.update', $order->id) }}" class="ajax-payment-form d-flex align-items-center">
                            @csrf
                            @method('PUT')
                            <select name="payment_status_id" class="form-select form-select-sm me-2">
                                <option value="">Chọn TT thanh toán</option>
                                @foreach($paymentStatuses as $ps)
                                    <option value="{{ $ps->id }}" {{ $order->payment_status_id == $ps->id ? 'selected' : '' }}>{{ $ps->status_name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Lưu</button>
                        </form>
                    </td>
                            <td>
                                <div class="d-flex">
                                    <form method="POST" action="{{ route('orders.update', $order->id) }}" class="d-flex me-2 ajax-status-form">
                                        @csrf
                                        @method('PUT')
                                        <select name="status_id" class="form-select form-select-sm me-2">
                                            <option value="">Chọn trạng thái</option>
                                            @foreach($statuses as $s)
                                                <option value="{{ $s->id }}" {{ $order->status_id == $s->id ? 'selected' : '' }}>{{ $s->status_name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                                    </form>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // Client-side fallback search
        (function () {
            const input = document.getElementById('searchInput');
            if (!input) return;
            input.addEventListener('keyup', function () {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#ordersTable tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        })();

        // AJAX status update handler: update in-place without page reload
        let __pendingConfirmForm = null;
        let __pendingConfirmModal = null;

        document.getElementById('confirmActionBtn').addEventListener('click', function () {
            if (!__pendingConfirmForm) return;
            // mark confirmed and re-submit
            __pendingConfirmForm.dataset.confirmed = '1';
            if (__pendingConfirmModal) __pendingConfirmModal.hide();
            // re-dispatch submit event
            __pendingConfirmForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            __pendingConfirmForm = null;
            __pendingConfirmModal = null;
        });

        document.querySelectorAll('.ajax-status-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const action = form.getAttribute('action');
                const fd = new FormData(form);

                // controls
                const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button');
                const select = form.querySelector('select[name="status_id"]');
                const row = form.closest('tr');
                const currentStatusId = row ? row.getAttribute('data-status-id') : null;

                // if nothing selected
                const selectedVal = select ? select.value : '';
                if (!selectedVal) {
                    if (window.showToast) window.showToast('Vui lòng chọn trạng thái trước khi cập nhật', 'warning');
                    return;
                }

                // prevent no-op updates
                if (currentStatusId && String(currentStatusId) === String(selectedVal)) {
                    if (window.showToast) window.showToast('Trạng thái không thay đổi', 'info');
                    return;
                }

                // confirm for cancellation or destructive transitions (use modal)
                const selectedText = select ? select.options[select.selectedIndex].text : '';
                if (selectedText && selectedText.toLowerCase().includes('hủy')) {
                    // if not already confirmed, show modal and defer submission
                    if (form.dataset.confirmed !== '1') {
                        __pendingConfirmForm = form;
                        const orderCode = row ? (row.querySelector('td') ? row.querySelector('td').textContent.trim() : '') : '';
                        const body = document.getElementById('confirmModalBody');
                        if (body) body.textContent = `Bạn có chắc muốn hủy đơn ${orderCode}? Hành động này có thể không hoàn tác.`;
                        const modalEl = new bootstrap.Modal(document.getElementById('confirmActionModal'));
                        modalEl.show();
                        __pendingConfirmModal = modalEl;
                        return;
                    }
                }

                // disable controls & show spinner
                let originalBtnHtml = null;
                if (submitBtn) {
                    originalBtnHtml = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang...';
                }
                if (select) select.disabled = true;

                // find csrf token
                const meta = document.querySelector('meta[name="csrf-token"]');
                const csrf = meta ? meta.getAttribute('content') : (fd.get('_token') || '');

                // Determine method override (use actual method when possible)
                const method = fd.get('_method') || 'POST';

                fetch(action, {
                    method: (method === 'PUT' ? 'PUT' : method),
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: fd,
                }).then(res => {
                    if (!res.ok) {
                        if (res.status === 422) {
                            return res.json().then(jsonErr => {
                                const msg = jsonErr.message || (jsonErr.errors && jsonErr.errors.status_id && jsonErr.errors.status_id[0]) || 'Chuyển trạng thái không hợp lệ';
                                if (window.showToast) window.showToast(msg, 'danger');
                                throw new Error('validation');
                            });
                        }
                        throw new Error('network');
                    }
                    return res.json();
                }).then(json => {
                    if (json && json.order_id) {
                        const row = document.querySelector('tr[data-order-id="' + json.order_id + '"]');
                        if (row) {
                            const badge = row.querySelector('.order-status-badge');
                            if (badge) {
                                badge.className = json.status_class + ' order-status-badge';
                                badge.textContent = json.status_name;
                            }
                            row.setAttribute('data-status-id', json.status_id);
                        }
                        if (window.showToast) window.showToast('Cập nhật trạng thái thành công', 'success');
                    } else {
                        if (window.showToast) window.showToast('Không nhận được phản hồi hợp lệ', 'warning');
                    }
                }).catch(err => {
                    if (err.message === 'validation') {
                        // already shown toast
                    } else {
                        console.error('Update error', err);
                        if (window.showToast) window.showToast('Không thể cập nhật trạng thái', 'danger');
                    }
                }).finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        if (originalBtnHtml !== null) submitBtn.innerHTML = originalBtnHtml;
                    }
                    if (select) select.disabled = false;
                    // cleanup any temporary confirmed flag
                    try { delete form.dataset.confirmed; } catch (e) {}
                });
            });
        });

        // AJAX payment update handler
        document.querySelectorAll('.ajax-payment-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const action = form.getAttribute('action');
                const fd = new FormData(form);

                const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button');
                const select = form.querySelector('select[name="payment_status_id"]');
                if (submitBtn) {
                    const original = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    // find csrf
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    const csrf = meta ? meta.getAttribute('content') : (fd.get('_token') || '');

                    fetch(action, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: fd,
                    }).then(res => {
                        if (!res.ok) {
                            if (res.status === 422) return res.json().then(j => Promise.reject(j));
                            return Promise.reject({ message: 'Network error' });
                        }
                        return res.json();
                    }).then(json => {
                        if (json && json.order_id) {
                            const row = document.querySelector('tr[data-order-id="' + json.order_id + '"]');
                            if (row) {
                                // update payment badge if present
                                const paymentCell = row.querySelector('td:nth-child(7)');
                                if (paymentCell && json.payment_status_name) {
                                    paymentCell.innerHTML = '<span class="' + json.payment_status_class + '">' + (json.payment_status_name || '---') + '</span>';
                                }
                            }
                            if (window.showToast) window.showToast('Cập nhật trạng thái thanh toán thành công', 'success');
                        }
                    }).catch(err => {
                        console.error('Payment update error', err);
                        const msg = (err && err.message) || (err && err.errors && err.errors.payment_status_id && err.errors.payment_status_id[0]) || 'Không thể cập nhật trạng thái thanh toán';
                        if (window.showToast) window.showToast(msg, 'danger');
                    }).finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = original;
                        }
                    });
                }
            });
        });
    </script>
    <!-- Confirmation Modal (Bootstrap) -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmActionModalLabel">Xác nhận hành động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    Bạn có chắc chắn muốn thực hiện hành động này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmActionBtn">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
@endsection
