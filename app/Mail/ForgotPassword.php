<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('BFUB UPI | Forgot Password')
            ->markdown('mails.forgot_password')
            ->with([
                'name' => $this->data->name,
                'email' => $this->data->email,
                'token' => $this->data->token
            ])
            ->text('mails.forgot_password_plain_text');
    }
}
