<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ReportExportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $misReportPath = storage_path('app/public/mis');
        $files = File::files($misReportPath);
   
        date_default_timezone_set('Asia/Kolkata');
        $subtractDays = env('SUBTRACT_DAYS');
        $currentDate = Carbon::now()->subDays($subtractDays);

        $today = Carbon::now();
        $todaydDate = $today->format('d-M-Y');

        $formDate = $currentDate->format('d-M-Y');
        $current_time = date("h:i A");

        return (new MailMessage)
        ->subject('Daily MIS2 Report (' . $formDate . ' to ' . $todaydDate .')')
        ->line('Please find the attached daily MIS2 Reports. This report has been generated by our system and includes data of last 3 months.')
        ->attach($files);
    }
    

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
