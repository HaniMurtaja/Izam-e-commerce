<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];


    public function scopeSearch($query, $name = null, $minPrice = null, $maxPrice = null)
{
    if ($name) {

        $query->where('name', 'like', '%' . $name . '%');
    }

    if ($minPrice) {

        $query->where('price', '>=', $minPrice);
    }

    if ($maxPrice) {

        $query->where('price', '<=', $maxPrice);
    }
    
    return $query;
}

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }
}
