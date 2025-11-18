<?php

namespace App\Services;

use App\Models\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentStatusService
{
    public const PAID_NAME = 'Đã thanh toán';
    public const REFUNDED_NAME = 'Đã hoàn tiền';

    /**
     * Return PaymentStatus model by name or null.
     */
    public function getByName(string $name): ?PaymentStatus
    {
        try {
            return PaymentStatus::where('status_name', $name)->first();
        } catch (\Throwable $e) {
            Log::warning('PaymentStatus table missing or query failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getIdByName(string $name): ?int
    {
        $m = $this->getByName($name);
        return $m ? $m->id : null;
    }

    public function getPaidId(): ?int
    {
        return $this->getIdByName(self::PAID_NAME);
    }

    public function getRefundedId(): ?int
    {
        return $this->getIdByName(self::REFUNDED_NAME);
    }

    public function markAsPaid(Order $order): bool
    {
        $id = $this->getPaidId();
        if (! $id) return false;
        $order->payment_status_id = $id;
        return (bool) $order->save();
    }

    public function markAsRefunded(Order $order): bool
    {
        $id = $this->getRefundedId();
        if (! $id) return false;
        $order->payment_status_id = $id;
        return (bool) $order->save();
    }

    public function badgeClassFromName(?string $name): string
    {
        if (! $name) return 'badge bg-light text-dark';
        return config('payment.status_classes')[$name] ?? 'badge bg-light text-dark';
    }

    public function badgeClassFromId(?int $id): string
    {
        if (! $id) return 'badge bg-light text-dark';
        try {
            $name = PaymentStatus::find($id)?->status_name ?? null;
            return $this->badgeClassFromName($name);
        } catch (\Throwable $e) {
            Log::warning('Failed to resolve payment status class by id: ' . $e->getMessage());
            return 'badge bg-light text-dark';
        }
    }
}
