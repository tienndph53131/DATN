<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ReturnRequestConfirmation extends Mailable
{
    // Stubbed: return-request emails removed.
    public function build()
    {
        return $this->subject('Notification')->view('emails.blank');
    }
}
