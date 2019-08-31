<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $fillable = [
        'title','description','user_id'
    ];

    public $timestamps = false;

    public function labels()
    {
        return $this->belongsToMany('\App\Model\Labels');
    }

    public function users()
    {
        return $this->belongsToMany('\App\Model\Users','users_notes');
    }
}
