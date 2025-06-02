<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    protected $fillable = [
        'category_id',
        'badge',
        'product_image',
        'product_name',
        'product_subtitle',
        'regular_price',
        'sales_price',
        'product_status'
    ];

    public function product_category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
 