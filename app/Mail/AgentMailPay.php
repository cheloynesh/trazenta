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

class AgentMailPay extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id,$date)
    {
        $this->id = $id;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $id= $this->id;
        $date = $this->date;
        $auxmnth = explode("-", $date);
        $mnth = $months[intval($auxmnth[1])];
        $year = $auxmnth[0];
        $month = $auxmnth[1];

        $user = DB::select('call mailAgentPay(?,?)',[$id,$this->date]);

        $name = $user[0]->aname;
        $this->subject('Comisiones Trazenta')->view('mail.agentmailpay', compact('name','year','mnth'));

        if(intval($user[0]->contrec) != 0)
        {
            $rec = DB::select('call previewRecPay(?,?)',[$id,$this->date]);
            foreach($rec as $rc)
            {
                $this->attach(public_path("comition_files/rec_pay/".$rc->pay_doc));
            }
        }

        if(intval($user[0]->contpp) != 0)
        {
            $pp = DB::select('call previewFstPay(?,?)',[$id,$this->date]);
            foreach($pp as $pps)
            {
                $this->attach(public_path("comition_files/fst_pay/".$pps->fst_pay_doc));
            }
        }

        if(intval($user[0]->contpa != 0))
        {
            $pa = DB::select('call previewAdPay(?,?)',[$id,$this->date]);
            foreach($pa as $pas)
            {
                $this->attach(public_path("comition_files/mov_pay/".$pas->mov_pay_doc));
            }
        }

        if(intval($user[0]->lpnopay != 0))
        {
            $lp = DB::select('call previewLpPay(?,?)',[$id,$this->date]);
            foreach($lp as $lps)
            {
                $this->attach(public_path("comition_files/lp_pay/".$lps->lp_pay_doc));
            }
        }

        // dd($user,$rec,$pp,$lp);

        return $this;
        // return $this->subject('Correo informativo ELAN')->attach(public_path("comition_files/fst_pay/FST_309_12_2024.pdf"))->view('mail.agentmailpay', compact('today'));
    }
}
