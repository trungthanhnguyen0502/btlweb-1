<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $title = 'Đặt lại mật khẩu';

    public $reset_link = "";

    public $code = "";

    public $system_name = "Call-Log-IT";

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.password_reset')
            ->subject($this->title)
            ->with('title', $this->title)
            ->with('system_name', $this->system_name)
            ->with('code', $this->code)
            ->with('reset_link', $this->reset_link);
    }
}
