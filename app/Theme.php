<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    public function businessCards()
    {
        return $this->hasMany(BusinessCard::class, 'theme_id', 'theme_id');
    }
}
