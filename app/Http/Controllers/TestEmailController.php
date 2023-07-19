<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function sendTestEmail()
    {
        $recipientEmail = 'vikas.singh@eternitysolutions.net';

        $data = [
            'name' => 'Vikas Singh',
        ];

        // Send the email using the 'test-email' view (resources/views/emails/test-email.blade.php)
        Mail::send('emails.test-email', $data, function ($message) use ($recipientEmail) {
            $message->to($recipientEmail);
            $message->subject('Forge Email Working');
        });

        return 'Test email sent successfully to ' . $recipientEmail;
    }
}
