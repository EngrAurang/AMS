<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $startDate;
    public $endDate;
    public $numberOfDays;
    /**
     * Create a new message instance.
     */
    public function __construct($name, $startDate, $endDate,$numberOfDays)
    {

        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->numberOfDays = $numberOfDays;
    }

    // public function build()
    // {
    //     // dd("no");
    //     return $this->view('admin-notification')
    //         ->subject('Leave Apply');
    // }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Apply For Leave',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
