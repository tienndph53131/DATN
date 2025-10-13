<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['variant_id', 'attribute_value_id'];

    // Quan hệ với ProductVariant 
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

