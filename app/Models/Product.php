<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'category_id',
        'name',
        'price',
        'sale_price',
        'image',
        'quantity',
        'view',
        'date',
        'description',
        'status'
=======
        'category_id', 'name', 'image',
         'view', 'date', 'description', 'status'
>>>>>>> origin/tien
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
<<<<<<< HEAD
=======

>>>>>>> origin/tien
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
<<<<<<< HEAD
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
=======
    public function images()
{
    return $this->hasMany(ProductImage::class);
}

>>>>>>> origin/tien
}
