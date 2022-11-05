<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class ResourceCollection
{
    /**
     * The Collection instance.
     */
    public $collection;

    /**
     * Create a new ResourceCollection instance.
     *
     * @param  Collection  $collection
     * @return void
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }
}
