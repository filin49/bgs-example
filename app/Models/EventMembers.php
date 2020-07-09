<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMembers extends Model
{
    protected $fillable = [
        'name',
        'surname',
        'email',
        'event_id',
    ];

    protected $hidden = ['updated_at', 'created_at'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

