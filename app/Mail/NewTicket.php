<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $who_created;

    public $team_name;

    public $title;

    public $deadline;

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
        return $this->subject('Yêu cầu mới đến bộ phận IT')
            ->view('mail.new_ticket')
            ->with('who_created', $this->who_created)
            ->with('subject', $this->title)
            ->with('deadline', $this->deadline)
            ->with('team_name', $this->team_name);
    }
}
