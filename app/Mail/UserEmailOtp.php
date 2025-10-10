<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserEmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $name;

    public function __construct($otp, $name = null)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Your OTP Code')
                    ->view('emails.send_otp') // This is your blade file
                    ->with([
                        'otp' => $this->otp,
                        'name' => $this->name
                    ]);
    }
}
