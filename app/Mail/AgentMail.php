<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\Profile;
use App\Permission;
use DateTime;
use DB;

class AgentMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $idview = $this->id;
        date_default_timezone_set('America/Mexico_City');
        $today = new DateTime();
        // dd($receiptMail, $renovMail, $pendMail, $vencMail);

        return $this->subject('Comisiones Trazenta')->view('mail.agentmail', compact('today'));
        // return $this->subject('Correo informativo ELAN')->attach(public_path("comition_files/fst_pay/FST_309_12_2024.pdf"))->view('mail.agentmail', compact('today'));
    }
}
