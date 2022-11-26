<?php

class ExampleHelperTest extends \Tests\TestCase
{
    public function test_logger() {
        logger('Debug message');
        $this->assertTrue(true);
    }

    public function test_logger_params() {
        logger('User has logged in.', ['id' => 400]);
        $this->assertTrue(true);
    }

    public function test_logger_err() {
        logger()->error('You are not allowed here.');
        $this->assertTrue(true);
    }

    public function test_report() {
        // report('Something went wrong.');
        $this->assertTrue(true);
    }

    public function test_request() {
        $request = request();
        $value = request('key', 'simple');
        $this->assertEquals($value, 'simple');
        $this->assertNotNull($request);
    }

    public function test_transform() {
        $callback = function ($value) {
            return $value * 2;
        };
        $result = transform(5, $callback); // 10

        $this->assertEquals($result, 10);
    }
}
