<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $user = Auth::user();
        if (! $user) return redirect()->back();

        $notif = $user->notifications()->where('id', $id)->first();
        if ($notif) {
            $notif->markAsRead();
            $data = $notif->data ?? [];
            if (isset($data['order_id'])) {
                // Redirect to admin order show if possible
                try {
                    return redirect()->route('orders.show', $data['order_id']);
                } catch (\Throwable $_) {
                    return redirect()->back();
                }
            }
        }
        return redirect()->back();
    }
}
