<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessDisable extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $note;
    public function __construct($user, $note)
    {
        $this->user = $user;
        $this->note = $note;
    }

    public function build()
    {
        return $this->subject('Usaha Anda Dinonaktifkan')
            ->view('emails.BusinessDisable');
    }
}
