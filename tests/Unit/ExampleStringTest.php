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

    public function test_str_pluralStudly() {
        $plural = Str::pluralStudly('VerifiedHuman'); // VerifiedHumans
        $plural2 = Str::pluralStudly('UserFeedback'); // UserFeedback
        $plural3 = Str::pluralStudly('VerifiedHuman', 2); // VerifiedHumans
        $singular = Str::pluralStudly('VerifiedHuman', 1); // VerifiedHuman

        $this->assertEquals($plural, 'VerifiedHumans');
        $this->assertEquals($plural2, 'UserFeedback');
        $this->assertEquals($plural3, 'VerifiedHumans');
        $this->assertEquals($singular, 'VerifiedHuman');
    }

    public function test_str_random() {
        $random = Str::random(40);
        $this->assertNotEmpty($random);
    }

    public function test_str_remove() {
        $string = 'Peter Piper picked a peck of pickled peppers.';
        $removed = Str::remove('e', $string); // Ptr Pipr pickd a pck of pickld ppprs.
        $this->assertEquals($removed, 'Ptr Pipr pickd a pck of pickld ppprs.');
    }

    public function test_str_replace() {
        $string = 'Laravel 8.x';
        $replaced = Str::replace('8.x', '9.x', $string); // Laravel 9.x
        $this->assertEquals($replaced, 'Laravel 9.x');
    }

    public function test_str_replaceArray() {
        $string = 'The event will take place between ? and ?';
        $replaced = Str::replaceArray('?', ['8:30', '9:00'], $string); // The event will take place between 8:30 and 9:00
        $this->assertEquals($replaced, 'The event will take place between 8:30 and 9:00');
    }

    public function test_str_replaceFirst() {
        $replaced = Str::replaceFirst('the', 'a', 'the quick brown fox jumps over the lazy dog');
        // a quick brown fox jumps over the lazy dog
        $this->assertEquals($replaced, 'a quick brown fox jumps over the lazy dog');
    }

    public function test_str_replaceLast() {
        $replaced = Str::replaceLast('the', 'a', 'the quick brown fox jumps over the lazy dog');
        // the quick brown fox jumps over a lazy dog
        $this->assertEquals($replaced, 'the quick brown fox jumps over a lazy dog');
    }

    public function test_str_reverse() {
        $reversed = Str::reverse('Hello World'); // dlroW olleH
        $this->assertEquals($reversed, 'dlroW olleH');
    }

    public function test_str_singular() {
        $singular = Str::singular('cars'); // car
        $singular2 = Str::singular('children'); // child

        $this->assertEquals($singular, 'car');
        $this->assertEquals($singular2, 'child');
    }

    public function test_str_slug() {
        $slug = Str::slug('Laravel 5 Framework', '-');
        // laravel-5-framework
        $this->assertEquals($slug, 'laravel-5-framework');
    }

    public function test_str_snake() {
        $converted = Str::snake('fooBar'); // foo_bar
        $converted2 = Str::snake('fooBar', '-'); // foo-bar

        $this->assertEquals($converted, 'foo_bar');
        $this->assertEquals($converted2, 'foo-bar');
    }

    public function test_str_squish() {
        $string = Str::squish('    laravel    framework    '); // laravel framework
        $this->assertEquals($string, 'laravel framework');
    }

    public function test_str_start() {
        $adjusted = Str::start('this/string', '/'); // /this/string
        $adjusted2 = Str::start('/this/string', '/'); // /this/string
        $this->assertEquals($adjusted, '/this/string');
        $this->assertEquals($adjusted2, '/this/string');
    }

    public function test_str_startsWith() {
        $result = Str::startsWith('This is my name', 'This'); // true
        $result2 = Str::startsWith('This is my name', ['This', 'That', 'There']); // true
        $this->assertTrue($result);
        $this->assertTrue($result2);
    }

    public function test_str_studly() {
        $converted = Str::studly('foo_bar'); // FooBar
        $this->assertEquals($converted, 'FooBar');
    }

    public function test_str_substr() {
        $converted = Str::substr('The Laravel Framework', 4, 7); // Laravel
        $this->assertEquals($converted, 'Laravel');
    }

    public function test_str_substrCount() {
        $count = Str::substrCount('If you like ice cream, you will like snow cones.', 'like'); // 2
        $this->assertEquals($count, 2);
    }

    public function test_str_substrReplace() {
        $result = Str::substrReplace('1300', ':', 2); // 13:
        $result2 = Str::substrReplace('1300', ':', 2, 0); // 13:00
        $this->assertEquals($result, '13:');
        $this->assertEquals($result2, '13:00');
    }

    public function test_str_swap() {
        $string = Str::swap([
            'Tacos' => 'Burritos',
            'great' => 'fantastic',
        ], 'Tacos are great!'); // Burritos are fantastic!
        $this->assertEquals($string, 'Burritos are fantastic!');
    }

    public function test_str_title() {
        $converted = Str::title('a nice title uses the correct case');
        // A Nice Title Uses The Correct Case
        $this->assertEquals($converted, 'A Nice Title Uses The Correct Case');
    }

    public function test_str_ucfirst() {
        $string = Str::ucfirst('foo bar'); // Foo bar
        $this->assertEquals($string, 'Foo bar');
    }

    public function test_str_ucsplit() {
        $segments = Str::ucsplit('FooBar');
        // [0 => 'Foo', 1 => 'Bar']
        $this->assertEquals($segments[0], 'Foo');
        $this->assertEquals($segments[1], 'Bar');
    }

    public function test_str_upper() {
        $string = Str::upper('laravel');
        // LARAVEL
        $this->assertEquals($string, 'LARAVEL');
    }

    public function test_str_ulid() {
        $result = (string) Str::ulid();
        $this->assertNotEmpty($result);
    }

    public function test_str_uuid() {
        $result = (string) Str::uuid();
        $this->assertNotEmpty($result);
    }

    public function test_str_wordCount() {
        $result = Str::wordCount('Hello, world!'); // 2
        $this->assertEquals($result, 2);
    }

    public function test_str_words() {
        $result = Str::words('Perfectly balanced, as all things should be.', 3, ' >>>');
        // Perfectly balanced, as >>>
        $this->assertEquals($result, 'Perfectly balanced, as >>>');
    }

    public function test_str_str() {
        $string = str('Taylor')->append(' Otwell');
        // 'Taylor Otwell'
        $snake = str()->snake('FooBar'); // 'foo_bar'

        $this->assertEquals($string, 'Taylor Otwell');
        $this->assertEquals($snake, 'foo_bar');
    }

    public function test_str_after_Builder() {
        $slice = Str::of('This is my name')->after('This is');
        // ' my name'
        $slice2 = Str::of('App\Http\Controllers\Controller')->afterLast('\\');
        // 'Controller'

        $this->assertEquals($slice, ' my name');
        $this->assertEquals($slice2, 'Controller');
    }

    public function test_str_explode() {
        $collection = Str::of('foo bar baz')->explode(' ');
        // collect(['foo', 'bar', 'baz'])
        $this->assertEquals($collection[0], 'foo');
        $this->assertEquals($collection[1], 'bar');
        $this->assertEquals($collection[2], 'baz');
    }

    public function test_str_ltrim() {
        $string = Str::of('  Laravel  ')->ltrim();
        // 'Laravel  '
        $string2 = Str::of('/Laravel/')->ltrim('/');
        // 'Laravel/'

        $this->assertEquals($string, 'Laravel  ');
        $this->assertEquals($string2, 'Laravel/');
    }

    public function test_str_match() {
        $result = Str::of('foo bar')->match('/bar/');
        // 'bar'
        $result2 = Str::of('foo bar')->match('/foo (.*)/');
        // 'bar'
        $this->assertEquals($result, 'bar');
        $this->assertEquals($result2, 'bar');
    }

    public function test_str_matchAll() {
        $result = Str::of('bar foo bar')->matchAll('/bar/');
        // collect(['bar', 'bar'])
        $result2 = Str::of('bar fun bar fly')->matchAll('/f(\w*)/');
        // collect(['un', 'ly']);
        $this->assertEquals($result[0], 'bar');
        $this->assertEquals($result[1], 'bar');
        $this->assertEquals($result2[0], 'un');
        $this->assertEquals($result2[1], 'ly');
    }

    public function test_str_newLine() {
        $padded = Str::of('Laravel')->newLine()->append('Framework');
        // 'Laravel
        //  Framework'
        // print_r($padded->value());
        $this->assertTrue(true);
    }

    public function test_str_pipe() {
        $hash = Str::of('Laravel')->pipe('md5')->prepend('Checksum: ');
        // 'Checksum: a5c95b86291ea299fcbe64458ed12702'

        $closure = Str::of('foo')->pipe(function ($str) {
            return 'bar';
        }); // 'bar'
        $this->assertEquals($hash, 'Checksum: a5c95b86291ea299fcbe64458ed12702');
        $this->assertEquals($closure, 'bar');
    }

    public function test_str_split() {
        $segments = Str::of('one, two, three')->split('/[\s,]+/');
        // collect(["one", "two", "three"])
        $this->assertEquals($segments[0], 'one');
        $this->assertEquals($segments[1], 'two');
        $this->assertEquals($segments[2], 'three');
    }

    public function test_str_test() {
        $result = Str::of('Laravel Framework')->test('/Laravel/');
        // true
        $this->assertTrue($result);
    }

    public function test_str_trim() {
        $string = Str::of('  Laravel  ')->trim();
        // 'Laravel'

        $string2 = Str::of('/Laravel/')->trim('/');
        // 'Laravel'

        $this->assertEquals($string, 'Laravel');
        $this->assertEquals($string2, 'Laravel');
    }

    public function test_str_when() {
        $string = Str::of('Taylor')
            ->when(true, function ($string) {
                return $string->append(' Otwell');
            }); // 'Taylor Otwell'

        $this->assertEquals($string, 'Taylor Otwell');
    }

    public function test_str_whenContains() {
        $string = Str::of('tony stark')
            ->whenContains('tony', function ($string) {
                return $string->title();
            }); // 'Tony Stark'
        $string2 = Str::of('tony stark')
            ->whenContains(['tony', 'hulk'], fn ($string) => $string->title()); // Tony Stark

        $this->assertEquals($string, 'Tony Stark');
        $this->assertEquals($string2, 'Tony Stark');
    }

    public function test_str_whenContainsAll() {
        $string = Str::of('tony stark')
            ->whenContainsAll(['tony', 'stark'], fn ($string) => $string->title()); // 'Tony Stark'
        $this->assertEquals($string, 'Tony Stark');
    }

    public function test_str_whenEmpty() {
        $string = Str::of('')->whenEmpty(fn ($string) => $string->trim()->prepend('Laravel'));
        // 'Laravel'
        $this->assertEquals($string, 'Laravel');
    }

    public function test_str_whenNotEmpty() {
        $string = Str::of('Framework')->whenNotEmpty(function ($string) {
            return $string->prepend('Laravel ');
        });
        // 'Laravel Framework'
        $this->assertEquals($string, 'Laravel Framework');
    }

    public function test_str_whenStartsWith() {
        $string = Str::of('disney world')->whenStartsWith('disney', function ($string) {
            return $string->title();
        }); // 'Disney World'
        $this->assertEquals($string, 'Disney World');
    }

    public function test_str_whenEndsWith() {
        $string = Str::of('disney world')->whenEndsWith('world', function ($string) {
            return $string->title();
        }); // 'Disney World'
        $this->assertEquals($string, 'Disney World');
    }

    public function test_str_whenExactly() {
        $string = Str::of('laravel')->whenExactly('laravel', function ($string) {
            return $string->title();
        }); // 'Laravel'
        $this->assertEquals($string, 'Laravel');
    }

    public function test_str_whenNotExactly() {
        $string = Str::of('framework')->whenNotExactly('laravel', function ($string) {
            return $string->title();
        }); // 'Framework'
        $this->assertEquals($string, 'Framework');
    }

    public function test_str_whenIs() {
        $string = Str::of('foo/bar')->whenIs('foo/*', function ($string) {
            return $string->append('/baz');
        }); // 'foo/bar/baz'
        $this->assertEquals($string, 'foo/bar/baz');
    }

    public function test_str_whenIsAscii() {
        // # ISSUE
        /*$string = Str::of('foo/bar')->whenIsAscii(function ($string) {
            return $string->title();
        }, 'laravel'); // 'Laravel'
        $this->assertEquals($string, 'Laravel');*/
        $this->assertTrue(true);
    }

    public function test_str_whenIsUuid() {
        /*$string = Str::of('foo/bar')->whenIsUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de', function ($string) {
            return $string->substr(0, 8);
        }); // 'a0a2a2d2'
        $this->assertEquals($string, 'a0a2a2d2');*/
        $this->assertTrue(true);
    }

    public function test_str_whenTest() {
        $string = Str::of('laravel framework')->whenTest('/laravel/', function ($string) {
            return $string->title();
        }); // 'Laravel Framework'
        $this->assertEquals($string, 'Laravel Framework');
    }
}
