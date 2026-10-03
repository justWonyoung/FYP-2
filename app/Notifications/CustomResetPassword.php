<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class CustomResetPassword extends Notification
{

    use Queueable;


    public $token;



    public function __construct($token)
    {
        $this->token = $token;
    }




    public function via($notifiable)
    {
        return ['mail'];
    }





    public function toMail($notifiable)
{


    $url = url(
        route(
            'password.reset',
            [
                'token' => $this->token,
                'email' => $notifiable->email,
            ],
            false
        )
    );


    return (new MailMessage)

        ->subject(
            'NEXORA Password Reset'
        )


        ->greeting(
             'Hello '.$notifiable->full_name
        )


        ->line(
            'We received a request to reset your NEXORA account password.'
        )


        ->line(
            'Click the button below to create a new password.'
        )


        ->action(
            'Reset Password',
            $url
        )


        ->line(
            'This password reset link will expire in 60 minutes.'
        )


        ->line(
            'If you did not request this password reset, you can safely ignore this email.'
        )


        ->salutation(
            'Regards, NEXORA Operational System'
        );


}



}