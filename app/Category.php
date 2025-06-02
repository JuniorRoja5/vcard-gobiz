<?php

namespace App;

use App\StoreProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasMany(StoreProduct::class, 'category_id', 'category_id');
    }
}
