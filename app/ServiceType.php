<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use SoftDeletes;

    protected $table = "Service_Type";
    protected $fillable =["name"];
    protected $dates = ["deleted_at"];
}
