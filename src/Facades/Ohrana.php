<?php
namespace stoykov\Ohrana\Facades;

use Illuminate\Support\Facades\Facade;

class Ohrana extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ohrana';
    }
}