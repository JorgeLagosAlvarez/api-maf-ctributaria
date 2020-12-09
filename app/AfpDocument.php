<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AfpDocument extends Model
{
    protected $hidden = [
        'status_id', 'created_at', 'updated_at', 
    ];
    
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function afp()
    {
        return $this->belongsTo(Afp::class);
    }
}
