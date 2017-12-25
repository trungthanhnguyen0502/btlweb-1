<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeTeam extends Mailable
{
    use Queueable, SerializesModels;

    public $who_assigned;

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
        return $this->subject('Chuyển yêu cầu đến bộ phận IT')
            ->view('mail.change_team')
            ->with('who_assigned', $this->who_assigned)
            ->with('subject', $this->title)
            ->with('deadline', $this->deadline)
            ->with('team_name', $this->team_name);
    }
}
