<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Labels extends Model
{
    protected $fillable = [
        'name','user_id'
    ];

    public $timestamps = false;

    public function notes()
    {
        return $this->belongsToMany('\App\Notes');
    }
    
}
