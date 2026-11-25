<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket_Folios extends Model
{
    protected $table = "Ticket_Folios";
    protected $fillable =["fk_ticket", "folio","date_limit"];
}
