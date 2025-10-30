<?php

namespace App\Models;
<<<<<<< HEAD
=======
use App\Models\Attribute;

use App\Models\VariantAttribute;

>>>>>>> origin/tien

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value'
    ];

<<<<<<< HEAD
    // Quan hệ với attribute 
=======
    // Quan hệ với attribute
>>>>>>> origin/tien
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

<<<<<<< HEAD
    // Quan hệ với variant_attributes 
=======
    // Quan hệ với variant_attributes
>>>>>>> origin/tien
    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class, 'attribute_value_id');
    }
<<<<<<< HEAD
=======
    public function variants()
{
    return $this->belongsToMany(
        \App\Models\ProductVariant::class,
        'variant_attributes',
        'attribute_value_id',
        'variant_id'
    );
}

>>>>>>> origin/tien
}
