<?php

namespace Gsantoscomp\SharedVetDb\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = 'animals';

    protected $fillable = [
        'name',
        'animal_type',
        'client_id'
    ];

    public function client()
    {
        return $this->belongsTo('Gsantoscomp\SharedVetDb\Models\Client', 'client_id');
    }
}
