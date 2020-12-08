<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public $fillable = ['module_code', 'module_name', 'module_term'];
}
