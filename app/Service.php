<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{

    use SoftDeletes;

    protected $table = "Services";
    protected $fillable =[
        'fk_insurance','fk_nuc','fk_service_type','type','delivered','fk_status','pay_date'];
    protected $dates = ["deleted_at"];
}
