<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class SendEmailVerificationCodeAndLink extends Notification
{
    use Queueable;

    public string $code;

    /**
     * Create a new notification instance.
     *
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;
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
        // Generate signed URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Montessori ERP - Email Verification Code & Link')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for joining Montessori ERP. Please verify your email address to activate your account and log in.')
            ->line('Your 6-Digit Email Verification Code is:')
            ->line('## **' . $this->code . '**')
            ->line('This verification code expires in 60 minutes.')
            ->action('Verify Email Address Link', $verificationUrl)
            ->line('Alternatively, you can copy and paste the verification code directly on your verification screen.')
            ->line('If you did not create an account, no further action is required.');
    }
}
