<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRate extends Model
{
    use SoftDeletes;

    protected $table = "ExchangeRate";
    protected $fillable =["name","fund_type"];
    protected $dates = ["deleted_at"];
}
