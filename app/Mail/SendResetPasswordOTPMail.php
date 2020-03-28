<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordOTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans("auth_jwt.password_reset_request_otp_mail_title"))
        ->view('emails.auth.jwt.password_reset_otp');
    }
}
