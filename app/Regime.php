<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regime extends Model
{
    use SoftDeletes;

    protected $table = "Regime";
    protected $fillable =["name","iva","ret_isr","ret_iva"];
    protected $dates = ["deleted_at"];
}
