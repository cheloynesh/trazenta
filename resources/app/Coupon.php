<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $table = "Coupon";
    protected $fillable =["number", "amount", "pay_date", "fk_nuc"];
    protected $dates = ["deleted_at"];
}
