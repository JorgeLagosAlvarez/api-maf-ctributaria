<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SituacionTributaria extends Model
{
    protected $hidden = [
        'status_id', 'created_at', 'updated_at', 
    ];
    
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
