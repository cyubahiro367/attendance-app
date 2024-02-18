<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendAttendanceRecord extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $names;
    /**
     * Create a new message instance.
     */
    public function __construct(string $names)
    {
        $this->names = $names;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = config('mail.from.address');
        $senderName = config('mail.from.name');

        return new Envelope(
            from: new Address($from, $senderName),
            subject: 'Attendance Record',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.SendAttendanceRecord',
            with: [
                'names' => $this->names,
            ],
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
