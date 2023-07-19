<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function sendTestEmail()
    {
        $recipientEmail = 'vikas.singh@eternitysolutions.net'; 

        // Send the email without using a view
        Mail::raw('This is a test email.', function ($message) use ($recipientEmail) {
            $message->to($recipientEmail);
            $message->subject('Email From Forge');
        });

        return 'Test email sent successfully to ' . $recipientEmail;
    }
}
