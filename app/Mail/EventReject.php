<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReject extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $title;
    public $note;

    public function __construct($user, $title, $note)
    {
        $this->title = $title;
        $this->user = $user;
        $this->note = $note;
    }

    public function build()
    {
        return $this->subject('Pengajuan Event Anda Ditolak')
            ->view('emails.EventRejected');
    }
}
