<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventApprove extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $title;

    public function __construct($user, $title)
    {
        $this->title = $title;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Informasi Event Disetujui')
            ->view('emails.EventApprove');
    }
}
