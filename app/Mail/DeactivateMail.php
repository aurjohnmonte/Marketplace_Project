<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeactivateMail extends Mailable
{
use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $data;
    public $message;

    public function __construct($data)
    {
        $this->data = $data;
        // $this->message = $data['message'];
    }

    //SEND MESSAGE
    public function build()
    {
        Log::info('greet', ['greet'=>'hello po neh agi nasab kos build method']);
        return $this->subject('YOUR ACCOUNT HAS BEEN DEACTIVATED BY THE ADMIN')
                    ->view('emails.deactivatemail');
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
