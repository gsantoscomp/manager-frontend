<?php

namespace Gsantoscomp\SharedVetDb\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'user_id',
        'animal_id',
        'initial_date',
        'final_date',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo('Gsantoscomp\SharedVetDb\Models\User', 'user_id');
    }

    public function animal()
    {
        return $this->belongsTo('Gsantoscomp\SharedVetDb\Models\Animal', 'animal_id');
    }
}
