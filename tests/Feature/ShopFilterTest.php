<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;

class ShopFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_filter_min_max_returns_expected_products()
    {
        // create two products with variants
        $p1 = Product::factory()->create(['name' => 'Cheap Product', 'status' => 1]);
        $p2 = Product::factory()->create(['name' => 'Expensive Product', 'status' => 1]);

        ProductVariant::factory()->create(['product_id' => $p1->id, 'price' => 100]);
        ProductVariant::factory()->create(['product_id' => $p2->id, 'price' => 1000]);

        $res = $this->get('/shop?min_price=50&max_price=200');
        $res->assertStatus(200);
        $res->assertSee('Cheap Product');
        $res->assertDontSee('Expensive Product');
    }
}
