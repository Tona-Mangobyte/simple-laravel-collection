<?php

use Illuminate\Support\Str;

class ExampleStringTest extends \Tests\TestCase
{
    public function test_str_after() {
        $slice = Str::after('This is my name', 'This is'); // ' my name'
        $this->assertEquals($slice, ' my name');
    }
}
