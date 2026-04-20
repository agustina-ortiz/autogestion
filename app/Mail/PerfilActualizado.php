<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PerfilActualizado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $legajo,
        public string $motivo,
        public string $mensaje,
        public ?string $direccion = null,
        public ?string $direccion1 = null,
        public ?string $telefono = null,
        public ?string $telefono1 = null,
        public ?string $email = null,
        public ?string $email1 = null,
        public ?string $imagen = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Autogestión - ' . $this->motivo . ' - Legajo ' . $this->legajo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.perfil-actualizado',
        );
    }
}
