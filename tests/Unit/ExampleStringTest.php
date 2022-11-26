<?php

use Illuminate\Support\Str;

class ExampleStringTest extends \Tests\TestCase
{
    public function test_str_after() {
        $slice = Str::after('This is my name', 'This is'); // ' my name'
        $this->assertEquals($slice, ' my name');
    }

    public function test_str_afterLast() {
        $slice = Str::afterLast('App\Http\Controllers\Controller', '\\'); // 'Controller'
        $this->assertEquals($slice, 'Controller');
    }

    public function test_str_ascii() {
        $slice = Str::ascii('û'); // 'u'
        $this->assertEquals($slice, 'u');
    }

    public function test_str_before() {
        $slice = Str::before('This is my name', 'my name'); // 'This is '
        $this->assertEquals($slice, 'This is ');
    }

    public function test_str_beforeLast() {
        $slice = Str::beforeLast('This is my name', 'is'); // 'This '
        $this->assertEquals($slice, 'This ');
    }

    public function test_str_between() {
        $slice = Str::between('This is my name', 'This', 'name'); // ' is my '
        $this->assertEquals($slice, ' is my ');
    }

    public function test_str_betweenFirst() {
        $slice = Str::betweenFirst('[a] bc [d]', '[', ']'); // 'a'
        $this->assertEquals($slice, 'a');
    }

    public function test_str_camel() {
        $converted = Str::camel('foo_bar'); // fooBar
        $this->assertEquals($converted, 'fooBar');
    }

    public function test_str_contains() {
        $contains = Str::contains('This is my name', 'my'); // true
        $contains2 = Str::contains('This is my name', ['my', 'foo']); // true
        $this->assertTrue($contains);
        $this->assertTrue($contains2);
    }

    public function test_str_containsAll() {
        $containsAll = Str::containsAll('This is my name', ['my', 'name']); // true
        $this->assertTrue($containsAll);
    }

    public function test_str_endsWith() {
        $result = Str::endsWith('This is my name', 'name'); // true

        $result2 = Str::endsWith('This is my name', ['name', 'foo']); // true

        $result3 = Str::endsWith('This is my name', ['this', 'foo']); // false

        $this->assertTrue($result);
        $this->assertTrue($result2);
        $this->assertFalse($result3);
    }

    public function test_str_excerpt() {
        $excerpt = Str::excerpt('This is my name', 'my', [
            'radius' => 3
        ]); // '...is my na...'
        $excerpt2 = Str::excerpt('This is my name', 'name', [
            'radius' => 3,
            'omission' => '(...) '
        ]); // '(...) my name'

        $this->assertEquals($excerpt, '...is my na...');
        $this->assertEquals($excerpt2, '(...) my name');
    }

    public function test_str_finish() {
        $adjusted = Str::finish('this/string', '/'); // this/string/
        $this->assertEquals($adjusted, 'this/string/');
    }

    public function test_str_headline() {
        $headline = Str::headline('steve_jobs'); // Steve Jobs
        $headline2 = Str::headline('EmailNotificationSent'); // Email Notification Sent

        $this->assertEquals($headline, 'Steve Jobs');
        $this->assertEquals($headline2, 'Email Notification Sent');
    }

    public function test_str_inlineMarkdown() {
        $html = Str::inlineMarkdown('**Laravel**'); // <strong>Laravel</strong>
        // print_r($html);
        $this->assertTrue(true);
    }

    public function test_str_is() {
        $matches = Str::is('foo*', 'foobar'); // true
        $matches2 = Str::is('baz*', 'foobar'); // false

        $this->assertTrue($matches);
        $this->assertFalse($matches2);
    }

    public function test_str_isAscii() {
        $isAscii = Str::isAscii('Taylor'); // true
        $isAscii2 = Str::isAscii('ü'); // false

        $this->assertTrue($isAscii);
        $this->assertFalse($isAscii2);
    }

    public function test_str_isJson() {
        $result = Str::isJson('[1,2,3]'); // true
        $result2 = Str::isJson('{"first": "John", "last": "Doe"}'); // true
        $result3 = Str::isJson('{first: "John", last: "Doe"}'); // false
        $this->assertTrue($result);
        $this->assertTrue($result2);
        $this->assertFalse($result3);
    }

    public function test_str_isUuid() {
        $isUuid = Str::isUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de'); // true
        $isUuid2 = Str::isUuid('laravel'); // false
        $this->assertTrue($isUuid);
        $this->assertFalse($isUuid2);
    }

    public function test_str_kebab() {
        $converted = Str::kebab('fooBar'); // foo-bar
        $this->assertEquals($converted, 'foo-bar');
    }

    public function test_str_lcfirst() {
        $string = Str::lcfirst('Foo Bar'); // foo Bar
        $this->assertEquals($string, 'foo Bar');
    }

    public function test_str_length() {
        $length = Str::length('Laravel'); // 7
        $this->assertEquals($length, 7);
    }

    public function test_str_limit() {
        $truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20); // The quick brown fox...
        $truncated2 = Str::limit('The quick brown fox jumps over the lazy dog', 20, ' (...)'); // The quick brown fox (...)
        $this->assertEquals($truncated, 'The quick brown fox...');
        $this->assertEquals($truncated2, 'The quick brown fox (...)');
    }

    public function test_str_lower() {
        $converted = Str::lower('LARAVEL'); // laravel
        $this->assertEquals($converted, 'laravel');
    }

    public function test_str_markdown() {
        $html = Str::markdown('# Laravel'); // <h1>Laravel</h1>

        $html = Str::markdown('# Taylor <b>Otwell</b>', [
            'html_input' => 'strip',
        ]); // <h1>Taylor Otwell</h1>
        $this->assertTrue(true);
    }

    public function test_str_mask() {
        $string = Str::mask('taylor@example.com', '*', 3); // tay***************
        $string2 = Str::mask('taylor@example.com', '*', -15, 3);// tay***@example.com
        $this->assertEquals($string, 'tay***************');
        $this->assertEquals($string2, 'tay***@example.com');
    }

    public function test_str_padBoth() {
        $padded = Str::padBoth('James', 10, '_'); // '__James___'
        $padded2 = Str::padBoth('James', 10); // '  James   '
        $this->assertEquals($padded, '__James___');
        $this->assertEquals($padded2, '  James   ');
    }

    public function test_str_padLeft() {
        $padded = Str::padLeft('James', 10, '-='); // '-=-=-James'
        $padded2 = Str::padLeft('James', 10); // '     James'
        $this->assertEquals($padded, '-=-=-James');
        $this->assertEquals($padded2, '     James');
    }

    public function test_str_padRight() {
        $padded = Str::padRight('James', 10, '-'); // 'James-----'
        $padded2 = Str::padRight('James', 10); // 'James     '
        $this->assertEquals($padded, 'James-----');
        $this->assertEquals($padded2, 'James     ');
    }

    public function test_str_plural() {
        $plural = Str::plural('car'); // cars
        $plural2 = Str::plural('child'); // children
        $plural3 = Str::plural('child', 2); // children
        $singular = Str::plural('child', 1); // child

        $this->assertEquals($plural, 'cars');
        $this->assertEquals($plural2, 'children');
        $this->assertEquals($plural3, 'children');
        $this->assertEquals($singular, 'child');
    }
}
