<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'status'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }
}