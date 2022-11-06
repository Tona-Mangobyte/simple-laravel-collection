<?php

use App\Helpers\Currency;
use App\Helpers\ResourceCollection;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Collection;

/*
 * @doc https://laravel.com/docs/9.x/collections
 * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Collections/Collection.php
 * **/

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
        $result = collect([1, 2, 3, 4, 5])->map(function ($item, $key) {
            return $item * 2;
        })->all(); // [2, 4, 6, 8, 10]

        $this->assertEquals($result[0], 2);
        $this->assertEquals($result[1], 4);
        $this->assertEquals($result[2], 6);
        $this->assertEquals($result[3], 8);
        $this->assertEquals($result[4], 10);
    }

    public function test_collection_mapInto() {
        $result = collect(['USD', 'EUR', 'GBP'])
            ->mapInto(Currency::class)
            ->all(); // [Currency('USD'), Currency('EUR'), Currency('GBP')]
        $this->assertEquals($result[0], new Currency("USD"));
        $this->assertEquals($result[1], new Currency("EUR"));
        $this->assertEquals($result[2], new Currency("GBP"));
    }

    public function test_collection_mapSpread() {
        $result = collect([0, 1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->chunk(2)
            ->mapSpread(function ($even, $odd) {
                return $even + $odd;
            })->all(); // [1, 5, 9, 13, 17]

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[1], 5);
        $this->assertEquals($result[2], 9);
        $this->assertEquals($result[3], 13);
        $this->assertEquals($result[4], 17);
    }

    public function test_collection_mapToGroups() {
        $collection = collect([
            [
                'name' => 'John Doe',
                'department' => 'Sales',
            ],
            [
                'name' => 'Jane Doe',
                'department' => 'Sales',
            ],
            [
                'name' => 'Johnny Doe',
                'department' => 'Marketing',
            ]
        ]);

        $grouped = $collection->mapToGroups(function ($item, $key) {
            return [$item['department'] => $item['name']];
        });
        $groups = $grouped->all();
        /*
            [
                'Sales' => ['John Doe', 'Jane Doe'],
                'Marketing' => ['Johnny Doe'],
            ]
        */
        $salesGroup = $grouped->get('Sales')->all(); // ['John Doe', 'Jane Doe']

        $this->assertEquals($groups['Sales'][0], "John Doe");
        $this->assertEquals($groups['Sales'][1], "Jane Doe");
        $this->assertEquals($groups['Marketing'][0], "Johnny Doe");

        $this->assertEquals($salesGroup[0], "John Doe");
        $this->assertEquals($salesGroup[1], "Jane Doe");
    }

    public function test_collection_mapWithKeys() {
        $result = collect([
            [
                'name' => 'John',
                'department' => 'Sales',
                'email' => 'john@example.com',
            ],
            [
                'name' => 'Jane',
                'department' => 'Marketing',
                'email' => 'jane@example.com',
            ]
        ])->mapWithKeys(function ($item, $key) {
            return [$item['email'] => $item['name']];
        })->all();
        /*
            [
                'john@example.com' => 'John',
                'jane@example.com' => 'Jane',
            ]
        */

        $this->assertEquals($result['john@example.com'], "John");
        $this->assertEquals($result['jane@example.com'], "Jane");
    }

    public function test_collection_max() {
        $result = collect([
            ['foo' => 10],
            ['foo' => 20]
        ])->max('foo'); // 20
        $result2 = collect([1, 2, 3, 4, 5])->max(); // 5

        $this->assertEquals($result, 20);
        $this->assertEquals($result2, 5);
    }

    public function test_collection_median() {
        $result = collect([
            ['foo' => 10],
            ['foo' => 10],
            ['foo' => 20],
            ['foo' => 40]
        ])->median('foo'); // 15

        $result2 = collect([1, 1, 2, 4])->median(); // 1.5

        $this->assertEquals($result, 15);
        $this->assertEquals($result2, 1.5);
    }

    public function test_collection_merge() {
        $result = collect(['product_id' => 1, 'price' => 100])
            ->merge(['price' => 200, 'discount' => false])
            ->all(); // ['product_id' => 1, 'price' => 200, 'discount' => false]

        $result2 = collect(['Desk', 'Chair'])
            ->merge(['Bookcase', 'Door'])
            ->all(); // ['Desk', 'Chair', 'Bookcase', 'Door']

        $this->assertEquals($result['product_id'], 1);
        $this->assertEquals($result['price'], 200);
        $this->assertEquals($result['discount'], false);

        $this->assertEquals($result2[0], "Desk");
        $this->assertEquals($result2[1], "Chair");
        $this->assertEquals($result2[2], "Bookcase");
        $this->assertEquals($result2[3], "Door");
    }

    public function test_collection_mergeRecursive() {
        $result = collect(['product_id' => 1, 'price' => 100])
            ->mergeRecursive([
                'product_id' => 2,
                'price' => 200,
                'discount' => false
            ])->all(); // ['product_id' => [1, 2], 'price' => [100, 200], 'discount' => false]

        $this->assertEquals($result['product_id'][0], 1);
        $this->assertEquals($result['product_id'][1], 2);
        $this->assertEquals($result['price'][0], 100);
        $this->assertEquals($result['price'][1], 200);
        $this->assertEquals($result['discount'], false);
    }

    public function test_collection_min() {
        $result = collect([['foo' => 10], ['foo' => 20]])->min('foo'); // 10
        $result2 = collect([1, 2, 3, 4, 5])->min(); // 1

        $this->assertEquals($result, 10);
        $this->assertEquals($result2, 1);
    }

    public function test_collection_mode() {
        $result = collect([
            ['foo' => 10],
            ['foo' => 10],
            ['foo' => 20],
            ['foo' => 40]
        ])->mode('foo'); // [10]
        $result2 = collect([1, 1, 2, 4])->mode(); // [1]
        $result3 = collect([1, 1, 2, 2])->mode(); // [1, 2]

        $this->assertEquals($result[0], 10);
        $this->assertEquals($result2[0], 1);
        $this->assertEquals($result3[0], 1);
        $this->assertEquals($result3[1], 2);
    }

    public function test_collection_nth() {
        $collection = collect(['a', 'b', 'c', 'd', 'e', 'f']);
        $result = $collection->nth(4); // ['a', 'e']
        $result2 = $collection->nth(4, 1); // ['b', 'f']

        $this->assertEquals($result[0], "a");
        $this->assertEquals($result[1], "e");
        $this->assertEquals($result2[0], "b");
        $this->assertEquals($result2[1], "f");
    }

    public function test_collection_only() {
        $result = collect([
            'product_id' => 1,
            'name' => 'Desk',
            'price' => 100,
            'discount' => false
        ])->only(['product_id', 'name'])
            ->all(); // ['product_id' => 1, 'name' => 'Desk']

        $this->assertEquals($result['product_id'], 1);
        $this->assertEquals($result['name'], "Desk");
    }

    public function test_collection_pad() {
        $collection = collect(['A', 'B', 'C']);

        $result = $collection->pad(5, 0)
            ->all(); // ['A', 'B', 'C', 0, 0]

        $result2 = $collection->pad(-5, 0)
            ->all(); // [0, 0, 'A', 'B', 'C']

        $this->assertEquals($result[0], "A");
        $this->assertEquals($result2[0], 0);
    }

    public function test_collection_partition() {
        $collection = collect([1, 2, 3, 4, 5, 6]);

        [$underThree, $equalOrAboveThree] = $collection->partition(function ($i) {
            return $i < 3;
        });

        $underThree->all(); // [1, 2]
        $equalOrAboveThree->all(); // [3, 4, 5, 6]

        $this->assertEquals($underThree->count(), 2);
        $this->assertEquals($equalOrAboveThree->count(), 4);
    }

    public function test_collection_pipe() {
        $collection = collect([1, 2, 3]);

        $result = $collection->pipe(function ($collection) {
            return $collection->sum();
        }); // 6

        $this->assertEquals($result, 6);
    }

    public function test_collection_pipeInto() {
        $collection = collect([1, 2, 3]);

        $resource = $collection->pipeInto(ResourceCollection::class);
        $result = $resource->collection->all(); // [1, 2, 3]

        $this->assertEquals($resource->collection->count(), 3);
        $this->assertEquals($result[0], 1);
    }

    public function test_collection_pipeThrough() {
        $result = collect([1, 2, 3])->pipeThrough([
            function ($collection) {
                return $collection->merge([4, 5]);
            },
            function ($collection) {
                return $collection->sum();
            },
        ]); // 15

        $this->assertEquals($result, 15);
    }

    public function test_collection_pluck() {
        $collection = collect([
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);

        $result = $collection->pluck('name')
            ->all(); // ['Desk', 'Chair']
        $result2 = $collection->pluck('name', 'product_id')
            ->all(); // ['prod-100' => 'Desk', 'prod-200' => 'Chair']

        $result3 = collect([
            [
                'name' => 'Laracon',
                'speakers' => [
                    'first_day' => ['Rosa', 'Judith'],
                ],
            ],
            [
                'name' => 'VueConf',
                'speakers' => [
                    'first_day' => ['Abigail', 'Joey'],
                ],
            ],
        ])->pluck('speakers.first_day')
            ->all(); // [['Rosa', 'Judith'], ['Abigail', 'Joey']]

        $result4 = collect([
            ['brand' => 'Tesla',  'color' => 'red'],
            ['brand' => 'Pagani', 'color' => 'white'],
            ['brand' => 'Tesla',  'color' => 'black'],
            ['brand' => 'Pagani', 'color' => 'orange'],
        ])->pluck('color', 'brand')
            ->all(); // ['Tesla' => 'black', 'Pagani' => 'orange']

        $this->assertEquals($result[0], "Desk");
        $this->assertEquals($result[1], "Chair");
        $this->assertEquals($result2['prod-100'], "Desk");
        $this->assertEquals($result2['prod-200'], "Chair");

        $this->assertEquals($result3[0][0], "Rosa");
        $this->assertEquals($result3[0][1], "Judith");
        $this->assertEquals($result3[1][0], "Abigail");
        $this->assertEquals($result3[1][1], "Joey");

        $this->assertEquals($result4['Tesla'], "black");
    }

    /*
     * The pop method removes and returns the last item from the collection
     * */
    public function test_collection_pop() {
        $collection = collect([1, 2, 3, 4, 5]);

        $removed = $collection->pop(); // 5
        $result = $collection->all(); // [1, 2, 3, 4]

        $collection2 = collect([1, 2, 3, 4, 5]);
        $removed2 = $collection2->pop(3); // collect([5, 4, 3])
        $result2 = $collection2->all();// [1, 2]

        $this->assertEquals($removed, 5);
        $this->assertEquals($result[0], 1);

        $this->assertEquals($removed2->count(), 3);
        $this->assertEquals($result2[0], 1);
        $this->assertEquals($result2[1], 2);
    }

    public function test_collection_prepend() {
        $collection = collect([1, 2, 3, 4, 5]);
        $collection->prepend(0);
        $result = $collection->all(); // [0, 1, 2, 3, 4, 5]

        $collection2 = collect(['one' => 1, 'two' => 2]);
        $collection2->prepend(0, 'zero');
        $result2 = $collection2->all(); // ['zero' => 0, 'one' => 1, 'two' => 2]

        $this->assertEquals(count($result), 6);

        $this->assertEquals($result2['zero'], 0);
        $this->assertEquals($result2['one'], 1);
        $this->assertEquals($result2['two'], 2);
    }

    /*
     * The pull method removes and returns an item from the collection by its key
     * */
    public function test_collection_pull() {
        $collection = collect(['product_id' => 'prod-100', 'name' => 'Desk']);
        $removed = $collection->pull('name'); // 'Desk'
        $result = $collection->all(); // ['product_id' => 'prod-100']

        $this->assertEquals($removed, "Desk");
        $this->assertEquals($result['product_id'], "prod-100");
    }

    public function test_collection_push() {
        $result = collect([1, 2, 3, 4])
            ->push(5)
            ->all(); // [1, 2, 3, 4, 5]

        $this->assertEquals(count($result), 5);
        $this->assertEquals($result[0], 1);
    }

    public function test_collection_put() {
        $result = collect(['product_id' => 1, 'name' => 'Desk'])
            ->put('price', 100)
            ->all(); // ['product_id' => 1, 'name' => 'Desk', 'price' => 100]

        $this->assertEquals($result['product_id'], 1);
        $this->assertEquals($result['name'], "Desk");
        $this->assertEquals($result['price'], 100);
    }

    public function test_collection_random() {
        $result = collect([1, 2, 3, 4, 5])
            ->random();
        $result2 = collect([1, 2, 3, 4, 5])->random(3)->all();
        $result3 = collect([1, 2, 3, 4, 5])
            ->random(fn ($items) => min(10, count($items)))
            ->all();

        $this->assertIsInt($result);
        $this->assertEquals(count($result2), 3);
        $this->assertEquals(count($result3), 5);
    }

    public function test_collection_range() {
        $result = collect()->range(3, 6)->all(); // [3, 4, 5, 6]
        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], 3);
    }

    public function test_collection_reduce() {
        $result = collect([1, 2, 3])->reduce(function ($carry, $item) {
            return $carry + $item;
        }); // 6
        $result2 = collect([1, 2, 3])->reduce(function ($carry, $item) {
            return $carry + $item;
        }, 4); // 10

        $collection = collect([
            'usd' => 1400,
            'gbp' => 1200,
            'eur' => 1000,
        ]);
        $ratio = [
            'usd' => 1,
            'gbp' => 1.37,
            'eur' => 1.22,
        ];
        $result3 = $collection->reduce(function ($carry, $value, $key) use ($ratio) {
            return $carry + ($value * $ratio[$key]);
        }); // 4264

        $this->assertEquals($result, 6);
        $this->assertEquals($result2, 10);
        $this->assertEquals($result3, 4264);
    }

    public function test_collection_reject() {
        $result = collect([1, 2, 3, 4])
            ->reject(function ($value, $key) {
                return $value > 2;
            })->all(); // [1, 2]

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[1], 2);
    }

    public function test_collection_replace() {
        $result = collect(['Taylor', 'Abigail', 'James'])
            ->replace([1 => 'Victoria', 3 => 'Finn'])
            ->all(); // ['Taylor', 'Victoria', 'James', 'Finn']

        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], "Taylor");
        $this->assertEquals($result[1], "Victoria");
        $this->assertEquals($result[2], "James");
        $this->assertEquals($result[3], "Finn");
    }

    public function test_collection_replaceRecursive() {
        $result = collect([
            'Taylor',
            'Abigail',
            [
                'James',
                'Victoria',
                'Finn'
            ]
        ])->replaceRecursive([
            'Charlie',
            2 => [1 => 'King']
        ])->all(); // ['Charlie', 'Abigail', ['James', 'King', 'Finn']]

        $this->assertEquals($result[0], "Charlie");
        $this->assertEquals($result[1], "Abigail");
        $this->assertEquals($result[2][0], "James");
        $this->assertEquals($result[2][1], "King");
        $this->assertEquals($result[2][2], "Finn");
    }

    public function test_collection_reverse() {
        $result = collect(['a', 'b', 'c', 'd', 'e'])->reverse()->all();
        /*
            [
                4 => 'e',
                3 => 'd',
                2 => 'c',
                1 => 'b',
                0 => 'a',
            ]
        */
        $this->assertEquals($result[4], "e");
    }

    public function test_collection_search() {
        $result = collect([2, 4, 6, 8])->search(4); // 1
        $result2 = collect([2, 4, 6, 8])->search('4', $strict = true); // false
        $result3 = collect([2, 4, 6, 8])->search(function ($item, $key) {
            return $item > 5;
        }); // 2

        $this->assertEquals($result, 1);
        $this->assertEquals($result2, false);
        $this->assertEquals($result3, 2);
    }

    public function test_collection_shift() {
        $collection = collect([1, 2, 3, 4, 5]);

        $removed = $collection->shift(); // 1
        $result = $collection->all(); // [2, 3, 4, 5]

        $this->assertEquals($removed, 1);

        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], 2);
    }

    public function test_collection_shuffle() {
        $result = collect([1, 2, 3, 4, 5])
            ->shuffle()
            ->all(); // generated randomly

        $this->assertEquals(count($result), 5);
    }

    public function test_collection_skip() {
        $result = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->skip(4)
            ->all(); // [5, 6, 7, 8, 9, 10]

        $this->assertEquals(count($result), 6);
        $this->assertEquals($result[4], 5);
    }

    public function test_collection_skipUntil() {
        $collection = collect([1, 2, 3, 4]);

        $result = $collection->skipUntil(function ($item) {
            return $item >= 3;
        })->all(); // [3, 4]

        $result2 = $collection->skipUntil(3)->all(); // [3, 4]

        $this->assertEquals($result[2], 3);
        $this->assertEquals($result[3], 4);
        $this->assertEquals($result2[2], 3);
        $this->assertEquals($result2[3], 4);
    }

    public function test_collection_skipWhile() {
        $collection = collect([1, 2, 3, 4]);
        $result = $collection->skipWhile(function ($item) {
            return $item <= 3;
        })->all(); // [4]

        $this->assertEquals($result[3], 4);
    }

    public function test_collection_slice() {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->slice(4)->all(); // [5, 6, 7, 8, 9, 10]
        $result2 = $collection->slice(4, 2)->all(); // [5, 6]

        $this->assertEquals($result[4], 5);

        $this->assertEquals($result2[4], 5);
        $this->assertEquals($result2[5], 6);
    }

    public function test_collection_sliding() {
        $collection = collect([1, 2, 3, 4, 5]);

        $result = $collection->sliding(2)
            ->toArray(); // [[1, 2], [2, 3], [3, 4], [4, 5]]

        $result2 = $collection->sliding(3, step: 2)
            ->toArray(); // [[1, 2, 3], [3, 4, 5]]

        $this->assertEquals($result[0][0], 1);
        $this->assertEquals($result[0][1], 2);
        $this->assertEquals($result[0][1], 2);

        $this->assertEquals($result2[0][0], 1);
        $this->assertEquals($result2[0][1], 2);
        $this->assertEquals($result2[0][2], 3);
        $this->assertEquals($result2[1][2], 3);
        $this->assertEquals($result2[1][3], 4);
        $this->assertEquals($result2[1][4], 5);
    }

    public function test_collection_sole() {
        $result = collect([1, 2, 3, 4])->sole(function ($value, $key) {
            return $value === 2;
        }); // 2

        $result2 = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ])->sole('product', 'Chair'); // ['product' => 'Chair', 'price' => 100]

        $this->assertEquals($result, 2);

        $this->assertEquals($result2['product'], "Chair");
        $this->assertEquals($result2['price'], 100);
    }

    public function test_collection_sort() {
        $result = collect([5, 3, 1, 2, 4])->sort()
            ->values()
            ->all(); // [1, 2, 3, 4, 5]

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[1], 2);
        $this->assertEquals($result[2], 3);
        $this->assertEquals($result[3], 4);
        $this->assertEquals($result[4], 5);
    }

    public function test_collection_sortBy() {
        $result = collect([
            ['name' => 'Desk', 'price' => 200],
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
        ])->sortBy('price')
            ->values()->all();
        /*
            [
                ['name' => 'Chair', 'price' => 100],
                ['name' => 'Bookcase', 'price' => 150],
                ['name' => 'Desk', 'price' => 200],
            ]
        */

        $result2 = collect([
            ['title' => 'Item 1'],
            ['title' => 'Item 12'],
            ['title' => 'Item 3'],
        ])->sortBy('title', SORT_NATURAL)
            ->values()->all();
        /*
            [
                ['title' => 'Item 1'],
                ['title' => 'Item 3'],
                ['title' => 'Item 12'],
            ]
        */

        $result3 = collect([
            ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
            ['name' => 'Chair', 'colors' => ['Black']],
            ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
        ])->sortBy(function ($product, $key) {
            return count($product['colors']);
        })->values()->all();
        /*
            [
                ['name' => 'Chair', 'colors' => ['Black']],
                ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
                ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
            ]
        */

        $result4 = collect([
            ['name' => 'Taylor Otwell', 'age' => 34],
            ['name' => 'Abigail Otwell', 'age' => 30],
            ['name' => 'Taylor Otwell', 'age' => 36],
            ['name' => 'Abigail Otwell', 'age' => 32],
        ])->sortBy([
            ['name', 'asc'],
            ['age', 'desc'],
        ])->values()->all();
        /*
            [
                ['name' => 'Abigail Otwell', 'age' => 32],
                ['name' => 'Abigail Otwell', 'age' => 30],
                ['name' => 'Taylor Otwell', 'age' => 36],
                ['name' => 'Taylor Otwell', 'age' => 34],
            ]
        */

        $result5 = collect([
            ['name' => 'Taylor Otwell', 'age' => 34],
            ['name' => 'Abigail Otwell', 'age' => 30],
            ['name' => 'Taylor Otwell', 'age' => 36],
            ['name' => 'Abigail Otwell', 'age' => 32],
        ])->sortBy([
            fn ($a, $b) => $a['name'] <=> $b['name'],
            fn ($a, $b) => $b['age'] <=> $a['age'],
        ])->values()->all();
        /*
            [
                ['name' => 'Abigail Otwell', 'age' => 32],
                ['name' => 'Abigail Otwell', 'age' => 30],
                ['name' => 'Taylor Otwell', 'age' => 36],
                ['name' => 'Taylor Otwell', 'age' => 34],
            ]
        */

        $this->assertEquals($result[0]['name'], "Chair");
        $this->assertEquals($result[0]['price'], 100);

        $this->assertEquals($result2[0]['title'], "Item 1");

        $this->assertEquals($result3[0]['name'], "Chair");
        $this->assertEquals(count($result3[0]['colors']), 1);

        $this->assertEquals($result4[0]['name'], "Abigail Otwell");
        $this->assertEquals($result4[0]['age'], 32);

        $this->assertEquals($result5[0]['name'], "Abigail Otwell");
        $this->assertEquals($result5[0]['age'], 32);
    }

    public function test_collection_sortDesc() {
        $result = collect([5, 3, 1, 2, 4])
            ->sortDesc()
            ->values()->all(); // [5, 4, 3, 2, 1]

        $this->assertEquals($result[0], 5);
    }

    public function test_collection_sortKeys() {
        $result = collect([
            'id' => 22345,
            'first' => 'John',
            'last' => 'Doe',
        ])->sortKeys()->all();
        /*
            [
                'first' => 'John',
                'id' => 22345,
                'last' => 'Doe',
            ]
        */
        $this->assertEquals($result['id'], 22345);
        $this->assertEquals($result['first'], "John");
        $this->assertEquals($result['last'], "Doe");
    }

    public function test_collection_sortKeysUsing() {
        $result = collect([
            'ID' => 22345,
            'first' => 'John',
            'last' => 'Doe',
        ])->sortKeysUsing('strnatcasecmp')->all();
        /*
            [
                'first' => 'John',
                'ID' => 22345,
                'last' => 'Doe',
            ]
        */

        $this->assertEquals($result['ID'], 22345);
        $this->assertEquals($result['first'], "John");
        $this->assertEquals($result['last'], "Doe");
    }

    public function test_collection_splice() {
        $collection = collect([1, 2, 3, 4, 5]);
        $result = $collection->splice(2)
            ->all(); // [3, 4, 5]
        $result2 = $collection->all(); // [1, 2]

        $collection2 = collect([1, 2, 3, 4, 5]);
        $result3 = $collection2->splice(2, 1)->all(); // [3]
        $result4 = $collection2->all(); // [1, 2, 4, 5]

        $collection3 = collect([1, 2, 3, 4, 5]);
        $result5 = $collection3->splice(2, 1, [10, 11])
            ->all(); // [3]
        $result6 = $collection3->all(); // [1, 2, 10, 11, 4, 5]

        $this->assertEquals($result[0], 3);
        $this->assertEquals($result[1], 4);
        $this->assertEquals($result[2], 5);

        $this->assertEquals($result2[0], 1);
        $this->assertEquals($result2[1], 2);

        $this->assertEquals($result3[0], 3);

        $this->assertEquals($result4[0], 1);
        $this->assertEquals($result4[1], 2);
        $this->assertEquals($result4[2], 4);
        $this->assertEquals($result4[3], 5);

        $this->assertEquals($result5[0], 3);

        $this->assertEquals($result6[0], 1);
        $this->assertEquals($result6[5], 5);
    }

    public function test_collection_split() {
        $result = collect([1, 2, 3, 4, 5])
            ->split(3)
            ->all(); // [[1, 2], [3, 4], [5]]

        $this->assertEquals($result[0][0], 1);
        $this->assertEquals($result[1][0], 3);
        $this->assertEquals($result[2][0], 5);
    }

    public function test_collection_splitIn() {
        $result = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->splitIn(3)
            ->all(); // [[1, 2, 3, 4], [5, 6, 7, 8], [9, 10]]

        $this->assertEquals($result[0][0], 1);
        $this->assertEquals($result[1][4], 5);
        $this->assertEquals($result[2][8], 9);
    }

    public function test_collection_sum() {
        $result = collect([1, 2, 3, 4, 5])->sum(); // 15

        $result2 = collect([
            ['name' => 'JavaScript: The Good Parts', 'pages' => 176],
            ['name' => 'JavaScript: The Definitive Guide', 'pages' => 1096],
        ])->sum('pages'); // 1272

        $result3 = collect([
            ['name' => 'Chair', 'colors' => ['Black']],
            ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
            ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
        ])->sum(function ($product) {
            return count($product['colors']);
        }); // 6

        $this->assertEquals($result, 15);
        $this->assertEquals($result2, 1272);
        $this->assertEquals($result3, 6);
    }

    public function test_collection_take() {
        $result = collect([0, 1, 2, 3, 4, 5])
            ->take(3)
            ->all(); // [0, 1, 2]

        $result2 = collect([0, 1, 2, 3, 4, 5])
            ->take(-2)->all();// [4, 5]

        $this->assertEquals($result[0], 0);
        $this->assertEquals($result[1], 1);
        $this->assertEquals($result[2], 2);

        $this->assertEquals($result2[4], 4);
        $this->assertEquals($result2[5], 5);
    }

    public function test_collection_takeUntil() {
        $result = collect([1, 2, 3, 4])->takeUntil(function ($item) {
            return $item >= 3;
        })->all(); // [1, 2]

        $result2 = collect([1, 2, 3, 4])->takeUntil(3)->all(); // [1, 2]

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[1], 2);

        $this->assertEquals($result2[0], 1);
        $this->assertEquals($result2[1], 2);
    }

    public function test_collection_takeWhile() {
        $result = collect([1, 2, 3, 4])->takeWhile(function ($item) {
            return $item < 3;
        })->all(); // [1, 2]

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[1], 2);
    }

    public function test_collection_tap() {
        $result = collect([2, 4, 3, 1, 5])
            ->sort()
            ->tap(function ($collection) {
                Log::debug('Values after sorting', $collection->values()->all());
            })
            ->shift(); // 1
        $this->assertEquals($result, 1);
    }

    public function test_collection_times() {
        $result = Collection::times(10, function ($number) {
            return $number * 9;
        })->all(); // [9, 18, 27, 36, 45, 54, 63, 72, 81, 90]

        $this->assertEquals($result[0], 9);
        $this->assertEquals($result[9], 90);
    }

    public function test_collection_toArray() {
        $result = collect(['name' => 'Desk', 'price' => 200])
            ->toArray();
        /*
            [
                ['name' => 'Desk', 'price' => 200],
            ]
        */

        $this->assertEquals($result['name'], "Desk");
        $this->assertEquals($result['price'], 200);
    }

    public function test_collection_transform() {
        $result = collect([1, 2, 3, 4, 5])
            ->transform(function ($item, $key) {
                    return $item * 2;
            })->all(); // [2, 4, 6, 8, 10]

        $this->assertEquals($result[0], 2);
        $this->assertEquals($result[1], 4);
        $this->assertEquals($result[2], 6);
        $this->assertEquals($result[3], 8);
        $this->assertEquals($result[4], 10);
    }

    public function test_collection_undot() {
        $result = collect([
            'name.first_name' => 'Marie',
            'name.last_name' => 'Valentine',
            'address.line_1' => '2992 Eagle Drive',
            'address.line_2' => '',
            'address.suburb' => 'Detroit',
            'address.state' => 'MI',
            'address.postcode' => '48219'
        ])->undot()->toArray();
        /*
            [
                "name" => [
                    "first_name" => "Marie",
                    "last_name" => "Valentine",
                ],
                "address" => [
                    "line_1" => "2992 Eagle Drive",
                    "line_2" => "",
                    "suburb" => "Detroit",
                    "state" => "MI",
                    "postcode" => "48219",
                ],
            ]
        */

        $this->assertEquals($result['name']['first_name'], "Marie");
        $this->assertEquals($result['name']['last_name'], "Valentine");
        $this->assertEquals($result['address']['postcode'], "48219");
    }

    public function test_collection_union() {
        $result = collect([1 => ['a'], 2 => ['b']])
            ->union([3 => ['c'], 1 => ['d']])
            ->all(); // [1 => ['a'], 2 => ['b'], 3 => ['c']]

        $this->assertEquals($result[1][0], "a");
        $this->assertEquals($result[2][0], "b");
        $this->assertEquals($result[3][0], "c");
    }

    public function test_collection_unique() {
        $result = collect([1, 1, 2, 2, 3, 4, 2])
            ->unique()->values()->all(); // [1, 2, 3, 4]

        $result2 = collect([
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'iPhone 5', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
            ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
        ])->unique('brand')->values()->all();
        /*
            [
                ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
                ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
            ]
        */

        $result3 = collect([
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'iPhone 5', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
            ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
        ])->unique(function ($item) {
            return $item['brand'].$item['type'];
        })->values()->all();
        /*
            [
                ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
                ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
                ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
                ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
            ]
        */

        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[1], 2);
        $this->assertEquals($result[2], 3);
        $this->assertEquals($result[3], 4);

        $this->assertEquals($result2[0]['name'], "iPhone 6");
        $this->assertEquals($result2[1]['name'], "Galaxy S6");

        $this->assertEquals($result3[0]['name'], "iPhone 6");
        $this->assertEquals($result3[0]['brand'], "Apple");
        $this->assertEquals($result3[0]['type'], "phone");
    }

    public function test_collection_unless() {
        $result = collect([1, 2, 3])
            ->unless(true, function ($collection) {
                return $collection->push(4);
                })
            ->unless(false, function ($collection) {
                return $collection->push(5);
            })->all(); // [1, 2, 3, 5]

        $result2 = collect([1, 2, 3])
            ->unless(true, function ($collection) {
                return $collection->push(4);
            }, function ($collection) {
                return $collection->push(5);
            })->all(); // [1, 2, 3, 5]

        $this->assertEquals(count($result), 4);
        $this->assertEquals(count($result2), 4);
    }

    public function test_collection_value() {
        $result = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Speaker', 'price' => 400],
        ])->value('price'); // 200

        $this->assertEquals($result, 200);
    }

    public function test_collection_values() {
        $result = collect([
            10 => ['product' => 'Desk', 'price' => 200],
            11 => ['product' => 'Desk', 'price' => 200],
        ])->values()->all();
        /*
            [
                0 => ['product' => 'Desk', 'price' => 200],
                1 => ['product' => 'Desk', 'price' => 200],
            ]
        */

        $this->assertEquals($result[0]['product'], "Desk");
        $this->assertEquals($result[0]['price'], 200);
    }

    public function test_collection_when() {
        $result = collect([1, 2, 3])
            ->when(true, function ($collection, $value) {
                return $collection->push(4);
            })
            ->when(false, function ($collection, $value) {
                return $collection->push(5);
            })->all(); // [1, 2, 3, 4]

        $result2 = collect([1, 2, 3])
            ->when(false, function ($collection, $value) {
                return $collection->push(4);
            }, function ($collection) {
                return $collection->push(5);
            })->all(); // [1, 2, 3, 5]

        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], 1);
        $this->assertEquals($result[3], 4);

        $this->assertEquals(count($result2), 4);
        $this->assertEquals($result2[0], 1);
        $this->assertEquals($result2[3], 5);
    }

    public function test_collection_whenEmpty() {
        $result = collect(['Michael', 'Tom'])
            ->whenEmpty(function ($collection) {
                return $collection->push('Adam');
            })->all(); // ['Michael', 'Tom']

        $result2 = collect()->whenEmpty(function ($collection) {
            return $collection->push('Adam');
        })->all(); // ['Adam']


        $result3 = collect(['Michael', 'Tom'])->whenEmpty(function ($collection) {
            return $collection->push('Adam');
        }, function ($collection) {
            return $collection->push('Taylor');
        })->all(); // ['Michael', 'Tom', 'Taylor']

        $this->assertEquals($result[0], "Michael");
        $this->assertEquals($result[1], "Tom");

        $this->assertEquals($result2[0], "Adam");

        $this->assertEquals($result3[0], "Michael");
        $this->assertEquals($result3[1], "Tom");
        $this->assertEquals($result3[2], "Taylor");
    }

    public function test_collection_whenNotEmpty() {
        $result = collect(['michael', 'tom'])->whenNotEmpty(function ($collection) {
            return $collection->push('adam');
        })->all(); // ['michael', 'tom', 'adam']

        $result2 = collect()->whenNotEmpty(function ($collection) {
            return $collection->push('adam');
        })->all(); // []

        $result3 = collect()->whenNotEmpty(function ($collection) {
            return $collection->push('adam');
        }, function ($collection) {
            return $collection->push('taylor');
        })->all(); // ['taylor']

        $this->assertEquals($result[0], "michael");
        $this->assertEquals($result[1], "tom");
        $this->assertEquals($result[2], "adam");

        $this->assertEquals(count($result2), 0);

        $this->assertEquals(count($result3), 1);
        $this->assertEquals($result3[0], "taylor");
    }

    public function test_collection_where() {
        $result = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ])->where('price', 100)->all();
        /*
            [
                ['product' => 'Chair', 'price' => 100],
                ['product' => 'Door', 'price' => 100],
            ]
        */

        $result2 = collect([
            ['name' => 'Jim', 'deleted_at' => '2019-01-01 00:00:00'],
            ['name' => 'Sally', 'deleted_at' => '2019-01-02 00:00:00'],
            ['name' => 'Sue', 'deleted_at' => null],
        ])->where('deleted_at', '!=', null)
            ->all();
        /*
            [
                ['name' => 'Jim', 'deleted_at' => '2019-01-01 00:00:00'],
                ['name' => 'Sally', 'deleted_at' => '2019-01-02 00:00:00'],
            ]
        */

        $this->assertEquals($result[1]['product'], "Chair");
        $this->assertEquals($result[3]['price'], 100);

        $this->assertEquals($result2[0]['name'], "Jim");
        $this->assertEquals($result2[1]['name'], "Sally");
    }

    public function test_collection_whereBetween() {
        $result = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => 30],
            ['product' => 'Door', 'price' => 100],
        ])->whereBetween('price', [100, 200])->all();
        /*
            [
                ['product' => 'Desk', 'price' => 200],
                ['product' => 'Bookcase', 'price' => 150],
                ['product' => 'Door', 'price' => 100],
            ]
        */

        $this->assertEquals($result[0]['product'], "Desk");
        $this->assertEquals($result[0]['price'], 200);
    }

    public function test_collection_whereIn() {
        $result = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ])->whereIn('price', [150, 200])->all();
        /*
            [
                ['product' => 'Desk', 'price' => 200],
                ['product' => 'Bookcase', 'price' => 150],
            ]
        */

        $this->assertEquals($result[0]['product'], "Desk");
        $this->assertEquals($result[0]['price'], 200);
    }

    public function test_collection_whereInstanceOf() {
        $result = collect([
            new User,
            new User,
            new Currency("Simple"),
        ])->whereInstanceOf(User::class)->all(); // [App\Models\User, App\Models\User]
        $this->assertEquals(count($result), 2);
    }

    public function test_collection_whereNotBetween() {
        $result = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => 30],
            ['product' => 'Door', 'price' => 100],
        ])->whereNotBetween('price', [100, 200])->all();
        /*
            [
                ['product' => 'Chair', 'price' => 80],
                ['product' => 'Pencil', 'price' => 30],
            ]
        */
        $this->assertEquals($result[1]['product'], "Chair");
        $this->assertEquals($result[1]['price'], 80);
        $this->assertEquals($result[3]['product'], "Pencil");
        $this->assertEquals($result[3]['price'], 30);
    }

    public function test_collection_whereNotIn() {
        $result = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ])->whereNotIn('price', [150, 200])->all();
        /*
            [
                ['product' => 'Chair', 'price' => 100],
                ['product' => 'Door', 'price' => 100],
            ]
        */

        $this->assertEquals($result[1]['product'], "Chair");
        $this->assertEquals($result[1]['price'], 100);
        $this->assertEquals($result[3]['product'], "Door");
        $this->assertEquals($result[3]['price'], 100);
    }

    public function test_collection_whereNotNull() {
        $result = collect([
            ['name' => 'Desk'],
            ['name' => null],
            ['name' => 'Bookcase'],
        ])->whereNotNull('name')->all();
        /*
            [
                ['name' => 'Desk'],
                ['name' => 'Bookcase'],
            ]
        */

        $this->assertEquals($result[0]['name'], "Desk");
        $this->assertEquals($result[2]['name'], "Bookcase");
    }

    public function test_collection_whereNull() {
        $result = collect([
            ['name' => 'Desk'],
            ['name' => null],
            ['name' => 'Bookcase'],
        ])->whereNull('name')->all();
        /*
            [
                ['name' => null],
            ]
        */

        $this->assertNull($result[1]['name']);
    }

    public function test_collection_zip() {
        $result = collect(['Chair', 'Desk'])
            ->zip([100, 200])
            ->all(); // [['Chair', 100], ['Desk', 200]]
        $this->assertEquals($result[0][0], "Chair");
        $this->assertEquals($result[0][1], 100);
        $this->assertEquals($result[1][0], "Desk");
        $this->assertEquals($result[1][1], 200);
    }
}
