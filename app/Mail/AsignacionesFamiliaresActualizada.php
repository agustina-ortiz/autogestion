<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AsignacionesFamiliaresActualizada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public int    $legajo,
        public int    $anio,
        public int    $periodo,
        public array  $formularios,
        public array  $tiposAdjunto,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "DDJJ Asignaciones Familiares — Legajo {$this->legajo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.asignaciones-familiares-actualizada',
        );
    }
}