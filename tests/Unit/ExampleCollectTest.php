<?php

use Tests\TestCase;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Collection;

class ExampleCollectTest extends TestCase
{
    public function test_collection_list() {
        $result = collect([1, 2, 3])->all();
        $this->assertEquals($result[0], 1);
    }

    public function test_collection_avg() {
        $result1 = collect([
            ['foo' => 10],
            ['foo' => 10],
            ['foo' => 20],
            ['foo' => 40]
        ])->avg('foo');
        $this->assertEquals($result1, 20);

        $result2 = collect([1, 1, 2, 4])->avg();
        $this->assertEquals($result2, 2);
    }

    public function test_collection_chunk() {
        $collection = collect([1, 2, 3, 4, 5, 6, 7]);
        $chunks = $collection->chunk(4);

        $result = $chunks->all();
        $this->assertEquals($result[0][1], 2);
    }

    public function test_collection_chunkWhile() {
        $collection = collect(str_split('AABBCCCD'));

        $chunks = $collection->chunkWhile(function ($value, $key, $chunk) {
            return $value === $chunk->last();
        });

        $result = $chunks->all(); // // [['A', 'A'], ['B', 'B'], ['C', 'C', 'C'], ['D']]
        $this->assertEquals($result[0][0], "A");
        $this->assertEquals($result[0][1], "A");
        $this->assertEquals($result[1][2], "B");
        $this->assertEquals($result[1][3], "B");
    }

    public function test_collection_collapse() {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);
        $collapsed = $collection->collapse();

        $result = $collapsed->all(); // [1, 2, 3, 4, 5, 6, 7, 8, 9]
        $this->assertEquals($result[0], 1);
    }

    public function test_collection_copy() {
        $collectionA = collect([1, 2, 3]);
        $collectionB = $collectionA->collect();

        $result = $collectionB->all(); // [1, 2, 3]
        $this->assertEquals($result[0], 1);
    }

    public function test_collection_lazy() {
        $lazyCollection = LazyCollection::make(function () {
            yield 1;
            yield 2;
            yield 3;
        });
        $collection = $lazyCollection->collect();

        $result = $collection->all(); // [1, 2, 3]
        $this->assertEquals($result[0], 1);
    }

    public function test_collection_combine() {
        $collection = collect(['name', 'age']);
        $combined = $collection->combine(['George', 29]);

        $result = $combined->all(); // ['name' => 'George', 'age' => 29]
        $this->assertEquals($result['name'], "George");
        $this->assertEquals($result['age'], 29);
    }

    public function test_collection_concat() {
        $collection = collect(['John Doe']);
        $concatenated = $collection->concat(['Jane Doe'])->concat(['name' => 'Johnny Doe']);

        $result = $concatenated->all(); // ['John Doe', 'Jane Doe', 'Johnny Doe']
        $this->assertEquals($result[0], "John Doe");
    }

    public function test_collection_contains() {
        $collection = collect([1, 2, 3, 4, 5]);

        $result1 = $collection->contains(function ($value, $key) {
            return $value > 5;
        }); // false

        $collection = collect(['name' => 'Desk', 'price' => 100]);

        $result2 = $collection->contains('Desk'); // true
        $result3 = $collection->contains('New York'); // false

        $collection = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ]);
        $result4 = $collection->contains('product', '=', 'Bookcase'); // false
        $result5 = $collection->contains('product', '=', 'Desk'); // true

        $this->assertEquals($result1, false);
        $this->assertEquals($result2, true);
        $this->assertEquals($result3, false);
        $this->assertEquals($result4, false);
        $this->assertEquals($result5, true);

    }

    public function test_collection_containsOneItem() {
        $result1 = collect([])->containsOneItem(); // false
        $result2 = collect(['1'])->containsOneItem(); // true
        $result3 = collect(['1', '2'])->containsOneItem(); // false

        $this->assertEquals($result1, false);
        $this->assertEquals($result2, true);
        $this->assertEquals($result3, false);
    }

    public function test_collection_count() {
        $collection = collect([1, 2, 3, 4]);

        $result = $collection->count(); // 4
        $this->assertEquals($result, 4);
    }

    public function test_collection_countBy() {
        $collection = collect([1, 2, 2, 2, 3]);

        $counted = $collection->countBy();
        $result = $counted->all(); // [1 => 1, 2 => 3, 3 => 1]

        $collection = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);
        $counted = $collection->countBy(function ($email) {
            return substr(strrchr($email, "@"), 1);
        });
        $result2 = $counted->all(); // ['gmail.com' => 2, 'yahoo.com' => 1]


        $this->assertEquals($result['1'], 1);
        $this->assertEquals($result['2'], 3);
        $this->assertEquals($result['3'], 1);
        $this->assertEquals($result2['gmail.com'], 2);
        $this->assertEquals($result2['yahoo.com'], 1);
    }

    public function test_collection_crossJoin() {
        $collection = collect([1, 2]);

        $matrix = $collection->crossJoin(['a', 'b']);

        $result = $matrix->all();
        /*
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ]
        */
        $this->assertEquals($result[0][0], 1);
        $this->assertEquals($result[0][1], "a");
        $this->assertEquals($result[1][0], 1);
        $this->assertEquals($result[1][1], "b");
    }

    /*
     * The dd method dumps the collection's items and ends execution of the script:
     * */
    public function test_collection_dd() {
        $collection = collect(['John Doe', 'Jane Doe']);
        // $collection->dd(); // ends execution of the script
        // $collection->dump(); // stop executing the script
        /*
            Collection {
                #items: array:2 [
                    0 => "John Doe"
                    1 => "Jane Doe"
                ]
            }
        */
        $this->assertTrue(true);
    }

    public function test_collection_diff() {
        $collection = collect([1, 2, 3, 4, 5]);
        $diff = $collection->diff([2, 4, 6, 8]);
        $result = $diff->all(); // [1, 3, 5]

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[2], 3);
        $this->assertEquals($result[4], 5);
    }

    public function test_collection_diffAssoc() {
        $collection = collect([
            'color' => 'orange',
            'type' => 'fruit',
            'remain' => 6,
        ]);

        $diff = $collection->diffAssoc([
            'color' => 'yellow',
            'type' => 'fruit',
            'remain' => 3,
            'used' => 6,
        ]);

        $result = $diff->all(); // ['color' => 'orange', 'remain' => 6]
        $this->assertEquals($result['color'], "orange");
        $this->assertEquals($result['remain'], 6);
    }

    public function test_collection_diffKeys() {
        $collection = collect([
            'one' => 10,
            'two' => 20,
            'three' => 30,
            'four' => 40,
            'five' => 50,
        ]);

        $diff = $collection->diffKeys([
            'two' => 2,
            'four' => 4,
            'six' => 6,
            'eight' => 8,
        ]);

        $result = $diff->all(); // ['one' => 10, 'three' => 30, 'five' => 50]
        $this->assertEquals($result['one'], 10);
        $this->assertEquals($result['three'], 30);
        $this->assertEquals($result['five'], 50);
    }

    public function test_collection_doesntContain() {
        $collection = collect([1, 2, 3, 4, 5]);

        $result = $collection->doesntContain(function ($value, $key) {
            return $value < 5;
        }); // false

        $collection = collect(['name' => 'Desk', 'price' => 100]);

        $result2 = $collection->doesntContain('Table'); // true
        $result3 = $collection->doesntContain('Desk'); // false

        $this->assertEquals($result, false);
        $this->assertEquals($result2, true);
        $this->assertEquals($result3, false);
    }

    public function test_collection_duplicates() {
        $collection = collect(['a', 'b', 'a', 'c', 'b']);
        $result = $collection->duplicates()->all(); // [2 => 'a', 4 => 'b']

        $employees = collect([
            ['email' => 'abigail@example.com', 'position' => 'Developer'],
            ['email' => 'james@example.com', 'position' => 'Designer'],
            ['email' => 'victoria@example.com', 'position' => 'Developer'],
        ]);
        $result2 = $employees->duplicates('position')->all(); // [2 => 'Developer']

        $this->assertEquals($result[2], 'a');
        $this->assertEquals($result[4], 'b');

        $this->assertEquals($result2[2], 'Developer');
    }

    public function test_collection_every() {
        $result = collect([1, 2, 3, 4])->every(function ($value, $key) {
            return $value > 2;
        }); // false

        $result2 = collect([])->every(function ($value, $key) {
            return $value > 2;
        });// true

        $this->assertEquals($result, false);
        $this->assertEquals($result2, true);
    }

    public function test_collection_except() {
        $collection = collect(['product_id' => 1, 'price' => 100, 'discount' => false]);
        $filtered = $collection->except(['price', 'discount']);

        $result = $filtered->all(); // ['product_id' => 1]

        $this->assertEquals($result['product_id'], 1);
    }

    public function test_collection_filter() {
        $collection = collect([1, 2, 3, 4]);

        $result = $collection->filter(function ($value, $key) {
            return $value > 2;
        })->all(); // [3, 4]
        $result2 = $collection->reject(function ($value, $key) {
            return $value < 3;
        })->all(); // [3, 4]

        $result3 = collect([1, 2, 3, null, false, '', 0, []])->filter()->all(); // [1, 2, 3]

        $this->assertEquals($result[2], 3);
        $this->assertEquals($result[3], 4);

        $this->assertEquals($result2[2], 3);
        $this->assertEquals($result2[3], 4);

        $this->assertEquals($result3[0], 1);
        $this->assertEquals($result3[1], 2);
        $this->assertEquals($result3[2], 3);

    }

    public function test_collection_firstWhere() {
        $collection = collect([
            ['name' => 'Regena', 'age' => null],
            ['name' => 'Linda', 'age' => 14],
            ['name' => 'Diego', 'age' => 23],
            ['name' => 'Linda', 'age' => 84],
        ]);

        $result = $collection->firstWhere('name', 'Linda'); // ['name' => 'Linda', 'age' => 14]
        $result2 = $collection->firstWhere('age', '>=', 18); // ['name' => 'Diego', 'age' => 23]
        $result3 = $collection->firstWhere('age'); // ['name' => 'Linda', 'age' => 14]

        $this->assertEquals($result['name'], "Linda");
        $this->assertEquals($result['age'], 14);

        $this->assertEquals($result2['name'], "Diego");
        $this->assertEquals($result2['age'], 23);

        $this->assertEquals($result3['name'], "Linda");
        $this->assertEquals($result3['age'], 14);
    }

    public function test_collection_flatMap() {
        $collection = collect([
            ['name' => 'Sally'],
            ['school' => 'Arkansas'],
            ['age' => 28]
        ]);

        $result = $collection->flatMap(function ($values) {
            return array_map('strtoupper', $values);
        })->all(); // ['name' => 'SALLY', 'school' => 'ARKANSAS', 'age' => '28'];

        $this->assertEquals($result['name'], "SALLY");
        $this->assertEquals($result['school'], "ARKANSAS");
        $this->assertEquals($result['age'], "28");
    }

    public function test_collection_flatten() {
        $collection = collect([
            'name' => 'taylor',
            'languages' => [
                'php', 'javascript'
            ]
        ]);
        $result = $collection->flatten()->all(); // ['taylor', 'php', 'javascript'];

        $collection2 = collect([
            'Apple' => [
                [
                    'name' => 'iPhone 6S',
                    'brand' => 'Apple'
                ],
            ],
            'Samsung' => [
                [
                    'name' => 'Galaxy S7',
                    'brand' => 'Samsung'
                ],
            ],
        ]);

        $products = $collection2->flatten(1);
        $result2 = $products->values()->all();
        /*
            [
                ['name' => 'iPhone 6S', 'brand' => 'Apple'],
                ['name' => 'Galaxy S7', 'brand' => 'Samsung'],
            ]
        */

        $this->assertEquals($result[0], "taylor");
        $this->assertEquals($result[1], "php");
        $this->assertEquals($result[2], "javascript");

        $this->assertEquals($result2[0]['name'], "iPhone 6S");
        $this->assertEquals($result2[0]['brand'], "Apple");
        $this->assertEquals($result2[1]['name'], "Galaxy S7");
        $this->assertEquals($result2[1]['brand'], "Samsung");
    }

    public function test_collection_flip() {
        $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);

        $result = $collection->flip()->all(); // ['taylor' => 'name', 'laravel' => 'framework']
        $this->assertEquals($result['taylor'], "name");
        $this->assertEquals($result['laravel'], "framework");
    }

    public function test_collection_forget() {
        $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
        $result = $collection->forget('name')->all(); // ['framework' => 'laravel']

        $this->assertEquals($result['framework'], "laravel");
    }

    public function test_collection_forPage() {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->forPage(2, 3)->all(); // [4, 5, 6]

        $this->assertEquals($result[3], 4);
        $this->assertEquals($result[4], 5);
        $this->assertEquals($result[5], 6);
    }

    public function test_collection_get() {
        $result = collect(['name' => 'taylor', 'framework' => 'laravel'])->get('name'); // taylor
        // get with default value
        $result2 = collect(['name' => 'taylor', 'framework' => 'laravel'])->get('age', 34); // 34
        $result3 = collect(['name' => 'taylor', 'framework' => 'laravel'])->get('email', function () {
            return 'taylor@example.com';
        }); // taylor@example.com

        $this->assertEquals($result, "taylor");
        $this->assertEquals($result2, 34);
        $this->assertEquals($result3, "taylor@example.com");
    }

    public function test_collection_groupBy() {
        $collection = collect([
            ['account_id' => 'account-x10', 'product' => 'Chair'],
            ['account_id' => 'account-x10', 'product' => 'Bookcase'],
            ['account_id' => 'account-x11', 'product' => 'Desk'],
        ]);

        $result = $collection->groupBy('account_id')->all();
        /*
            [
                'account-x10' => [
                    ['account_id' => 'account-x10', 'product' => 'Chair'],
                    ['account_id' => 'account-x10', 'product' => 'Bookcase'],
                ],
                'account-x11' => [
                    ['account_id' => 'account-x11', 'product' => 'Desk'],
                ],
            ]
        */

        $result2 = $collection->groupBy(function ($item, $key) {
            return substr($item['account_id'], -3);
        })->all();
        /*
            [
                'x10' => [
                    ['account_id' => 'account-x10', 'product' => 'Chair'],
                    ['account_id' => 'account-x10', 'product' => 'Bookcase'],
                ],
                'x11' => [
                    ['account_id' => 'account-x11', 'product' => 'Desk'],
                ],
            ]
        */


        $data = new Collection([
            10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
            20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
            30 => ['user' => 3, 'skill' => 2, 'roles' => ['Role_1']],
            40 => ['user' => 4, 'skill' => 2, 'roles' => ['Role_2']],
        ]);

        $result3 = $data->groupBy(['skill', function ($item) {
            return $item['roles'];
        }], preserveKeys: true);
        /*
        [
            1 => [
                'Role_1' => [
                    10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
                    20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
                ],
                'Role_2' => [
                    20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
                ],
                'Role_3' => [
                    10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
                ],
            ],
            2 => [
                'Role_1' => [
                    30 => ['user' => 3, 'skill' => 2, 'roles' => ['Role_1']],
                ],
                'Role_2' => [
                    40 => ['user' => 4, 'skill' => 2, 'roles' => ['Role_2']],
                ],
            ],
        ];
        */

        $this->assertEquals($result['account-x10']->count(), 2);
        $this->assertEquals($result['account-x11']->count(), 1);

        $this->assertEquals($result2['x10']->count(), 2);
        $this->assertEquals($result2['x11']->count(), 1);

        $this->assertEquals($result3[1]->count(), 3);
        $this->assertEquals($result3[2]->count(), 2);
    }

    public function test_collection_has() {
        $collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

        $result = $collection->has('product'); // true
        $result2 = $collection->has(['product', 'amount']); // true
        $result3 = $collection->has(['amount', 'price']); // false

        $this->assertEquals($result, true);
        $this->assertEquals($result2, true);
        $this->assertEquals($result3, false);
    }

    public function test_collection_hasAny() {
        $collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

        $result = $collection->hasAny(['product', 'price']); // true
        $result2 = $collection->hasAny(['name', 'price']); // false

        $this->assertEquals($result, true);
        $this->assertEquals($result2, false);
    }

    public function test_collection_implode() {
        $collection = collect([
            ['account_id' => 1, 'product' => 'Desk'],
            ['account_id' => 2, 'product' => 'Chair'],
        ]);
        $result = $collection->implode('product', ', '); // Desk, Chair
        $result2 = $collection->implode(function ($item, $key) {
            return strtoupper($item['product']);
        }, ', '); // DESK, CHAIR
        $result3 = collect([1, 2, 3, 4, 5])->implode('-'); // '1-2-3-4-5'

        $this->assertEquals($result, "Desk, Chair");
        $this->assertEquals($result2, "DESK, CHAIR");
        $this->assertEquals($result3, "1-2-3-4-5");
    }

    public function test_collection_intersect() {
        $collection = collect(['Desk', 'Sofa', 'Chair']);

        $result = $collection->intersect(['Desk', 'Chair', 'Bookcase'])->all(); // [0 => 'Desk', 2 => 'Chair']
        $result2 = $collection->intersect(['Desk', 'Chair', 'Bookcase'])->flip()->all(); // ['Desk' => 0', 'Chair' => 2]


        $this->assertEquals($result[0], "Desk");
        $this->assertEquals($result[2], "Chair");

        $this->assertEquals($result2['Desk'], 0);
        $this->assertEquals($result2['Chair'], 2);
    }

    public function test_collection_intersectByKeys() {
        $collection = collect([
            'serial' => 'UX301', 'type' => 'screen', 'year' => 2009,
        ]);
        $result = $collection->intersectByKeys([
            'reference' => 'UX404', 'type' => 'tab', 'year' => 2011,
        ])->all(); // ['type' => 'screen', 'year' => 2009]

        $this->assertEquals($result['type'], "screen");
        $this->assertEquals($result['year'], 2009);
    }

    public function test_collection_join() {
        $result = collect(['a', 'b', 'c'])->join(', '); // 'a, b, c'
        $result2 = collect(['a', 'b', 'c'])->join(', ', ', and '); // 'a, b, and c'
        $result3 = collect(['a', 'b'])->join(', ', ' and '); // 'a and b'
        $result4 = collect(['a'])->join(', ', ' and '); // 'a'
        $result5 = collect([])->join(', ', ' and '); // ''

        $this->assertEquals($result, "a, b, c");
        $this->assertEquals($result2, "a, b, and c");
        $this->assertEquals($result3, "a and b");
        $this->assertEquals($result4, "a");
        $this->assertEquals($result5, "");
    }

    public function test_collection_keyBy() {
        $collection = collect([
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);
        $result = $collection->keyBy('product_id')->all();
        /*
            [
                'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
                'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
            ]
        */

        $result2 = $collection->keyBy(function ($item, $key) {
            return strtoupper($item['product_id']);
        })->all();
        /*
            [
                'PROD-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
                'PROD-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
            ]
        */

        $this->assertEquals($result['prod-100']['product_id'], "prod-100");
        $this->assertEquals($result['prod-100']['name'], "Desk");
        $this->assertEquals($result['prod-200']['product_id'], "prod-200");
        $this->assertEquals($result['prod-200']['name'], "Chair");

        $this->assertEquals($result2['PROD-100']['product_id'], "prod-100");
        $this->assertEquals($result2['PROD-100']['name'], "Desk");
        $this->assertEquals($result2['PROD-200']['product_id'], "prod-200");
        $this->assertEquals($result2['PROD-200']['name'], "Chair");
    }

    public function test_collection_keys() {
        $collection = collect([
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);

        $result = $collection->keys()->all(); // ['prod-100', 'prod-200']

        $this->assertEquals($result[0], "prod-100");
        $this->assertEquals($result[1], "prod-200");
    }

    public function test_collection_last() {
        $result = collect([1, 2, 3, 4])->last(function ($value, $key) {
            return $value < 3;
        }); // 2
        $result2 = collect([1, 2, 3, 4])->last(); // 4

        $this->assertEquals($result, 2);
        $this->assertEquals($result2, 4);
    }

    public function test_collection_map() {
        // map in collection of laravel
    }
}
