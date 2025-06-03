<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\AgentMail;
use App\Mail\AgentMailPay;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function mailsender()
    {
        Mail::to('dicarloarceo@gmail.com')->send(new AgentMailPay(23,"2025-06-01"));
        return(true);
    }
}
