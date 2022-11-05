<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleCollectionExtendTest extends TestCase
{
    public function test_collection_extend_toUpper() {
        // extend method to collection
        Collection::macro('toUpper', function () {
            return $this->map(function ($value) {
                return Str::upper($value);
            });
        });

        $collection = collect(['first', 'second']);
        $result = $collection->toUpper();
        $this->assertEquals($result[0], "FIRST");
        $this->assertEquals($result[1], "SECOND");
    }

    public function test_collection_extend_toLocale() {
        Collection::macro('toLocale', function ($locale) {
            return $this->map(function ($value) use ($locale) {
                return Lang::get($value, [], $locale);
            });
        });

        $collection = collect(['auth.throttle', 'auth.password', 'auth.failed']);

        $translated = $collection->toLocale('en');
        $this->assertEquals($translated[0], "Too many login attempts. Please try again in :seconds seconds.");
        $this->assertEquals($translated[1], "The provided password is incorrect.");
        $this->assertEquals($translated[2], "These credentials do not match our records.");
    }
}
