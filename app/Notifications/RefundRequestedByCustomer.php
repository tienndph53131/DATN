<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class RefundRequestedByCustomer extends Notification
{
    // Stub notification; refund request feature removed.
    public function via($notifiable) { return []; }
}
