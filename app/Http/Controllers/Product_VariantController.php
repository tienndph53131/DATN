<?php

namespace App\Http\Controllers;

use App\Models\Product;
class Product_VariantController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['images','variants.attributes.attributeValue.attribute'])->findOrFail($id);

        $attributes = [];
        foreach ($product->variants as $variant){
            foreach ($variant->attributes as $variantAttribute) {
                $attribute_name = $variantAttribute->attributeValue->attribute->name;
                $attribute_value = $variantAttribute->attributeValue->value;
                $attributes[$attribute_name][] = $attribute_value;
            }
        }
        foreach ($attributes as $key => $values){
            $attributes[$key] = array_unique($values);
        }
        return view('layouts.admin.detail', compact('product', 'attributes'));
    }

}