<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function testProductList()
    {
        factory(Product::class, 5)->create();

        $response = $this->getJson(route('products.index'));

        $response->assertStatus(200)->assertJsonCount(5);
    }

    public function testShowProduct()
    {
        $product = factory(Product::class)->create();

        $response = $this->getJson(route('products.show', ['product' => $product->id]));

        $response->assertStatus(200)->assertJson([
            'name' => $product->name,
            'id' => $product->id,
            'price' => $product->price
        ]);
    }

    public function testCreateProduct()
    {
        $name = 'Foo bar';
        $description = 'Lorem ipsum dolor sit amet';
        $price = 69.90;

        $response = $this->postJson(route('products.store'), [
            'name' => $name,
            'description' => $description,
            'price' => $price
        ]);

        $response->assertStatus(201)->assertJson([
            'created' => true,
            'name' => 'Foo bar'
        ]);
    }

    public function testUpdateProduct()
    {
        $product = factory(Product::class)->create();
        $oldName = $product->name;
        $newName = 'Foo bar';

        $response = $this->patchJson(route('products.update', ['product' => $product->id]), [
            'name' => $newName
        ]);

        $response->assertStatus(200)->assertJson([
            'name' => $newName
        ])->assertJsonMissing([
            'name' => $oldName
        ]);
    }

    public function testDeleteProduct()
    {
        $product = factory(Product::class)->create();
        $productId = $product->id;

        $this->getJson(route('products.show', ['product' => $productId]))->assertStatus(200)->assertJson([
            'name' => $product->name
        ]);

        $response = $this->deleteJson(route('products.destroy', ['product' => $productId]));

        $response->assertStatus(200)->assertJson([
            'deleted' => true
        ]);

        $this->getJson(route('products.show', ['product' => $productId]))->assertStatus(404);
    }
}
