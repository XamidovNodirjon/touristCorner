<?php

namespace App\Mail;

use App\Models\Library;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Termwind\Components\Li;

class MaterialMail extends Mailable
{
    use Queueable, SerializesModels;

    public $material;
    protected $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct(Library $material)
    {
        $this->material = $material;
    }

    public function build()
    {
        $filePath = storage_path('app/public/' . $this->material->file_path);

        return $this->subject('Siz so‘ragan material')
                    ->view('emails.material')
                    ->attach($filePath, [
                        'as' => $this->material->title_en . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Material Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
