<?php

namespace App\Models;

use App\Models\AttributeValue;
use App\Models\VariantAttribute;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Quan hệ với attribute_values
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
