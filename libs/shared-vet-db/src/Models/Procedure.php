<?php

namespace Gsantoscomp\SharedVetDb\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $table = 'procedures';

    protected $fillable = [
        'name',
        'price',
        'description'
    ];
}
