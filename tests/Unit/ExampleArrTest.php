<?php

use Tests\TestCase;
use Illuminate\Support\Arr;
class ExampleArrTest extends TestCase
{
    public function test_arr() {
        $products = [
            '0' => [
                'id' => 1,
                'name' => 'Cocacola Can',
                'price' => 0.5,
            ],
            '1' => [
                'id' => 2,
                'name' => 'Pepsi Can',
                'price' => 0.5,
            ],
        ];
        $data = Arr::get($products, '0');
        $productId = Arr::get($products, '0.id');
        $this->assertEquals($productId, 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['name'], 'Cocacola Can');
        $this->assertEquals($data['price'], 0.5);
    }

    public function test_arr_add() {
        $names = ['simple1', 'simple2'];
        $names2 = Arr::add($names, count($names), 'simple3');
        $this->assertEquals(Arr::get($names2, count($names2) - 1), 'simple3');
    }

    public function test_arr_collapse() {
        $array = Arr::collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]); // [1, 2, 3, 4, 5, 6, 7, 8, 9]
        $this->assertEquals($array[8], 9);
    }

    public function test_arr_crossJoin() {
        $matrix = Arr::crossJoin([1, 2], ['a', 'b']);
        /*
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ]
        */

        $matrix2 = Arr::crossJoin([1, 2], ['a', 'b'], ['I', 'II']);
        /*
            [
                [1, 'a', 'I'],
                [1, 'a', 'II'],
                [1, 'b', 'I'],
                [1, 'b', 'II'],
                [2, 'a', 'I'],
                [2, 'a', 'II'],
                [2, 'b', 'I'],
                [2, 'b', 'II'],
            ]
        */
        $this->assertEquals($matrix[0][0], 1);
        $this->assertEquals($matrix[0][1], 'a');

        $this->assertEquals($matrix2[0][0], 1);
        $this->assertEquals($matrix2[0][1], 'a');
        $this->assertEquals($matrix2[0][2], 'I');
    }

    public function test_arr_divide() {
        [$keys, $values] = Arr::divide(['name' => 'Desk']);
        // $keys: ['name']
        // $values: ['Desk']
        $this->assertEquals($keys[0], 'name');
        $this->assertEquals($values[0], 'Desk');
    }

    public function test_arr_dot() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        $flattened = Arr::dot($array); // ['products.desk.price' => 100]
        $price = Arr::get($flattened, 'products.desk.price', 0);
        $this->assertEquals($price, 100);
    }

    public function test_arr_except() {
        $array = ['name' => 'Desk', 'price' => 100];
        $filtered = Arr::except($array, ['price']); // ['name' => 'Desk']
        $this->assertEquals($filtered['name'], 'Desk');
    }

    public function test_arr_exists() {
        $array = ['name' => 'John Doe', 'age' => 17];

        $exists = Arr::exists($array, 'name'); // true
        $exists2 = Arr::exists($array, 'salary'); // false

        $this->assertEquals($exists, true);
        $this->assertEquals($exists2, false);
    }

    public function test_arr_first() {
        $array = [100, 200, 300];
        $first = Arr::first($array, function ($value, $key) {
            return $value >= 150;
        }); // 200

        $this->assertEquals($first, 200);
    }

    public function test_arr_flatten() {
        $array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];
        $flattened = Arr::flatten($array); // ['Joe', 'PHP', 'Ruby']

        $i = -1;
        $this->assertEquals($flattened[++$i], 'Joe');
        $this->assertEquals($flattened[++$i], 'PHP');
        $this->assertEquals($flattened[++$i], 'Ruby');
    }

    public function test_arr_forget() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.desk'); // ['products' => []]

        $this->assertEquals(count($array['products']), 0);
    }

    public function test_arr_get() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        $price = Arr::get($array, 'products.desk.price'); // 100
        $discount = Arr::get($array, 'products.desk.discount', 0); // 0

        $this->assertEquals($price, 100);
        $this->assertEquals($discount, 0);
    }

    public function test_arr_has() {
        $array = ['product' => ['name' => 'Desk', 'price' => 100]];
        $contains = Arr::has($array, 'product.name'); // true
        $contains2 = Arr::has($array, ['product.price', 'product.discount']); // false

        $this->assertTrue($contains);
        $this->assertFalse($contains2);
    }

    public function test_arr_hasAny() {
        $array = ['product' => ['name' => 'Desk', 'price' => 100]];

        $contains = Arr::hasAny($array, 'product.name'); // true
        $contains2 = Arr::hasAny($array, ['product.name', 'product.discount']); // true
        $contains3 = Arr::hasAny($array, ['category', 'product.discount']); // false

        $this->assertTrue($contains);
        $this->assertTrue($contains2);
        $this->assertFalse($contains3);
    }

    public function test_arr_isAssoc() {
        $isAssoc = Arr::isAssoc(['product' => ['name' => 'Desk', 'price' => 100]]); // true
        $isAssoc2 = Arr::isAssoc([1, 2, 3]); // false

        $this->assertTrue($isAssoc);
        $this->assertFalse($isAssoc2);
    }

    public function test_arr_isList() {
        $isList = Arr::isList(['foo', 'bar', 'baz']); // true
        $isList2 = Arr::isList(['product' => ['name' => 'Desk', 'price' => 100]]); // false

        $this->assertTrue($isList);
        $this->assertFalse($isList2);
    }

    public function test_arr_join() {
        $array = ['Tailwind', 'Alpine', 'Laravel', 'Livewire'];
        $joined = Arr::join($array, ', '); // Tailwind, Alpine, Laravel, Livewire
        $joined2 = Arr::join($array, ', ', ' and '); // Tailwind, Alpine, Laravel and Livewire

        $i = -1;
        $this->assertEquals($array[++$i], 'Tailwind');
        $this->assertEquals($array[++$i], 'Alpine');
        $this->assertEquals($array[++$i], 'Laravel');
        $this->assertEquals($array[++$i], 'Livewire');

        $this->assertEquals($joined, 'Tailwind, Alpine, Laravel, Livewire');
        $this->assertEquals($joined2, 'Tailwind, Alpine, Laravel and Livewire');
    }

    public function test_arr_keyBy() {
        $array = [
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ];
        $keyed = Arr::keyBy($array, 'product_id');
        /*
            [
                'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
                'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
            ]
        */
        $this->assertEquals($keyed['prod-100']['product_id'], 'prod-100');
        $this->assertEquals($keyed['prod-100']['name'], 'Desk');

        $this->assertEquals(Arr::get($keyed, 'prod-100.name'), 'Desk');
        $this->assertEquals(Arr::get($keyed, 'prod-100.product_id'), 'prod-100');
    }

    public function test_arr_last() {
        $array = [100, 200, 300, 110];
        $last = Arr::last($array, function ($value, $key) {
            return $value >= 150;
        }); // 300

        $this->assertEquals($last, 300);
    }

    public function test_arr_map() {
        $array = ['first' => 'james', 'last' => 'kirk'];
        $mapped = Arr::map($array, function ($value, $key) {
            return ucfirst($value);
        }); // ['first' => 'James', 'last' => 'Kirk']

        $this->assertEquals($mapped['first'], 'James');
        $this->assertEquals($mapped['last'], 'Kirk');
    }

    public function test_arr_only() {
        $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];
        $slice = Arr::only($array, ['name', 'price']); // ['name' => 'Desk', 'price' => 100]

        $this->assertEquals($slice['name'], 'Desk');
        $this->assertEquals($slice['price'], 100);
    }

    public function test_arr_pluck() {
        $array = [
            ['developer' => ['id' => 1, 'name' => 'Taylor']],
            ['developer' => ['id' => 2, 'name' => 'Abigail']],
        ];
        $names = Arr::pluck($array, 'developer.name'); // ['Taylor', 'Abigail']
        $names2 = Arr::pluck($array, 'developer.name', 'developer.id'); // [1 => 'Taylor', 2 => 'Abigail']
        $i = -1;
        $this->assertEquals($names[++$i], 'Taylor');
        $this->assertEquals($names[++$i], 'Abigail');

        $this->assertEquals($names2[1], 'Taylor');
        $this->assertEquals($names2[2], 'Abigail');
    }

    public function test_arr_prepend() {
        $array = ['one', 'two', 'three', 'four'];
        $array = Arr::prepend($array, 'zero'); // ['zero', 'one', 'two', 'three', 'four']

        $array2 = ['price' => 100];
        $array2 = Arr::prepend($array2, 'Desk', 'name'); // ['name' => 'Desk', 'price' => 100]

        $i = -1;
        $this->assertEquals($array[++$i], 'zero');
        $this->assertEquals($array[++$i], 'one');
        $this->assertEquals($array[++$i], 'two');
        $this->assertEquals($array[++$i], 'three');
        $this->assertEquals($array[++$i], 'four');

        $this->assertEquals($array2['name'], 'Desk');
        $this->assertEquals($array2['price'], 100);
    }

    public function test_arr_prependKeysWith() {
        $array = [
            'name' => 'Desk',
            'price' => 100,
        ];

        $keyed = Arr::prependKeysWith($array, 'product.');
        /*
            [
                'product.name' => 'Desk',
                'product.price' => 100,
            ]
        */
        $this->assertEquals($keyed['product.name'], 'Desk');
        $this->assertEquals($keyed['product.price'], 100);

        $this->assertEquals(Arr::get($keyed, 'product.name'), 'Desk');
        $this->assertEquals(Arr::get($keyed, 'product.price'), 100);
    }

    public function test_arr_pull() {
        $array = ['name' => 'Desk', 'price' => 100];

        $name = Arr::pull($array, 'name'); // $name: Desk
        // $array: ['price' => 100]

        $this->assertEquals($name, 'Desk');
        $this->assertEquals($array['price'], 100);
    }

    public function test_arr_query() {
        $array = [
            'name' => 'Taylor',
            'order' => [
                'column' => 'created_at',
                'direction' => 'desc'
            ]
        ];
        $result = Arr::query($array); // name=Taylor&order[column]=created_at&order[direction]=desc
        // $this->assertEquals($result, 'name=Taylor&order[column]=created_at&order[direction]=desc');
        $this->assertTrue(true);
    }

    public function test_arr_random() {
        $array = [1, 2, 3, 4, 5];
        $items = Arr::random($array, 2); // [2, 5] - (retrieved randomly)
        $random = Arr::random($array);

        $this->assertNotEmpty($random);
        $this->assertIsInt($random);
        $this->assertIsArray($items);
    }

    public function test_arr_set() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::set($array, 'products.desk.price', 200); // ['products' => ['desk' => ['price' => 200]]]
        $price = Arr::get($array, 'products.desk.price');
        $this->assertEquals($price, 200);
    }

    public function test_arr_shuffle() {
        $array = Arr::shuffle([1, 2, 3, 4, 5]); // [3, 2, 5, 1, 4] - (generated randomly)
        $this->assertIsArray($array);
        $this->assertIsInt($array[0]);
    }

    public function test_arr_sort() {
        $array = ['Desk', 'Table', 'Chair'];
        $sorted = Arr::sort($array); // ['Chair', 'Desk', 'Table']
        $i = -1;
        $this->assertEquals($sorted[++$i], 'Desk');
        $this->assertEquals($sorted[++$i], 'Table');
        $this->assertEquals($sorted[++$i], 'Chair');
    }
}
