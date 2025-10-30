<?php

namespace App\Models;

<<<<<<< HEAD
=======
use App\Models\AttributeValue;
use App\Models\VariantAttribute;


>>>>>>> origin/tien
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
