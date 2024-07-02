<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge_Moves extends Model
{
    use SoftDeletes;

    protected $table = "Charge_Moves";
    protected $fillable =["amount","apply_date","fk_fund","fk_charge"];
    protected $dates = ["deleted_at"];
}
