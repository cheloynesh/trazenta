<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documents extends Model
{
    use SoftDeletes;

    protected $table = "Documents";
    protected $fillable =["name","fk_key","fk_section"];
    protected $dates = ["deleted_at"];
}
