<?php

use Tests\TestCase;
use Illuminate\Support\LazyCollection;

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
        $this->assertEquals($result1, false);

        $collection = collect(['name' => 'Desk', 'price' => 100]);

        $result2 = $collection->contains('Desk'); // true
        $result3 = $collection->contains('New York'); // false
        $this->assertEquals($result2, true);
        $this->assertEquals($result3, false);

        $collection = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ]);

        $result4 = $collection->contains('product', '=', 'Bookcase'); // false
        $result5 = $collection->contains('product', '=', 'Desk'); // true
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
        $this->assertEquals($result['1'], 1);
        $this->assertEquals($result['2'], 3);
        $this->assertEquals($result['3'], 1);

        $collection = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);
        $counted = $collection->countBy(function ($email) {
            return substr(strrchr($email, "@"), 1);
        });
        $result2 = $counted->all(); // ['gmail.com' => 2, 'yahoo.com' => 1]
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
}
