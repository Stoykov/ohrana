<?php
namespace stoykov\Ohrana\Exceptions;

class NoRepositoryException extends \Exception
{
    public function __construct($method)
    {
        parent::__construct('No repository set in ' . $method);
    }
}