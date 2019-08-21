<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $fillable = [
        'title','description'
    ];

    public function labels()
    {
        return $this->belongsToMany('\App\Labels');
    }
}
