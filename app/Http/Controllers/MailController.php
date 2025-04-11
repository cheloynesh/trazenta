<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\AgentMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function mailsender()
    {
        Mail::to('dicarloarceo@gmail.com')->send(new AgentMail(1));
        return(true);
    }
}
