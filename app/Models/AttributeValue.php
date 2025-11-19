<?php

namespace App\Models;

use App\Models\Attribute;

use App\Models\VariantAttribute;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value'
    ];


    // Quan hệ với attribute

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }


    // Quan hệ với variant_attributes

    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class, 'attribute_value_id');
    }

    public function productVariants()
{
    return $this->belongsToMany(
        \App\Models\ProductVariant::class,
        'variant_attributes',
        'attribute_value_id',
        'variant_id'
    );
}


}
