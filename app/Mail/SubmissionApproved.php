<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $businessName;

    public function __construct($user, $businessName)
    {
        $this->user = $user;
        $this->businessName = $businessName;
    }

    public function build()
    {
        return $this->subject('Pengajuan Usaha Anda Telah Disetujui')
            ->view('emails.submission_approved');
    }
}
