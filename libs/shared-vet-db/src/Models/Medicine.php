<?php

namespace Gsantoscomp\SharedVetDb\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $table = 'medicines';

    protected $fillable = [
        'name',
        'quantity',
        'purchase_price',
        'sale_price',
        'description'
    ];
}
