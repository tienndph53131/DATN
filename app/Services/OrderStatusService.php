<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderStatusLog;
use App\Models\ProductVariant;
use App\Services\PaymentStatusService;
use Illuminate\Support\Facades\Log;

class OrderStatusService
{
    /**
     * Validate whether a transition from the order's current status to the target status is allowed.
     * Returns array with keys: allowed(bool) and message(string|null)
     */
    public function isTransitionAllowed(Order $order, ?int $newStatusId): array
    {
        $fromName = $order->status->status_name ?? null;
        $toStatus = $newStatusId ? OrderStatus::find($newStatusId) : null;
        $toName = $toStatus->status_name ?? null;

        $transitions = config('order.status_transitions', []);
        $allowed = true;
        if ($fromName && array_key_exists($fromName, $transitions)) {
            $allowedList = $transitions[$fromName] ?? [];
            $allowed = in_array($toName, $allowedList, true);
        }

        if (! $allowed) {
            $msg = 'Chuyển trạng thái từ "' . ($fromName ?? '—') . '" sang "' . ($toName ?? '—') . '" không được phép.';
            return ['allowed' => false, 'message' => $msg];
        }

        return ['allowed' => true, 'message' => null];
    }

    /**
     * Apply a status change to the order, handle payment updates, auto-behaviors and logging.
     * Returns ['ok' => bool, 'message' => string|null, 'data' => array|null]
     */
    public function changeStatus(Order $order, ?int $newStatusId, ?int $newPaymentId = null, $changedBy = null): array
    {
        $oldStatusId = $order->status_id;
        $oldPayment = $order->payment_status_id;

        // Validate transition
        $check = $this->isTransitionAllowed($order, $newStatusId);
        if (! $check['allowed']) {
            return ['ok' => false, 'message' => $check['message'], 'data' => null];
        }

        $toStatus = $newStatusId ? OrderStatus::find($newStatusId) : null;
        $toName = $toStatus->status_name ?? null;

        // apply status & payment update
        $order->status_id = $newStatusId;
        if ($newPaymentId !== null && intval($newPaymentId) !== intval($oldPayment)) {
            $order->payment_status_id = $newPaymentId;
        }
        $order->save();

        // Automatic behaviors on specific target statuses
        $deliveredStatuses = ['Đã giao', 'Đã nhận', 'Thành công'];
        $toNameForAuto = $toName ?? ($order->status->status_name ?? null);

        if ($toNameForAuto && in_array($toNameForAuto, $deliveredStatuses, true)) {
            $paymentService = new PaymentStatusService();
            $paidId = $paymentService->getPaidId();
            if ($paidId && $order->payment_status_id != $paidId) {
                $order->payment_status_id = $paidId;
                $order->save();
                try {
                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'old_status_id' => null,
                        'new_status_id' => null,
                        'changed_by' => null,
                        'note' => sprintf('Auto-mark payment as "%s" because order status is "%s"', PaymentStatusService::PAID_NAME, $toNameForAuto),
                    ]);
                } catch (\Throwable $_e) {
                    Log::error('Failed to write auto payment status log: ' . $_e->getMessage());
                }
            }
        }

        // If the order moved to 'Hoàn hàng' (return), mark payment as refunded and restock items
        if ($toNameForAuto && $toNameForAuto === 'Hoàn hàng') {
            try {
                $paymentService = new PaymentStatusService();
                $refundId = $paymentService->getRefundedId();
                if ($refundId) {
                    $order->payment_status_id = $refundId;
                    $order->save();
                }

                foreach ($order->details as $d) {
                    try {
                        if ($d->product_variant_id) {
                            $variant = ProductVariant::find($d->product_variant_id);
                            if ($variant && isset($variant->stock_quantity)) {
                                $variant->increment('stock_quantity', $d->quantity);
                            }
                        }
                    } catch (\Throwable $_) {
                        // continue on errors
                    }
                }

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'old_status_id' => null,
                    'new_status_id' => null,
                    'changed_by' => $changedBy,
                    'note' => 'Order marked as Hoàn hàng — payment set to Đã hoàn tiền and items restocked',
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to process return actions: ' . $e->getMessage());
            }
        }

        // create audit log for status change
        try {
            OrderStatusLog::create([
                'order_id' => $order->id,
                'old_status_id' => $oldStatusId,
                'new_status_id' => $order->status_id,
                'changed_by' => $changedBy,
                'note' => null,
            ]);

            if ($newPaymentId !== null && intval($newPaymentId) !== intval($oldPayment)) {
                $oldName = null;
                $newName = null;
                try {
                    $oldName = $oldPayment ? (\App\Models\PaymentStatus::find($oldPayment)?->status_name) : null;
                    $newName = (\App\Models\PaymentStatus::find($newPaymentId)?->status_name);
                } catch (\Throwable $_) {}

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'old_status_id' => null,
                    'new_status_id' => null,
                    'changed_by' => $changedBy,
                    'note' => sprintf('Payment status: "%s" -> "%s"', $oldName ?? ($oldPayment ?: '—'), $newName ?? $newPaymentId),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to write order status log: ' . $e->getMessage());
        }

        // reload relations
        $order->load('status', 'paymentStatus');

        $statusName = $order->status->status_name ?? '';
        $statusClass = $this->mapStatusToBadge($statusName);

        $paymentService = new PaymentStatusService();
        $paymentName = $order->paymentStatus->status_name ?? null;
        $paymentClass = $paymentService->badgeClassFromName($paymentName);

        return [
            'ok' => true,
            'message' => null,
            'data' => [
                'order_id' => $order->id,
                'status_id' => $order->status_id,
                'status_name' => $statusName,
                'status_class' => $statusClass,
                'old_status_id' => $oldStatusId,
                'payment_status_id' => $order->payment_status_id,
                'payment_status_name' => $paymentName,
                'payment_status_class' => $paymentClass,
            ],
        ];
    }

    protected function mapStatusToBadge(?string $statusName): string
    {
        return match($statusName) {
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
    }
}
