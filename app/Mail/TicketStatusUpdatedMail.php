<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        switch($this->data['ticket']->fk_status)
        {
            case 50:
                return $this->subject('Hemos recibido el trámite de ' . $this->data['cliente'] . '.')->view('emails.ticket_uploaded');

            case 51:
                $vista = ($this->data['ticket']->ins_seg == 'inv') ? 'emails.ticket_minfo_inv' : 'emails.ticket_minfo';
                $email = $this->subject('Solicitamos información faltante de tu trámite ' . $this->data['ticketname'])->view($vista);
                return $this->adjuntarArchivos($email);

            case 52:
                return $this->subject('Tu solicitud del cliente ' . $this->data['cliente'] . ' se encuentra en trámite con el folio ' . $this->data['folio'] . '.')->view('emails.ticket_tram');

            case 53:
                $email = $this->subject('Te informamos que tú trámite ' . $this->data['ticketname'] . ', se encuentra suspendido.')
                              ->view('emails.ticket_susp');
                return $this->adjuntarArchivos($email);

            case 54:
                $email = $this->subject('Te informamos que tú trámite fue ATENDIDO ' . $this->data['ticketname'] . '.')
                              ->view('emails.ticket_attend');
                return $this->adjuntarArchivos($email);

            case 55:
                return $this->subject('Te informamos que tú trámite fue RECHAZADO ' . $this->data['ticketname'] . '.')->view('emails.ticket_rechaz');
        }
    }

    /**
     * Adjunta múltiples archivos si vienen en arreglo o uno solo si es legado
     */
    private function adjuntarArchivos($email)
    {
        // 🟢 1. Múltiples archivos (Nuevo esquema)
        if (!empty($this->data['archivos_adjuntos']) && is_array($this->data['archivos_adjuntos'])) {
            foreach ($this->data['archivos_adjuntos'] as $rutaArchivo) {
                if (file_exists($rutaArchivo)) {
                    $email->attach($rutaArchivo);
                }
            }
        } 
        // 🟢 2. Archivo único (Retrocompatibilidad)
        elseif (!empty($this->data['archivo_adjunto']) && file_exists($this->data['archivo_adjunto'])) {
            $email->attach($this->data['archivo_adjunto']);
        }

        return $email;
    }
}