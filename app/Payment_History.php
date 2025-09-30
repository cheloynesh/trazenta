<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment_History extends Model
{
    use SoftDeletes;

    protected $table = "Payment_History";
    protected $fillable =[
        'fk_agent','pay_date','invoice_date','pay_doc','invoice_doc','rec_amount'];
    protected $dates = ["deleted_at"];
}
