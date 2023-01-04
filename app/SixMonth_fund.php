<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SixMonth_fund extends Model
{
    use SoftDeletes;

    protected $table = "SixMonth_fund";
    protected $fillable =["nuc", "initial_date", "amount", "fk_client"];
    protected $dates = ["deleted_at"];
}
