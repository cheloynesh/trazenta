<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opening extends Model
{
    use SoftDeletes;

    protected $table = "Opening";
    protected $fillable =[
        'fk_agent','fk_currency','fk_status','pick_status','limit_status','agent_status','office_status','finestra_status','fk_insurance','contract',
        'nuc','name','firstname','lastname','fk_application','amount','fk_payment_form','domicile'];
    protected $dates = ["deleted_at"];
}
