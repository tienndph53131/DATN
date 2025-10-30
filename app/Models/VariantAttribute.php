<?php

namespace App\Models;

<<<<<<< HEAD
=======
use App\Models\AttributeValue;



>>>>>>> origin/tien
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['variant_id', 'attribute_value_id'];

<<<<<<< HEAD
    // Quan hệ với ProductVariant 
=======
    // Quan hệ với ProductVariant
>>>>>>> origin/tien
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Quan hệ với AttributeValue
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}

