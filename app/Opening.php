<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opening extends Model
{
    use SoftDeletes;

    protected $table = "Initials";
    protected $fillable =[
        'fk_agent','fk_currency','fund_type','fk_status','fk_insurance','contract','nuc','name','firstname','lastname',
        'fk_application','amount','fk_payment_form','domicile'];
    protected $dates = ["deleted_at"];
}
